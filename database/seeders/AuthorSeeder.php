<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Author;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authors = [
            ['name' => 'Andrea Hirata', 'bio' => 'Penulis Indonesia terkenal dengan karya Laskar Pelangi'],
            ['name' => 'Pramoedya Ananta Toer', 'bio' => 'Sastrawan Indonesia yang paling banyak diterjemahkan'],
            ['name' => 'Tere Liye', 'bio' => 'Penulis novel Indonesia yang sangat produktif'],
            ['name' => 'Dewi Lestari', 'bio' => 'Penulis Indonesia dengan novel Supernova'],
            ['name' => 'Habiburrahman El Shirazy', 'bio' => 'Penulis novel Islami Indonesia'],
            ['name' => 'Ahmad Fuadi', 'bio' => 'Penulis trilogi Negeri 5 Menara'],
            ['name' => 'James Clear', 'bio' => 'Penulis Atomic Habits'],
            ['name' => 'Robert T. Kiyosaki', 'bio' => 'Penulis Rich Dad Poor Dad'],
            ['name' => 'Dale Carnegie', 'bio' => 'Penulis How to Win Friends and Influence People'],
            ['name' => 'Stephen R. Covey', 'bio' => 'Penulis The 7 Habits of Highly Effective People'],
        ];

        foreach ($authors as $author) {
            Author::create($author);
        }
    }
}
