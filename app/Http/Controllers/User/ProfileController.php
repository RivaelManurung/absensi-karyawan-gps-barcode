<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\JobTitle;
use App\Models\Education;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display user profile page
     */
    public function index()
    {
        $user = Auth::user();
        
        return view('user.profile.index', compact('user'));
    }

    /**
     * Show edit profile form
     */
    public function edit()
    {
        $user = Auth::user();
        $divisions = Division::all();
        $jobTitles = JobTitle::all();
        $educations = Education::all();
        $shifts = Shift::all();
        
        return view('user.profile.edit', compact('user', 'divisions', 'jobTitles', 'educations', 'shifts'));
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $userId = Auth::id();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($userId)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'division_id' => 'nullable|exists:divisions,id',
            'job_title_id' => 'nullable|exists:job_titles,id',
            'education_id' => 'nullable|exists:educations,id',
            'shift_id' => 'nullable|exists:shifts,id',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'birth_date' => $request->birth_date,
            'birth_place' => $request->birth_place,
            'gender' => $request->gender,
            'division_id' => $request->division_id,
            'job_title_id' => $request->job_title_id,
            'education_id' => $request->education_id,
            'shift_id' => $request->shift_id,
        ];

        $user = User::find($userId);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            
            $profilePhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');
            $updateData['profile_photo_path'] = $profilePhotoPath;
        }

        // Update user data
        User::where('id', $userId)->update($updateData);

        return redirect()->route('user.profile.index')
                         ->with('success', 'Profile berhasil diperbarui!');
    }

    /**
     * Show change password form
     */
    public function changePasswordForm()
    {
        return view('user.profile.change-password');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak benar.']);
        }

        // Update password
        User::where('id', Auth::id())->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('user.profile.index')
                         ->with('success', 'Password berhasil diubah!');
    }

    /**
     * Delete profile photo
     */
    public function deleteProfilePhoto()
    {
        $user = Auth::user();
        
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            User::where('id', Auth::id())->update(['profile_photo_path' => null]);
        }

        return back()->with('success', 'Foto profile berhasil dihapus!');
    }
}
