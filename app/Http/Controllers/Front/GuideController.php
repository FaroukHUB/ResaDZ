<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;

class GuideController extends Controller
{
    public function index()
    {
        return view('front.pages.guide');
    }
}
