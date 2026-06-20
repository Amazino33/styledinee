<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Gallery;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function home()
    {
        $galleryItems = Gallery::where('is_active', true)->inRandomOrder()->get();
        return view('pages.home', compact('galleryItems'));
    }

    public function services()
    {
        $services = Service::where('is_active', true)->orderBy('sort_order')->get();
        $galleryItems = Gallery::where('is_active', true)->inRandomOrder()->get();
        return view('pages.services', compact('services', 'galleryItems'));
    }

    public function shop()
    {
        $products = Product::where('is_active', true)->where('is_published', true)->orderBy('sort_order')->get();
        return view('pages.shop', compact('products'));
    }

    public function gallery()
    {
        $items = Gallery::where('is_active', true)->orderBy('sort_order')->get();
        return view('pages.gallery', compact('items'));
    }

    public function contact()
    {
        $galleryItems = Gallery::where('is_active', true)->inRandomOrder()->get();
        return view('pages.contact', compact('galleryItems'));
    }

    public function submitEnquiry(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10'],
        ]);

        Enquiry::create($validated);

        return back()->with('success', 'Thank you, ' . $validated['name'] . '. We have received your enquiry and will be in touch within 24 hours.');
    }
}
