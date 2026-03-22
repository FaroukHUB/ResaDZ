<?php

namespace App\Filament\Loueur\Pages;

use App\Models\Review;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Reviews extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static string $view = 'filament.loueur.pages.reviews';

    protected static ?string $navigationLabel = 'Mes avis';

    protected static ?string $title = 'Mes avis clients';

    protected static ?int $navigationSort = 12;

    public ?int $respondingToId = null;
    public string $responseText = '';

    public static function getNavigationBadge(): ?string
    {
        $loueur = Auth::user()?->loueur;
        if (!$loueur) return null;

        $count = Review::where('loueur_id', $loueur->id)
            ->where('type', 'client_to_loueur')
            ->whereNull('response')
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    protected function getLoueur()
    {
        return Auth::user()?->loueur;
    }

    public function getReviews()
    {
        $loueur = $this->getLoueur();
        if (!$loueur) {
            return collect();
        }

        return Review::where('loueur_id', $loueur->id)
            ->where('type', 'client_to_loueur')
            ->where('is_public', true)
            ->with(['reviewer', 'booking.vehicle'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getStats(): array
    {
        $loueur = $this->getLoueur();
        if (!$loueur) {
            return [
                'total' => 0,
                'average' => 0,
                'distribution' => [],
                'without_response' => 0,
            ];
        }

        $reviews = Review::where('loueur_id', $loueur->id)
            ->where('type', 'client_to_loueur')
            ->where('is_public', true)
            ->where('is_approved', true);

        $total = $reviews->count();
        $avgRating = $reviews->avg('rating_overall') ?? 0;

        $distribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = Review::where('loueur_id', $loueur->id)
                ->where('type', 'client_to_loueur')
                ->where('is_public', true)
                ->where('is_approved', true)
                ->where('rating_overall', $i)
                ->count();
            $distribution[$i] = [
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100) : 0,
            ];
        }

        $withoutResponse = Review::where('loueur_id', $loueur->id)
            ->where('type', 'client_to_loueur')
            ->whereNull('response')
            ->count();

        return [
            'total' => $total,
            'average' => round($avgRating, 1),
            'distribution' => $distribution,
            'without_response' => $withoutResponse,
        ];
    }

    public function startResponding(int $reviewId): void
    {
        $this->respondingToId = $reviewId;
        $this->responseText = '';
    }

    public function cancelResponse(): void
    {
        $this->respondingToId = null;
        $this->responseText = '';
    }

    public function submitResponse(): void
    {
        if (empty(trim($this->responseText))) {
            Notification::make()
                ->title('Veuillez écrire une réponse')
                ->danger()
                ->send();
            return;
        }

        $review = Review::where('id', $this->respondingToId)
            ->where('loueur_id', $this->getLoueur()?->id)
            ->first();

        if (!$review) {
            Notification::make()
                ->title('Avis non trouvé')
                ->danger()
                ->send();
            return;
        }

        $review->update([
            'response' => $this->responseText,
            'responded_at' => now(),
        ]);

        $this->respondingToId = null;
        $this->responseText = '';

        Notification::make()
            ->title('Réponse publiée')
            ->body('Votre réponse est maintenant visible par tous les clients.')
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        return [
            'reviews' => $this->getReviews(),
            'stats' => $this->getStats(),
        ];
    }
}
