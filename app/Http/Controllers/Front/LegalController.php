<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class LegalController extends Controller
{
    /**
     * Mentions légales page.
     */
    public function mentionsLegales()
    {
        $companyName = Setting::get('company_name', 'ResaDZ');
        $companyAddress = Setting::get('company_address', '');
        $companyEmail = Setting::get('company_email', 'contact@resadz.com');
        $companyPhone = Setting::get('company_phone', '');

        return view('front.pages.legal.mentions-legales', compact(
            'companyName',
            'companyAddress',
            'companyEmail',
            'companyPhone'
        ));
    }

    /**
     * Conditions Générales d'Utilisation page.
     */
    public function cgu()
    {
        $companyName = Setting::get('company_name', 'ResaDZ');

        return view('front.pages.legal.cgu', compact('companyName'));
    }

    /**
     * Politique de confidentialité page.
     */
    public function confidentialite()
    {
        $companyName = Setting::get('company_name', 'ResaDZ');
        $companyEmail = Setting::get('company_email', 'contact@resadz.com');

        return view('front.pages.legal.confidentialite', compact('companyName', 'companyEmail'));
    }
}
