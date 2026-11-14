<?php

namespace App\Http\Controllers;

use App\Actions\AllocateDocumentNumber;
use App\Actions\CalculateQuotationTotals;
use App\Actions\ConvertQuotationToInvoice;
use App\Actions\SendQuotation;
use App\Enums\QuotationStatus;
use App\Http\Requests\StoreQuotationRequest;
use App\Http\Requests\UpdateQuotationRequest;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function __construct(
        private readonly CalculateQuotationTotals $totals,
        private readonly AllocateDocumentNumber $numbers,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Quotation::class);

        $status = $request->query('status');

        $quotations = Quotation::query()
            ->forCompany($request->user()->company_id)
            ->search($request->query('q'))
            ->when(
                in_array($status, QuotationStatus::values(), true),
                fn ($query) => $query->where('status', $status)
            )
            ->with('customer')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('quotations.index', [
            'quotations' => $quotations,
            'activeStatus' => $status,
            'search' => $request->query('q'),
            'company' => Company::current(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Quotation::class);

        $company = Company::current();

        return view('quotations.create', [
            'customers' => $this->customers($request),
            'services' => $this->services($request),
            'company' => $company,
            'issueDate' => now()->toDateString(),
            'validUntil' => now()->addDays($company?->quotation_valid_days ?? 30)->toDateString(),
        ]);
    }

    public function store(StoreQuotationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $quotation = DB::transaction(function () use ($data, $request) {
            $company = Company::current();

            $quotation = new Quotation($data);
            $quotation->company_id = $request->user()->company_id ?? $company?->id;
            $quotation->created_by = $request->user()->id;
            $quotation->currency = $company?->currency ?? 'SEK';
            $quotation->status = QuotationStatus::Draft;
            $quotation->save();

            $this->syncLines($quotation, $data['lines']);

            return $quotation;
        });

        return redirect()
            ->route('quotations.show', $quotation)
            ->with('status', __('quotations.created'));
    }

    public function show(Quotation $quotation): View
    {
        $this->authorize('view', $quotation);

        $quotation->load('lines', 'customer.contacts', 'contact', 'creator');

        return view('quotations.show', [
            'quotation' => $quotation,
            'company' => Company::current(),
        ]);
    }

    public function edit(Request $request, Quotation $quotation): View
    {
        $this->authorize('update', $quotation);

        $quotation->load('lines');

        return view('quotations.edit', [
            'quotation' => $quotation,
            'customers' => $this->customers($request),
            'services' => $this->services($request),
            'company' => Company::current(),
        ]);
    }

    public function update(UpdateQuotationRequest $request, Quotation $quotation): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $quotation) {
            $quotation->update($data);

            $quotation->lines()->delete();
            $quotation->unsetRelation('lines');

            $this->syncLines($quotation, $data['lines']);
        });

        return redirect()
            ->route('quotations.show', $quotation)
            ->with('status', __('quotations.updated'));
    }

    /** Allocates the number and locks the document. */
    public function send(Quotation $quotation): RedirectResponse
    {
        $this->authorize('send', $quotation);

        DB::transaction(function () use ($quotation) {
            $quotation->number ??= $this->numbers->handle($quotation->company_id, AllocateDocumentNumber::QUOTATION);
            $quotation->status = QuotationStatus::Sent;
            $quotation->sent_at = now();
            $quotation->save();
        });

        return back()->with('status', __('quotations.sent', ['number' => $quotation->number]));
    }

    /** Emails the quotation with its PDF and a link the customer can answer on. */
    public function email(Request $request, Quotation $quotation, SendQuotation $sender): RedirectResponse
    {
        $this->authorize('send', $quotation);

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $sender->handle($quotation, $validated['message'] ?? null);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', __('quotations.email_failed', ['reason' => $e->getMessage()]));
        }

        return back()->with('status', __('quotations.email_sent', ['email' => $quotation->fresh()->sent_to]));
    }

    public function accept(Request $request, Quotation $quotation): RedirectResponse
    {
        $this->authorize('decide', $quotation);

        $quotation->forceFill([
            'status' => QuotationStatus::Accepted,
            'accepted_at' => now(),
            'decision_note' => $request->string('decision_note')->toString() ?: null,
        ])->save();

        return back()->with('status', __('quotations.accepted'));
    }

    public function reject(Request $request, Quotation $quotation): RedirectResponse
    {
        $this->authorize('decide', $quotation);

        $quotation->forceFill([
            'status' => QuotationStatus::Rejected,
            'rejected_at' => now(),
            'decision_note' => $request->string('decision_note')->toString() ?: null,
        ])->save();

        return back()->with('status', __('quotations.rejected'));
    }

    public function pdf(Quotation $quotation): Response
    {
        $this->authorize('view', $quotation);

        $quotation->load('lines', 'customer', 'contact');

        $pdf = Pdf::loadView('quotations.pdf', [
            'quotation' => $quotation,
            'company' => Company::current(),
        ])->setPaper('a4');

        return $pdf->download($quotation->reference().'.pdf');
    }

    /** Copies an accepted quotation into a draft invoice. */
    public function convert(Request $request, Quotation $quotation, ConvertQuotationToInvoice $converter): RedirectResponse
    {
        $this->authorize('view', $quotation);
        $this->authorize('create', Invoice::class);

        $invoice = $converter->handle($quotation->load('lines', 'customer'), $request->user()->id);

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('status', __('invoices.converted'));
    }

    public function destroy(Quotation $quotation): RedirectResponse
    {
        $this->authorize('delete', $quotation);

        $quotation->delete();

        return redirect()
            ->route('quotations.index')
            ->with('status', __('quotations.deleted'));
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     */
    private function syncLines(Quotation $quotation, array $lines): void
    {
        foreach (array_values($lines) as $index => $line) {
            $quotation->lines()->create([
                ...$line,
                'discount_percent' => $line['discount_percent'] ?? 0,
                'sort_order' => $index,
            ]);
        }

        $this->totals->handle($quotation->load('lines'));
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
}
