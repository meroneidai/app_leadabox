<?php

namespace App\Actions;

use App\Mail\AdminAlertMail;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

/**
 * Every customer-triggered event lands in the team's inbox, so nothing is missed.
 */
class NotifyTeam
{
    /**
     * @param  array<int, array{label: string, value: string}>  $details
     */
    public function handle(
        string $headline,
        string $summary,
        array $details = [],
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        ?string $quote = null,
    ): void {
        $recipients = $this->recipients();

        if ($recipients === []) {
            return;
        }

        // A failed alert must never break the customer's action.
        try {
            Mail::to($recipients)->send(
                new AdminAlertMail($headline, $summary, $details, $actionUrl, $actionLabel, $quote)
            );
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * @return array<int, string>
     */
    private function recipients(): array
    {
        $admins = User::query()
            ->where('is_active', true)
            ->whereHas('roles', fn ($query) => $query->whereIn('name', ['admin', 'manager']))
            ->pluck('email')
            ->all();

        if ($admins !== []) {
            return $admins;
        }

        return array_filter([Company::current()?->email]);
    }
}
