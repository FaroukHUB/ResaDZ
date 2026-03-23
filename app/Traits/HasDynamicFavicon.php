<?php

namespace App\Traits;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

trait HasDynamicFavicon
{
    protected function getFaviconUrl(): string
    {
        $favicon = Setting::get('favicon', '');
        if ($favicon) {
            return Storage::url($favicon);
        }

        $logoLight = Setting::get('logo_light', '');
        if ($logoLight) {
            return Storage::url($logoLight);
        }

        return asset('assets/favicon.png');
    }
}
