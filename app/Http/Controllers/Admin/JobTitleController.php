<?php

namespace App\Http\Controllers\Admin;

use App\Models\JobTitle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JobTitleController extends Controller
{
    public function index()
    {
        // Menambahkan withCount untuk menghitung user di setiap jabatan
        $jobTitles = JobTitle::withCount('users')->latest()->paginate(10);
        return view('admin.job-titles.index', compact('jobTitles'));
    }

    // Metode create() dan edit() dihapus karena kita menggunakan modal

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:job_titles,name']);
        JobTitle::create($request->all());
        // Perbaikan: Mengarahkan ke nama route yang benar
        return redirect()->route('admin.job-titles.index')->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function update(Request $request, JobTitle $jobTitle)
    {
        $request->validate(['name' => 'required|string|max:255|unique:job_titles,name,' . $jobTitle->id]);
        $jobTitle->update($request->all());
        // Perbaikan: Mengarahkan ke nama route yang benar
        return redirect()->route('admin.job-titles.index')->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(JobTitle $jobTitle)
    {
        try {
            $jobTitle->delete();
            // Perbaikan: Mengarahkan ke nama route yang benar
            return redirect()->route('admin.job-titles.index')->with('success', 'Jabatan berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Gagal menghapus jabatan. Masih ada karyawan yang terhubung.');
        }
    }
}