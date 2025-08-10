<?php

namespace App\Http\Controllers\Admin;

use App\Models\Shift;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::latest()->paginate(10);
        return view('admin.shifts.index', compact('shifts'));
    }

    public function store(Request $request)
    {
        // Validasi dengan error bag bernama 'store'
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ], [], [], 'store');

        Shift::create($request->all());
        return redirect()->route('admin.shifts.index')->with('success', 'Shift berhasil ditambahkan.');
    }

    public function update(Request $request, Shift $shift)
    {
        // Validasi dengan error bag bernama 'update'
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ], [], [], 'update');

        $shift->update($request->all());
        return redirect()->route('admin.shifts.index')->with('success', 'Shift berhasil diperbarui.');
    }

    public function destroy(Shift $shift)
    {
        try {
            $shift->delete();
            return redirect()->route('admin.shifts.index')->with('success', 'Shift berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Gagal menghapus shift. Masih ada karyawan yang terhubung.');
        }
    }
}