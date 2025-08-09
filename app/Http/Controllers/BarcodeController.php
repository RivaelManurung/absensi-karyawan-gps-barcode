<?php

namespace App\Http\Controllers;

use App\Models\Barcode;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    public function index()
    {
        $barcodes = Barcode::latest()->paginate(10);
        return view('admin.barcodes.index', compact('barcodes'));
    }

    public function store(Request $request)
    {
        // Perbaikan: Validasi unik tidak memerlukan ID saat membuat data baru
        // Penambahan named error bag 'store'
        $request->validate([
            'name' => 'required|string|max:255',
            'value' => 'required|string|unique:barcodes,value',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric|min:1',
        ], [], [], 'store');

        Barcode::create($request->all());
        // Perbaikan: Redirect ke nama route yang benar
        return redirect()->route('admin.barcodes.index')->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function update(Request $request, Barcode $barcode)
    {
        // Perbaikan: Validasi unik harus mengabaikan ID saat ini
        // Penambahan named error bag 'update'
        $request->validate([
            'name' => 'required|string|max:255',
            'value' => 'required|string|unique:barcodes,value,' . $barcode->id,
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric|min:1',
        ], [], [], 'update');

        $barcode->update($request->all());
        // Perbaikan: Redirect ke nama route yang benar
        return redirect()->route('admin.barcodes.index')->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Barcode $barcode)
    {
        $barcode->delete();
        // Perbaikan: Redirect ke nama route yang benar
        return redirect()->route('admin.barcodes.index')->with('success', 'Lokasi berhasil dihapus.');
    }
}