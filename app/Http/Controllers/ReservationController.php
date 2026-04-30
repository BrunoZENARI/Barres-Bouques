<?php
namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    // MEMBER
    public function myReservations()
    {
        return response()->json(
            Reservation::with('book')->where('user_id', auth()->id())->orderByDesc('created_at')->get()
        );
    }

    public function store(Request $request)
    {
        $bookIds = $request->input('book_ids', []);
        $userId = auth()->id();
        $created = [];
        foreach ($bookIds as $bookId) {
            $exists = Reservation::where('user_id', $userId)->where('book_id', $bookId)
                ->whereIn('status', ['pending', 'ready'])->exists();
            if (!$exists) {
                $created[] = Reservation::create(['user_id' => $userId, 'book_id' => $bookId, 'status' => 'pending']);
            }
        }
        return response()->json($created, 201);
    }

    public function cancel($id)
    {
        $reservation = Reservation::where('id', $id)->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'ready'])->firstOrFail();
        $reservation->update(['status' => 'cancelled']);
        return response()->json(true);
    }

    // LIBRARIAN
    public function index(Request $request)
    {
        $query = Reservation::with(['user', 'book'])->orderByDesc('created_at');
        $status = $request->get('status');
        if ($status && $status !== 'all') $query->where('status', $status);
        return response()->json($query->get());
    }

    public function markReady($id)
    {
        Reservation::where('status', 'pending')->findOrFail($id)->update(['status' => 'ready']);
        return response()->json(true);
    }

    public function collect(Request $request, $id)
    {
        $reservation = Reservation::where('status', 'ready')->findOrFail($id);
        $dueDate = Carbon::parse($request->input('due_date', Carbon::today()->addDays(14)));
        Loan::create([
            'user_id'   => $reservation->user_id,
            'book_id'   => $reservation->book_id,
            'loan_date' => Carbon::today(),
            'due_date'  => $dueDate,
        ]);
        $reservation->update(['status' => 'collected']);
        return response()->json(true);
    }

    public function reject($id)
    {
        Reservation::whereIn('status', ['pending', 'ready'])->findOrFail($id)->update(['status' => 'cancelled']);
        return response()->json(true);
    }
}
