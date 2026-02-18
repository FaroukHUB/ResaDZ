{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {{-- Pages statiques --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('vehicles.index') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    {{-- Véhicules --}}
    @foreach($vehicles as $vehicle)
    <url>
        <loc>{{ route('vehicles.show', $vehicle->slug) }}</loc>
        <lastmod>{{ $vehicle->updated_at->toW3cString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    {{-- Loueurs --}}
    @foreach($loueurs as $loueur)
    <url>
        <loc>{{ route('loueur.show', $loueur->slug) }}</loc>
        <lastmod>{{ $loueur->updated_at->toW3cString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach
</urlset>
