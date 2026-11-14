<?php

namespace App\Console\Commands;

use App\Actions\NotifyTeam;
use App\Actions\SendInvoice;
use App\Models\Invoice;
use App\Support\Money;
use Illuminate\Console\Command;

/**
 * Daily reminder ladder for unpaid invoices. Safe to run repeatedly: an invoice
 * is never reminded twice on the same day.
 */
class SendInvoiceReminders extends Command
{
    protected $signature = 'invoices:remind {--dry-run : List what would be sent without sending}';

    protected $description = 'Email reminders for overdue invoices and alert the team';

    /** Days past due when a reminder goes out. */
    private const LADDER = [1, 7, 14, 30];

    public function handle(SendInvoice $sender, NotifyTeam $notifier): int
    {
        $invoices = Invoice::query()
            ->overdue()
            ->where('type', Invoice::TYPE_INVOICE)
            ->with('customer.contacts', 'contact', 'company')
            ->get();

        $sent = 0;
        $skipped = 0;

        foreach ($invoices as $invoice) {
            if (! $this->isDue($invoice)) {
                $skipped++;

                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("would remind {$invoice->reference()} ({$invoice->daysOverdue()} days)");
                $sent++;

                continue;
            }

            try {
                $sender->handle($invoice, null, isReminder: true);
                $sent++;
                $this->info("reminded {$invoice->reference()}");
            } catch (\Throwable $e) {
                report($e);
                $this->error("failed {$invoice->reference()}: {$e->getMessage()}");
            }
        }

        if ($sent > 0 && ! $this->option('dry-run')) {
            $notifier->handle(
                headline: __('invoices.reminder_alert_headline', ['count' => $sent]),
                summary: __('invoices.reminder_alert_summary', [
                    'count' => $sent,
                    'total' => Money::format((string) $invoices->sum(fn (Invoice $i) => (float) $i->amountDue())),
                ]),
                actionUrl: route('invoices.index', ['status' => 'overdue']),
            );
        }

        $this->line("sent={$sent} skipped={$skipped}");

        return self::SUCCESS;
    }

    private function isDue(Invoice $invoice): bool
    {
        if ($invoice->last_reminder_at?->isToday()) {
            return false;
        }

        $days = $invoice->daysOverdue();

        foreach (self::LADDER as $step) {
            if ($days >= $step && $invoice->reminder_count < array_search($step, self::LADDER, true) + 1) {
                return true;
            }
        }

        return false;
    }
}
