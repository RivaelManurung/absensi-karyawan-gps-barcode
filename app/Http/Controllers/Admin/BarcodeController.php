<?php

namespace App\Http\Controllers\Admin;

use App\Models\Barcode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Controllers\Controller;

class BarcodeController extends Controller
{
    /**
     * Menampilkan halaman daftar lokasi barcode.
     */
    public function index()
    {
        $barcodes = Barcode::latest()->paginate(10);
        return view('admin.barcodes.index', compact('barcodes'));
    }

    /**
     * Menyimpan lokasi barcode baru dari modal.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'value' => 'required|string|unique:barcodes,value',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric|min:1',
        ], [], [], 'store');

        Barcode::create($request->all());
        return redirect()->route('admin.barcodes.index')->with('success', 'Lokasi berhasil ditambahkan.');
    }

    /**
     * Memperbarui data lokasi barcode dari modal.
     */
    public function update(Request $request, Barcode $barcode)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'value' => ['required', 'string', Rule::unique('barcodes')->ignore($barcode->id)],
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.barcodes.index')
                ->withErrors($validator, 'update')
                ->withInput()
                ->with('failed_edit_id', $barcode->id);
        }

        $barcode->update($validator->validated());
        return redirect()->route('admin.barcodes.index')->with('success', 'Lokasi berhasil diperbarui.');
    }

    /**
     * Menghapus data lokasi barcode.
     */
    public function destroy(Barcode $barcode)
    {
        try {
            $barcode->delete();
            return redirect()->route('admin.barcodes.index')->with('success', 'Lokasi berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Gagal menghapus lokasi. Mungkin masih terhubung dengan data absensi.');
        }
    }

    /**
     * Menampilkan halaman khusus untuk satu QR Code.
     */
    public function showQr(Barcode $barcode)
    {
        return view('admin.barcodes.show-qr', compact('barcode'));
    }

    /**
     * Mengunduh gambar QR Code.
     */
    public function downloadQr(Barcode $barcode)
    {
        $qrCode = QrCode::format('png')
            ->size(500)
            ->errorCorrection('H')
            ->margin(1)
            ->generate($barcode->value);
        
        $fileName = 'qrcode-' . \Illuminate\Support\Str::slug($barcode->name) . '.png';

        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
}