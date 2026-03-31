<?php

namespace App\Filament\Loueur\Resources\VehicleResource\Pages;

use App\Filament\Loueur\Resources\VehicleResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Vehicle;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ListVehicles extends ListRecords
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajouter un véhicule'),
            Actions\Action::make('importCsv')
                ->label('Importer CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->form([
                    Forms\Components\Placeholder::make('csv_help')
                        ->label('')
                        ->content(new \Illuminate\Support\HtmlString('
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl text-sm">
                                <p class="font-semibold text-blue-900 dark:text-blue-100 mb-2">Format du fichier CSV :</p>
                                <p class="text-blue-800 dark:text-blue-200 font-mono text-xs mb-2">marque,modele,nom_complet,annee,prix_jour,transmission,carburant,places,couleur</p>
                                <p class="text-blue-800 dark:text-blue-200 text-xs">
                                    <strong>Exemple :</strong><br>
                                    Renault,Clio,Renault Clio 4,2020,5000,manual,essence,5,Blanc<br>
                                    Hyundai,Tucson,Hyundai Tucson 2023,2023,9000,automatic,diesel,5,Noir
                                </p>
                                <p class="text-blue-800 dark:text-blue-200 text-xs mt-2">
                                    <strong>Transmission :</strong> manual ou automatic<br>
                                    <strong>Carburant :</strong> essence, diesel, hybrid, electric
                                </p>
                            </div>
                        ')),
                    Forms\Components\FileUpload::make('csv_file')
                        ->label('Fichier CSV')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                        ->required()
                        ->storeFiles(false),
                ])
                ->action(function (array $data) {
                    $file = $data['csv_file'];
                    if (!$file || !method_exists($file, 'getRealPath')) {
                        Notification::make()->title('Fichier introuvable')->danger()->send();
                        return;
                    }

                    $loueur = Auth::user()->loueur;
                    if (!$loueur) {
                        Notification::make()->title('Erreur: pas de profil loueur')->danger()->send();
                        return;
                    }

                    // Charger les marques et catégories pour le mapping
                    $brands = Brand::pluck('id', 'name')->mapWithKeys(fn ($id, $name) => [strtolower($name) => $id])->toArray();
                    $categories = Category::pluck('id', 'name')->mapWithKeys(fn ($id, $name) => [strtolower($name) => $id])->toArray();
                    $defaultCategory = Category::first()?->id;

                    $handle = fopen($file->getRealPath(), 'r');
                    $header = fgetcsv($handle, 0, ',');
                    if (!$header) {
                        fclose($handle);
                        Notification::make()->title('Fichier CSV vide')->danger()->send();
                        return;
                    }

                    $header = array_map(fn ($h) => strtolower(trim($h)), $header);
                    $imported = 0;
                    $errors = 0;

                    while (($row = fgetcsv($handle, 0, ',')) !== false) {
                        if (count($row) < 5) {
                            $errors++;
                            continue;
                        }

                        $rowData = array_combine($header, array_pad($row, count($header), ''));

                        $brandName = trim($rowData['marque'] ?? '');
                        $model = trim($rowData['modele'] ?? '');
                        $fullName = trim($rowData['nom_complet'] ?? "{$brandName} {$model}");
                        $year = (int) ($rowData['annee'] ?? 0) ?: null;
                        $pricePerDay = (float) ($rowData['prix_jour'] ?? 0);
                        $transmission = in_array($rowData['transmission'] ?? '', ['manual', 'automatic']) ? $rowData['transmission'] : 'manual';
                        $fuelType = in_array($rowData['carburant'] ?? '', ['essence', 'diesel', 'hybrid', 'electric']) ? $rowData['carburant'] : 'essence';
                        $seats = (int) ($rowData['places'] ?? 5) ?: 5;
                        $color = trim($rowData['couleur'] ?? '') ?: null;

                        if (empty($brandName) || $pricePerDay <= 0) {
                            $errors++;
                            continue;
                        }

                        // Trouver ou créer la marque
                        $brandId = $brands[strtolower($brandName)] ?? null;
                        if (!$brandId) {
                            $brand = Brand::create([
                                'name' => $brandName,
                                'slug' => Str::slug($brandName),
                                'is_active' => true,
                            ]);
                            $brandId = $brand->id;
                            $brands[strtolower($brandName)] = $brandId;
                        }

                        Vehicle::create([
                            'loueur_id' => $loueur->id,
                            'brand_id' => $brandId,
                            'category_id' => $defaultCategory,
                            'model' => $model,
                            'full_name' => $fullName,
                            'slug' => Str::slug($fullName) . '-' . Str::random(4),
                            'year' => $year,
                            'price_per_day' => $pricePerDay,
                            'transmission' => $transmission,
                            'fuel_type' => $fuelType,
                            'seats' => $seats,
                            'color' => $color,
                            'status' => 'available',
                            'is_active' => false, // Inactif par défaut, le loueur active après ajout photos
                        ]);
                        $imported++;
                    }

                    fclose($handle);

                    Notification::make()
                        ->title("Import terminé : {$imported} véhicule(s) importé(s)" . ($errors > 0 ? ", {$errors} ligne(s) ignorée(s)" : ''))
                        ->body('Les véhicules sont inactifs par défaut — ajoutez les photos et activez-les.')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getHeaderContent(): ?\Illuminate\Contracts\View\View
    {
        return view('filament.loueur.partials.vehicle-photo-tip');
    }
}
