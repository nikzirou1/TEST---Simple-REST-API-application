<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

/**
 * Seeds the database with a small set of well-known books for development and demo purposes.
 * Run with: php artisan db:seed  (or  php artisan migrate:fresh --seed)
 */
class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            [
                'title'            => 'The Great Gatsby',
                'publisher'        => 'Scribner',
                'author'           => 'F. Scott Fitzgerald',
                'genre'            => 'Novel',
                'publication_date' => '1925-04-10',
                'word_count'       => 47094,
                'price'            => 12.99,
            ],
            [
                'title'            => '1984',
                'publisher'        => 'Secker & Warburg',
                'author'           => 'George Orwell',
                'genre'            => 'Dystopian Fiction',
                'publication_date' => '1949-06-08',
                'word_count'       => 88942,
                'price'            => 9.99,
            ],
            [
                'title'            => 'To Kill a Mockingbird',
                'publisher'        => 'J. B. Lippincott & Co.',
                'author'           => 'Harper Lee',
                'genre'            => 'Southern Gothic',
                'publication_date' => '1960-07-11',
                'word_count'       => 100388,
                'price'            => 14.99,
            ],
            [
                'title'            => "The Hitchhiker's Guide to the Galaxy",
                'publisher'        => 'Pan Books',
                'author'           => 'Douglas Adams',
                'genre'            => 'Science Fiction Comedy',
                'publication_date' => '1979-10-12',
                'word_count'       => 46333,
                'price'            => 10.49,
            ],
            [
                'title'            => 'Dune',
                'publisher'        => 'Chilton Books',
                'author'           => 'Frank Herbert',
                'genre'            => 'Science Fiction',
                'publication_date' => '1965-08-01',
                'word_count'       => 187000,
                'price'            => 15.99,
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
