<?php
namespace App\Http\Controllers;

use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function loans()
    {
        $today = Carbon::today();
        $loans = Loan::with('book')->where('user_id', auth()->id())->orderByDesc('loan_date')->get()
            ->map(function ($loan) use ($today) {
                $loan->status = $loan->return_date ? 'returned'
                    : ($loan->due_date->lt($today) ? 'overdue' : 'active');
                return $loan;
            });
        return response()->json($loans);
    }

    public function profile()
    {
        return response()->json(auth()->user()->only('id', 'nom', 'prenom', 'email'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'nom'    => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email'  => 'required|email|unique:users,email,' . auth()->id(),
        ]);
        auth()->user()->update($request->only('nom', 'prenom', 'email'));
        return response()->json(true);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);
        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return response()->json(['message' => 'Mot de passe actuel incorrect.'], 422);
        }
        auth()->user()->update(['password' => Hash::make($request->password)]);
        return response()->json(true);
    }
}
