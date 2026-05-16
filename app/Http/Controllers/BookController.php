<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(
 *     title="Book Library API",
 *     version="1.0.0",
 *     description="REST API for managing a book library. Supports full CRUD for books."
 * )
 * @OA\Server(url="http://localhost:8080", description="Docker (Nginx)")
 * @OA\Server(url="http://localhost:8000", description="Built-in PHP server")
 * @OA\Tag(name="Books", description="Book management endpoints")
 */
class BookController extends Controller
{
    // ── GET /api/books ────────────────────────────────────────────────────

    /**
     * @OA\Get(
     *     path="/api/books",
     *     tags={"Books"},
     *     summary="List all books",
     *     description="Returns all books. Supports optional partial, case-insensitive filtering.",
     *     @OA\Parameter(name="title",     in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="author",    in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="genre",     in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="publisher", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="List of books")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $books = Book::query()
            ->when($request->query('title'),     fn ($q, $v) => $q->where('title',     'like', "%{$v}%"))
            ->when($request->query('author'),    fn ($q, $v) => $q->where('author',    'like', "%{$v}%"))
            ->when($request->query('genre'),     fn ($q, $v) => $q->where('genre',     'like', "%{$v}%"))
            ->when($request->query('publisher'), fn ($q, $v) => $q->where('publisher', 'like', "%{$v}%"))
            ->orderBy('id')
            ->get();

        return response()->json($books);
    }

    // ── POST /api/books ───────────────────────────────────────────────────

    /**
     * @OA\Post(
     *     path="/api/books",
     *     tags={"Books"},
     *     summary="Create a new book",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","publisher","author","genre","publication_date","word_count","price"},
     *             @OA\Property(property="title",            type="string",  example="The Great Gatsby"),
     *             @OA\Property(property="publisher",        type="string",  example="Scribner"),
     *             @OA\Property(property="author",           type="string",  example="F. Scott Fitzgerald"),
     *             @OA\Property(property="genre",            type="string",  example="Novel"),
     *             @OA\Property(property="publication_date", type="string",  format="date", example="1925-04-10"),
     *             @OA\Property(property="word_count",       type="integer", example=47094),
     *             @OA\Property(property="price",            type="number",  format="float", example=12.99)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Book created"),
     *     @OA\Response(response=422, description="Validation errors")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'publisher'        => 'required|string|max:255',
            'author'           => 'required|string|max:255',
            'genre'            => 'required|string|max:100',
            'publication_date' => 'required|date_format:Y-m-d',
            'word_count'       => 'required|integer|min:0',
            'price'            => 'required|numeric|min:0',
        ]);

        $book = Book::create($data);

        return response()->json($book, Response::HTTP_CREATED);
    }

    // ── GET /api/books/{book} ─────────────────────────────────────────────

    /**
     * @OA\Get(
     *     path="/api/books/{id}",
     *     tags={"Books"},
     *     summary="Get a single book by ID",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Book found"),
     *     @OA\Response(response=404, description="Book not found")
     * )
     */
    public function show(Book $book): JsonResponse
    {
        // Route model binding resolves the Book automatically; 404 if not found
        return response()->json($book);
    }

    // ── PATCH /api/books/{book} ───────────────────────────────────────────

    /**
     * @OA\Patch(
     *     path="/api/books/{id}",
     *     tags={"Books"},
     *     summary="Partially update a book",
     *     description="Only the fields provided in the request body are updated.",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title",            type="string"),
     *             @OA\Property(property="publisher",        type="string"),
     *             @OA\Property(property="author",           type="string"),
     *             @OA\Property(property="genre",            type="string"),
     *             @OA\Property(property="publication_date", type="string", format="date"),
     *             @OA\Property(property="word_count",       type="integer"),
     *             @OA\Property(property="price",            type="number", format="float")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Book updated"),
     *     @OA\Response(response=404, description="Book not found"),
     *     @OA\Response(response=422, description="Validation errors")
     * )
     */
    public function update(Request $request, Book $book): JsonResponse
    {
        // "sometimes" means each rule only applies when the field is present — PATCH semantics
        $data = $request->validate([
            'title'            => 'sometimes|required|string|max:255',
            'publisher'        => 'sometimes|required|string|max:255',
            'author'           => 'sometimes|required|string|max:255',
            'genre'            => 'sometimes|required|string|max:100',
            'publication_date' => 'sometimes|required|date_format:Y-m-d',
            'word_count'       => 'sometimes|required|integer|min:0',
            'price'            => 'sometimes|required|numeric|min:0',
        ]);

        $book->update($data);

        return response()->json($book->fresh());
    }

    // ── DELETE /api/books/{book} ──────────────────────────────────────────

    /**
     * @OA\Delete(
     *     path="/api/books/{id}",
     *     tags={"Books"},
     *     summary="Delete a book",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Book deleted"),
     *     @OA\Response(response=404, description="Book not found")
     * )
     */
    public function destroy(Book $book): JsonResponse
    {
        $book->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
