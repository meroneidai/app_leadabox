<?php

namespace App\Http\Controllers;

use App\Actions\NotifyTeam;
use App\Models\Company;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Customer-facing project tracking. Internal notes and costs are never exposed here.
 */
class PublicProjectController extends Controller
{
    public function show(string $token): View
    {
        $project = $this->resolve($token);

        return view('projects.public', [
            'project' => $project,
            'company' => Company::current(),
            'tasks' => $project->tasks()->with('assignee')->orderBy('id')->get(),
            'comments' => $project->publicComments()->with('user', 'contact')->get(),
        ]);
    }

    public function comment(Request $request, string $token, NotifyTeam $notifier): RedirectResponse
    {
        $project = $this->resolve($token);

        $validated = $request->validate([
            'author_name' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $contact = $project->customer?->primaryContact();

        $project->addCustomerComment(
            $validated['body'],
            $contact?->id,
            $validated['author_name'] ?? $contact?->name,
        );

        $notifier->handle(
            headline: __('comments.alert_project_headline', ['project' => $project->name]),
            summary: __('comments.alert_project_summary', [
                'name' => $validated['author_name'] ?? $contact?->name ?? __('comments.customer'),
                'project' => $project->name,
            ]),
            details: [
                ['label' => __('customers.singular'), 'value' => $project->customer?->name ?? '—'],
                ['label' => __('projects.title'), 'value' => $project->name],
            ],
            actionUrl: route('projects.show', $project),
            actionLabel: __('comments.open_in_leadabox'),
            quote: $validated['body'],
        );

        return back()->with('status', __('comments.thanks'));
    }

    private function resolve(string $token): Project
    {
        $project = Project::query()
            ->where('public_token', $token)
            ->with('customer', 'company')
            ->firstOrFail();

        abort_unless($project->customer_can_view, 404);

        return $project;
    }
}
