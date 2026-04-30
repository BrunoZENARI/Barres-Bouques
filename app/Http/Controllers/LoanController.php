<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;

class LoanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Retourne la liste paginée des emprunts.
     */
    public function index(Request $request)
    {
        $data = json_decode($request->lazyEvent);

        $loans = Loan::with(['user', 'book']);
        $count = Loan::whereRaw('1 = 1');

        $env = new EnvController();
        $count = $env->QueryBuilder($count, $data, true);
        $loans = $env->QueryBuilder($loans, $data);

        return json_encode(['payload' => $loans->get(), 'count' => $count]);
    }

    /**
     * Retourne les statistiques pour le tableau de bord.
     */
    public function stats()
    {
        $today = Carbon::today();

        $overdueCount = Loan::whereNull('return_date')
            ->where('due_date', '<', $today)
            ->count();

        $dueTodayLoans = Loan::with(['user', 'book'])
            ->whereNull('return_date')
            ->whereDate('due_date', $today)
            ->get();

        return response()->json([
            'overdue_count'   => $overdueCount,
            'due_today_loans' => $dueTodayLoans,
        ]);
    }

    /**
     * Recherche d'utilisateurs pour l'autocomplétion (par nom, prénom ou email).
     */
    public function searchUsers(Request $request)
    {
        $q = $request->q;

        $users = User::where(function ($query) use ($q) {
                $query->where('nom', 'like', "%{$q}%")
                      ->orWhere('prenom', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhereRaw("CONCAT(prenom, ' ', nom) LIKE ?", ["%{$q}%"])
                      ->orWhereRaw("CONCAT(nom, ' ', prenom) LIKE ?", ["%{$q}%"]);
            })
            ->limit(10)
            ->get(['id', 'nom', 'prenom', 'email']);

        return response()->json($users);
    }

    /**
     * Recherche de livres disponibles pour l'autocomplétion (par titre ou auteur).
     */
    public function searchBooks(Request $request)
    {
        $q = $request->q;

        $books = Book::where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('author', 'like', "%{$q}%");
            })
            ->whereRaw('stock > (SELECT COUNT(*) FROM loans WHERE loans.book_id = books.id AND loans.return_date IS NULL)')
            ->limit(10)
            ->get(['id', 'title', 'author']);

        return response()->json($books);
    }

    /**
     * Retourne le détail d'un emprunt.
     */
    public function show($id)
    {
        $loan = Loan::with(['user', 'book'])->findOrFail($id);

        return json_encode($loan);
    }

    /**
     * Crée un ou plusieurs emprunts (un par livre sélectionné).
     */
    public function store(Request $request)
    {
        $data = json_decode($request->loan);

        $loanDate = Carbon::today();
        $dueDate  = Carbon::parse($data->due_date);

        foreach ($data->books as $book) {
            $loan = new Loan();
            $loan->user_id   = $data->user->id;
            $loan->book_id   = $book->id;
            $loan->loan_date = $loanDate;
            $loan->due_date  = $dueDate;
            $loan->save();
        }

        return json_encode(true);
    }

    /**
     * Enregistre le retour d'un emprunt.
     */
    public function update(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $loan->return_date = Carbon::today();

        $loan->save();

        return json_encode(true);
    }

    /**
     * Supprime un emprunt.
     */
    public function destroy($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->delete();

        return json_encode(true);
    }
}
