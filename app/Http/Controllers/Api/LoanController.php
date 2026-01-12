<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Book;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class LoanController extends Controller
{
    const LOAN_DURATION_DAYS = 14;

    /**
     * Display loans based on user role
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user->isAdmin()) {
                // Admin can see all loans
                $query = Loan::with(['member.user', 'book']);
                
                // Filter by status
                if ($request->has('status')) {
                    $query->where('status', $request->get('status'));
                }
                
                // Filter by member
                if ($request->has('member_id')) {
                    $query->where('member_id', $request->get('member_id'));
                }
            } else {
                // Member can only see their own loans
                $member = $user->member;
                if (!$member) {
                    return response()->json([
                        'message' => 'Member profile not found',
                    ], 404);
                }
                $query = Loan::with(['book'])->where('member_id', $member->id);
            }

            $perPage = $request->get('per_page', 15);
            $loans = $query->paginate($perPage);

            return response()->json([
                'message' => 'Loans retrieved successfully',
                'data' => $loans->items(),
                'pagination' => [
                    'total' => $loans->total(),
                    'per_page' => $loans->perPage(),
                    'current_page' => $loans->currentPage(),
                    'last_page' => $loans->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve loans',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a loan (member borrow book)
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user();
            
            // Get member profile
            $member = $user->member;
            if (!$member) {
                return response()->json([
                    'message' => 'Member profile not found',
                ], 404);
            }

            // Check member status
            if ($member->status !== 'active') {
                return response()->json([
                    'message' => 'Cannot borrow books. Your membership is ' . $member->status,
                ], 403);
            }

            $validated = $request->validate([
                'book_id' => 'required|exists:books,id',
            ]);

            $book = Book::find($validated['book_id']);

            // Check book availability
            if ($book->available_stock <= 0) {
                return response()->json([
                    'message' => 'Book is not available for borrowing',
                    'available_stock' => $book->available_stock,
                ], 409);
            }

            // Check if member already borrowed this book
            $existingLoan = Loan::where('member_id', $member->id)
                ->where('book_id', $book->id)
                ->where('status', 'borrowed')
                ->first();
            
            if ($existingLoan) {
                return response()->json([
                    'message' => 'You already borrowed this book',
                ], 409);
            }

            // Create loan
            $loanDate = Carbon::now();
            $dueDate = $loanDate->copy()->addDays(self::LOAN_DURATION_DAYS);

            $loan = Loan::create([
                'member_id' => $member->id,
                'book_id' => $book->id,
                'loan_date' => $loanDate,
                'due_date' => $dueDate,
                'status' => 'borrowed',
            ]);

            // Decrease available stock
            $book->decrement('available_stock');

            return response()->json([
                'message' => 'Book borrowed successfully',
                'data' => [
                    'loan' => $loan->load('book'),
                    'due_date' => $dueDate->format('Y-m-d'),
                    'days_to_return' => $dueDate->diffInDays(Carbon::now()),
                ],
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create loan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified loan
     */
    public function show(Loan $loan)
    {
        try {
            $user = request()->user();
            
            // Check authorization - member can only view their own loans
            if ($user->isMember() && $loan->member_id !== $user->member->id) {
                return response()->json([
                    'message' => 'Unauthorized to view this loan',
                ], 403);
            }

            $loan->load('member.user', 'book');

            return response()->json([
                'message' => 'Loan retrieved successfully',
                'data' => $loan,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Loan not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update loan (admin use)
     */
    public function update(Request $request, Loan $loan)
    {
        try {
            $validated = $request->validate([
                'status' => 'sometimes|in:borrowed,returned,late',
            ]);

            $loan->update($validated);

            return response()->json([
                'message' => 'Loan updated successfully',
                'data' => $loan,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update loan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Return a book
     */
    public function return(Request $request, Loan $loan)
    {
        try {
            $user = $request->user();

            // Check if loan is borrowed
            if ($loan->status !== 'borrowed') {
                return response()->json([
                    'message' => 'Loan is not in borrowed status',
                    'current_status' => $loan->status,
                ], 409);
            }

            // Check authorization
            if ($user->isMember() && $loan->member_id !== $user->member->id) {
                return response()->json([
                    'message' => 'Unauthorized to return this loan',
                ], 403);
            }

            $returnDate = Carbon::now();

            // Check if late
            $isLate = $returnDate > $loan->due_date;
            $status = $isLate ? 'late' : 'returned';

            // Update loan
            $loan->update([
                'return_date' => $returnDate,
                'status' => $status,
            ]);

            // Increase available stock
            $loan->book->increment('available_stock');

            return response()->json([
                'message' => 'Book returned successfully',
                'data' => [
                    'loan' => $loan,
                    'return_date' => $returnDate->format('Y-m-d'),
                    'is_late' => $isLate,
                    'days_late' => $isLate ? $returnDate->diffInDays($loan->due_date) : 0,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to return book',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
