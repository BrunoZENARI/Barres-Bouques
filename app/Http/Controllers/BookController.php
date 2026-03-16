<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Retourne la liste paginée des ouvrages.
     */
    public function index(Request $request)
    {
        $data = json_decode($request->lazyEvent);

        $books = Book::query();
        $count = Book::whereRaw('1 = 1');

        $env = new EnvController();
        $count = $env->QueryBuilder($count, $data, true);
        $books = $env->QueryBuilder($books, $data);

        return json_encode(['payload' => $books->get(), 'count' => $count]);
    }

    /**
     * Retourne le détail d'un ouvrage.
     */
    public function show($id)
    {
        $book = Book::findOrFail($id);

        return json_encode($book);
    }

    /**
     * Crée un nouvel ouvrage.
     */
    public function store(Request $request)
    {
        $data = json_decode($request->book);

        $book = new Book();
        $book->title = $data->title;
        $book->author = $data->author;
        $book->isbn = $data->isbn ?? null;
        $book->publisher = $data->publisher ?? null;
        $book->published_year = $data->published_year ?? null;
        $book->genre = $data->genre ?? null;
        $book->stock = $data->stock ?? 1;

        $book->save();

        return json_encode(true);
    }

    /**
     * Met à jour un ouvrage existant.
     */
    public function update(Request $request, $id)
    {
        $data = json_decode($request->book);

        $book = Book::findOrFail($id);
        $book->title = $data->title;
        $book->author = $data->author;
        $book->isbn = $data->isbn ?? null;
        $book->publisher = $data->publisher ?? null;
        $book->published_year = $data->published_year ?? null;
        $book->genre = $data->genre ?? null;
        $book->stock = $data->stock ?? 1;

        $book->save();

        return json_encode(true);
    }

    /**
     * Supprime un ouvrage.
     */
    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return json_encode(true);
    }
}
