<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Functional tests for the Books REST API.
 *
 * RefreshDatabase rolls back all changes after each test, so every test
 * starts with a clean SQLite in-memory database (configured in phpunit.xml).
 */
class BookTest extends TestCase
{
    use RefreshDatabase;

    // ── Helper ─────────────────────────────────────────────────────────────

    private function bookPayload(array $overrides = []): array
    {
        return array_merge([
            'title'            => 'Test Book',
            'publisher'        => 'Test Publisher',
            'author'           => 'Test Author',
            'genre'            => 'Fiction',
            'publication_date' => '2020-01-15',
            'word_count'       => 50000,
            'price'            => 9.99,
        ], $overrides);
    }

    private function createBook(array $overrides = []): Book
    {
        return Book::create($this->bookPayload($overrides));
    }

    // ── GET /api/books ──────────────────────────────────────────────────────

    public function test_list_returns_empty_array_when_no_books_exist(): void
    {
        $this->getJson('/api/books')
             ->assertOk()
             ->assertJson([]);
    }

    public function test_list_returns_all_books(): void
    {
        $this->createBook(['title' => 'Book A']);
        $this->createBook(['title' => 'Book B']);

        $this->getJson('/api/books')
             ->assertOk()
             ->assertJsonCount(2);
    }

    public function test_list_filters_by_title(): void
    {
        $this->createBook(['title' => 'Hamlet']);
        $this->createBook(['title' => 'Macbeth']);

        $this->getJson('/api/books?title=hamlet')
             ->assertOk()
             ->assertJsonCount(1)
             ->assertJsonFragment(['title' => 'Hamlet']);
    }

    public function test_list_filters_by_author(): void
    {
        $this->createBook(['author' => 'Shakespeare']);
        $this->createBook(['author' => 'Tolkien']);

        $this->getJson('/api/books?author=tolkien')
             ->assertOk()
             ->assertJsonCount(1)
             ->assertJsonFragment(['author' => 'Tolkien']);
    }

    public function test_list_filters_by_genre(): void
    {
        $this->createBook(['genre' => 'Fantasy']);
        $this->createBook(['genre' => 'Horror']);

        $this->getJson('/api/books?genre=fantasy')
             ->assertOk()
             ->assertJsonCount(1)
             ->assertJsonFragment(['genre' => 'Fantasy']);
    }

    public function test_list_filters_by_publisher(): void
    {
        $this->createBook(['publisher' => 'Penguin']);
        $this->createBook(['publisher' => 'Scribner']);

        $this->getJson('/api/books?publisher=penguin')
             ->assertOk()
             ->assertJsonCount(1)
             ->assertJsonFragment(['publisher' => 'Penguin']);
    }

    // ── POST /api/books ─────────────────────────────────────────────────────

    public function test_store_creates_book_and_returns_201(): void
    {
        $payload = $this->bookPayload(['title' => 'Dune', 'word_count' => 187000, 'price' => 15.99]);

        $this->postJson('/api/books', $payload)
             ->assertCreated()
             ->assertJsonFragment([
                 'title'            => 'Dune',
                 'word_count'       => 187000,
                 'publication_date' => '2020-01-15',
             ]);

        $this->assertDatabaseHas('books', ['title' => 'Dune']);
    }

    public function test_store_persists_all_fields_correctly(): void
    {
        $payload = $this->bookPayload();

        $response = $this->postJson('/api/books', $payload)->assertCreated();

        $data = $response->json();
        $this->assertIsInt($data['id']);
        $this->assertSame('Test Book', $data['title']);
        $this->assertSame('Test Publisher', $data['publisher']);
        $this->assertSame('Test Author', $data['author']);
        $this->assertSame('Fiction', $data['genre']);
        $this->assertSame('2020-01-15', $data['publication_date']);
        $this->assertSame(50000, $data['word_count']);
    }

