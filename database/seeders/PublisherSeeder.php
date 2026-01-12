<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Publisher;

class PublisherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $publishers = [
            ['name' => 'Gramedia Pustaka Utama', 'address' => 'Jakarta, Indonesia', 'email' => 'info@gramedia.com'],
            ['name' => 'Mizan', 'address' => 'Bandung, Indonesia', 'email' => 'info@mizan.com'],
            ['name' => 'Erlangga', 'address' => 'Jakarta, Indonesia', 'email' => 'info@erlangga.co.id'],
            ['name' => 'Bentang Pustaka', 'address' => 'Yogyakarta, Indonesia', 'email' => 'info@bentangpustaka.com'],
            ['name' => 'Republika', 'address' => 'Jakarta, Indonesia', 'email' => 'info@republika.co.id'],
            ['name' => 'Elex Media Komputindo', 'address' => 'Jakarta, Indonesia', 'email' => 'info@elexmedia.co.id'],
            ['name' => 'Andi Publisher', 'address' => 'Yogyakarta, Indonesia', 'email' => 'info@andipublisher.com'],
            ['name' => 'Serambi', 'address' => 'Jakarta, Indonesia', 'email' => 'info@serambi.co.id'],
        ];

        foreach ($publishers as $publisher) {
            Publisher::create($publisher);
        }
    }
}
