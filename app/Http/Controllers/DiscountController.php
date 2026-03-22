<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $discounts = Discount::orderBy('valid_until', 'desc')->get();

        return view('discount.index', compact('discounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'type'                => 'required|in:percentage,fixed',
            'code'                => 'required|string|max:50|unique:discounts,code',
            'description'         => 'nullable|string|max:1000',
            'percentage'          => 'required_if:type,percentage|nullable|numeric|min:0|max:100',
            'amount'              => 'required_if:type,fixed|nullable|numeric|min:0',
            'valid_from'          => 'required|date',
            'valid_until'         => 'required|date|after:valid_from',
            'is_active'           => 'nullable|boolean',
            'min_order_amount'    => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit'         => 'nullable|integer|min:1',
            'usage_per_user'      => 'nullable|integer|min:1',
            'banner_image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_banner'           => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_banner'] = $request->has('is_banner');
        $validated['percentage'] = $validated['percentage'] ?? 0;
        $validated['amount'] = $validated['amount'] ?? 0;
        $validated['usage_per_user'] = $validated['usage_per_user'] ?? 1;

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('discounts', 'public');
        }

        Discount::create($validated);

        return redirect()->back()->with('success', 'Diskon berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'type'                => 'required|in:percentage,fixed',
            'code'                => 'required|string|max:50|unique:discounts,code,' . $discount->id,
            'description'         => 'nullable|string|max:1000',
            'percentage'          => 'required_if:type,percentage|nullable|numeric|min:0|max:100',
            'amount'              => 'required_if:type,fixed|nullable|numeric|min:0',
            'valid_from'          => 'required|date',
            'valid_until'         => 'required|date|after:valid_from',
            'is_active'           => 'nullable|boolean',
            'min_order_amount'    => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit'         => 'nullable|integer|min:1',
            'usage_per_user'      => 'nullable|integer|min:1',
            'banner_image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_banner'           => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_banner'] = $request->has('is_banner');
        $validated['percentage'] = $validated['percentage'] ?? 0;
        $validated['amount'] = $validated['amount'] ?? 0;
        $validated['usage_per_user'] = $validated['usage_per_user'] ?? 1;

        if ($request->hasFile('banner_image')) {
            if ($discount->banner_image) {
                Storage::disk('public')->delete($discount->banner_image);
            }
            $validated['banner_image'] = $request->file('banner_image')->store('discounts', 'public');
        }

        $discount->update($validated);

        return redirect()->back()->with('success', 'Diskon berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Discount $discount)
    {
        if ($discount->banner_image) {
            Storage::disk('public')->delete($discount->banner_image);
        }

        $discount->delete();

        return redirect()->back()->with('success', 'Diskon berhasil dihapus!');
    }
}
