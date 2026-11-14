<?php

namespace App\Http\Controllers;

use App\Enums\BillingType;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Company;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Service::class);

        $type = $request->query('type');

        $services = Service::query()
            ->forCompany($request->user()->company_id)
            ->search($request->query('q'))
            ->when(
                in_array($type, BillingType::values(), true),
                fn ($query) => $query->where('billing_type', $type)
            )
            ->with('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Service $service) => $service->category?->name ?? __('services.uncategorised'));

        return view('services.index', [
            'grouped' => $services,
            'activeType' => $type,
            'search' => $request->query('q'),
            'company' => Company::current(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Service::class);

        return view('services.create', [
            'categories' => $this->categories(),
            'company' => Company::current(),
        ]);
    }

    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $service = new Service($request->validated());
        $service->company_id = $request->user()->company_id ?? Company::current()?->id;
        $service->save();

        return redirect()
            ->route('services.index')
            ->with('status', __('services.created'));
    }

    public function edit(Service $service): View
    {
        $this->authorize('update', $service);

        return view('services.edit', [
            'service' => $service,
            'categories' => $this->categories(),
            'company' => Company::current(),
        ]);
    }

    public function update(UpdateServiceRequest $request, Service $service): RedirectResponse
    {
        $service->update($request->validated());

        return redirect()
            ->route('services.index')
            ->with('status', __('services.updated'));
    }

    public function destroy(Service $service): RedirectResponse
    {
        $this->authorize('delete', $service);

        // Deactivated rather than deleted so historical quotation lines stay intact.
        $service->update(['is_active' => false]);

        return redirect()
            ->route('services.index')
            ->with('status', __('services.deactivated'));
    }

    /**
     * @return Collection<int, ServiceCategory>
     */
    private function categories()
    {
        return ServiceCategory::query()
            ->where('company_id', auth()->user()->company_id)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
}
