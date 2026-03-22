<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    /**
     * Display a listing of tables.
     */
    public function index()
    {
        $tables = Table::orderBy('table_number')->get();
        return view('table.index', compact('tables'));
    }

    /**
     * Store a newly created table.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_number' => 'required|string|max:20|unique:tables,table_number',
            'capacity'     => 'required|integer|min:1|max:50',
            'location'     => 'nullable|string|max:255',
            'description'  => 'nullable|string|max:500',
            'is_active'    => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['status'] = 'available';

        Table::create($validated);

        return redirect()->back()->with('success', "Meja #{$validated['table_number']} berhasil ditambahkan!");
    }

    /**
     * Update the specified table.
     */
    public function update(Request $request, Table $table)
    {
        $validated = $request->validate([
            'table_number' => 'required|string|max:20|unique:tables,table_number,' . $table->id,
            'capacity'     => 'required|integer|min:1|max:50',
            'location'     => 'nullable|string|max:255',
            'description'  => 'nullable|string|max:500',
            'is_active'    => 'nullable|boolean',
            'status'       => 'nullable|in:available,occupied,reserved',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $table->update($validated);

        return redirect()->back()->with('success', "Meja #{$table->table_number} berhasil diperbarui!");
    }

    /**
     * Remove the specified table (soft delete).
     */
    public function destroy(Table $table)
    {
        $tableNumber = $table->table_number;
        $table->delete();

        return redirect()->back()->with('success', "Meja #{$tableNumber} berhasil dihapus!");
    }
}
