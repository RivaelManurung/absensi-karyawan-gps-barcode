<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Division;
use App\Models\JobTitle;
use App\Models\Education;
use App\Models\Shift; // <-- 1. Tambahkan model Shift
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        // Ambil semua data master yang dibutuhkan untuk modal
        $divisions = Division::orderBy('name')->get();
        $jobTitles = JobTitle::orderBy('name')->get();
        $educations = Education::all();
        $shifts = Shift::orderBy('name')->get(); // <-- 2. Ambil data Shift

        $users = User::with(['division', 'jobTitle', 'shift'])->latest()->paginate(10);
        
        // Kirim semua data ke view
        return view('admin.users.index', compact('users', 'divisions', 'jobTitles', 'educations', 'shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'group' => 'required|in:user,admin,superadmin',
            'division_id' => 'required|exists:divisions,id',
            'job_title_id' => 'required|exists:job_titles,id',
            'shift_id' => 'required|exists:shifts,id', // <-- 3. Tambahkan validasi untuk shift
        ], [], [], 'store');

        $data = $request->except('password');
        $data['password'] = Hash::make($request->password);

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'group' => 'required|in:user,admin,superadmin',
            'division_id' => 'required|exists:divisions,id',
            'job_title_id' => 'required|exists:job_titles,id',
            'shift_id' => 'required|exists:shifts,id', // <-- 4. Tambahkan validasi untuk shift
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