<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use Illuminate\Support\Facades\File;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('data/Books_Clean_Final.csv');
        
        if (!File::exists($path)) {
            $this->command->error("File CSV tidak ditemukan di: $path");
            return;
        }

        $csvData = File::get($path);
        $rows = array_map('str_getcsv', explode("\n", $csvData));
        $header = array_shift($rows); 

        foreach ($rows as $row) {
            // Cek jika baris kosong atau tidak punya data title
            if (empty($row[1])) continue; 
            
            Book::create([
                'title'     => $row[1],
                'author'    => $row[2],
                'year'      => (int)$row[3],
                'image_url' => $row[6], // Menambahkan image_url dari kolom indeks ke-6
            ]);
        }
        
        $this->command->info("Data berhasil di-seed!");
    }
}