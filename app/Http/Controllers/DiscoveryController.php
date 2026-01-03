<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DiscoveryController extends Controller
{
    public function index()
    {
        // 1. Ambil Kategori
        $categories = \App\Models\Category::withCount('fotos')->get();

        // 2. Ambil Foto Acak (Random) untuk inspirasi
        $randomPhotos = \App\Models\Foto::withCompleteDetails()
                            ->where('status', 'approved')
                            ->inRandomOrder()
                            ->take(15)
                            ->get();

        return view('layouts.discovery', [
            'categories' => $categories,
            'randomPhotos' => $randomPhotos
        ]);
    }
}
