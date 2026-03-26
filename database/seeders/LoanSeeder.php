<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        $memberRole = Role::where('slug', 'member')->first();
        $members = User::where('role_id', $memberRole->id)->get();
        $books = Book::all();

        if ($members->isEmpty() || $books->isEmpty()) {
            return;
        }

        // Emprunts en cours (non retournés)
        $activeLoans = [
            ['days_ago' => 5,  'due_in_days' => 9],
            ['days_ago' => 10, 'due_in_days' => 4],
            ['days_ago' => 3,  'due_in_days' => 11],
        ];

        foreach ($activeLoans as $index => $loanData) {
            $loanDate = Carbon::today()->subDays($loanData['days_ago']);

            Loan::create([
                'user_id'     => $members->get($index % $members->count())->id,
                'book_id'     => $books->get($index % $books->count())->id,
                'loan_date'   => $loanDate,
                'due_date'    => $loanDate->copy()->addDays(14),
                'return_date' => null,
            ]);
        }

        // Emprunts en retard (non retournés, date dépassée)
        $overdueLoans = [
            ['days_ago' => 20],
            ['days_ago' => 18],
        ];

        foreach ($overdueLoans as $index => $loanData) {
            $loanDate = Carbon::today()->subDays($loanData['days_ago']);

            Loan::create([
                'user_id'     => $members->get(($index + 3) % $members->count())->id,
                'book_id'     => $books->get(($index + 3) % $books->count())->id,
                'loan_date'   => $loanDate,
                'due_date'    => $loanDate->copy()->addDays(14),
                'return_date' => null,
            ]);
        }

        // Emprunts terminés (retournés)
        $returnedLoans = [
            ['days_ago' => 30, 'returned_days_ago' => 16],
            ['days_ago' => 25, 'returned_days_ago' => 10],
            ['days_ago' => 40, 'returned_days_ago' => 26],
        ];

        foreach ($returnedLoans as $index => $loanData) {
            $loanDate = Carbon::today()->subDays($loanData['days_ago']);

            Loan::create([
                'user_id'     => $members->get(($index + 5) % $members->count())->id,
                'book_id'     => $books->get(($index + 5) % $books->count())->id,
                'loan_date'   => $loanDate,
                'due_date'    => $loanDate->copy()->addDays(14),
                'return_date' => Carbon::today()->subDays($loanData['returned_days_ago']),
            ]);
        }
    }
}
