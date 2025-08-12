@extends('User.Layout.single-page')

@section('title', 'Edit Profile')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Edit Profile</h4>
            <p class="text-muted mb-0">Perbarui informasi personal Anda</p>
        </div>
        <a href="{{ route('user.profile.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i>Kembali
        </a>
    </div>

    <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Profile Photo -->
            <div class="col-xl-4 col-lg-5 col-md-5">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-image me-2"></i>Foto Profile
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            @if($user->profile_photo_path)
                                <img src="{{ Storage::url($user->profile_photo_path) }}" 
                                     alt="Profile Photo" 
                                     class="rounded-circle" 
                                     style="width: 150px; height: 150px; object-fit: cover;"
                                     id="profilePreview">
                            @else
                                <div class="bg-label-primary rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                                     style="width: 150px; height: 150px; font-size: 4rem;"
                                     id="profilePlaceholder">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        
                        <div class="mb-3">
                            <input type="file" name="profile_photo" id="profile_photo" class="form-control" accept="image/*" onchange="previewImage(this)">
                            <div class="form-text">
                                Format: JPG, JPEG, PNG (Maks. 2MB)
                            </div>
                            @error('profile_photo')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        @if($user->profile_photo_path)
                            <form action="{{ route('user.profile.delete-photo') }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" 
                                        onclick="return confirm('Yakin ingin menghapus foto profile?')">
                                    <i class="bx bx-trash me-1"></i>Hapus Foto
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="col-xl-8 col-lg-7 col-md-7">
                <!-- Personal Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-user me-2"></i>Informasi Personal
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="name">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="phone">No. Telepon</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="gender">Jenis Kelamin</label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="birth_date">Tanggal Lahir</label>
                                <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                                       id="birth_date" name="birth_date" value="{{ old('birth_date', $user->birth_date) }}">
                                @error('birth_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="birth_place">Tempat Lahir</label>
                                <input type="text" class="form-control @error('birth_place') is-invalid @enderror" 
                                       id="birth_place" name="birth_place" value="{{ old('birth_place', $user->birth_place) }}">
                                @error('birth_place')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="address">Alamat</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Work Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-briefcase me-2"></i>Informasi Pekerjaan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="division_id">Divisi</label>
                                <select class="form-select @error('division_id') is-invalid @enderror" id="division_id" name="division_id">
                                    <option value="">Pilih Divisi</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}" 
                                                {{ old('division_id', $user->division_id) == $division->id ? 'selected' : '' }}>
                                            {{ $division->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('division_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="job_title_id">Jabatan</label>
                                <select class="form-select @error('job_title_id') is-invalid @enderror" id="job_title_id" name="job_title_id">
                                    <option value="">Pilih Jabatan</option>
                                    @foreach($jobTitles as $jobTitle)
                                        <option value="{{ $jobTitle->id }}" 
                                                {{ old('job_title_id', $user->job_title_id) == $jobTitle->id ? 'selected' : '' }}>
                                            {{ $jobTitle->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('job_title_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="education_id">Pendidikan</label>
                                <select class="form-select @error('education_id') is-invalid @enderror" id="education_id" name="education_id">
                                    <option value="">Pilih Pendidikan</option>
                                    @foreach($educations as $education)
                                        <option value="{{ $education->id }}" 
                                                {{ old('education_id', $user->education_id) == $education->id ? 'selected' : '' }}>
                                            {{ $education->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('education_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="shift_id">Shift Kerja</label>
                                <select class="form-select @error('shift_id') is-invalid @enderror" id="shift_id" name="shift_id">
                                    <option value="">Pilih Shift</option>
                                    @foreach($shifts as $shift)
                                        <option value="{{ $shift->id }}" 
                                                {{ old('shift_id', $user->shift_id) == $shift->id ? 'selected' : '' }}>
                                            {{ $shift->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('shift_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('user.profile.index') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-x me-1"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const preview = document.getElementById('profilePreview');
            const placeholder = document.getElementById('profilePlaceholder');
            
            if (preview) {
                preview.src = e.target.result;
            } else if (placeholder) {
                placeholder.outerHTML = `<img src="${e.target.result}" alt="Profile Photo" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;" id="profilePreview">`;
            }
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
