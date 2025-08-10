<?php

namespace App\Http\Controllers\Admin;

use App\Models\Division;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::withCount('users')->latest()->paginate(10);
        return view('admin.divisions.index', compact('divisions'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:divisions,name']);
        Division::create($request->all());
        return redirect()->route('admin.divisions.index')->with('success', 'Divisi berhasil ditambahkan.');
    }

    public function update(Request $request, Division $division)
    {
        $request->validate(['name' => 'required|string|max:255|unique:divisions,name,' . $division->id]);
        $division->update($request->all());
        return redirect()->route('admin.divisions.index')->with('success', 'Divisi berhasil diperbarui.');
    }

    public function destroy(Division $division)
    {
        try {
            $division->delete();
            return redirect()->route('admin.divisions.index')->with('success', 'Divisi berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Gagal menghapus divisi. Masih ada karyawan yang terhubung.');
        }
    }
}
