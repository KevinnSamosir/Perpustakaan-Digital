<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Category;
use App\Models\Author;
use App\Models\Publisher;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing categories, authors, publishers or use IDs
        $categories = Category::pluck('id', 'name')->toArray();
        $authors = Author::pluck('id', 'name')->toArray();
        $publishers = Publisher::pluck('id', 'name')->toArray();

        $books = [
            [
                'title' => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'isbn' => '978-602-8519-93-1',
                'category' => 'Novel',
                'publication_year' => 2005,
                'stock' => 10,
                'available_stock' => 8,
                'description' => 'Laskar Pelangi adalah novel pertama karya Andrea Hirata yang diterbitkan oleh Bentang Pustaka pada tahun 2005. Novel ini bercerita tentang kehidupan 10 anak dari keluarga miskin yang bersekolah di sebuah sekolah Muhammadiyah di Belitung yang penuh dengan keterbatasan.',
                'pages' => 529,
                'language' => 'Indonesia',
            ],
            [
                'title' => 'Bumi Manusia',
                'author' => 'Pramoedya Ananta Toer',
                'isbn' => '978-979-433-112-2',
                'category' => 'Sejarah',
                'publication_year' => 1980,
                'stock' => 8,
                'available_stock' => 5,
                'description' => 'Bumi Manusia adalah sebuah novel sejarah karya Pramoedya Ananta Toer yang ditulis saat dipenjara di Pulau Buru. Novel ini merupakan bagian pertama dari Tetralogi Buru yang menceritakan tentang kehidupan Minke di masa kolonial Belanda.',
                'pages' => 535,
                'language' => 'Indonesia',
            ],
            [
                'title' => 'Filosofi Teras',
                'author' => 'Henry Manampiring',
                'isbn' => '978-602-291-536-8',
                'category' => 'Self-Help',
                'publication_year' => 2018,
                'stock' => 12,
                'available_stock' => 10,
                'description' => 'Filosofi Teras membahas tentang filsafat Stoisisme yang dipopulerkan oleh para filsuf Yunani dan Romawi kuno seperti Seneca, Epictetus, dan Marcus Aurelius. Buku ini mengajarkan cara mengelola emosi dan menghadapi berbagai tantangan hidup.',
                'pages' => 346,
                'language' => 'Indonesia',
            ],
            [
                'title' => 'Atomic Habits',
                'author' => 'James Clear',
                'isbn' => '978-602-291-778-2',
                'category' => 'Produktivitas',
                'publication_year' => 2018,
                'stock' => 6,
                'available_stock' => 0,
                'description' => 'Atomic Habits adalah buku tentang cara membangun kebiasaan baik dan menghilangkan kebiasaan buruk. James Clear memperkenalkan konsep perubahan kecil yang dilakukan secara konsisten dapat menghasilkan hasil yang luar biasa.',
                'pages' => 320,
                'language' => 'Indonesia',
            ],
            [
                'title' => 'Sapiens: Riwayat Singkat Umat Manusia',
                'author' => 'Yuval Noah Harari',
                'isbn' => '978-602-6379-00-4',
                'category' => 'Sejarah',
                'publication_year' => 2011,
                'stock' => 7,
                'available_stock' => 4,
                'description' => 'Sapiens menceritakan sejarah umat manusia dari masa prasejarah hingga era modern. Yuval Noah Harari mengajak pembaca untuk memahami bagaimana Homo sapiens bisa mendominasi Bumi dan bagaimana peradaban manusia berkembang.',
                'pages' => 512,
                'language' => 'Indonesia',
            ],
            [
                'title' => 'The Psychology of Money',
                'author' => 'Morgan Housel',
                'isbn' => '978-602-6379-50-9',
                'category' => 'Bisnis',
                'publication_year' => 2020,
                'stock' => 9,
                'available_stock' => 6,
                'description' => 'The Psychology of Money membahas hubungan unik antara manusia dan uang. Morgan Housel menjelaskan bahwa kesuksesan finansial tidak selalu tentang pengetahuan teknis, tetapi lebih tentang perilaku dan pola pikir.',
                'pages' => 256,
                'language' => 'Indonesia',
            ],
            [
                'title' => 'Negeri 5 Menara',
                'author' => 'Ahmad Fuadi',
                'isbn' => '978-979-433-778-0',
                'category' => 'Novel',
                'publication_year' => 2009,
                'stock' => 10,
                'available_stock' => 7,
                'description' => 'Negeri 5 Menara adalah novel karya Ahmad Fuadi yang menceritakan tentang persahabatan enam santri di sebuah pondok pesantren di Ponorogo. Novel ini mengangkat tema pendidikan, persahabatan, dan mimpi.',
                'pages' => 423,
                'language' => 'Indonesia',
            ],
            [
                'title' => 'Dilan: Dia adalah Dilanku Tahun 1990',
                'author' => 'Pidi Baiq',
                'isbn' => '978-602-291-112-4',
                'category' => 'Novel',
                'publication_year' => 2014,
                'stock' => 8,
                'available_stock' => 2,
                'description' => 'Dilan 1990 adalah novel romansa karya Pidi Baiq yang menceritakan kisah cinta antara Dilan dan Milea di tahun 1990. Novel ini sukses diadaptasi menjadi film layar lebar yang sangat populer.',
                'pages' => 332,
                'language' => 'Indonesia',
            ],
            [
                'title' => 'Perahu Kertas',
                'author' => 'Dee Lestari',
                'isbn' => '978-979-433-850-3',
                'category' => 'Novel',
                'publication_year' => 2009,
                'stock' => 6,
                'available_stock' => 4,
                'description' => 'Perahu Kertas adalah novel karya Dee Lestari yang menceritakan tentang Kugy dan Keenan, dua anak muda yang memiliki passion berbeda namun takdir yang sama. Novel ini mengangkat tema cinta, mimpi, dan pencarian jati diri.',
                'pages' => 444,
                'language' => 'Indonesia',
            ],
            [
                'title' => 'Pulang',
                'author' => 'Tere Liye',
                'isbn' => '978-602-9747-87-5',
                'category' => 'Novel',
                'publication_year' => 2015,
                'stock' => 10,
                'available_stock' => 8,
                'description' => 'Pulang adalah novel karya Tere Liye yang menceritakan tentang Bujang, seorang pria yang harus menghadapi masa lalunya yang kelam. Novel ini mengangkat tema keluarga, pengampunan, dan arti sebuah rumah.',
                'pages' => 400,
                'language' => 'Indonesia',
            ],
            [
                'title' => 'Sebuah Seni untuk Bersikap Bodo Amat',
                'author' => 'Mark Manson',
                'isbn' => '978-602-291-429-3',
                'category' => 'Self-Help',
                'publication_year' => 2016,
                'stock' => 8,
                'available_stock' => 5,
                'description' => 'Buku ini mengajarkan pendekatan yang berbeda dalam menjalani hidup yang baik. Mark Manson berpendapat bahwa kita harus belajar untuk peduli pada hal-hal yang benar-benar penting dan berhenti mengkhawatirkan hal-hal yang tidak penting.',
                'pages' => 224,
                'language' => 'Indonesia',
            ],
            [
                'title' => 'Rich Dad Poor Dad',
                'author' => 'Robert T. Kiyosaki',
                'isbn' => '978-602-291-662-4',
                'category' => 'Bisnis',
                'publication_year' => 1997,
                'stock' => 7,
                'available_stock' => 3,
                'description' => 'Rich Dad Poor Dad menceritakan pelajaran finansial yang diterima Robert Kiyosaki dari dua ayahnya. Buku ini mengajarkan perbedaan cara berpikir antara orang kaya dan orang miskin tentang uang.',
                'pages' => 336,
                'language' => 'Indonesia',
            ],
        ];

        foreach ($books as $bookData) {
            // Find or create related records
            $categoryId = null;
            $authorId = null;
            $publisherId = null;

            // Match category
            if (isset($bookData['category'])) {
                $categoryId = $categories[$bookData['category']] ?? 
                    (Category::where('name', 'like', '%' . $bookData['category'] . '%')->first()?->id);
            }

            // Match author
            if (isset($bookData['author'])) {
                $authorId = $authors[$bookData['author']] ?? 
                    (Author::where('name', 'like', '%' . $bookData['author'] . '%')->first()?->id);
                
                // Create author if not exists
                if (!$authorId) {
                    $author = Author::create(['name' => $bookData['author']]);
                    $authorId = $author->id;
                    $authors[$bookData['author']] = $authorId;
                }
            }

            Book::updateOrCreate(
                ['isbn' => $bookData['isbn']],
                [
                    'title' => $bookData['title'],
                    'slug' => Str::slug($bookData['title']) . '-' . Str::random(5),
                    'author' => $bookData['author'],
                    'isbn' => $bookData['isbn'],
                    'category' => $bookData['category'],
                    'category_id' => $categoryId,
                    'author_id' => $authorId,
                    'publisher_id' => $publisherId,
                    'publication_year' => $bookData['publication_year'],
                    'stock' => $bookData['stock'],
                    'available_stock' => $bookData['available_stock'],
                    'description' => $bookData['description'],
                    'pages' => $bookData['pages'] ?? null,
                    'language' => $bookData['language'] ?? 'Indonesia',
                    'is_active' => true,
                    'is_featured' => rand(0, 1),
                ]
            );
        }

        $this->command->info('Books seeded successfully!');
    }
}
