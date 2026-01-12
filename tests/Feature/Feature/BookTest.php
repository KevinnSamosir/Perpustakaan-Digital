<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $adminToken;
    private $member;
    private $memberToken;

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
        $this->memberToken = $this->member->createToken('API Token')->plainTextToken;
    }

    /**
     * Test member can view list of books
     */
    public function test_member_can_view_books_list(): void
    {
        Book::create([
            'title' => 'Laravel Book',
            'author' => 'John Doe',
            'isbn' => '978-0-123456-78-9',
            'publication_year' => 2023,
            'category' => 'Programming',
            'stock' => 5,
            'available_stock' => 5,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->memberToken")
                        ->getJson('/api/books');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data',
                    'pagination',
                ]);
    }

    /**
     * Test admin can create book
     */
    public function test_admin_can_create_book(): void
    {
        $response = $this->withHeader('Authorization', "Bearer $this->adminToken")
                        ->postJson('/api/books', [
                            'title' => 'New Book',
                            'author' => 'Jane Doe',
                            'isbn' => '978-0-123456-78-9',
                            'publication_year' => 2024,
                            'category' => 'Programming',
                            'stock' => 10,
                            'description' => 'A great programming book',
                        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'data',
                ])->assertJson([
                    'message' => 'Book created successfully',
                    'data' => [
                        'title' => 'New Book',
                        'author' => 'Jane Doe',
                        'isbn' => '978-0-123456-78-9',
                    ],
                ]);

        $this->assertDatabaseHas('books', [
            'isbn' => '978-0-123456-78-9',
        ]);
    }

    /**
     * Test member cannot create book
     */
    public function test_member_cannot_create_book(): void
    {
        $response = $this->withHeader('Authorization', "Bearer $this->memberToken")
                        ->postJson('/api/books', [
                            'title' => 'New Book',
                            'author' => 'Jane Doe',
                            'isbn' => '978-0-123456-78-9',
                            'publication_year' => 2024,
                            'category' => 'Programming',
                            'stock' => 10,
                        ]);

        $response->assertStatus(403);
    }

    /**
     * Test creating book with invalid ISBN
     */
    public function test_creating_book_with_duplicate_isbn_fails(): void
    {
        Book::create([
            'title' => 'Existing Book',
            'author' => 'John Doe',
            'isbn' => '978-0-123456-78-9',
            'publication_year' => 2023,
            'category' => 'Programming',
            'stock' => 5,
            'available_stock' => 5,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->adminToken")
                        ->postJson('/api/books', [
                            'title' => 'New Book',
                            'author' => 'Jane Doe',
                            'isbn' => '978-0-123456-78-9',
                            'publication_year' => 2024,
                            'category' => 'Programming',
                            'stock' => 10,
                        ]);

        $response->assertStatus(422);
    }

    /**
     * Test admin can update book
     */
    public function test_admin_can_update_book(): void
    {
        $book = Book::create([
            'title' => 'Old Title',
            'author' => 'John Doe',
            'isbn' => '978-0-123456-78-9',
            'publication_year' => 2023,
            'category' => 'Programming',
            'stock' => 5,
            'available_stock' => 5,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->adminToken")
                        ->putJson("/api/books/{$book->id}", [
                            'title' => 'Updated Title',
                        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Book updated successfully',
                    'data' => [
                        'title' => 'Updated Title',
                    ],
                ]);
    }

    /**
     * Test admin can delete book
     */
    public function test_admin_can_delete_book(): void
    {
        $book = Book::create([
            'title' => 'Book to Delete',
            'author' => 'John Doe',
            'isbn' => '978-0-123456-78-9',
            'publication_year' => 2023,
            'category' => 'Programming',
            'stock' => 5,
            'available_stock' => 5,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->adminToken")
                        ->deleteJson("/api/books/{$book->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    /**
     * Test getting single book details
     */
    public function test_can_get_book_details(): void
    {
        $book = Book::create([
            'title' => 'Book Details',
            'author' => 'John Doe',
            'isbn' => '978-0-123456-78-9',
            'publication_year' => 2023,
            'category' => 'Programming',
            'stock' => 5,
            'available_stock' => 5,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->memberToken")
                        ->getJson("/api/books/{$book->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Book retrieved successfully',
                    'data' => [
                        'id' => $book->id,
                        'title' => 'Book Details',
                    ],
                ]);
    }

    /**
     * Test search books by title
     */
    public function test_can_search_books_by_title(): void
    {
        Book::create([
            'title' => 'Laravel Programming',
            'author' => 'John Doe',
            'isbn' => '978-0-111111-11-1',
            'publication_year' => 2023,
            'category' => 'Programming',
            'stock' => 5,
            'available_stock' => 5,
        ]);

        Book::create([
            'title' => 'PHP Basics',
            'author' => 'Jane Doe',
            'isbn' => '978-0-222222-22-2',
            'publication_year' => 2023,
            'category' => 'Programming',
            'stock' => 3,
            'available_stock' => 3,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->memberToken")
                        ->getJson('/api/books?search=Laravel');

        $response->assertStatus(200)
                ->assertJsonCount(1, 'data');
    }

    /**
     * Test check book availability
     */
    public function test_can_check_book_availability(): void
    {
        $book = Book::create([
            'title' => 'Available Book',
            'author' => 'John Doe',
            'isbn' => '978-0-123456-78-9',
            'publication_year' => 2023,
            'category' => 'Programming',
            'stock' => 5,
            'available_stock' => 2,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->memberToken")
                        ->getJson("/api/books/{$book->id}/check-availability");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'is_available' => true,
                        'available_stock' => 2,
                    ],
                ]);
    }
}
