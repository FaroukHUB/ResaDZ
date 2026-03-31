<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Loueur;
use App\Models\Vehicle;

class LoueurController extends Controller
{
    public function index()
    {
        // Priorite aux partenaires en vedette
        $featuredLoueurs = Loueur::where('is_active', true)
            ->where('is_featured_partner', true)
            ->withCount('vehicles')
            ->orderBy('partner_order')
            ->get();

        // Puis les autres loueurs tries par note
        $otherLoueurs = Loueur::where('is_active', true)
            ->where(function ($query) {
                $query->where('is_featured_partner', false)
                    ->orWhereNull('is_featured_partner');
            })
            ->withCount('vehicles')
            ->orderBy('rating', 'desc')
            ->get();

        // Combiner les deux listes
        $loueurs = $featuredLoueurs->concat($otherLoueurs);

        return view('front.pages.loueurs', compact('loueurs', 'featuredLoueurs'));
    }

    public function show(string $slug)
    {
        $loueur = Loueur::with('settings')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $vehicles = Vehicle::with(['brand', 'category', 'loueur.settings'])
            ->where('loueur_id', $loueur->id)
            ->where('is_active', true)
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $activeOffers = \App\Models\VehicleOffer::where('loueur_id', $loueur->id)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with('vehicle')
            ->get();

        $conditions = $loueur->getConditions();
        $autoBadges = $loueur->getAutoBadges();

        return view('front.pages.loueur', compact('loueur', 'vehicles', 'activeOffers', 'conditions', 'autoBadges'));
    }
}
