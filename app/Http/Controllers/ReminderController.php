<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanReminder;
use App\Models\ReminderSetting;
use App\Notifications\LoanDueSoonNotification;
use App\Notifications\LoanOverdueNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ReminderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getSettings()
    {
        return response()->json(ReminderSetting::getInstance());
    }

    public function updateSettings(Request $request)
    {
        $settings = ReminderSetting::getInstance();
        $settings->update($request->only([
            'days_before_due',
            'reminder_due_soon_enabled',
            'reminder_overdue_enabled',
            'librarian_email',
        ]));

        return response()->json($settings);
    }

    public function getOverdueLoans()
    {
        $loans = Loan::with(['user', 'book'])
            ->whereNull('return_date')
            ->where('due_date', '<', Carbon::today())
            ->orderBy('due_date')
            ->get();

        return response()->json($loans);
    }

    public function getDueSoonLoans()
    {
        $settings = ReminderSetting::getInstance();
        $targetDate = Carbon::today()->addDays($settings->days_before_due);

        $loans = Loan::with(['user', 'book'])
            ->whereNull('return_date')
            ->whereDate('due_date', '>=', Carbon::today())
            ->whereDate('due_date', '<=', $targetDate)
            ->orderBy('due_date')
            ->get();

        return response()->json($loans);
    }

    public function sendReminder($loanId)
    {
        $loan = Loan::with(['user', 'book'])->findOrFail($loanId);

        if ($loan->isReturned()) {
            return response()->json(['message' => 'Ce livre a déjà été retourné.'], 422);
        }

        $today = Carbon::today();
        $type = $loan->due_date->lt($today) ? 'overdue' : 'due_soon';

        if ($type === 'overdue') {
            $loan->user->notify(new LoanOverdueNotification($loan));
        } else {
            $loan->user->notify(new LoanDueSoonNotification($loan));
        }

        LoanReminder::create([
            'loan_id' => $loan->id,
            'type'    => $type,
            'sent_at' => now(),
        ]);

        return response()->json(['message' => 'Rappel envoyé avec succès.']);
    }

    public function sendAll()
    {
        Artisan::call('reminders:send');

        return response()->json([
            'message' => 'Rappels automatiques exécutés.',
            'output'  => Artisan::output(),
        ]);
    }
}
