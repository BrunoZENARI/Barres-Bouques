<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Models\LoanReminder;
use App\Models\ReminderSetting;
use App\Notifications\LoanDueSoonNotification;
use App\Notifications\LoanOverdueNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendLoanReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = "Envoie les rappels d'emprunt (retards et retours imminents)";

    public function handle(): int
    {
        $settings = ReminderSetting::getInstance();
        $today = Carbon::today();
        $countDueSoon = 0;
        $countOverdue = 0;

        if ($settings->reminder_due_soon_enabled) {
            $targetDate = $today->copy()->addDays($settings->days_before_due);

            $loans = Loan::with(['user', 'book'])
                ->whereNull('return_date')
                ->whereDate('due_date', $targetDate)
                ->whereDoesntHave('reminders', function ($q) {
                    $q->where('type', 'due_soon');
                })
                ->get();

            foreach ($loans as $loan) {
                $loan->user->notify(new LoanDueSoonNotification($loan));
                LoanReminder::create([
                    'loan_id' => $loan->id,
                    'type'    => 'due_soon',
                    'sent_at' => now(),
                ]);
                $countDueSoon++;
            }

            $this->info("Rappels retour imminent envoyés : {$countDueSoon}");
        }

        if ($settings->reminder_overdue_enabled) {
            $loans = Loan::with(['user', 'book'])
                ->whereNull('return_date')
                ->where('due_date', '<', $today)
                ->whereDoesntHave('reminders', function ($q) use ($today) {
                    $q->where('type', 'overdue')->whereDate('sent_at', $today);
                })
                ->get();

            foreach ($loans as $loan) {
                $loan->user->notify(new LoanOverdueNotification($loan));
                LoanReminder::create([
                    'loan_id' => $loan->id,
                    'type'    => 'overdue',
                    'sent_at' => now(),
                ]);
                $countOverdue++;
            }

            $this->info("Rappels retard envoyés : {$countOverdue}");
        }

        $this->info('Total rappels envoyés : ' . ($countDueSoon + $countOverdue));

        return Command::SUCCESS;
    }
}
