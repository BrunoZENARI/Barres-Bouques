<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Loan;
use App\Models\LoanReminder;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    // Poids de popularité par titre (sur 100 tirages)
    private array $popularity = [
        'Le Petit Prince'                             => 18,
        'Harry Potter et la Philosophie de la Pierre' => 16,
        'Les Misérables'                              => 14,
        'Dune'                                        => 12,
        "L'Étranger"                                  => 11,
        'Sapiens'                                     => 10,
        'Le Seigneur des Anneaux'                     => 9,
        'Germinal'                                    => 5,
        'Madame Bovary'                               => 4,
        'Une brève histoire du temps'                 => 1,
    ];

    // Multiplicateurs saisonniers par mois
    private array $seasonal = [
        1  => 1.3, // Janvier  — vacances d'hiver
        2  => 0.8, // Février  — creux
        3  => 0.85,
        4  => 1.0,
        5  => 1.05,
        6  => 1.1,
        7  => 1.6, // Juillet  — pic estival
        8  => 1.55,// Août     — pic estival
        9  => 1.3, // Septembre— rentrée
        10 => 1.0,
        11 => 1.0,
        12 => 1.4, // Décembre — fêtes
    ];

    public function run(): void
    {
        // Nettoyage dans l'ordre pour respecter les FK
        LoanReminder::query()->delete();
        Loan::query()->delete();

        $memberRole = Role::where('slug', 'member')->first();
        $members    = User::where('role_id', $memberRole->id)->get();
        $books      = Book::all()->keyBy('title');

        if ($members->isEmpty() || $books->isEmpty()) {
            $this->command->warn('Pas de membres ou de livres. Lancez UserSeeder et BookSeeder d\'abord.');
            return;
        }

        // Construire le pool de livres pondéré
        $pool = [];
        foreach ($this->popularity as $title => $weight) {
            $book = $books->get($title);
            if (!$book) continue;
            for ($i = 0; $i < $weight; $i++) {
                $pool[] = $book;
            }
        }

        $today        = Carbon::today();
        $start        = Carbon::create(2021, 4, 1)->startOfMonth();
        $totalMonths  = $start->diffInMonths($today);
        $memberCount  = $members->count();
        $memberIndex  = 0;

        $current    = $start->copy();
        $monthIndex = 0;
        $total      = 0;

        while ($current->lte($today->copy()->startOfMonth())) {
            // Croissance linéaire : 5 emprunts/mois en départ → 15 en fin de période
            $base  = 5 + ($monthIndex / max($totalMonths, 1)) * 10;
            $count = (int) round($base * ($this->seasonal[$current->month] ?? 1.0));

            // Dernier mois en cours : proportionnel aux jours écoulés
            if ($current->isSameMonth($today)) {
                $count = (int) round($count * ($today->day / $current->daysInMonth));
            }

            for ($i = 0; $i < $count; $i++) {
                $maxDay  = $current->isSameMonth($today) ? $today->day : $current->daysInMonth;
                $day     = rand(1, max(1, $maxDay));
                $loanDate = $current->copy()->setDay($day);

                if ($loanDate->gt($today)) continue;

                $dueDate = $loanDate->copy()->addDays(14);
                $book    = $pool[array_rand($pool)];
                $member  = $members[$memberIndex % $memberCount];
                $memberIndex++;

                // Statut selon la date
                if ($dueDate->lt($today)) {
                    if (rand(1, 100) <= 88) {
                        // Retourné : entre -7j et +10j par rapport à la date limite
                        $offset     = rand(-7, 10);
                        $returnDate = $dueDate->copy()->addDays($offset);
                        if ($returnDate->lt($loanDate)) {
                            $returnDate = $loanDate->copy()->addDays(rand(3, 14));
                        }
                        if ($returnDate->gt($today)) {
                            $returnDate = $today->copy();
                        }
                    } else {
                        // En retard (non retourné)
                        $returnDate = null;
                    }
                } else {
                    // Encore dans les délais — en cours
                    $returnDate = null;
                }

                Loan::create([
                    'user_id'     => $member->id,
                    'book_id'     => $book->id,
                    'loan_date'   => $loanDate,
                    'due_date'    => $dueDate,
                    'return_date' => $returnDate,
                ]);
                $total++;
            }

            $current->addMonth();
            $monthIndex++;
        }

        // Emprunts en cours bien visibles dans le dashboard
        $activeFixtures = [2, 4, 6, 8, 10, 12];
        foreach ($activeFixtures as $daysAgo) {
            $loanDate = $today->copy()->subDays($daysAgo);
            Loan::create([
                'user_id'     => $members[$memberIndex++ % $memberCount]->id,
                'book_id'     => $pool[array_rand($pool)]->id,
                'loan_date'   => $loanDate,
                'due_date'    => $loanDate->copy()->addDays(14),
                'return_date' => null,
            ]);
            $total++;
        }

        // Emprunts en retard bien visibles
        $overdueFixtures = [20, 25, 30, 35];
        foreach ($overdueFixtures as $daysAgo) {
            $loanDate = $today->copy()->subDays($daysAgo);
            Loan::create([
                'user_id'     => $members[$memberIndex++ % $memberCount]->id,
                'book_id'     => $pool[array_rand($pool)]->id,
                'loan_date'   => $loanDate,
                'due_date'    => $loanDate->copy()->addDays(14),
                'return_date' => null,
            ]);
            $total++;
        }

        $this->command->info("✓ {$total} emprunts générés sur 5 ans.");
    }
}
