<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    /**
     * Show store settings page (single-record form + FAQ list).
     */
    public function index()
    {
        $store = Store::first();
        $faqs  = Faq::orderBy('order')->orderBy('id')->get();

        return view('store.settings', compact('store', 'faqs'));
    }

    /**
     * Update store settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string|max:1000',
            'about_us'          => 'nullable|string|max:5000',
            'logo'              => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'address'           => 'required|string|max:500',
            'phone'             => 'nullable|string|max:30',
            'email'             => 'nullable|email|max:255',
            'opening_time'      => 'nullable|date_format:H:i',
            'closing_time'      => 'nullable|date_format:H:i',
            'google_maps_url'   => 'nullable|url|max:1000',
            'google_maps_embed' => 'nullable|string|max:3000',
            'latitude'          => 'nullable|numeric|between:-90,90',
            'longitude'         => 'nullable|numeric|between:-180,180',
            'whatsapp_number'   => 'nullable|string|max:30',
            'instagram'         => 'nullable|string|max:500',
            'facebook'          => 'nullable|string|max:500',
            'tiktok'            => 'nullable|string|max:500',
            'twitter'           => 'nullable|string|max:500',
            'is_active'         => 'nullable|boolean',
        ]);

        $store = Store::first();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($store && $store->logo) {
                Storage::disk('public')->delete($store->logo);
            }
            $validated['logo'] = $request->file('logo')->store('stores', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        if ($store) {
            $store->update($validated);
        } else {
            $store = Store::create($validated);
        }

        return back()->with('success', 'Pengaturan toko berhasil disimpan.');
    }

    /**
     * Store a new FAQ.
     */
    public function storeFaq(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer'   => 'required|string|max:3000',
            'category' => 'nullable|string|max:100',
            'order'    => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order']     = $validated['order'] ?? 0;

        Faq::create($validated);

        return back()->with('success', 'FAQ berhasil ditambahkan.');
    }

    /**
     * Update an existing FAQ.
     */
    public function updateFaq(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question'  => 'required|string|max:500',
            'answer'    => 'required|string|max:3000',
            'category'  => 'nullable|string|max:100',
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['order']     = $validated['order'] ?? 0;

        $faq->update($validated);

        return back()->with('success', 'FAQ berhasil diperbarui.');
    }

    /**
     * Delete a FAQ.
     */
    public function destroyFaq(Faq $faq)
    {
        $faq->delete();

        return back()->with('success', 'FAQ berhasil dihapus.');
    }
}
