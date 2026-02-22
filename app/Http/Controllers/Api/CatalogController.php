<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Option;
use App\Models\TimeSlot;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class CatalogController extends Controller
{
    /**
     * Liste des marques
     */
    public function brands(): JsonResponse
    {
        $brands = Brand::withCount(['vehicles' => function ($query) {
                $query->active()->where('status', 'available');
            }])
            ->active()
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $brands->map(fn ($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'slug' => $b->slug,
                'logo' => $b->logo ? asset($b->logo) : null,
                'vehicles_count' => $b->vehicles_count,
            ]),
        ]);
    }

    /**
     * Liste des catégories
     */
    public function categories(): JsonResponse
    {
        $categories = Category::withCount(['vehicles' => function ($query) {
                $query->active()->where('status', 'available');
            }])
            ->active()
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
                'description' => $c->description,
                'image' => $c->image ? asset($c->image) : null,
                'vehicles_count' => $c->vehicles_count,
            ]),
        ]);
    }

    /**
     * Liste des options disponibles
     */
    public function options(): JsonResponse
    {
        $options = Option::active()->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $options->map(fn ($o) => [
                'id' => $o->id,
                'name' => $o->name,
                'slug' => $o->slug,
                'description' => $o->description,
                'price' => $o->price,
                'price_formatted' => $o->formatted_price,
                'price_type' => $o->price_type,
                'icon' => $o->icon,
            ]),
        ]);
    }

    /**
     * Liste des créneaux horaires
     */
    public function timeSlots(): JsonResponse
    {
        $slots = TimeSlot::active()->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $slots->map(fn ($s) => [
                'id' => $s->id,
                'start_time' => substr($s->start_time, 0, 5),
                'end_time' => substr($s->end_time, 0, 5),
                'label' => $s->label,
            ]),
        ]);
    }

    /**
     * Paramètres publics du site
     */
    public function settings(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'site' => [
                    'name' => Setting::get('company_name', 'ResaDZ'),
                    'description' => Setting::get('company_tagline', 'Marketplace de location de voitures'),
                    'currency' => Setting::get('currency', 'DZD'),
                ],
                'contact' => [
                    'email' => Setting::get('company_email', ''),
                    'phone' => Setting::get('company_phone', ''),
                    'whatsapp' => Setting::get('whatsapp'),
                    'address' => Setting::get('company_address', ''),
                ],
                'booking' => [
                    'min_rental_days' => (int) Setting::get('min_rental_days', 1),
                    'advance_booking_days' => (int) Setting::get('advance_booking_days', 1),
                ],
                'social' => [
                    'facebook' => Setting::get('facebook'),
                    'instagram' => Setting::get('instagram'),
                    'tiktok' => Setting::get('tiktok'),
                ],
            ],
        ]);
    }
}
