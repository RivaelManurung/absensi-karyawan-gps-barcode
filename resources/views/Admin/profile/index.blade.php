@extends('Admin.Layout.main')
@section('title', 'Profile Admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold py-3 mb-2">
                        <span class="text-muted fw-light">Dashboard /</span> Profile Admin
                    </h4>
                    <p class="text-muted mb-0">Kelola informasi profile dan pengaturan akun Anda</p>
                </div>
                <div>
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                        <i class="bx bx-user me-1"></i>
                        {{ ucfirst(auth()->user()->group) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="bx bx-check-circle me-2 fs-4"></i>
            <div>
                <strong>Berhasil!</strong> {{ session('success') }}
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="bx bx-error-circle me-2 fs-4"></i>
            <div>
                <strong>Error!</strong> {{ session('error') }}
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-start">
            <i class="bx bx-error-circle me-2 fs-4"></i>
            <div>
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Profile Photo Card -->
        <div class="col-xl-4 col-lg-5 col-md-5">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="mb-4">
                        @if($user->profile_photo_path)
                            <img src="{{ asset('storage/photos/' . $user->profile_photo_path) }}" 
                                 alt="Profile Photo" 
                                 class="rounded-circle border shadow"
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="bg-light border rounded-circle d-inline-flex align-items-center justify-content-center shadow"
                                 style="width: 150px; height: 150px;">
                                <i class="bx bx-user text-muted" style="font-size: 4rem;"></i>
                            </div>
                        @endif
                    </div>
                    
                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    <span class="badge bg-primary-subtle text-primary mb-3">
                        {{ ucfirst($user->group) }}
                    </span>

                    <!-- Photo Actions -->
                    <div class="mt-4">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#photoModal">
                            <i class="bx bx-upload me-1"></i>
                            {{ $user->profile_photo_path ? 'Ganti Foto' : 'Upload Foto' }}
                        </button>
                        
                        @if($user->profile_photo_path)
                        <form action="{{ route('admin.profile.remove-photo') }}" method="POST" class="d-inline">
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

            <!-- Quick Info Card -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Informasi Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-buildings text-primary me-2"></i>
                                <div>
                                    <small class="text-muted">Divisi</small>
                                    <div>{{ $user->division->name ?? 'Tidak ada' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-briefcase text-success me-2"></i>
                                <div>
                                    <small class="text-muted">Jabatan</small>
                                    <div>{{ $user->jobTitle->name ?? 'Tidak ada' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-time text-warning me-2"></i>
                                <div>
                                    <small class="text-muted">Shift</small>
                                    <div>{{ $user->shift->name ?? 'Tidak ada' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-calendar text-info me-2"></i>
                                <div>
                                    <small class="text-muted">Bergabung</small>
                                    <div>{{ $user->created_at->format('d F Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="col-xl-8 col-lg-7 col-md-7">
            <!-- Profile Form -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Informasi Profile</h6>
                    <span class="badge bg-light text-dark">Data Pribadi</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" 
                                       value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" 
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Telepon</label>
                                <input type="text" name="phone" class="form-control" 
                                       value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
                                @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="birth_date" class="form-control" 
                                       value="{{ old('birth_date', $user->birth_date) }}">
                                @error('birth_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="address" class="form-control" rows="3" 
                                      placeholder="Masukkan alamat lengkap">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Divisi</label>
                                <select name="division_id" class="form-select">
                                    <option value="">Pilih Divisi</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}" 
                                                {{ old('division_id', $user->division_id) == $division->id ? 'selected' : '' }}>
                                            {{ $division->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('division_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jabatan</label>
                                <select name="job_title_id" class="form-select">
                                    <option value="">Pilih Jabatan</option>
                                    @foreach($jobTitles as $jobTitle)
                                        <option value="{{ $jobTitle->id }}" 
                                                {{ old('job_title_id', $user->job_title_id) == $jobTitle->id ? 'selected' : '' }}>
                                            {{ $jobTitle->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('job_title_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pendidikan</label>
                                <select name="education_id" class="form-select">
                                    <option value="">Pilih Pendidikan</option>
                                    @foreach($educations as $education)
                                        <option value="{{ $education->id }}" 
                                                {{ old('education_id', $user->education_id) == $education->id ? 'selected' : '' }}>
                                            {{ $education->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('education_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Shift</label>
                                <select name="shift_id" class="form-select">
                                    <option value="">Pilih Shift</option>
                                    @foreach($shifts as $shift)
                                        <option value="{{ $shift->id }}" 
                                                {{ old('shift_id', $user->shift_id) == $shift->id ? 'selected' : '' }}>
                                            {{ $shift->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('shift_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Ubah Password</h6>
                    <span class="badge bg-warning-subtle text-warning">Keamanan</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.update-password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control" required>
                            @error('current_password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="password" class="form-control" required>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-1"></i>
                            Password harus minimal 8 karakter dan mengandung kombinasi huruf dan angka.
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="bx bx-key me-1"></i>Ubah Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Photo Upload Modal -->
<div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Foto Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.profile.update-photo') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Foto</label>
                        <input type="file" name="photo" class="form-control" accept="image/*" required>
                        <div class="form-text">
                            Format yang didukung: JPG, PNG, GIF. Maksimal 2MB.
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-1"></i>
                        Foto akan otomatis diresize untuk mengoptimalkan performa aplikasi.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-upload me-1"></i>Upload Foto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
    
    // Preview uploaded photo
    const photoInput = document.querySelector('input[name="photo"]');
    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // You can add preview functionality here if needed
                    console.log('File selected:', file.name);
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endpush
