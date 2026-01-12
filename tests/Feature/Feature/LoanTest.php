<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Member;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    private $member;
    private $memberToken;
    private $admin;
    private $adminToken;
    private $book;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);
        $this->adminToken = $this->admin->createToken('API Token')->plainTextToken;

        // Create member user
        $this->member = User::create([
            'name' => 'Member User',
            'email' => 'member@example.com',
            'password' => bcrypt('password123'),
            'role' => 'member',
        ]);
        
        Member::create([
            'user_id' => $this->member->id,
            'member_number' => 'MBR00001',
            'join_date' => Carbon::now()->toDateString(),
            'status' => 'active',
        ]);
        
        $this->memberToken = $this->member->createToken('API Token')->plainTextToken;

        // Create test book
        $this->book = Book::create([
            'title' => 'Test Book',
            'author' => 'John Doe',
            'isbn' => '978-0-123456-78-9',
            'publication_year' => 2023,
            'category' => 'Programming',
            'stock' => 5,
            'available_stock' => 5,
        ]);
    }

    /**
     * Test member can borrow a book
     */
    public function test_member_can_borrow_book(): void
    {
        $response = $this->withHeader('Authorization', "Bearer $this->memberToken")
                        ->postJson('/api/loans', [
                            'book_id' => $this->book->id,
                        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'loan',
                        'due_date',
                        'days_to_return',
                    ],
                ])->assertJson([
                    'message' => 'Book borrowed successfully',
                ]);

        // Check available stock decreased
        $this->assertEquals(4, $this->book->fresh()->available_stock);

        // Check loan created in database
        $this->assertDatabaseHas('loans', [
            'member_id' => $this->member->member->id,
            'book_id' => $this->book->id,
            'status' => 'borrowed',
        ]);
    }

    /**
     * Test member cannot borrow unavailable book
     */
    public function test_member_cannot_borrow_unavailable_book(): void
    {
        $unavailableBook = Book::create([
            'title' => 'Out of Stock Book',
            'author' => 'Jane Doe',
            'isbn' => '978-0-999999-99-9',
            'publication_year' => 2023,
            'category' => 'Programming',
            'stock' => 0,
            'available_stock' => 0,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->memberToken")
                        ->postJson('/api/loans', [
                            'book_id' => $unavailableBook->id,
                        ]);

        $response->assertStatus(409)
                ->assertJson([
                    'message' => 'Book is not available for borrowing',
                ]);
    }

    /**
     * Test member cannot borrow same book twice
     */
    public function test_member_cannot_borrow_same_book_twice(): void
    {
        // First loan
        $this->withHeader('Authorization', "Bearer $this->memberToken")
            ->postJson('/api/loans', [
                'book_id' => $this->book->id,
            ]);

        // Try second loan
        $response = $this->withHeader('Authorization', "Bearer $this->memberToken")
                        ->postJson('/api/loans', [
                            'book_id' => $this->book->id,
                        ]);

        $response->assertStatus(409)
                ->assertJson([
                    'message' => 'You already borrowed this book',
                ]);
    }

    /**
     * Test member can view their loans
     */
    public function test_member_can_view_their_loans(): void
    {
        Loan::create([
            'member_id' => $this->member->member->id,
            'book_id' => $this->book->id,
            'loan_date' => Carbon::now(),
            'due_date' => Carbon::now()->addDays(14),
            'status' => 'borrowed',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->memberToken")
                        ->getJson('/api/loans');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data',
                    'pagination',
                ]);
    }

    /**
     * Test member can return a book
     */
    public function test_member_can_return_borrowed_book(): void
    {
        $loan = Loan::create([
            'member_id' => $this->member->member->id,
            'book_id' => $this->book->id,
            'loan_date' => Carbon::now(),
            'due_date' => Carbon::now()->addDays(14),
            'status' => 'borrowed',
        ]);

        // Decrease available stock to simulate borrowing
        $this->book->decrement('available_stock');

        $response = $this->withHeader('Authorization', "Bearer $this->memberToken")
                        ->putJson("/api/loans/{$loan->id}/return");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'loan',
                        'return_date',
                        'is_late',
                    ],
                ]);

        // Check available stock increased
        $this->assertEquals(5, $this->book->fresh()->available_stock);

        // Check loan status updated
        $this->assertEquals('returned', $loan->fresh()->status);
    }

    /**
     * Test returning book after due date marks as late
     */
    public function test_returning_book_after_due_date_marks_as_late(): void
    {
        $loan = Loan::create([
            'member_id' => $this->member->member->id,
            'book_id' => $this->book->id,
            'loan_date' => Carbon::now()->subDays(20),
            'due_date' => Carbon::now()->subDays(6), // 6 days overdue
            'status' => 'borrowed',
        ]);

        $this->book->decrement('available_stock');

        $response = $this->withHeader('Authorization', "Bearer $this->memberToken")
                        ->putJson("/api/loans/{$loan->id}/return");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'is_late' => true,
                    ],
                ]);

        $this->assertEquals('late', $loan->fresh()->status);
    }

    /**
     * Test admin can view all loans
     */
    public function test_admin_can_view_all_loans(): void
    {
        Loan::create([
            'member_id' => $this->member->member->id,
            'book_id' => $this->book->id,
            'loan_date' => Carbon::now(),
            'due_date' => Carbon::now()->addDays(14),
            'status' => 'borrowed',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->adminToken")
                        ->getJson('/api/loans');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data',
                    'pagination',
                ]);
    }

    /**
     * Test member cannot view other member loans
     */
    public function test_member_cannot_view_other_member_loans(): void
    {
        // Create another member
        $otherMember = User::create([
            'name' => 'Other Member',
            'email' => 'other@example.com',
            'password' => bcrypt('password123'),
            'role' => 'member',
        ]);

        $otherMemberProfile = Member::create([
            'user_id' => $otherMember->id,
            'member_number' => 'MBR00002',
            'join_date' => Carbon::now()->toDateString(),
            'status' => 'active',
        ]);

        $loan = Loan::create([
            'member_id' => $otherMemberProfile->id,
            'book_id' => $this->book->id,
            'loan_date' => Carbon::now(),
            'due_date' => Carbon::now()->addDays(14),
            'status' => 'borrowed',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->memberToken")
                        ->getJson("/api/loans/{$loan->id}");

        // Member should get 200 but their own loans, not others
        $response->assertStatus(403);
    }

    /**
     * Test cannot return already returned book
     */
    public function test_cannot_return_already_returned_book(): void
    {
        $loan = Loan::create([
            'member_id' => $this->member->member->id,
            'book_id' => $this->book->id,
            'loan_date' => Carbon::now()->subDays(14),
            'due_date' => Carbon::now(),
            'return_date' => Carbon::now(),
            'status' => 'returned',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->memberToken")
                        ->putJson("/api/loans/{$loan->id}/return");

        $response->assertStatus(409)
                ->assertJson([
                    'message' => 'Loan is not in borrowed status',
                ]);
    }

    /**
     * Test inactive member cannot borrow books
     */
    public function test_inactive_member_cannot_borrow_books(): void
    {
        // Suspend member
        $this->member->member->update(['status' => 'suspended']);

        $response = $this->withHeader('Authorization', "Bearer $this->memberToken")
                        ->postJson('/api/loans', [
                            'book_id' => $this->book->id,
                        ]);

        $response->assertStatus(403)
                ->assertJson([
                    'message' => 'Cannot borrow books. Your membership is suspended',
                ]);
    }
}
