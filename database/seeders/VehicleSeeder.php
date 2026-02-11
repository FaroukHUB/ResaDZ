<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les catégories
        $categories = [
            ['name' => 'SUV', 'slug' => 'suv', 'sort_order' => 1],
            ['name' => 'Berline', 'slug' => 'berline', 'sort_order' => 2],
            ['name' => 'Citadine', 'slug' => 'citadine', 'sort_order' => 3],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        // Créer les marques avec logos
        $brands = [
            ['name' => 'Volkswagen', 'slug' => 'volkswagen', 'logo' => 'assets/vw.png', 'sort_order' => 1],
            ['name' => 'BMW', 'slug' => 'bmw', 'logo' => 'assets/bmw.svg', 'sort_order' => 2],
            ['name' => 'Mercedes', 'slug' => 'mercedes', 'logo' => 'assets/mercedes.svg', 'sort_order' => 3],
            ['name' => 'Audi', 'slug' => 'audi', 'logo' => 'assets/Audi.webp', 'sort_order' => 4],
            ['name' => 'Peugeot', 'slug' => 'peugeot', 'logo' => 'assets/peugeot.svg', 'sort_order' => 5],
            ['name' => 'Renault', 'slug' => 'renault', 'logo' => 'assets/renault.svg', 'sort_order' => 6],
            ['name' => 'Dacia', 'slug' => 'dacia', 'logo' => 'assets/dacia.jpg', 'sort_order' => 7],
            ['name' => 'Fiat', 'slug' => 'fiat', 'logo' => 'assets/fiat.svg', 'sort_order' => 8],
            ['name' => 'Seat', 'slug' => 'seat', 'logo' => 'assets/Seat.svg', 'sort_order' => 9],
            ['name' => 'Skoda', 'slug' => 'skoda', 'logo' => 'assets/skoda.svg', 'sort_order' => 10],
            ['name' => 'Suzuki', 'slug' => 'suzuki', 'logo' => 'assets/logosuzuki.jpg', 'sort_order' => 11],
            ['name' => 'Hyundai', 'slug' => 'hyundai', 'logo' => 'assets/Hyundai.svg', 'sort_order' => 12],
        ];

        foreach ($brands as $brandData) {
            Brand::firstOrCreate(
                ['slug' => $brandData['slug']],
                $brandData
            );
        }

        // Données des véhicules (converties depuis l'ancien format)
        $vehicles = [
            // VW
            ['brand' => 'volkswagen', 'model' => 'Tiguan 2025', 'full_name' => 'VW Tiguan 2025', 'price' => 45000, 'price_eur' => 179, 'image' => 'assets/Tiguan-2024-2025.webp', 'category' => 'suv'],
            ['brand' => 'volkswagen', 'model' => 'Tiguan 2025 Gris', 'full_name' => 'VW Tiguan 2025 Gris', 'price' => 45000, 'price_eur' => 179, 'image' => 'assets/tiguan2025gris.jpg', 'category' => 'suv'],
            ['brand' => 'volkswagen', 'model' => 'T-ROC 2025', 'full_name' => 'VW T-Roc 2025', 'price' => 20000, 'price_eur' => 79, 'image' => 'assets/troc.jpg', 'category' => 'suv'],
            ['brand' => 'volkswagen', 'model' => 'Golf 8 2025', 'full_name' => 'VW Golf 8 2025', 'price' => 20000, 'price_eur' => 79, 'image' => 'assets/Golf 8 2025.webp', 'category' => 'berline'],
            ['brand' => 'volkswagen', 'model' => 'T-Cross 2023', 'full_name' => 'VW T-Cross 2023', 'price' => 15000, 'price_eur' => 59, 'image' => 'assets/tcross.jpg', 'category' => 'suv'],
            ['brand' => 'volkswagen', 'model' => 'Tiguan 2022-2024', 'full_name' => 'VW Tiguan 2022-2024', 'price' => 25000, 'price_eur' => 99, 'image' => 'assets/tiguan 2022 2023.webp', 'category' => 'suv'],
            ['brand' => 'volkswagen', 'model' => 'Tiguan 2022-2024 Noir', 'full_name' => 'VW Tiguan 2022-2024 Noir', 'price' => 25000, 'price_eur' => 99, 'image' => 'assets/tiguannoir.jpg', 'category' => 'suv'],
            ['brand' => 'volkswagen', 'model' => 'Tiguan 2022-2024 Gris', 'full_name' => 'VW Tiguan 2022-2024 Gris', 'price' => 25000, 'price_eur' => 99, 'image' => 'assets/tiguangris.jpg', 'category' => 'suv'],

            // BMW
            ['brand' => 'bmw', 'model' => 'Série 1 2025', 'full_name' => 'BMW Série 1 2025', 'price' => 20000, 'price_eur' => 79, 'image' => 'assets/Bmw-Serie1.webp', 'category' => 'berline'],
            ['brand' => 'bmw', 'model' => 'X2 2023', 'full_name' => 'BMW X2 2023', 'price' => 18000, 'price_eur' => 75, 'image' => 'assets/x2.jpg', 'category' => 'suv'],

            // Mercedes
            ['brand' => 'mercedes', 'model' => 'GLE 2024', 'full_name' => 'Mercedes GLE 2024', 'price' => 70000, 'price_eur' => 270, 'image' => 'assets/Gle-2024.webp', 'category' => 'suv'],
            ['brand' => 'mercedes', 'model' => 'CLA 2022', 'full_name' => 'Mercedes CLA 2022', 'price' => 30000, 'price_eur' => 119, 'image' => 'assets/cla.jpg', 'category' => 'suv'],

            // Hyundai
            ['brand' => 'hyundai', 'model' => 'Creta 2019', 'full_name' => 'Hyundai Creta 2019', 'price' => 8000, 'price_eur' => 30, 'image' => 'assets/hyundai-creta-2019.webp', 'category' => 'suv'],

            // Peugeot
            ['brand' => 'peugeot', 'model' => '3008 GT', 'full_name' => 'Peugeot 3008 GT', 'price' => 18000, 'price_eur' => 69, 'image' => 'assets/Peugeot-3008_gt-2022-2024.webp', 'category' => 'suv'],
            ['brand' => 'peugeot', 'model' => '208 2022', 'full_name' => 'Peugeot 208 2022', 'price' => 10000, 'price_eur' => 39, 'image' => 'assets/Peugeot2082022.webp', 'category' => 'berline'],

            // Audi
            ['brand' => 'audi', 'model' => 'Q3 2023', 'full_name' => 'Audi Q3 2023', 'price' => 35000, 'price_eur' => 135, 'image' => 'assets/Audi-Q3-2023.webp', 'category' => 'suv'],
            ['brand' => 'audi', 'model' => 'Q3 2025 Diesel', 'full_name' => 'Audi Q3 2025 Diesel', 'price' => 45000, 'price_eur' => 179, 'image' => 'assets/Audi-Q3-2025.webp', 'category' => 'suv', 'fuel' => 'diesel'],
            ['brand' => 'audi', 'model' => 'Q5 2021', 'full_name' => 'Audi Q5 2021', 'price' => 40000, 'price_eur' => 169, 'image' => 'assets/Audi-Q5-2021.webp', 'category' => 'suv'],
            ['brand' => 'audi', 'model' => 'Q5 2022', 'full_name' => 'Audi Q5 2022', 'price' => 35000, 'price_eur' => 135, 'image' => 'assets/Audi-Q5-2022.webp', 'category' => 'suv'],
            ['brand' => 'audi', 'model' => 'Q3 2025 Essence', 'full_name' => 'Audi Q3 2025 Essence', 'price' => 45000, 'price_eur' => 179, 'image' => 'assets/Audi-Q3-2025.webp', 'category' => 'suv', 'fuel' => 'essence'],
            ['brand' => 'audi', 'model' => 'A1 2022', 'full_name' => 'Audi A1 2022', 'price' => 10000, 'price_eur' => 39, 'image' => 'assets/Audi-A1-2022.webp', 'category' => 'berline'],
            ['brand' => 'audi', 'model' => 'Q2 2023', 'full_name' => 'Audi Q2 2023', 'price' => 20000, 'price_eur' => 79, 'image' => 'assets/Audi-Q2-2022.webp', 'category' => 'suv'],

            // Fiat
            ['brand' => 'fiat', 'model' => '500X 2024', 'full_name' => 'Fiat 500X 2024', 'price' => 8000, 'price_eur' => 30, 'image' => 'assets/500x.webp', 'category' => 'berline'],
            ['brand' => 'fiat', 'model' => '500 2025', 'full_name' => 'Fiat 500 2025', 'price' => 6000, 'price_eur' => 25, 'image' => 'assets/500.webp', 'category' => 'citadine'],
            ['brand' => 'fiat', 'model' => '500 2025 Rouge', 'full_name' => 'Fiat 500 2025 Rouge', 'price' => 6000, 'price_eur' => 25, 'image' => 'assets/fiat500rouge.jpg', 'category' => 'citadine'],
            ['brand' => 'fiat', 'model' => 'Tipo 2025', 'full_name' => 'Fiat Tipo 2025', 'price' => 8000, 'price_eur' => 30, 'image' => 'assets/fiat-tipo-2025.webp', 'category' => 'berline'],
            ['brand' => 'fiat', 'model' => 'Tipo 2023 Auto', 'full_name' => 'Fiat Tipo 2023 Automatique', 'price' => 8000, 'price_eur' => 30, 'image' => 'assets/tipo41.jpg', 'category' => 'berline', 'transmission' => 'automatic'],
            ['brand' => 'fiat', 'model' => 'Doblo 2025', 'full_name' => 'Fiat Doblo 2025', 'price' => 9000, 'price_eur' => 35, 'image' => 'assets/doblo.webp', 'category' => 'berline'],
            ['brand' => 'fiat', 'model' => 'Doblo 2025 Gris', 'full_name' => 'Fiat Doblo 2025 Gris', 'price' => 9000, 'price_eur' => 35, 'image' => 'assets/doblogris.jpg', 'category' => 'berline'],
            ['brand' => 'fiat', 'model' => 'Doblo 2025 Vert', 'full_name' => 'Fiat Doblo 2025 Vert', 'price' => 9000, 'price_eur' => 35, 'image' => 'assets/doblovert.jpg', 'category' => 'berline'],

            // Renault
            ['brand' => 'renault', 'model' => 'Clio 5 Essence', 'full_name' => 'Renault Clio 5 2022 Essence', 'price' => 8000, 'price_eur' => 30, 'image' => 'assets/clio5.jpg', 'category' => 'citadine', 'fuel' => 'essence'],
            ['brand' => 'renault', 'model' => 'Clio 5 Diesel', 'full_name' => 'Renault Clio 5 2022 Diesel', 'price' => 9000, 'price_eur' => 35, 'image' => 'assets/clionoir.jpg', 'category' => 'citadine', 'fuel' => 'diesel'],
            ['brand' => 'renault', 'model' => 'Symbol', 'full_name' => 'Renault Symbol', 'price' => 5000, 'price_eur' => 20, 'image' => 'assets/symbol.webp', 'category' => 'citadine'],

            // Suzuki
            ['brand' => 'suzuki', 'model' => 'Swift 2025', 'full_name' => 'Suzuki Swift 2025', 'price' => 8000, 'price_eur' => 30, 'image' => 'assets/swift.jpg', 'category' => 'citadine'],

            // Skoda
            ['brand' => 'skoda', 'model' => 'Kamiq 2022', 'full_name' => 'Skoda Kamiq 2022', 'price' => 10000, 'price_eur' => 39, 'image' => 'assets/kamiq2022.jpg', 'category' => 'suv'],
            ['brand' => 'skoda', 'model' => 'Fabia 2022', 'full_name' => 'Skoda Fabia 2022', 'price' => 8000, 'price_eur' => 30, 'image' => 'assets/fabia.jpg', 'category' => 'citadine'],
            ['brand' => 'skoda', 'model' => 'Fabia 2019', 'full_name' => 'Skoda Fabia 2019', 'price' => 6000, 'price_eur' => 25, 'image' => 'assets/fabia2019.jpg', 'category' => 'citadine'],

            // Seat
            ['brand' => 'seat', 'model' => 'Cupra Formentor 2023', 'full_name' => 'Seat Cupra Formentor 2023', 'price' => 18000, 'price_eur' => 69, 'image' => 'assets/Seat-cupra-formentor-2023.webp', 'category' => 'suv'],
            ['brand' => 'seat', 'model' => 'Ateca 2022', 'full_name' => 'Seat Ateca 2022', 'price' => 15000, 'price_eur' => 59, 'image' => 'assets/ateca.jpg', 'category' => 'suv'],
            ['brand' => 'seat', 'model' => 'Ibiza 2018', 'full_name' => 'Seat Ibiza 2018', 'price' => 6000, 'price_eur' => 25, 'image' => 'assets/Seat-Ibiza-2018.webp', 'category' => 'berline'],
            ['brand' => 'seat', 'model' => 'Ibiza 2024 Auto', 'full_name' => 'Seat Ibiza 2024 Automatique', 'price' => 9000, 'price_eur' => 35, 'image' => 'assets/Seat-Ibiza-2025.webp', 'category' => 'berline', 'transmission' => 'automatic'],

            // Dacia
            ['brand' => 'dacia', 'model' => 'Duster 2021-2022', 'full_name' => 'Dacia Duster 2021-2022', 'price' => 8000, 'price_eur' => 30, 'image' => 'assets/Dacia-duster-2021-2023.webp', 'category' => 'suv'],
            ['brand' => 'dacia', 'model' => 'Duster 2023-2024', 'full_name' => 'Dacia Duster 2023-2024', 'price' => 10000, 'price_eur' => 39, 'image' => 'assets/Dacia-duster-2023-2025.webp', 'category' => 'suv'],
            ['brand' => 'dacia', 'model' => 'Duster 2025', 'full_name' => 'Dacia Duster 2025', 'price' => 13000, 'price_eur' => 49, 'image' => 'assets/duster2025.jpg', 'category' => 'suv'],
            ['brand' => 'dacia', 'model' => 'Sandero Stepway', 'full_name' => 'Dacia Sandero Stepway', 'price' => 6000, 'price_eur' => 25, 'image' => 'assets/stepway.webp', 'category' => 'citadine'],
            ['brand' => 'dacia', 'model' => 'Sandero', 'full_name' => 'Dacia Sandero', 'price' => 6000, 'price_eur' => 25, 'image' => 'assets/sandero.jpg', 'category' => 'citadine'],
        ];

        $order = 0;
        foreach ($vehicles as $vehicleData) {
            $brand = Brand::where('slug', $vehicleData['brand'])->first();
            $category = Category::where('slug', $vehicleData['category'])->first();

            if (!$brand || !$category) {
                continue;
            }

            $slug = Str::slug($vehicleData['full_name']);
            $baseSlug = $slug;
            $counter = 1;
            while (Vehicle::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            Vehicle::create([
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'model' => $vehicleData['model'],
                'full_name' => $vehicleData['full_name'],
                'slug' => $slug,
                'price_per_day' => $vehicleData['price'],
                'price_per_day_eur' => $vehicleData['price_eur'] ?? null,
                'image' => $vehicleData['image'],
                'transmission' => $vehicleData['transmission'] ?? 'automatic',
                'fuel_type' => $vehicleData['fuel'] ?? 'diesel',
                'seats' => 5,
                'doors' => 5,
                'status' => 'available',
                'is_active' => true,
                'sort_order' => $order++,
            ]);
        }

        $this->command->info('Véhicules importés: ' . Vehicle::count());
    }
}
