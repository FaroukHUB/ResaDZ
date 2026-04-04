<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\VehicleModel;
use Illuminate\Database\Seeder;

class VehicleModelSeeder extends Seeder
{
    public function run(): void
    {
        $models = [
            'renault' => ['Clio', 'Clio 5', 'Symbol', 'Duster', 'Logan', 'Megane', 'Kadjar', 'Captur', 'Austral'],
            'hyundai' => ['i10', 'i20', 'i30', 'Tucson', 'Accent', 'Creta', 'Kona', 'Santa Fe', 'Elantra'],
            'dacia' => ['Sandero', 'Sandero Stepway', 'Duster', 'Logan', 'Jogger', 'Spring'],
            'peugeot' => ['208', '308', '2008', '3008', '5008', 'Partner', 'Rifter', '301'],
            'volkswagen' => ['Golf', 'Polo', 'Tiguan', 'T-Roc', 'T-Cross', 'Caddy', 'Passat', 'Touareg'],
            'seat' => ['Ibiza', 'Leon', 'Arona', 'Ateca', 'Cupra Formentor', 'Tarraco'],
            'toyota' => ['Corolla', 'Yaris', 'RAV4', 'Hilux', 'C-HR', 'Land Cruiser', 'Fortuner'],
            'kia' => ['Picanto', 'Rio', 'Ceed', 'Sportage', 'Sorento', 'Stonic', 'Niro', 'Seltos'],
            'chevrolet' => ['Spark', 'Aveo', 'Cruze', 'Captiva', 'Trax', 'Optra'],
            'fiat' => ['500', '500X', 'Tipo', 'Doblo', 'Panda', 'Punto'],
            'bmw' => ['Série 1', 'Série 3', 'Série 5', 'X1', 'X2', 'X3', 'X5'],
            'mercedes' => ['Classe A', 'Classe C', 'Classe E', 'CLA', 'GLA', 'GLC', 'GLE'],
            'audi' => ['A1', 'A3', 'A4', 'Q2', 'Q3', 'Q5', 'Q7'],
            'skoda' => ['Fabia', 'Octavia', 'Kamiq', 'Karoq', 'Kodiaq', 'Scala'],
            'suzuki' => ['Swift', 'Vitara', 'S-Cross', 'Jimny', 'Celerio', 'Baleno'],
            'nissan' => ['Micra', 'Qashqai', 'Juke', 'X-Trail', 'Navara', 'Sunny'],
            'citroen' => ['C3', 'C4', 'C5 Aircross', 'Berlingo', 'C-Elysée'],
            'ford' => ['Fiesta', 'Focus', 'Kuga', 'Puma', 'Ranger', 'Mustang'],
            'opel' => ['Corsa', 'Astra', 'Crossland', 'Grandland', 'Mokka'],
            'mitsubishi' => ['L200', 'ASX', 'Outlander', 'Pajero', 'Eclipse Cross'],
            'geely' => ['Coolray', 'Azkarra', 'Emgrand', 'Monjaro', 'Tugella'],
            'chery' => ['Tiggo 2', 'Tiggo 4', 'Tiggo 7', 'Tiggo 8', 'Arrizo 5'],
            'haval' => ['H6', 'Jolion', 'H9', 'Dargo'],
            'mg' => ['ZS', 'HS', 'MG5', 'MG3', 'Marvel R'],
            'byd' => ['Atto 3', 'Seal', 'Dolphin', 'Tang', 'Han'],
            'cupra' => ['Formentor', 'Born', 'Leon', 'Ateca'],
            'livan' => ['X3 Pro', 'X6 Pro', 'S6 Pro'],
            'jetour' => ['Dashing', 'X70', 'X90', 'T2'],
            'changan' => ['CS35', 'CS55', 'CS75', 'Alsvin', 'Uni-T'],
        ];

        foreach ($models as $brandSlug => $modelNames) {
            $brand = Brand::where('slug', $brandSlug)->first();
            if (!$brand) continue;

            foreach ($modelNames as $modelName) {
                VehicleModel::firstOrCreate(
                    ['brand_id' => $brand->id, 'name' => $modelName],
                    ['is_active' => true]
                );
            }
        }
    }
}
