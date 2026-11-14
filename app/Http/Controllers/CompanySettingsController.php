<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CompanySettingsController extends Controller
{
    public function edit(): View
    {
        $this->authorize('manageSettings', Company::class);

        return view('settings.company', [
            'company' => Company::current(),
            'locales' => config('app.supported_locales'),
        ]);
    }

    public function update(UpdateCompanyRequest $request): RedirectResponse
    {
        $company = Company::current();

        $company->update($request->validated());

        Company::forgetCurrent();

        return redirect()
            ->route('settings.company.edit')
            ->with('status', __('settings.updated'));
    }
}
