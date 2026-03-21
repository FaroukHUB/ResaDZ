<?php

namespace App\Filament\Chauffeur\Resources;

use App\Filament\Chauffeur\Resources\ChauffeurVehicleResource\Pages;
use App\Models\ChauffeurVehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ChauffeurVehicleResource extends Resource
{
    protected static ?string $model = ChauffeurVehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Mon Parc';

    protected static ?string $navigationLabel = 'Mes Vehicules';

    protected static ?string $modelLabel = 'Vehicule';

    protected static ?string $pluralModelLabel = 'Vehicules';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $loueur = Auth::user()->loueur;

        return parent::getEloquentQuery()
            ->when($loueur, fn ($query) => $query->where('loueur_id', $loueur->id));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations du vehicule')
                    ->icon('heroicon-o-truck')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('brand')
                            ->label('Marque')
                            ->placeholder('Ex: Toyota, Mercedes, Peugeot...')
                            ->required()
                            ->maxLength(50),

                        Forms\Components\TextInput::make('model')
                            ->label('Modele')
                            ->placeholder('Ex: Corolla, Classe E, 308...')
                            ->required()
                            ->maxLength(50),

                        Forms\Components\TextInput::make('year')
                            ->label('Annee')
                            ->numeric()
                            ->minValue(1990)
                            ->maxValue(date('Y') + 1)
                            ->placeholder(date('Y')),

                        Forms\Components\TextInput::make('license_plate')
                            ->label('Immatriculation')
                            ->placeholder('Ex: 00123-123-16')
                            ->maxLength(20),

                        Forms\Components\TextInput::make('color')
                            ->label('Couleur')
                            ->placeholder('Ex: Noir, Blanc, Gris...')
                            ->maxLength(30),

                        Forms\Components\Select::make('vehicle_type')
                            ->label('Type de vehicule')
                            ->options(ChauffeurVehicle::VEHICLE_TYPES)
                            ->default('berline')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Capacites')
                    ->icon('heroicon-o-users')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('seats')
                            ->label('Places passagers')
                            ->helperText('Hors chauffeur')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(50)
                            ->default(4)
                            ->required(),

                        Forms\Components\TextInput::make('luggage_capacity')
                            ->label('Valises')
                            ->helperText('Grandes valises')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(20)
                            ->default(2)
                            ->required(),

                        Forms\Components\TextInput::make('hand_luggage_capacity')
                            ->label('Bagages a main')
                            ->helperText('Sacs, petits bagages')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(20)
                            ->default(2)
                            ->required(),
                    ]),

                Forms\Components\Section::make('Equipements')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Toggle::make('has_air_conditioning')
                            ->label('Climatisation')
                            ->default(true),

                        Forms\Components\Toggle::make('has_wifi')
                            ->label('WiFi embarque')
                            ->default(false),

                        Forms\Components\Toggle::make('has_usb_charger')
                            ->label('Chargeur USB')
                            ->default(true),

                        Forms\Components\Toggle::make('has_child_seat')
                            ->label('Siege enfant disponible')
                            ->default(false),

                        Forms\Components\Toggle::make('has_wheelchair_access')
                            ->label('Acces fauteuil roulant')
                            ->default(false),

                        Forms\Components\Toggle::make('accepts_animals')
                            ->label('Animaux acceptes')
                            ->default(false),
                    ]),

                Forms\Components\Section::make('Photos')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\FileUpload::make('photos')
                            ->label('Photos du vehicule')
                            ->helperText('Ajoutez des photos de votre vehicule (interieur et exterieur)')
                            ->image()
                            ->multiple()
                            ->maxFiles(6)
                            ->maxSize(2048)
                            ->directory('chauffeur-vehicles')
                            ->reorderable()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Statut')
                    ->icon('heroicon-o-check-circle')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Vehicule actif')
                            ->helperText('Desactivez si le vehicule n\'est pas disponible temporairement')
                            ->default(true),

                        Forms\Components\Toggle::make('is_primary')
                            ->label('Vehicule principal')
                            ->helperText('Vehicule utilise par defaut pour les nouvelles courses')
                            ->default(false),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->placeholder('Informations supplementaires...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photos')
                    ->label('Photo')
                    ->circular()
                    ->stacked()
                    ->limit(1)
                    ->defaultImageUrl(url('/images/default-vehicle.png')),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Vehicule')
                    ->searchable(['brand', 'model'])
                    ->sortable(['brand'])
                    ->weight('bold')
                    ->description(fn ($record) => $record->license_plate),

                Tables\Columns\TextColumn::make('vehicle_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'berline' => 'primary',
                        'suv' => 'success',
                        'van' => 'warning',
                        'minibus' => 'info',
                        'luxury' => 'danger',
                        'moto' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ChauffeurVehicle::VEHICLE_TYPES[$state] ?? $state),

                Tables\Columns\TextColumn::make('seats')
                    ->label('Places')
                    ->alignCenter()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('luggage_capacity')
                    ->label('Valises')
                    ->alignCenter()
                    ->badge()
                    ->color('warning'),

                Tables\Columns\IconColumn::make('has_air_conditioning')
                    ->label('Clim')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('has_wifi')
                    ->label('WiFi')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_primary')
                    ->label('Principal')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ajoute le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vehicle_type')
                    ->label('Type')
                    ->options(ChauffeurVehicle::VEHICLE_TYPES),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->trueLabel('Actifs')
                    ->falseLabel('Inactifs'),
            ])
            ->actions([
                Tables\Actions\Action::make('set_primary')
                    ->label('Principal')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->visible(fn ($record) => !$record->is_primary && $record->is_active)
                    ->requiresConfirmation()
                    ->modalHeading('Definir comme vehicule principal')
                    ->modalDescription('Ce vehicule sera utilise par defaut pour les nouvelles courses.')
                    ->action(function ($record) {
                        $record->setAsPrimary();
                        Notification::make()
                            ->title('Vehicule principal defini')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('is_primary', 'desc')
            ->emptyStateHeading('Aucun vehicule')
            ->emptyStateDescription('Ajoutez votre premier vehicule pour commencer a recevoir des courses.')
            ->emptyStateIcon('heroicon-o-truck');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChauffeurVehicles::route('/'),
            'create' => Pages\CreateChauffeurVehicle::route('/create'),
            'edit' => Pages\EditChauffeurVehicle::route('/{record}/edit'),
        ];
    }
}
