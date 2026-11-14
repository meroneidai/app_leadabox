<?php

namespace App\Http\Controllers;

use App\Enums\CustomerStatus;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Company;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Customer::class);

        $status = $request->query('status');

        $customers = Customer::query()
            ->forCompany($request->user()->company_id)
            ->search($request->query('q'))
            ->when(
                in_array($status, CustomerStatus::values(), true),
                fn ($query) => $query->where('status', $status)
            )
            ->withCount('projects')
            ->with('contacts')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('customers.index', [
            'customers' => $customers,
            'activeStatus' => $status,
            'search' => $request->query('q'),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Customer::class);

        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $customer = DB::transaction(function () use ($request) {
            $company = Company::current();

            $customer = new Customer($request->validated());
            $customer->company_id = $request->user()->company_id ?? $company?->id;
            $customer->save();

            if ($request->filled('contact_name')) {
                $customer->contacts()->create([
                    'name' => $request->input('contact_name'),
                    'email' => $request->input('contact_email'),
                    'phone' => $request->input('contact_phone'),
                    'job_title' => $request->input('contact_job_title'),
                    'is_primary' => true,
                    'receives_quotations' => true,
                    'receives_invoices' => true,
                    'receives_project_updates' => true,
                    'receives_renewals' => true,
                ]);
            }

            return $customer;
        });

        return redirect()
            ->route('customers.show', $customer)
            ->with('status', __('customers.created'));
    }

    public function show(Customer $customer): View
    {
        $this->authorize('view', $customer);

        $customer->load([
            'contacts' => fn ($query) => $query->orderByDesc('is_primary')->orderBy('name'),
            'projects' => fn ($query) => $query->latest()->limit(10),
        ]);

        return view('customers.show', ['customer' => $customer]);
    }

    public function edit(Customer $customer): View
    {
        $this->authorize('update', $customer);

        return view('customers.edit', ['customer' => $customer]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return redirect()
            ->route('customers.show', $customer)
            ->with('status', __('customers.updated'));
    }

    /** Soft deleted so historical quotations, invoices and projects keep their customer. */
    public function destroy(Customer $customer): RedirectResponse
    {
        $this->authorize('delete', $customer);

        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('status', __('customers.deleted'));
    }
}
