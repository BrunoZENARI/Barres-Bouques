<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            [
                'title'          => 'Les Misérables',
                'author'         => 'Victor Hugo',
                'isbn'           => '978-2-07-040951-0',
                'publisher'      => 'Gallimard',
                'published_year' => 1862,
                'genre'          => 'Roman',
                'stock'          => 3,
            ],
            [
                'title'          => 'Le Petit Prince',
                'author'         => 'Antoine de Saint-Exupéry',
                'isbn'           => '978-2-07-040850-6',
                'publisher'      => 'Gallimard',
                'published_year' => 1943,
                'genre'          => 'Roman',
                'stock'          => 5,
            ],
            [
                'title'          => 'L\'Étranger',
                'author'         => 'Albert Camus',
                'isbn'           => '978-2-07-036024-5',
                'publisher'      => 'Gallimard',
                'published_year' => 1942,
                'genre'          => 'Roman',
                'stock'          => 2,
            ],
            [
                'title'          => 'Germinal',
                'author'         => 'Émile Zola',
                'isbn'           => '978-2-07-040743-1',
                'publisher'      => 'Gallimard',
                'published_year' => 1885,
                'genre'          => 'Roman',
                'stock'          => 4,
            ],
            [
                'title'          => 'Madame Bovary',
                'author'         => 'Gustave Flaubert',
                'isbn'           => '978-2-07-036303-1',
                'publisher'      => 'Gallimard',
                'published_year' => 1857,
                'genre'          => 'Roman',
                'stock'          => 2,
            ],
            [
                'title'          => 'Harry Potter et la Philosophie de la Pierre',
                'author'         => 'J.K. Rowling',
                'isbn'           => '978-2-07-054052-3',
                'publisher'      => 'Gallimard Jeunesse',
                'published_year' => 1997,
                'genre'          => 'Fantasy',
                'stock'          => 6,
            ],
            [
                'title'          => 'Dune',
                'author'         => 'Frank Herbert',
                'isbn'           => '978-2-07-036057-3',
                'publisher'      => 'Robert Laffont',
                'published_year' => 1965,
                'genre'          => 'Science-fiction',
                'stock'          => 3,
            ],
            [
                'title'          => 'Le Seigneur des Anneaux',
                'author'         => 'J.R.R. Tolkien',
                'isbn'           => '978-2-267-01918-8',
                'publisher'      => 'Christian Bourgois',
                'published_year' => 1954,
                'genre'          => 'Fantasy',
                'stock'          => 4,
            ],
            [
                'title'          => 'Sapiens',
                'author'         => 'Yuval Noah Harari',
                'isbn'           => '978-2-226-25701-7',
                'publisher'      => 'Albin Michel',
                'published_year' => 2011,
                'genre'          => 'Essai',
                'stock'          => 3,
            ],
            [
                'title'          => 'Une brève histoire du temps',
                'author'         => 'Stephen Hawking',
                'isbn'           => '978-2-08-081488-7',
                'publisher'      => 'Flammarion',
                'published_year' => 1988,
                'genre'          => 'Sciences',
                'stock'          => 2,
            ],
        ];

        foreach ($books as $book) {
            Book::firstOrCreate(['isbn' => $book['isbn']], $book);
        }
    }
}
