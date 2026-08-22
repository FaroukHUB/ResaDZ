<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Loueur;
use App\Models\Vehicle;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [];

        // Homepage - highest priority
        $urls[] = [
            'loc' => url('/'),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        // Vehicles list page
        $urls[] = [
            'loc' => route('vehicles.index'),
            'changefreq' => 'daily',
            'priority' => '0.8',
        ];

        // Loueurs list page
        $urls[] = [
            'loc' => route('loueurs.index'),
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ];

        // Blog index page
        $urls[] = [
            'loc' => route('blog.index'),
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ];

        // Individual vehicle pages
        $vehicles = Vehicle::where('is_active', true)
            ->whereIn('status', ['available', 'reserved'])
            ->orderBy('updated_at', 'desc')
            ->get(['slug', 'updated_at']);

        foreach ($vehicles as $vehicle) {
            $urls[] = [
                'loc' => route('vehicles.show', $vehicle->slug),
                'lastmod' => $vehicle->updated_at->toW3cString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }

        // Slugs consolides vers une page metier (redirection 301) : jamais dans le sitemap
        $redirectedSlugs = [
            'location-voiture-aeroport-alger-guide-complet-2024',
            'location-voiture-aeroport-alger-guide-complet-2026',
        ];

        // Blog posts
        $posts = BlogPost::published()
            ->whereNotIn('slug', $redirectedSlugs)
            ->orderBy('published_at', 'desc')
            ->get(['slug', 'published_at', 'updated_at']);

        foreach ($posts as $post) {
            $urls[] = [
                'loc' => route('blog.show', $post->slug),
                'lastmod' => $post->updated_at->toW3cString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        }

        // Loueur profile pages
        $loueurs = Loueur::where('is_active', true)
            ->where('is_suspended', false)
            ->orderBy('updated_at', 'desc')
            ->get(['slug', 'updated_at']);

        foreach ($loueurs as $loueur) {
            $urls[] = [
                'loc' => route('loueur.show', $loueur->slug),
                'lastmod' => $loueur->updated_at->toW3cString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }

        // Static pages (only pages that exist and work)
        $staticPages = [
            ['loc' => route('comment-ca-marche'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('legal.mentions-legales'), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => route('legal.cgu'), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => route('legal.confidentialite'), 'changefreq' => 'yearly', 'priority' => '0.3'],
        ];
        $urls = array_merge($urls, $staticPages);

        // SEO landing pages (dedicated content)
        $seoPages = [
            ['loc' => url('/location-voiture-alger'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => url('/location-voiture-aeroport-alger'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => url('/location-voiture-aeroport-oran'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => url('/location-voiture-aeroport-constantine'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => url('/location-voiture-aeroport-bejaia'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => url('/location-voiture-aeroport-annaba'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => url('/location-voiture-oran'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => url('/location-voiture-constantine'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => url('/location-voiture-annaba'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => url('/location-voiture-blida'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => url('/location-voiture-boumerdes'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => url('/location-voiture-setif'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => url('/location-voiture-sidi-bel-abbes'), 'changefreq' => 'weekly', 'priority' => '0.9'],
        ];
        $urls = array_merge($urls, $seoPages);

        // Dynamic wilaya pages — only for wilayas that have active loueurs
        $wilayas = config('resadz.wilayas', []);
        $dedicatedSlugs = ['alger', 'aeroport-alger', 'oran', 'constantine', 'annaba', 'blida', 'boumerdes', 'setif', 'sidi-bel-abbes'];

        foreach ($wilayas as $code => $name) {
            $wilayaSlug = Str::slug($name);
            // Skip wilayas that already have dedicated pages
            if (in_array($wilayaSlug, $dedicatedSlugs)) continue;

            // Only include wilayas that have active loueurs
            $hasLoueurs = Loueur::where('is_active', true)
                ->where('is_suspended', false)
                ->where(function ($q) use ($name, $wilayaSlug) {
                    $q->whereRaw('LOWER(wilaya) = ?', [strtolower($name)])
                      ->orWhereRaw('LOWER(wilaya) = ?', [strtolower($wilayaSlug)])
                      ->orWhere('disponible_national', true);
                })
                ->exists();

            if ($hasLoueurs) {
                $urls[] = [
                    'loc' => route('vehicles.by-wilaya', $wilayaSlug),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            }
        }

        // Build XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= '    <url>' . "\n";
            $xml .= '        <loc>' . htmlspecialchars($url['loc'], ENT_XML1, 'UTF-8') . '</loc>' . "\n";

            if (isset($url['lastmod'])) {
                $xml .= '        <lastmod>' . $url['lastmod'] . '</lastmod>' . "\n";
            }

            $xml .= '        <changefreq>' . $url['changefreq'] . '</changefreq>' . "\n";
            $xml .= '        <priority>' . $url['priority'] . '</priority>' . "\n";
            $xml .= '    </url>' . "\n";
        }

        $xml .= '</urlset>' . "\n";

        return response($xml, 200)
            ->header('Content-Type', 'text/xml; charset=utf-8');
    }

    public function robots()
    {
        $sitemapUrl = url('/sitemap.xml');

        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Allow: /loueur/*\n";
        $content .= "\n";
        $content .= "Disallow: /admin\n";
        $content .= "Disallow: /admin/*\n";
        $content .= "Disallow: /chauffeur\n";
        $content .= "Disallow: /chauffeur/*\n";
        $content .= "Disallow: /livewire/*\n";
        $content .= "Disallow: /espace-client/*\n";
        $content .= "Disallow: /ma-reservation/*\n";
        $content .= "Disallow: /reserver/*\n";
        $content .= "Disallow: /paiement/*\n";
        $content .= "Disallow: /stripe/*\n";
        $content .= "Disallow: /connexion\n";
        $content .= "Disallow: /inscription\n";
        $content .= "Disallow: /deconnexion\n";
        $content .= "Disallow: /mot-de-passe/*\n";
        $content .= "Disallow: /*?*\n";
        $content .= "\n";
        $content .= "Sitemap: {$sitemapUrl}\n";

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}
