<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Loueur;
use App\Models\Vehicle;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::where('is_active', true)
            ->where('status', 'available')
            ->orderBy('updated_at', 'desc')
            ->get();

        $loueurs = Loueur::where('is_active', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        $content = view('front.pages.sitemap', compact('vehicles', 'loueurs'))->render();

        return response($content, 200)
            ->header('Content-Type', 'text/xml; charset=utf-8');
    }

    public function robots()
    {
        $sitemapUrl = url('/sitemap.xml');

        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin\n";
        $content .= "Disallow: /admin/*\n";
        $content .= "Disallow: /loueur\n";
        $content .= "Disallow: /loueur/*\n";
        $content .= "Disallow: /livewire/*\n";
        $content .= "\n";
        $content .= "Sitemap: {$sitemapUrl}\n";

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}
