<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Book::query();

            // Search by title or author
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('author', 'like', "%{$search}%");
            }

            // Filter by category
            if ($request->has('category')) {
                $query->where('category', $request->get('category'));
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $books = $query->paginate($perPage);

            return response()->json([
                'message' => 'Books retrieved successfully',
                'data' => $books->items(),
                'pagination' => [
                    'total' => $books->total(),
                    'per_page' => $books->perPage(),
                    'current_page' => $books->currentPage(),
                    'last_page' => $books->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve books',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'isbn' => 'required|string|unique:books',
                'publication_year' => 'required|integer|min:1900|max:' . date('Y'),
                'category' => 'required|string|max:100',
                'stock' => 'required|integer|min:0',
                'description' => 'nullable|string',
            ]);

            $book = Book::create([
                'title' => $validated['title'],
                'author' => $validated['author'],
                'isbn' => $validated['isbn'],
                'publication_year' => $validated['publication_year'],
                'category' => $validated['category'],
                'stock' => $validated['stock'],
                'available_stock' => $validated['stock'],
                'description' => $validated['description'] ?? null,
            ]);

            return response()->json([
                'message' => 'Book created successfully',
                'data' => $book,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create book',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        try {
            return response()->json([
                'message' => 'Book retrieved successfully',
                'data' => $book,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Book not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        try {
            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'author' => 'sometimes|string|max:255',
                'isbn' => 'sometimes|string|unique:books,isbn,' . $book->id,
                'publication_year' => 'sometimes|integer|min:1900|max:' . date('Y'),
                'category' => 'sometimes|string|max:100',
                'stock' => 'sometimes|integer|min:0',
                'description' => 'nullable|string',
            ]);

            // Update available_stock if stock is changed
            if (isset($validated['stock'])) {
                $oldStock = $book->stock;
                $newStock = $validated['stock'];
                $difference = $newStock - $oldStock;
                $validated['available_stock'] = $book->available_stock + $difference;
            }

            $book->update($validated);

            return response()->json([
                'message' => 'Book updated successfully',
                'data' => $book,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update book',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        try {
            // Check if book has active loans
            $activeLoans = $book->loans()->where('status', 'borrowed')->count();
            if ($activeLoans > 0) {
                return response()->json([
                    'message' => 'Cannot delete book with active loans',
                    'active_loans' => $activeLoans,
                ], 409);
            }

            $book->delete();

            return response()->json([
                'message' => 'Book deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete book',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check book availability
     */
    public function checkAvailability(Book $book)
    {
        try {
            return response()->json([
                'message' => 'Book availability retrieved',
                'data' => [
                    'book_id' => $book->id,
                    'title' => $book->title,
                    'total_stock' => $book->stock,
                    'available_stock' => $book->available_stock,
                    'is_available' => $book->available_stock > 0,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to check availability',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
