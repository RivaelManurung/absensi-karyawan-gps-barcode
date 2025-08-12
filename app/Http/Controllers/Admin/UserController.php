<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Division;
use App\Models\JobTitle;
use App\Models\Education;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Menampilkan halaman MANAJEMEN KARYAWAN (tabel biasa dengan CRUD).
     */
    public function index()
    {
        // Ambil semua data master yang dibutuhkan untuk modal
        $divisions = Division::orderBy('name')->get();
        $jobTitles = JobTitle::orderBy('name')->get();
        $educations = Education::all();
        $shifts = Shift::orderBy('name')->get();

        // Ambil semua user dengan paginasi untuk ditampilkan di tabel utama
        $users = User::with(['division', 'jobTitle', 'shift'])->latest()->paginate(15);
        
        return view('admin.users.index', compact('users', 'divisions', 'jobTitles', 'educations', 'shifts'));
    }

    /**
     * Menampilkan halaman KARYAWAN PER DIVISI (hanya untuk melihat).
     */
    public function perDivision()
    {
        // Ambil data divisi, beserta user di dalamnya
        $divisionsWithUsers = Division::with(['users.jobTitle', 'users.shift'])
            ->orderBy('name')
            ->get();
            
        // Ambil user yang belum punya divisi
        $usersWithoutDivision = User::whereNull('division_id')
            ->with(['jobTitle', 'shift'])
            ->latest()
            ->get();
        
        return view('admin.users.per-division', compact('divisionsWithUsers', 'usersWithoutDivision'));
    }

    /**
     * Menampilkan form untuk membuat user baru.
     */
    public function create()
    {
        $divisions = Division::all();
        $jobTitles = JobTitle::all();
        $shifts = Shift::all();
        $educations = Education::all();
        
        return view('admin.users.create-page', compact('divisions', 'jobTitles', 'shifts', 'educations'));
    }

    /**
     * Menampilkan detail user.
     */
    public function show(User $user)
    {
        $user->load(['division', 'jobTitle', 'education', 'shift']);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Menampilkan form untuk edit user.
     */
    public function edit(User $user)
    {
        $divisions = Division::orderBy('name')->get();
        $jobTitles = JobTitle::orderBy('name')->get();
        $educations = Education::all();
        $shifts = Shift::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'divisions', 'jobTitles', 'educations', 'shifts'));
    }

    /**
     * Menyimpan pengguna baru dari modal.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'group' => 'required|in:user,admin,superadmin',
            'division_id' => 'required|exists:divisions,id',
            'job_title_id' => 'required|exists:job_titles,id',
            'shift_id' => 'required|exists:shifts,id',
        ], [], [], 'store');

        $data = $request->except('password');
        $data['password'] = Hash::make($request->password);

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Memperbarui data pengguna dari modal.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'group' => 'required|in:user,admin,superadmin',
            'division_id' => 'required|exists:divisions,id',
            'job_title_id' => 'required|exists:job_titles,id',
            'shift_id' => 'required|exists:shifts,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.users.index')
                ->withErrors($validator, 'update')
                ->withInput()
                ->with('failed_edit_id', $user->id);
        }

        $data = $validator->validated();
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Menghapus pengguna.
     */
    public function destroy(User $user)
    {
        if ($user->group === 'superadmin' || $user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Aksi tidak diizinkan.');
        }
        
        try {
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
        } catch(\Illuminate\Database\QueryException $e) {
             return redirect()->route('admin.users.index')->with('error', 'Gagal menghapus pengguna. Mungkin masih terhubung dengan data lain.');
        }
    }
}