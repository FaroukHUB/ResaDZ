<?php

namespace App\Filament\Admin\Pages;

use App\Models\Prospect;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ProspectKanban extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';

    protected static ?string $navigationGroup = 'Gestion';

    protected static ?string $navigationLabel = 'Prospects (Kanban)';

    protected static ?string $title = 'CRM Prospects';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.admin.pages.prospect-kanban';

    public static function getNavigationBadge(): ?string
    {
        return Prospect::parRelance()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public function getViewData(): array
    {
        $columns = [
            'non_contacte' => ['label' => 'Non contacté', 'color' => '#6B7280', 'bg' => 'bg-gray-50'],
            'contacte' => ['label' => 'Contacté', 'color' => '#3B82F6', 'bg' => 'bg-blue-50'],
            'interesse' => ['label' => 'Intéressé', 'color' => '#F59E0B', 'bg' => 'bg-amber-50'],
            'inscrit' => ['label' => 'Inscrit', 'color' => '#10B981', 'bg' => 'bg-green-50'],
            'pas_interesse' => ['label' => 'Pas intéressé', 'color' => '#EF4444', 'bg' => 'bg-red-50'],
        ];

        $prospects = Prospect::orderBy('updated_at', 'desc')->get()->groupBy('statut');

        $stats = [
            'total' => Prospect::count(),
            'relances' => Prospect::parRelance()->count(),
            'today' => Prospect::where('date_dernier_contact', now()->toDateString())->count(),
        ];

        return compact('columns', 'prospects', 'stats');
    }

    public function moveProspect(int $prospectId, string $newStatus): void
    {
        $prospect = Prospect::findOrFail($prospectId);
        $updates = ['statut' => $newStatus];

        if (in_array($newStatus, ['contacte', 'interesse', 'inscrit'])) {
            $updates['date_dernier_contact'] = now()->toDateString();
        }

        $prospect->update($updates);

        Notification::make()
            ->title('Prospect déplacé → ' . Prospect::STATUTS[$newStatus])
            ->success()
            ->send();
    }

    public function deleteProspect(int $prospectId): void
    {
        Prospect::findOrFail($prospectId)->delete();

        Notification::make()
            ->title('Prospect supprimé')
            ->success()
            ->send();
    }
}
