<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\LoanController;

Route::get('/health', function () {
    return response()->json([
        'message' => 'API is running',
        'timestamp' => now(),
    ]);
});

// Public routes
Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes - require authentication
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::group(['prefix' => 'auth'], function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // Member routes - accessible by all authenticated users
    Route::group(['prefix' => 'books'], function () {
        Route::get('/', [BookController::class, 'index']);      // List all books
        Route::get('/{book}', [BookController::class, 'show']); // Get single book
        Route::get('/{book}/check-availability', [BookController::class, 'checkAvailability']);
    });

    // Loans routes - accessible by all authenticated users
    Route::group(['prefix' => 'loans'], function () {
        Route::get('/', [LoanController::class, 'index']);           // Get user's loans
        Route::post('/', [LoanController::class, 'store']);          // Create loan (borrow book)
        Route::get('/{loan}', [LoanController::class, 'show']);      // Get single loan
        Route::put('/{loan}/return', [LoanController::class, 'return']); // Return book
    });

    // Admin routes
    Route::middleware('admin.only')->group(function () {
        // Book management
        Route::group(['prefix' => 'books', 'controller' => BookController::class], function () {
            Route::post('/', 'store');                 // Create book
            Route::put('/{book}', 'update');           // Update book
            Route::delete('/{book}', 'destroy');       // Delete book
        });

        // Member management
        Route::group(['prefix' => 'members', 'controller' => MemberController::class], function () {
            Route::get('/', 'index');                  // List members
            Route::post('/', 'store');                 // Create member
            Route::get('/{member}', 'show');           // Get single member
            Route::put('/{member}', 'update');         // Update member
            Route::delete('/{member}', 'destroy');     // Delete member
        });

        // Loan management
        Route::group(['prefix' => 'loans', 'controller' => LoanController::class], function () {
            Route::get('/{loan}', 'show');             // Get single loan
            Route::put('/{loan}', 'update');           // Update loan
        });
    });
});
