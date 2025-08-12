@extends('User.Layout.single-page')

@section('title', 'Profile Saya')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Berhasil!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Gagal!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Profile Saya</h4>
            <p class="text-muted mb-0">Kelola informasi personal dan pengaturan akun Anda</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('user.profile.edit') }}" class="btn btn-primary">
                <i class="bx bx-edit me-1"></i>Edit Profile
            </a>
            <a href="{{ route('user.profile.change-password') }}" class="btn btn-outline-secondary">
                <i class="bx bx-lock me-1"></i>Ubah Password
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Profile Photo Card -->
        <div class="col-xl-4 col-lg-5 col-md-5">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="position-relative d-inline-block">
                        @if($user->profile_photo_path)
                            <img src="{{ Storage::url($user->profile_photo_path) }}" 
                                 alt="Profile Photo" 
                                 class="rounded-circle" 
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="bg-label-primary rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 150px; height: 150px; font-size: 4rem;">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                        
                        @if($user->profile_photo_path)
                            <form action="{{ route('user.profile.delete-photo') }}" method="POST" class="d-inline position-absolute top-0 end-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger rounded-circle" 
                                        onclick="return confirm('Yakin ingin menghapus foto profile?')"
                                        style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;"
                                        title="Hapus foto profile">
                                    <i class="bx bx-trash" style="font-size: 14px;"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                    
                    <h5 class="mt-3 mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-1">{{ $user->email }}</p>
                    <span class="badge bg-label-primary">{{ ucfirst($user->group) }}</span>
                    
                    @if($user->nip)
                        <div class="mt-3">
                            <small class="text-muted">NIP: {{ $user->nip }}</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Profile Details -->
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
                            <label class="form-label text-muted">Nama Lengkap</label>
                            <div class="fw-semibold">{{ $user->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Email</label>
                            <div>{{ $user->email ?? '-' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">No. Telepon</label>
                            <div>{{ $user->phone ?? '-' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Jenis Kelamin</label>
                            <div>
                                @if($user->gender === 'male')
                                    Laki-laki
                                @elseif($user->gender === 'female')
                                    Perempuan
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Tanggal Lahir</label>
                            <div>
                                @if($user->birth_date)
                                    {{ \Carbon\Carbon::parse($user->birth_date)->format('d F Y') }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Tempat Lahir</label>
                            <div>{{ $user->birth_place ?? '-' }}</div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label text-muted">Alamat</label>
                            <div>{{ $user->address ?? '-' }}</div>
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
                            <label class="form-label text-muted">Divisi</label>
                            <div>
                                @if($user->division)
                                    <span class="badge bg-label-primary">{{ $user->division->name }}</span>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Jabatan</label>
                            <div>
                                @if($user->jobTitle)
                                    <span class="badge bg-label-success">{{ $user->jobTitle->name }}</span>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Pendidikan</label>
                            <div>
                                @if($user->education)
                                    <span class="badge bg-label-info">{{ $user->education->name }}</span>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Shift Kerja</label>
                            <div>
                                @if($user->shift)
                                    <span class="badge bg-label-warning">{{ $user->shift->name }}</span>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-cog me-2"></i>Informasi Akun
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Tipe Akun</label>
                            <div>
                                <span class="badge bg-label-secondary">{{ ucfirst($user->group) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Bergabung Sejak</label>
                            <div>{{ $user->created_at->format('d F Y') }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Terakhir Diupdate</label>
                            <div>{{ $user->updated_at->format('d F Y, H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
