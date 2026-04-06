<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VehicleTemplateResource\Pages;
use App\Models\Brand;
use App\Models\VehicleTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VehicleTemplateResource extends Resource
{
    protected static ?string $model = VehicleTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?string $navigationLabel = 'Visuels véhicules';

    protected static ?string $modelLabel = 'Visuel';

    protected static ?string $pluralModelLabel = 'Visuels véhicules';

    protected static ?int $navigationSort = 4;

    public static function shouldRegisterNavigation(): bool
    {
        return \Illuminate\Support\Facades\Cache::store('file')
            ->remember('has_vehicle_templates_table', 3600, function () {
                try {
                    return \Illuminate\Support\Facades\Schema::hasTable('vehicle_templates');
                } catch (\Exception $e) {
                    return false;
                }
            });
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Visuel véhicule')->schema([
                Forms\Components\Select::make('brand_id')
                    ->label('Marque')
                    ->options(Brand::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->live(),

                Forms\Components\Select::make('model_name')
                    ->label('Modèle')
                    ->required()
                    ->searchable()
                    ->options(function (Forms\Get $get) {
                        $brandId = $get('brand_id');
                        if (!$brandId) return [];
                        return \App\Models\VehicleModel::where('brand_id', $brandId)
                            ->where('is_active', true)
                            ->orderBy('name')
                            ->pluck('name', 'name')
                            ->toArray();
                    })
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom du modèle')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->createOptionUsing(function (array $data, Forms\Get $get): string {
                        $brandId = $get('brand_id');
                        if ($brandId) {
                            \App\Models\VehicleModel::firstOrCreate(
                                ['brand_id' => $brandId, 'name' => $data['name']],
                                ['is_active' => true]
                            );
                        }
                        return $data['name'];
                    })
                    ->createOptionModalHeading('Ajouter un modèle'),

                Forms\Components\Select::make('color')
                    ->label('Couleur')
                    ->required()
                    ->options([
                        'noir' => 'Noir',
                        'blanc' => 'Blanc',
                        'gris' => 'Gris',
                        'rouge' => 'Rouge',
                        'bleu' => 'Bleu',
                        'vert' => 'Vert',
                        'beige' => 'Beige',
                        'marron' => 'Marron',
                        'orange' => 'Orange',
                        'jaune' => 'Jaune',
                    ]),

                Forms\Components\FileUpload::make('image_path')
                    ->label('Image du visuel')
                    ->image()
                    ->directory('vehicle-templates')
                    ->visibility('public')
                    ->required()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('16:10'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Actif')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Visuel')
                    ->disk('public')
                    ->height(60)
                    ->width(96),

                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Marque')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('model_name')
                    ->label('Modèle')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('color')
                    ->label('Couleur')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color(fn ($state) => match($state) {
                        'noir' => 'gray',
                        'blanc' => 'gray',
                        'rouge' => 'danger',
                        'bleu' => 'info',
                        'vert' => 'success',
                        'orange' => 'warning',
                        'jaune' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ajouté le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('brand_id')
            ->filters([
                Tables\Filters\SelectFilter::make('brand_id')
                    ->label('Marque')
                    ->relationship('brand', 'name'),
                Tables\Filters\SelectFilter::make('color')
                    ->label('Couleur')
                    ->options([
                        'noir' => 'Noir', 'blanc' => 'Blanc', 'gris' => 'Gris',
                        'rouge' => 'Rouge', 'bleu' => 'Bleu',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicleTemplates::route('/'),
            'create' => Pages\CreateVehicleTemplate::route('/create'),
            'edit' => Pages\EditVehicleTemplate::route('/{record}/edit'),
        ];
    }
}
