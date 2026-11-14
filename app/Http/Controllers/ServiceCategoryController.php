<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ServiceCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Service::class);

        $categories = ServiceCategory::query()
            ->where('company_id', $request->user()->company_id)
            ->withCount('services')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('services.categories', ['categories' => $categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Service::class);

        $validated = $this->validateCategory($request, $request->user()->company_id);

        $category = new ServiceCategory($validated);
        $category->company_id = $request->user()->company_id;
        $category->save();

        return back()->with('status', __('services.categories.created'));
    }

    public function update(Request $request, ServiceCategory $category): RedirectResponse
    {
        $this->authorizeCategory($request, $category);

        $validated = $this->validateCategory($request, $category->company_id, $category->id);

        // Keep the slug in step with a renamed category.
        $category->fill($validated)->slug = str($validated['name'])->slug()->value();
        $category->save();

        return back()->with('status', __('services.categories.updated'));
    }

    public function destroy(Request $request, ServiceCategory $category): RedirectResponse
    {
        $this->authorizeCategory($request, $category);

        if ($category->services()->exists()) {
            throw ValidationException::withMessages([
                'category' => __('services.categories.in_use'),
            ]);
        }

        $category->delete();

        return back()->with('status', __('services.categories.deleted'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validateCategory(Request $request, ?int $companyId, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('service_categories', 'name')
                    ->where(fn ($query) => $query->where('company_id', $companyId))
                    ->ignore($ignoreId),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);
    }

    private function authorizeCategory(Request $request, ServiceCategory $category): void
    {
        $this->authorize('create', Service::class);

        abort_unless($request->user()->company_id === $category->company_id, 403);
    }
}
