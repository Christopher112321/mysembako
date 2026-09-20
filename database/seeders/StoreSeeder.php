<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan kategori-kategori dasar tersedia
        $categories = $this->ensureCategories();

        // Toko 1: Semarang
        $storeSemarang = User::updateOrCreate(
            ['email' => 'toko.semarang@mysembako.com'],
            [
                'name' => 'Toserba Buhmi',
                'email' => 'toko.semarang@mysembako.com',
                'password' => Hash::make('toko123'),
                'role_id' => 1, // Owner role
                'phone' => '024-1234567',
                'location' => 'Semarang',
            ]
        );

        // Toko 2: Jakarta
        $storeJakarta = User::updateOrCreate(
            ['email' => 'toko.jakarta@mysembako.com'],
            [
                'name' => 'ToJakarta',
                'email' => 'toko.jakarta@mysembako.com',
                'password' => Hash::make('toko123'),
                'role_id' => 1, // Owner role
                'phone' => '021-7654321',
                'location' => 'Jakarta',
            ]
        );

        // Produk untuk Toko Semarang
        $productsSemarang = [
            [
                'name' => 'Beras Premium 5kg',
                'price' => 75000,
                'stock' => 50,
                'description' => 'Beras premium kualitas terbaik, cocok untuk konsumsi sehari-hari.',
                'status' => 'aktif',
                'category' => 'Beras',
            ],
            [
                'name' => 'Minyak Goreng Bimoli 2L',
                'price' => 32000,
                'stock' => 100,
                'description' => 'Minyak goreng berkualitas tinggi, bebas kolesterol.',
                'status' => 'aktif',
                'category' => 'Minyak Goreng',
            ],
            [
                'name' => 'Gula Pasir Gulaku 1kg',
                'price' => 15000,
                'stock' => 80,
                'description' => 'Gula pasir putih halus, cocok untuk berbagai keperluan.',
                'status' => 'aktif',
                'category' => 'Gula Pasir',
            ],
            [
                'name' => 'Telur Ayam Ras 1kg',
                'price' => 28000,
                'stock' => 60,
                'description' => 'Telur ayam ras segar, kualitas terbaik.',
                'status' => 'aktif',
                'category' => 'Telur',
            ],
            [
                'name' => 'Bumbu Dapur Komplet',
                'price' => 25000,
                'stock' => 40,
                'description' => 'Paket bumbu dapur lengkap: garam, merica, ketumbar, dll.',
                'status' => 'aktif',
                'category' => 'Bumbu Dapur',
            ],
            [
                'name' => 'Mie Indomie Goreng 1 Dus',
                'price' => 45000,
                'stock' => 70,
                'description' => 'Mie instan Indomie goreng, 1 dus isi 40 bungkus.',
                'status' => 'aktif',
                'category' => 'Mie & Pasta',
            ],
            [
                'name' => 'Kopi Kapal Api 200gr',
                'price' => 18000,
                'stock' => 90,
                'description' => 'Kopi bubuk Kapal Api, rasa khas nusantara.',
                'status' => 'aktif',
                'category' => 'Kopi & Teh',
            ],
            [
                'name' => 'Susu UHT Ultra Milk 1L',
                'price' => 22000,
                'stock' => 55,
                'description' => 'Susu UHT segar, kaya kalsium dan vitamin.',
                'status' => 'aktif',
                'category' => 'Susu & Olahan',
            ],
        ];

        // Produk untuk Toko Jakarta
        $productsJakarta = [
            [
                'name' => 'Beras IR 64 10kg',
                'price' => 120000,
                'stock' => 45,
                'description' => 'Beras IR 64 kualitas premium, pulen dan enak.',
                'status' => 'aktif',
                'category' => 'Beras',
            ],
            [
                'name' => 'Minyak Goreng Filma 1L',
                'price' => 18000,
                'stock' => 120,
                'description' => 'Minyak goreng Filma, praktis dan ekonomis.',
                'status' => 'aktif',
                'category' => 'Minyak Goreng',
            ],
            [
                'name' => 'Gula Pasir Rose Brand 1kg',
                'price' => 16000,
                'stock' => 95,
                'description' => 'Gula pasir Rose Brand, manis alami.',
                'status' => 'aktif',
                'category' => 'Gula Pasir',
            ],
            [
                'name' => 'Telur Ayam Kampung 1kg',
                'price' => 35000,
                'stock' => 50,
                'description' => 'Telur ayam kampung organik, lebih sehat dan bergizi.',
                'status' => 'aktif',
                'category' => 'Telur',
            ],
            [
                'name' => 'Bumbu Racik Ayam Goreng',
                'price' => 5000,
                'stock' => 150,
                'description' => 'Bumbu instan untuk ayam goreng, praktis dan lezat.',
                'status' => 'aktif',
                'category' => 'Bumbu Dapur',
            ],
            [
                'name' => 'Mie Sedap Goreng 1 Dus',
                'price' => 42000,
                'stock' => 85,
                'description' => 'Mie instan Sedap goreng, 1 dus isi 40 bungkus.',
                'status' => 'aktif',
                'category' => 'Mie & Pasta',
            ],
            [
                'name' => 'Teh Botol Sosro 1 Dus',
                'price' => 55000,
                'stock' => 65,
                'description' => 'Teh botol Sosro, 1 dus isi 24 botol.',
                'status' => 'aktif',
                'category' => 'Kopi & Teh',
            ],
            [
                'name' => 'Susu Kental Manis Frisian Flag',
                'price' => 12000,
                'stock' => 110,
                'description' => 'Susu kental manis Frisian Flag, 1 kaleng 370gr.',
                'status' => 'aktif',
                'category' => 'Susu & Olahan',
            ],
            [
                'name' => 'Air Minum Galon 19L',
                'price' => 20000,
                'stock' => 30,
                'description' => 'Air minum galon isi ulang, bersih dan sehat.',
                'status' => 'aktif',
                'category' => 'Air Minum / Galon',
            ],
            [
                'name' => 'Gas LPG 3kg',
                'price' => 25000,
                'stock' => 25,
                'description' => 'Gas LPG 3kg, aman dan praktis.',
                'status' => 'aktif',
                'category' => 'Gas LPG',
            ],
        ];

        // Insert produk untuk Toko Semarang
        foreach ($productsSemarang as $productData) {
            $category = $categories->firstWhere('name', $productData['category']);
            Product::updateOrCreate(
                [
                    'owner_id' => $storeSemarang->id,
                    'name' => $productData['name'],
                ],
                [
                    'owner_id' => $storeSemarang->id,
                    'name' => $productData['name'],
                    'price' => $productData['price'],
                    'stock' => $productData['stock'],
                    'description' => $productData['description'],
                    'status' => $productData['status'],
                    'category_id' => $category ? $category->id : null,
                ]
            );
        }

        // Insert produk untuk Toko Jakarta
        foreach ($productsJakarta as $productData) {
            $category = $categories->firstWhere('name', $productData['category']);
            Product::updateOrCreate(
                [
                    'owner_id' => $storeJakarta->id,
                    'name' => $productData['name'],
                ],
                [
                    'owner_id' => $storeJakarta->id,
                    'name' => $productData['name'],
                    'price' => $productData['price'],
                    'stock' => $productData['stock'],
                    'description' => $productData['description'],
                    'status' => $productData['status'],
                    'category_id' => $category ? $category->id : null,
                ]
            );
        }

        $this->command->info('Toko Semarang created successfully!');
        $this->command->info('Email: toko.semarang@mysembako.com');
        $this->command->info('Password: toko123');
        $this->command->info('Location: Semarang');
        $this->command->info('Products: ' . count($productsSemarang));
        $this->command->info('');
        $this->command->info('Toko Jakarta created successfully!');
        $this->command->info('Email: toko.jakarta@mysembako.com');
        $this->command->info('Password: toko123');
        $this->command->info('Location: Jakarta');
        $this->command->info('Products: ' . count($productsJakarta));
    }

    /**
     * Pastikan kategori-kategori dasar tersedia di database.
     */
    private function ensureCategories()
    {
        $categoryNames = [
            'Beras',
            'Minyak Goreng',
            'Gula Pasir',
            'Telur',
            'Bumbu Dapur',
            'Mie & Pasta',
            'Kopi & Teh',
            'Susu & Olahan',
            'Air Minum / Galon',
            'Gas LPG',
            'Snack & Cemilan',
        ];

        $categories = collect();

        foreach ($categoryNames as $name) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
            $categories->push($category);
        }

        return $categories;
    }
}

