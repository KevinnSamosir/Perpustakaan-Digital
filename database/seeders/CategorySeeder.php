<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Novel', 'description' => 'Karya fiksi prosa panjang', 'icon' => 'fa-book'],
            ['name' => 'Sejarah', 'description' => 'Buku tentang sejarah dan peristiwa masa lalu', 'icon' => 'fa-landmark'],
            ['name' => 'Sains', 'description' => 'Buku ilmiah dan pengetahuan alam', 'icon' => 'fa-flask'],
            ['name' => 'Teknologi', 'description' => 'Buku tentang teknologi dan komputer', 'icon' => 'fa-laptop-code'],
            ['name' => 'Bisnis', 'description' => 'Buku tentang bisnis dan ekonomi', 'icon' => 'fa-briefcase'],
            ['name' => 'Self-Help', 'description' => 'Buku pengembangan diri', 'icon' => 'fa-lightbulb'],
            ['name' => 'Pendidikan', 'description' => 'Buku pelajaran dan pendidikan', 'icon' => 'fa-graduation-cap'],
            ['name' => 'Agama', 'description' => 'Buku keagamaan dan spiritualitas', 'icon' => 'fa-pray'],
            ['name' => 'Kesehatan', 'description' => 'Buku tentang kesehatan dan kedokteran', 'icon' => 'fa-heartbeat'],
            ['name' => 'Biografi', 'description' => 'Kisah hidup tokoh-tokoh terkenal', 'icon' => 'fa-user-tie'],
            ['name' => 'Anak-anak', 'description' => 'Buku untuk anak-anak', 'icon' => 'fa-child'],
            ['name' => 'Komik', 'description' => 'Komik dan manga', 'icon' => 'fa-comment-dots'],
            ['name' => 'Jurnal', 'description' => 'Jurnal ilmiah dan akademik', 'icon' => 'fa-file-alt'],
            ['name' => 'Majalah', 'description' => 'Majalah dan publikasi berkala', 'icon' => 'fa-newspaper'],
            ['name' => 'Ensiklopedia', 'description' => 'Buku referensi dan ensiklopedia', 'icon' => 'fa-book-open'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'icon' => $category['icon'],
            ]);
        }
    }
}
