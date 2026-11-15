<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\CompanySettingsController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\PublicInvoiceController;
use App\Http\Controllers\PublicProjectController;
use App\Http\Controllers\PublicQuotationController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::put('locale/{locale}', [LocaleController::class, 'update'])->name('locale.update');

// Customer-facing quotation approval. The token is the only credential.
Route::prefix('q/{token}')->name('public.quotations.')->middleware('throttle:30,1')->group(function () {
    Route::get('/', [PublicQuotationController::class, 'show'])->name('show');
    Route::post('accept', [PublicQuotationController::class, 'accept'])->name('accept');
    Route::post('reject', [PublicQuotationController::class, 'reject'])->name('reject');
    Route::post('comment', [PublicQuotationController::class, 'comment'])->name('comment');
});

// Customer project tracking, no account required.
Route::prefix('p/{token}')->name('public.projects.')->middleware('throttle:30,1')->group(function () {
    Route::get('/', [PublicProjectController::class, 'show'])->name('show');
    Route::post('comment', [PublicProjectController::class, 'comment'])->name('comment');
});

Route::get('i/{token}', [PublicInvoiceController::class, 'show'])
    ->middleware('throttle:30,1')
    ->name('public.invoices.show');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:6,1');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:6,1')->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->middleware('throttle:6,1')->name('password.store');
});

Route::redirect('register', '/login')->name('register.disabled');

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::middleware('can:view customers')->group(function () {
        Route::resource('customers', CustomerController::class);

        Route::post('customers/{customer}/contacts', [ContactController::class, 'store'])->name('customers.contacts.store');
        Route::put('contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
        Route::delete('contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
    });

    Route::middleware('can:view services')->group(function () {
        Route::get('services/categories', [ServiceCategoryController::class, 'index'])->name('service-categories.index');
        Route::post('services/categories', [ServiceCategoryController::class, 'store'])->name('service-categories.store');
        Route::put('services/categories/{category}', [ServiceCategoryController::class, 'update'])->name('service-categories.update');
        Route::delete('services/categories/{category}', [ServiceCategoryController::class, 'destroy'])->name('service-categories.destroy');

        Route::resource('services', ServiceController::class)->except('show');
    });

    Route::middleware('can:view quotations')->group(function () {
        Route::resource('quotations', QuotationController::class);
        Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('quotations.pdf');
        Route::post('quotations/{quotation}/send', [QuotationController::class, 'send'])->name('quotations.send');
        Route::post('quotations/{quotation}/email', [QuotationController::class, 'email'])->name('quotations.email');
        Route::post('quotations/{quotation}/convert', [QuotationController::class, 'convert'])->name('quotations.convert');
        Route::post('quotations/{quotation}/accept', [QuotationController::class, 'accept'])->name('quotations.accept');
        Route::post('quotations/{quotation}/reject', [QuotationController::class, 'reject'])->name('quotations.reject');
    });

    Route::middleware('can:view invoices')->group(function () {
        Route::resource('invoices', InvoiceController::class);
        Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
        Route::post('invoices/{invoice}/issue', [InvoiceController::class, 'issue'])->name('invoices.issue');
        Route::post('invoices/{invoice}/payments', [InvoiceController::class, 'storePayment'])->name('invoices.payments.store');
        Route::post('invoices/{invoice}/credit', [InvoiceController::class, 'credit'])->name('invoices.credit');
        Route::post('invoices/{invoice}/email', [InvoiceController::class, 'email'])->name('invoices.email');
    });

    Route::middleware('can:view projects')->group(function () {
        Route::resource('projects', ProjectController::class);

        Route::post('projects/{project}/members', [ProjectMemberController::class, 'store'])->name('projects.members.store');
        Route::delete('projects/{project}/members/{user}', [ProjectMemberController::class, 'destroy'])->name('projects.members.destroy');

        Route::post('projects/{project}/tasks', [TaskController::class, 'store'])->name('projects.tasks.store');
        Route::get('tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
        Route::put('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
        Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    });

    Route::middleware('can:view team')->group(function () {
        Route::get('team', [TeamController::class, 'index'])->name('team.index');
        Route::get('team/create', [TeamController::class, 'create'])->middleware('can:manage team')->name('team.create');
        Route::post('team', [TeamController::class, 'store'])->middleware('can:manage team')->name('team.store');
        Route::post('team/{user}/send-invite', [TeamController::class, 'sendInvite'])->middleware('can:manage team')->name('team.send-invite');
        Route::get('team/{user}/edit', [TeamController::class, 'edit'])->name('team.edit');
        Route::put('team/{user}', [TeamController::class, 'update'])->name('team.update');
        Route::patch('team/{user}/toggle-active', [TeamController::class, 'toggleActive'])->middleware('can:manage team')->name('team.toggle-active');
    });

    Route::middleware('can:manage settings')->group(function () {
        Route::get('settings/company', [CompanySettingsController::class, 'edit'])->name('settings.company.edit');
        Route::put('settings/company', [CompanySettingsController::class, 'update'])->name('settings.company.update');
    });
});
