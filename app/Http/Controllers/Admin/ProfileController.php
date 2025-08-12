<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Division;
use App\Models\JobTitle;
use App\Models\Education;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the admin profile page.
     */
    public function index()
    {
        $user = Auth::user();
        $divisions = Division::orderBy('name')->get();
        $jobTitles = JobTitle::orderBy('name')->get();
        $educations = Education::orderBy('name')->get();
        $shifts = Shift::orderBy('name')->get();

        return view('Admin.profile.index', compact('user', 'divisions', 'jobTitles', 'educations', 'shifts'));
    }

    /**
     * Update the admin profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'division_id' => 'nullable|exists:divisions,id',
            'job_title_id' => 'nullable|exists:job_titles,id',
            'education_id' => 'nullable|exists:educations,id',
            'shift_id' => 'nullable|exists:shifts,id',
        ]);

        $user->update($request->only([
            'name',
            'email',
            'phone',
            'address',
            'birth_date',
            'division_id',
            'job_title_id',
            'education_id',
            'shift_id'
        ]));

        return redirect()->route('admin.profile.index')
            ->with('success', 'Profile berhasil diperbarui!');
    }

    /**
     * Update the admin password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('admin.profile.index')
                ->withErrors(['current_password' => 'Password saat ini tidak benar.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.profile.index')
            ->with('success', 'Password berhasil diperbarui!');
    }

    /**
     * Upload and update profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->profile_photo_path && file_exists(public_path('storage/photos/' . $user->profile_photo_path))) {
                unlink(public_path('storage/photos/' . $user->profile_photo_path));
            }

            // Store new photo
            $photo = $request->file('photo');
            $photoName = time() . '_' . $user->id . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('storage/photos'), $photoName);

            // Update user photo
            $user->update(['profile_photo_path' => $photoName]);

            return redirect()->route('admin.profile.index')
                ->with('success', 'Foto profil berhasil diperbarui!');
        }

        return redirect()->route('admin.profile.index')
            ->with('error', 'Gagal mengupload foto profil.');
    }

    /**
     * Remove profile photo.
     */
    public function removePhoto()
    {
        $user = Auth::user();

        if ($user->profile_photo_path) {
            // Delete photo file
            if (file_exists(public_path('storage/photos/' . $user->profile_photo_path))) {
                unlink(public_path('storage/photos/' . $user->profile_photo_path));
            }

            // Remove photo from database
            $user->update(['profile_photo_path' => null]);

            return redirect()->route('admin.profile.index')
                ->with('success', 'Foto profil berhasil dihapus!');
        }

        return redirect()->route('admin.profile.index')
            ->with('error', 'Tidak ada foto profil untuk dihapus.');
    }
}
