<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuses = Status::orderBy('name')->paginate(10);
        return view('admin.statuses.index', compact('statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:statuses,name',
            'description' => 'nullable|string',
        ]);

        Status::create($request->all());
        return redirect()->route('admin.statuses.index')->with('success', 'Status berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Status $status)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:statuses,name,' . $status->id,
            'description' => 'nullable|string',
        ]);

        $status->update($request->all());
        return redirect()->route('admin.statuses.index')->with('success', 'Status berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Status $status)
    {
        try {
            // Cek apakah status masih digunakan di attendance
            if ($status->attendances()->count() > 0) {
                return back()->with('error', 'Gagal menghapus status. Masih ada data absensi yang menggunakan status ini.');
            }
            
            $status->delete();
            return redirect()->route('admin.statuses.index')->with('success', 'Status berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus status. Masih ada data yang terkait.');
        }
    }
}
