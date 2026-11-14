<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
use App\Enums\QuotationStatus;
use App\Enums\TaskStatus;
use App\Models\Comment;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Task;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $companyId = $user->company_id;

        $projectIds = Project::accessibleBy($user)->pluck('id');

        return view('dashboard', [
            'stats' => $this->stats($user, $companyId, $projectIds),
            'myTasks' => $this->myTasks($user, $projectIds),
            'overdueInvoices' => $user->can('view invoices')
                ? Invoice::forCompany($companyId)->overdue()->with('customer')->orderBy('due_date')->take(5)->get()
                : collect(),
            'unreadMessages' => Comment::query()
                ->whereNull('user_id')
                ->whereNull('read_at')
                ->with('commentable')
                ->latest('id')
                ->take(5)
                ->get(),
            'recentQuotations' => $user->can('view quotations')
                ? Quotation::forCompany($companyId)->with('customer')->latest('id')->take(5)->get()
                : collect(),
            'projects' => Project::accessibleBy($user)
                ->withCount([
                    'tasks',
                    'tasks as completed_tasks_count' => fn ($query) => $query->where('status', TaskStatus::Done),
                ])
                ->latest()
                ->take(4)
                ->get(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function stats($user, ?int $companyId, $projectIds): array
    {
        $openQuotations = Quotation::forCompany($companyId)->where('status', QuotationStatus::Sent);

        return [
            'customers' => $user->can('view customers')
                ? Customer::forCompany($companyId)->count()
                : null,
            'open_quotations' => $user->can('view quotations') ? (clone $openQuotations)->count() : null,
            'open_quotations_value' => $user->can('view quotations')
                ? Money::format((string) ((clone $openQuotations)->sum('total') ?: 0))
                : null,
            'open_projects' => Project::accessibleBy($user)->where('status', ProjectStatus::Active)->count(),
            'overdue_invoices' => $user->can('view invoices')
                ? Invoice::forCompany($companyId)->overdue()->count()
                : null,
            'overdue_tasks' => Task::whereIn('project_id', $projectIds)
                ->where('status', '!=', TaskStatus::Done)
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', now())
                ->count(),
        ];
    }

    private function myTasks($user, $projectIds)
    {
        return Task::query()
            ->with('project')
            ->whereIn('project_id', $projectIds)
            ->where('assigned_to', $user->id)
            ->where('status', '!=', TaskStatus::Done)
            ->orderByRaw('due_date is null, due_date asc')
            ->take(6)
            ->get();
    }
}
