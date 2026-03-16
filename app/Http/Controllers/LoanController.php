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
     * Retourne le détail d'un emprunt.
     */
    public function show($id)
    {
        $loan = Loan::with(['user', 'book'])->findOrFail($id);

        return json_encode($loan);
    }

    /**
     * Crée un nouvel emprunt.
     */
    public function store(Request $request)
    {
        $data = json_decode($request->loan);

        $loanDate = Carbon::today();
        $dueDate = Carbon::today()->addDays(14);

        $loan = new Loan();
        $loan->user_id = $data->user_id;
        $loan->book_id = $data->book_id;
        $loan->loan_date = $loanDate;
        $loan->due_date = $dueDate;

        $loan->save();

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
