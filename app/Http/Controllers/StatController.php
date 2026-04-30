<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function periodStart(string $period): Carbon
    {
        return match ($period) {
            'week'    => Carbon::now()->subWeeks(11)->startOfWeek(),
            'quarter' => Carbon::now()->subQuarters(7)->startOfQuarter(),
            'year'    => Carbon::now()->subYears(4)->startOfYear(),
            default   => Carbon::now()->subMonths(11)->startOfMonth(),
        };
    }

    /**
     * KPIs globaux filtrés par période.
     */
    public function kpis(Request $request)
    {
        $today = Carbon::today();
        $start = $this->periodStart($request->get('period', 'month'));

        $total      = Loan::where('loan_date', '>=', $start)->count();
        $active     = Loan::where('loan_date', '>=', $start)->whereNull('return_date')->count();
        $overdue    = Loan::where('loan_date', '>=', $start)->whereNull('return_date')->where('due_date', '<', $today)->count();
        $returned   = Loan::where('loan_date', '>=', $start)->whereNotNull('return_date')->count();
        $returnRate = $total > 0 ? round($returned / $total * 100, 1) : 0;

        return response()->json([
            'total'       => $total,
            'active'      => $active,
            'overdue'     => $overdue,
            'returned'    => $returned,
            'return_rate' => $returnRate,
        ]);
    }

    /**
     * Emprunts par période (week | month | quarter | year).
     */
    public function loansByPeriod(Request $request)
    {
        $period = $request->get('period', 'month');

        switch ($period) {
            case 'week':
                $start  = Carbon::now()->subWeeks(11)->startOfWeek();
                $format = '%Y-%u';
                $label  = fn($row) => 'S' . $row->period_key;
                $count  = 12;
                break;
            case 'quarter':
                $start  = Carbon::now()->subQuarters(7)->startOfQuarter();
                $format = '%Y-%m';
                $label  = fn($row) => $row->period_key;
                $count  = 8;
                break;
            case 'year':
                $start  = Carbon::now()->subYears(4)->startOfYear();
                $format = '%Y';
                $label  = fn($row) => $row->period_key;
                $count  = 5;
                break;
            default: // month
                $start  = Carbon::now()->subMonths(11)->startOfMonth();
                $format = '%Y-%m';
                $label  = fn($row) => $row->period_key;
                $count  = 12;
        }

        $rows = Loan::select(
                DB::raw("DATE_FORMAT(loan_date, '{$format}') as period_key"),
                DB::raw('COUNT(*) as total')
            )
            ->where('loan_date', '>=', $start)
            ->groupBy('period_key')
            ->orderBy('period_key')
            ->get();

        return response()->json([
            'labels' => $rows->map($label)->values(),
            'data'   => $rows->pluck('total')->values(),
        ]);
    }

    /**
     * Top 10 livres les plus empruntés sur la période.
     */
    public function topBooks(Request $request)
    {
        $start = $this->periodStart($request->get('period', 'month'));

        $books = Loan::select('book_id', DB::raw('COUNT(*) as total'))
            ->with('book:id,title,author')
            ->where('loan_date', '>=', $start)
            ->groupBy('book_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn($row) => [
                'label' => $row->book->title . ' — ' . $row->book->author,
                'total' => $row->total,
            ]);

        return response()->json([
            'labels' => $books->pluck('label')->values(),
            'data'   => $books->pluck('total')->values(),
        ]);
    }

    /**
     * Livres peu ou jamais empruntés (moins de 2 emprunts).
     */
    public function lowActivityBooks()
    {
        $books = Book::select('books.id', 'books.title', 'books.author', 'books.genre')
            ->selectRaw('COUNT(loans.id) as loan_count')
            ->leftJoin('loans', 'books.id', '=', 'loans.book_id')
            ->groupBy('books.id', 'books.title', 'books.author', 'books.genre')
            ->having('loan_count', '<', 2)
            ->orderBy('loan_count')
            ->orderBy('books.title')
            ->get();

        return response()->json($books);
    }

    /**
     * Répartition : rendus / en cours / en retard sur la période.
     */
    public function loanStatus(Request $request)
    {
        $today = Carbon::today();
        $start = $this->periodStart($request->get('period', 'month'));

        $returned = Loan::where('loan_date', '>=', $start)->whereNotNull('return_date')->count();
        $overdue  = Loan::where('loan_date', '>=', $start)->whereNull('return_date')->where('due_date', '<', $today)->count();
        $active   = Loan::where('loan_date', '>=', $start)->whereNull('return_date')->where('due_date', '>=', $today)->count();

        return response()->json([
            'labels' => ['Rendus', 'En cours', 'En retard'],
            'data'   => [$returned, $active, $overdue],
        ]);
    }

    /**
     * Export CSV des statistiques globales.
     */
    public function exportCsv()
    {
        $today = Carbon::today();

        $loans = Loan::with(['user', 'book'])
            ->select('loans.*')
            ->get()
            ->map(fn($loan) => [
                $loan->user->nom . ' ' . $loan->user->prenom,
                $loan->user->email,
                $loan->book->title,
                $loan->book->author,
                $loan->loan_date->format('d/m/Y'),
                $loan->due_date->format('d/m/Y'),
                $loan->return_date ? $loan->return_date->format('d/m/Y') : '',
                $loan->return_date ? 'Rendu' : ($loan->due_date->lt($today) ? 'En retard' : 'En cours'),
            ]);

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="statistiques_emprunts_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($loans) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['Emprunteur', 'Email', 'Ouvrage', 'Auteur', 'Date emprunt', 'Date retour prévue', 'Date retour réel', 'Statut'], ';');
            foreach ($loans as $row) {
                fputcsv($handle, $row, ';');
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