    public function test_store_returns_422_when_required_fields_missing(): void
    {
        $this->postJson('/api/books', ['title' => 'Only Title'])
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['publisher', 'author', 'genre', 'publication_date', 'word_count', 'price']);
    }

    public function test_store_returns_422_when_title_is_empty(): void
    {
        $this->postJson('/api/books', $this->bookPayload(['title' => '']))
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['title']);
    }

    public function test_store_returns_422_when_word_count_is_negative(): void
    {
        $this->postJson('/api/books', $this->bookPayload(['word_count' => -1]))
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['word_count']);
    }

    public function test_store_returns_422_when_price_is_negative(): void
    {
        $this->postJson('/api/books', $this->bookPayload(['price' => -5]))
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['price']);
    }

    public function test_store_returns_422_when_date_format_is_invalid(): void
    {
        $this->postJson('/api/books', $this->bookPayload(['publication_date' => 'not-a-date']))
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['publication_date']);
    }

    // ── GET /api/books/{id} ─────────────────────────────────────────────────

    public function test_show_returns_book_by_id(): void
    {
        $book = $this->createBook(['title' => 'Specific Book']);

        $this->getJson("/api/books/{$book->id}")
             ->assertOk()
             ->assertJsonFragment(['id' => $book->id, 'title' => 'Specific Book']);
    }

    public function test_show_returns_404_for_missing_book(): void
    {
        $this->getJson('/api/books/99999')
             ->assertNotFound();
    }

    // ── PATCH /api/books/{id} ───────────────────────────────────────────────

    public function test_update_changes_only_supplied_fields(): void
    {
        $book = $this->createBook(['title' => 'Original Title', 'price' => 10.00]);

        $response = $this->patchJson("/api/books/{$book->id}", ['title' => 'Updated Title'])
                         ->assertOk();

        $this->assertSame('Updated Title', $response->json('title'));
        // price must be unchanged
        $this->assertEquals(10.00, $response->json('price'));
    }

    public function test_update_changes_price(): void
    {
        $book = $this->createBook();

        $this->patchJson("/api/books/{$book->id}", ['price' => 29.99])
             ->assertOk()
             ->assertJsonFragment(['price' => '29.99']);
    }

    public function test_update_returns_404_for_missing_book(): void
    {
        $this->patchJson('/api/books/99999', ['title' => 'New'])
             ->assertNotFound();
    }

    public function test_update_returns_422_when_title_set_to_empty(): void
    {
        $book = $this->createBook();

        $this->patchJson("/api/books/{$book->id}", ['title' => ''])
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['title']);
    }

    public function test_update_returns_422_when_date_format_invalid(): void
    {
        $book = $this->createBook();

        $this->patchJson("/api/books/{$book->id}", ['publication_date' => '31-12-2020'])
             ->assertUnprocessable()
             ->assertJsonValidationErrors(['publication_date']);
    }

    // ── DELETE /api/books/{id} ──────────────────────────────────────────────

    public function test_destroy_returns_204(): void
    {
        $book = $this->createBook();

        $this->deleteJson("/api/books/{$book->id}")
             ->assertNoContent();
    }

    public function test_deleted_book_no_longer_exists(): void
    {
        $book = $this->createBook();
        $id   = $book->id;

        $this->deleteJson("/api/books/{$id}");

        $this->getJson("/api/books/{$id}")->assertNotFound();
        $this->assertDatabaseMissing('books', ['id' => $id]);
    }

    public function test_destroy_returns_404_for_missing_book(): void
    {
        $this->deleteJson('/api/books/99999')
             ->assertNotFound();
    }

    // ── Response shape ──────────────────────────────────────────────────────

    public function test_book_response_contains_all_required_fields(): void
    {
        $book = $this->createBook();

        $response = $this->getJson("/api/books/{$book->id}")->assertOk();

        foreach (['id', 'title', 'publisher', 'author', 'genre', 'publication_date', 'word_count', 'price'] as $field) {
            $this->assertArrayHasKey($field, $response->json());
        }
    }
}
