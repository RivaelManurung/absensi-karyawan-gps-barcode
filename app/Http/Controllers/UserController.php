<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Division;
use App\Models\JobTitle;
use App\Models\Education;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Eager load relasi untuk menghindari N+1 problem
        $users = User::with(['division', 'jobTitle'])->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        // Ambil data master untuk dropdown di form
        $divisions = Division::all();
        $jobTitles = JobTitle::all();
        $educations = Education::all();
        return view('users.create', compact('divisions', 'jobTitles', 'educations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'division_id' => 'required|exists:divisions,id',
            'job_title_id' => 'required|exists:job_titles,id',
        ]);

        $data = $request->except('password');
        $data['password'] = Hash::make($request->password);

        User::create($data);

        return redirect()->route('users.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $divisions = Division::all();
        $jobTitles = JobTitle::all();
        $educations = Education::all();
        return view('users.edit', compact('user', 'divisions', 'jobTitles', 'educations'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed', // Password opsional
            'division_id' => 'required|exists:divisions,id',
            'job_title_id' => 'required|exists:job_titles,id',
        ]);

        $data = $request->except('password');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Jangan hapus admin atau diri sendiri
        if ($user->group === 'superadmin' || $user->id === auth()->id()) {
            return back()->with('error', 'Aksi tidak diizinkan.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Karyawan berhasil dihapus.');
    }
}
