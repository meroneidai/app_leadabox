<?php

namespace App\Http\Controllers;

use App\Actions\CalculateInvoiceTotals;
use App\Actions\CreateCreditNote;
use App\Actions\IssueInvoice;
use App\Actions\RecordPayment;
use App\Actions\SendInvoice;
use App\Enums\InvoiceStatus;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly CalculateInvoiceTotals $totals,
        private readonly IssueInvoice $issuer,
        private readonly RecordPayment $payments,
        private readonly CreateCreditNote $credits,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Invoice::class);

        $status = $request->query('status');

        $invoices = Invoice::query()
            ->forCompany($request->user()->company_id)
            ->search($request->query('q'))
            ->when($status === 'overdue', fn ($query) => $query->overdue())
            ->when(
                in_array($status, InvoiceStatus::values(), true),
                fn ($query) => $query->where('status', $status)
            )
            ->with('customer')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('invoices.index', [
            'invoices' => $invoices,
            'activeStatus' => $status,
            'search' => $request->query('q'),
            'company' => Company::current(),
            'totals' => [
                'outstanding' => Invoice::forCompany($request->user()->company_id)
                    ->whereIn('status', [InvoiceStatus::Issued, InvoiceStatus::PartiallyPaid])
                    ->sum(DB::raw('total - amount_paid')),
                'overdue' => Invoice::forCompany($request->user()->company_id)
                    ->overdue()
                    ->sum(DB::raw('total - amount_paid')),
            ],
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Invoice::class);

        $company = Company::current();

        return view('invoices.create', [
            'customers' => $this->customers($request),
            'services' => $this->services($request),
            'projects' => $this->projects($request),
            'company' => $company,
            'issueDate' => now()->toDateString(),
            'paymentTerms' => $company?->payment_terms_days ?? 30,
        ]);
    }

    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $invoice = DB::transaction(function () use ($data, $request) {
            $company = Company::current();

            $invoice = new Invoice($data);
            $invoice->company_id = $request->user()->company_id ?? $company?->id;
            $invoice->created_by = $request->user()->id;
            $invoice->currency = $company?->currency ?? 'SEK';
            $invoice->status = InvoiceStatus::Draft;
            $invoice->due_date = now()->parse($data['issue_date'])->addDays((int) $data['payment_terms_days']);
            $invoice->save();

            $this->syncLines($invoice, $data['lines']);

            return $invoice;
        });

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('status', __('invoices.created'));
    }

    public function show(Invoice $invoice): View
    {
        $this->authorize('view', $invoice);

        $invoice->load('lines', 'customer', 'contact', 'payments.recorder', 'quotation', 'creditedInvoice', 'creditNotes');

        return view('invoices.show', [
            'invoice' => $invoice,
            'company' => Company::current(),
        ]);
    }

    public function edit(Request $request, Invoice $invoice): View
    {
        $this->authorize('update', $invoice);

        $invoice->load('lines');

        return view('invoices.edit', [
            'invoice' => $invoice,
            'customers' => $this->customers($request),
            'services' => $this->services($request),
            'projects' => $this->projects($request),
            'company' => Company::current(),
        ]);
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $invoice) {
            $invoice->update([
                ...$data,
                'due_date' => now()->parse($data['issue_date'])->addDays((int) $data['payment_terms_days']),
            ]);

            $invoice->lines()->delete();
            $invoice->unsetRelation('lines');

            $this->syncLines($invoice, $data['lines']);
        });

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('status', __('invoices.updated'));
    }

    public function issue(Invoice $invoice): RedirectResponse
    {
        $this->authorize('issue', $invoice);

        $this->issuer->handle($invoice);

        return back()->with('status', __('invoices.issued', ['number' => $invoice->number]));
    }

    public function storePayment(StorePaymentRequest $request, Invoice $invoice): RedirectResponse
    {
        $this->payments->handle($invoice, $request->validated(), $request->user()->id);

        return back()->with('status', __('invoices.payment_recorded'));
    }

    /** Emails the invoice with its PDF and a link the customer can open. */
    public function email(Request $request, Invoice $invoice, SendInvoice $sender): RedirectResponse
    {
        $this->authorize('view', $invoice);
        abort_unless($invoice->status->isIssued(), 403);

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $sender->handle($invoice, $validated['message'] ?? null);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', __('invoices.email_failed', ['reason' => $e->getMessage()]));
        }

        return back()->with('status', __('invoices.email_sent', ['email' => $invoice->fresh()->sent_to]));
    }

    public function credit(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('credit', $invoice);

        $credit = $this->credits->handle($invoice, $request->user()->id);

        return redirect()
            ->route('invoices.show', $credit)
            ->with('status', __('invoices.credited', ['number' => $credit->number]));
    }

    public function pdf(Invoice $invoice): Response
    {
        $this->authorize('view', $invoice);

        $invoice->load('lines', 'customer', 'contact');

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'company' => Company::current(),
        ])->setPaper('a4');

        return $pdf->download($invoice->reference().'.pdf');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $this->authorize('delete', $invoice);

        $invoice->delete();

        return redirect()
            ->route('invoices.index')
            ->with('status', __('invoices.deleted'));
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     */
    private function syncLines(Invoice $invoice, array $lines): void
    {
        foreach (array_values($lines) as $index => $line) {
            $invoice->lines()->create([
                ...$line,
                'discount_percent' => $line['discount_percent'] ?? 0,
                'sort_order' => $index,
            ]);
        }

        $this->totals->handle($invoice->load('lines'));
    }

    private function customers(Request $request)
    {
        return Customer::query()
            ->forCompany($request->user()->company_id)
            ->with('contacts')
            ->orderBy('name')
            ->get();
    }

    private function services(Request $request)
    {
        return Service::query()
            ->forCompany($request->user()->company_id)
            ->active()
            ->orderBy('name')
            ->get();
    }

    private function projects(Request $request)
    {
        return Project::query()
            ->where('company_id', $request->user()->company_id)
            ->orderBy('name')
            ->get();
    }
}
