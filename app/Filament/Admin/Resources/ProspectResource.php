<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProspectResource\Pages;
use App\Models\Loueur;
use App\Models\Prospect;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProspectResource extends Resource
{
    protected static ?string $model = Prospect::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationGroup = 'Gestion';

    protected static ?string $navigationLabel = 'Prospects';

    protected static ?string $modelLabel = 'Prospect';

    protected static ?string $pluralModelLabel = 'Prospects';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::parRelance()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Relances du jour';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations du prospect')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('nom')
                                ->label('Nom')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('telephone')
                                ->label('Téléphone')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(20)
                                ->tel()
                                ->placeholder('0555123456'),
                        ]),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->maxLength(255),
                            Forms\Components\Select::make('wilaya')
                                ->label('Wilaya')
                                ->options(config('resadz.wilayas'))
                                ->searchable(),
                        ]),
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('nb_vehicules')
                                ->label('Nb véhicules estimé')
                                ->numeric()
                                ->minValue(0),
                            Forms\Components\Select::make('source')
                                ->label('Source')
                                ->options(Prospect::SOURCES)
                                ->default('autre')
                                ->required(),
                            Forms\Components\Select::make('statut')
                                ->label('Statut')
                                ->options(Prospect::STATUTS)
                                ->default('non_contacte')
                                ->required(),
                        ]),
                    ]),
                Forms\Components\Section::make('Suivi')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\DatePicker::make('date_dernier_contact')
                                ->label('Date dernier contact'),
                            Forms\Components\DatePicker::make('relance_le')
                                ->label('Relance le'),
                        ]),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('telephone')
                    ->label('Téléphone')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Numéro copié')
                    ->icon('heroicon-o-phone'),
                Tables\Columns\TextColumn::make('wilaya')
                    ->label('Wilaya')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('nb_vehicules')
                    ->label('Véhicules')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('source')
                    ->label('Source')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'facebook' => 'info',
                        'google' => 'danger',
                        'telegram' => 'info',
                        'terrain' => 'warning',
                        'whatsapp' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Prospect::SOURCES[$state] ?? $state),
                Tables\Columns\TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'non_contacte' => 'gray',
                        'contacte' => 'info',
                        'interesse' => 'warning',
                        'inscrit' => 'success',
                        'pas_interesse' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Prospect::STATUTS[$state] ?? $state),
                Tables\Columns\TextColumn::make('date_dernier_contact')
                    ->label('Dernier contact')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('relance_le')
                    ->label('Relance')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->relance_le && $record->relance_le->isPast() ? 'danger' : null)
                    ->weight(fn ($record) => $record->relance_le && $record->relance_le->isPast() ? 'bold' : null)
                    ->description(fn ($record) => $record->relance_le && $record->relance_le->isPast() ? 'En retard !' : null),
                Tables\Columns\TextColumn::make('loueur.company_name')
                    ->label('Loueur lié')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('statut')
                    ->label('Statut')
                    ->options(Prospect::STATUTS),
                Tables\Filters\SelectFilter::make('source')
                    ->label('Source')
                    ->options(Prospect::SOURCES),
                Tables\Filters\SelectFilter::make('wilaya')
                    ->label('Wilaya')
                    ->options(config('resadz.wilayas'))
                    ->searchable(),
                Tables\Filters\Filter::make('relances_du_jour')
                    ->label('Relances du jour')
                    ->query(fn (Builder $query) => $query->parRelance()),
            ])
            ->actions([
                Tables\Actions\Action::make('marquerContacte')
                    ->label('Contacté')
                    ->icon('heroicon-o-phone-arrow-up-right')
                    ->color('info')
                    ->requiresConfirmation(false)
                    ->action(function (Prospect $record) {
                        $record->update([
                            'statut' => 'contacte',
                            'date_dernier_contact' => now()->toDateString(),
                        ]);
                        Notification::make()->title('Marqué comme contacté')->success()->send();
                    })
                    ->visible(fn (Prospect $record) => $record->statut !== 'inscrit'),
                Tables\Actions\Action::make('marquerInteresse')
                    ->label('Intéressé')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->action(function (Prospect $record) {
                        $record->update(['statut' => 'interesse']);
                        Notification::make()->title('Marqué comme intéressé')->success()->send();
                    })
                    ->visible(fn (Prospect $record) => !in_array($record->statut, ['inscrit', 'interesse'])),
                Tables\Actions\Action::make('programmerRelance')
                    ->label('Relance')
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->form([
                        Forms\Components\DatePicker::make('relance_le')
                            ->label('Date de relance')
                            ->required()
                            ->minDate(now()),
                    ])
                    ->action(function (Prospect $record, array $data) {
                        $record->update(['relance_le' => $data['relance_le']]);
                        Notification::make()->title('Relance programmée')->success()->send();
                    }),
                Tables\Actions\Action::make('lierLoueur')
                    ->label('Lier loueur')
                    ->icon('heroicon-o-link')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('loueur_id')
                            ->label('Loueur')
                            ->options(Loueur::where('is_active', true)->pluck('company_name', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (Prospect $record, array $data) {
                        $record->update([
                            'loueur_id' => $data['loueur_id'],
                            'statut' => 'inscrit',
                            'date_dernier_contact' => now()->toDateString(),
                        ]);
                        Notification::make()->title('Loueur lié — prospect converti')->success()->send();
                    })
                    ->visible(fn (Prospect $record) => $record->statut !== 'inscrit'),
                Tables\Actions\Action::make('copierWhatsApp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left')
                    ->color('success')
                    ->url(fn (Prospect $record) => 'https://wa.me/213' . ltrim($record->telephone, '0'))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('marquerContactes')
                        ->label('Marquer contactés')
                        ->icon('heroicon-o-phone-arrow-up-right')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(fn ($r) => $r->update([
                                'statut' => 'contacte',
                                'date_dernier_contact' => now()->toDateString(),
                            ]));
                            Notification::make()->title($records->count() . ' prospect(s) marqué(s) contactés')->success()->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('changerStatut')
                        ->label('Changer statut')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('statut')
                                ->label('Nouveau statut')
                                ->options(Prospect::STATUTS)
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each(fn ($r) => $r->update(['statut' => $data['statut']]));
                            Notification::make()->title($records->count() . ' prospect(s) mis à jour')->success()->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('importCsv')
                    ->label('Importer CSV')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('gray')
                    ->form([
                        Forms\Components\FileUpload::make('csv_file')
                            ->label('Fichier CSV')
                            ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                            ->required()
                            ->disk('local')
                            ->directory('temp-imports'),
                    ])
                    ->action(function (array $data) {
                        $path = storage_path('app/' . $data['csv_file']);
                        if (!file_exists($path)) {
                            Notification::make()->title('Fichier introuvable')->danger()->send();
                            return;
                        }

                        $handle = fopen($path, 'r');
                        $header = fgetcsv($handle, 0, ',');
                        if (!$header) {
                            fclose($handle);
                            Notification::make()->title('Fichier CSV vide')->danger()->send();
                            return;
                        }

                        // Normaliser les headers
                        $header = array_map(fn ($h) => strtolower(trim($h)), $header);
                        $imported = 0;
                        $skipped = 0;

                        while (($row = fgetcsv($handle, 0, ',')) !== false) {
                            $data = array_combine($header, $row);
                            $telephone = trim($data['telephone'] ?? '');

                            if (empty($telephone)) {
                                $skipped++;
                                continue;
                            }

                            // Skip doublons
                            if (Prospect::where('telephone', $telephone)->exists()) {
                                $skipped++;
                                continue;
                            }

                            Prospect::create([
                                'nom' => trim($data['nom'] ?? 'Inconnu'),
                                'telephone' => $telephone,
                                'email' => trim($data['email'] ?? '') ?: null,
                                'wilaya' => trim($data['wilaya'] ?? '') ?: null,
                                'nb_vehicules' => isset($data['nb_vehicules']) && is_numeric($data['nb_vehicules']) ? (int)$data['nb_vehicules'] : null,
                                'source' => in_array($data['source'] ?? '', array_keys(Prospect::SOURCES)) ? $data['source'] : 'autre',
                                'notes' => trim($data['notes'] ?? '') ?: null,
                            ]);
                            $imported++;
                        }

                        fclose($handle);
                        @unlink($path);

                        Notification::make()
                            ->title("Import terminé : {$imported} importé(s), {$skipped} ignoré(s)")
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProspects::route('/'),
            'create' => Pages\CreateProspect::route('/create'),
            'edit' => Pages\EditProspect::route('/{record}/edit'),
        ];
    }
}
