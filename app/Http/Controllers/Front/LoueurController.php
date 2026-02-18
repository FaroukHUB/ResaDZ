<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Loueur;
use App\Models\Vehicle;

class LoueurController extends Controller
{
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

        return view('front.pages.loueur', compact('loueur', 'vehicles'));
    }
}
