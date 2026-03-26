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

        // Blog index page
        $urls[] = [
            'loc' => route('blog.index'),
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ];

        // Individual vehicle pages
        $vehicles = Vehicle::where('is_active', true)
            ->where('status', 'available')
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

        // Blog posts
        $posts = BlogPost::published()
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

        // Loueur pages
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

        // Static pages
        $staticPages = [
            ['loc' => route('comment-ca-marche'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => url('/guide'), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => route('legal.mentions-legales'), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => route('legal.cgu'), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => route('legal.confidentialite'), 'changefreq' => 'yearly', 'priority' => '0.3'],
        ];
        $urls = array_merge($urls, $staticPages);

        // SEO landing pages
        $seoPages = [
            ['loc' => url('/location-voiture-alger'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => url('/location-voiture-aeroport-alger'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => url('/location-voiture-oran'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => url('/location-voiture-constantine'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => url('/location-voiture-annaba'), 'changefreq' => 'weekly', 'priority' => '0.9'],
        ];
        $urls = array_merge($urls, $seoPages);

        // Vehicles by wilaya pages
        $wilayas = config('resadz.wilayas', []);

        foreach ($wilayas as $code => $name) {
            $wilayaSlug = Str::slug($name);
            $urls[] = [
                'loc' => route('vehicles.by-wilaya', $wilayaSlug),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
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
        $content .= "Disallow: /admin\n";
        $content .= "Disallow: /admin/*\n";
        $content .= "Disallow: /loueur\n";
        $content .= "Disallow: /loueur/*\n";
        $content .= "Disallow: /chauffeur\n";
        $content .= "Disallow: /chauffeur/*\n";
        $content .= "Disallow: /livewire/*\n";
        $content .= "Disallow: /espace-client/*\n";
        $content .= "Disallow: /ma-reservation/*\n";
        $content .= "\n";
        $content .= "Sitemap: {$sitemapUrl}\n";

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}
