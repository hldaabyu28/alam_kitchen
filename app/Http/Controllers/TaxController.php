<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaxController extends Controller
{
    public function index()
    {
        $taxes = Tax::orderBy('created_at', 'desc')->get();
        return view('tax.index', compact('taxes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;
        
        Tax::create($validated);

        $role = Auth::user()->hasRole('super_admin') ? 'super_admin' : 'admin';
        return redirect()->route($role . '.tax.index')->with('success', 'Pajak berhasil ditambahkan.');
    }

    public function update(Request $request, Tax $tax)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $tax->update($validated);

        $role = Auth::user()->hasRole('super_admin') ? 'super_admin' : 'admin';
        return redirect()->route($role . '.tax.index')->with('success', 'Pajak berhasil diperbarui.');
    }

    public function destroy(Tax $tax)
    {
        $tax->delete();

        $role = auth()->user()->hasRole('super_admin') ? 'super_admin' : 'admin';
        return redirect()->route($role . '.tax.index')->with('success', 'Pajak berhasil dihapus.');
    }
}
