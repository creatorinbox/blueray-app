<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::orderBy('name')->get();
        return view('units.index', compact('units'));
    }

    public function create()
    {
        return view('units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:units,name',
            'symbol' => 'nullable|string|max:10',
        ]);

        Unit::create($validated);

        return redirect()->route('units.index')->with('success', 'Unit created');
    }

    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:units,name,'.$unit->id,
            'symbol' => 'nullable|string|max:10',
        ]);
        $unit->update($validated);
        return redirect()->route('units.index')->with('success', 'Unit updated');
    }

    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Unit deleted');
    }
}
