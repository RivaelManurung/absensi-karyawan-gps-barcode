@extends('admin.layout.main')

@section('title', 'Tambah Karyawan Baru')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center py-3 mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Karyawan /</span> Tambah Baru
        </h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Tambah Karyawan</h5>
                    <small class="text-muted">Lengkapi semua data yang diperlukan</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Data Pribadi -->
                            <div class="col-md-6">
                                <h6 class="mb-3">Data Pribadi</h6>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="nip" class="form-label">NIP</label>
                                    <input type="text" class="form-control @error('nip') is-invalid @enderror" 
                                           id="nip" name="nip" value="{{ old('nip') }}">
                                    @error('nip')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">No. Telepon</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="gender" class="form-label">Jenis Kelamin</label>
                                    <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Data Pekerjaan -->
                            <div class="col-md-6">
                                <h6 class="mb-3">Data Pekerjaan</h6>
                                
                                <div class="mb-3">
                                    <label for="division_id" class="form-label">Divisi <span class="text-danger">*</span></label>
                                    <select class="form-select @error('division_id') is-invalid @enderror" id="division_id" name="division_id" required>
                                        <option value="">Pilih Divisi</option>
                                        @foreach($divisions as $division)
                                            <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>
                                                {{ $division->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('division_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="job_title_id" class="form-label">Jabatan <span class="text-danger">*</span></label>
                                    <select class="form-select @error('job_title_id') is-invalid @enderror" id="job_title_id" name="job_title_id" required>
                                        <option value="">Pilih Jabatan</option>
                                        @foreach($jobTitles as $jobTitle)
                                            <option value="{{ $jobTitle->id }}" {{ old('job_title_id') == $jobTitle->id ? 'selected' : '' }}>
                                                {{ $jobTitle->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('job_title_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="shift_id" class="form-label">Shift <span class="text-danger">*</span></label>
                                    <select class="form-select @error('shift_id') is-invalid @enderror" id="shift_id" name="shift_id" required>
                                        <option value="">Pilih Shift</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                                {{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('shift_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="education_id" class="form-label">Pendidikan</label>
                                    <select class="form-select @error('education_id') is-invalid @enderror" id="education_id" name="education_id">
                                        <option value="">Pilih Pendidikan</option>
                                        @foreach($educations as $education)
                                            <option value="{{ $education->id }}" {{ old('education_id') == $education->id ? 'selected' : '' }}>
                                                {{ $education->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('education_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="group" class="form-label">Grup/Role <span class="text-danger">*</span></label>
                                    <select class="form-select @error('group') is-invalid @enderror" id="group" name="group" required>
                                        <option value="">Pilih Grup</option>
                                        <option value="user" {{ old('group') == 'user' ? 'selected' : '' }}>User/Karyawan</option>
                                        <option value="admin" {{ old('group') == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="superadmin" {{ old('group') == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                                    </select>
                                    @error('group')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Password Section -->
                        <hr class="my-4">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="mb-3">Data Login</h6>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-x me-1"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-check me-1"></i>Simpan Karyawan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
