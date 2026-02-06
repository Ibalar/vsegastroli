<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slide;

class SlideController extends Controller
{
    public function index(Request $request)
    {
        $cityId = $request->currentCity?->id ?? null;

        $slides = Slide::query()
            ->when($cityId, fn($q) => $q->where('city_id', $cityId))
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('home.slider', compact('slides'));
    }
}
