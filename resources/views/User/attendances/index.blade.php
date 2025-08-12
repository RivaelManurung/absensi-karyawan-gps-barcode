@extends('User.Layout.single-page')

@section('title', 'Dashboard Karyawan')

@section('content')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800 fw-bold">Dashboard Karyawan</h1>
                    <p class="text-muted mb-0">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                        <i class="bx bx-time me-1"></i>
                        <span id="current-time"></span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Notifications --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <div class="d-flex align-items-center">
            <i class="bx bx-check-circle me-2 fs-4"></i>
            <div>
                <strong>Berhasil!</strong> {{ session('success') }}
                
                {{-- Location Info for Success --}}
                @if(session('location_info'))
                <div class="mt-2 small">
                    <i class="bx bx-map-pin me-1"></i>
                    <strong>Lokasi:</strong> {{ session('location_info')['coordinates'] }} 
                    (Akurasi: {{ session('location_info')['accuracy'] }}, 
                    Kualitas: {{ session('location_info')['quality'] }})
                </div>
                @endif

                {{-- Attendance Details --}}
                @if(session('attendance_details'))
                <div class="mt-2 small">
                    <div class="row g-2">
                        <div class="col-6"><i class="bx bx-map me-1"></i> {{ session('attendance_details')['location'] }}</div>
                        <div class="col-6"><i class="bx bx-target-lock me-1"></i> {{ session('attendance_details')['distance'] }}</div>
                        <div class="col-6"><i class="bx bx-time me-1"></i> {{ session('attendance_details')['time'] }}</div>
                        <div class="col-6"><i class="bx bx-check-shield me-1"></i> {{ session('attendance_details')['status'] }}</div>
                    </div>
                </div>
                @endif

                {{-- Checkout Details --}}
                @if(session('checkout_details'))
                <div class="mt-2 small">
                    <div class="row g-2">
                        <div class="col-6"><i class="bx bx-time me-1"></i> {{ session('checkout_details')['time'] }}</div>
                        <div class="col-6"><i class="bx bx-timer me-1"></i> {{ session('checkout_details')['duration'] }}</div>
                        <div class="col-6"><i class="bx bx-target-lock me-1"></i> {{ session('checkout_details')['distance'] }}</div>
                        <div class="col-6"><i class="bx bx-wifi me-1"></i> {{ session('checkout_details')['accuracy'] }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm" role="alert">
        <div class="d-flex align-items-center">
            <i class="bx bx-error-circle me-2 fs-4"></i>
            <div>
                <strong>Peringatan!</strong> {{ session('warning') }}
                
                {{-- Location Info for Warning --}}
                @if(session('location_info'))
                <div class="mt-2 small">
                    <i class="bx bx-map-pin me-1"></i>
                    <strong>Lokasi Anda:</strong> {{ session('location_info')['coordinates'] }} 
                    (Akurasi: {{ session('location_info')['accuracy'] }}, 
                    Kualitas: {{ session('location_info')['quality'] }})
                </div>
                @endif
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <div class="d-flex align-items-center">
            <i class="bx bx-error-circle me-2 fs-4"></i>
            <div>
                <strong>Error!</strong> {{ session('error') }}
                
                {{-- Location Info for Error --}}
                @if(session('location_info'))
                <div class="mt-2 small">
                    <i class="bx bx-map-pin me-1"></i>
                    <strong>Lokasi Anda:</strong> {{ session('location_info')['coordinates'] }} 
                    (Akurasi: {{ session('location_info')['accuracy'] }}, 
                    Kualitas: {{ session('location_info')['quality'] }})
                </div>
                @endif

                {{-- Barcode Info --}}
                @if(session('barcode_info'))
                <div class="mt-1 small">
                    <i class="bx bx-qr me-1"></i> {{ session('barcode_info') }}
                </div>
                @endif

                {{-- Distance Info --}}
                @if(session('distance_info'))
                <div class="mt-1 small">
                    <i class="bx bx-target-lock me-1"></i> {{ session('distance_info') }}
                </div>
                @endif

                {{-- Barcode Error --}}
                @if(session('barcode_error'))
                <div class="mt-1 small">
                    <i class="bx bx-scan me-1"></i> {{ session('barcode_error') }}
                </div>
                @endif

                {{-- Technical Error --}}
                @if(session('technical_error'))
                <div class="mt-1 small text-muted">
                    <i class="bx bx-bug me-1"></i> {{ session('technical_error') }}
                </div>
                @endif
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Modern Navigation Tabs --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    <nav class="navbar navbar-expand-lg navbar-light bg-transparent px-3">
                        <ul class="nav nav-pills nav-fill w-100" role="tablist">
                            <li class="nav-item mx-1" role="presentation">
                                <button class="nav-link active rounded-pill px-4 py-3 fw-semibold" 
                                        data-bs-toggle="tab" data-bs-target="#tab-attendance" type="button" 
                                        role="tab" aria-selected="true">
                                    <i class="bx bx-fingerprint me-2 fs-5"></i>
                                    <span class="d-none d-md-inline">Absensi Hari Ini</span>
                                    <span class="d-md-none">Absensi</span>
                                </button>
                            </li>
                            <li class="nav-item mx-1" role="presentation">
                                <button class="nav-link rounded-pill px-4 py-3 fw-semibold" 
                                        data-bs-toggle="tab" data-bs-target="#tab-history" type="button" 
                                        role="tab" aria-selected="false">
                                    <i class="bx bx-history me-2 fs-5" 
                                       data-fallback-fa="fas fa-history" 
                                       data-fallback-emoji="📋"
                                       style="font-size: 1.25rem !important; 
                                              line-height: 1 !important; 
                                              display: inline-block !important;"></i>
                                    <span class="d-none d-md-inline">Riwayat Absensi</span>
                                    <span class="d-md-none">Riwayat</span>
                                </button>
                            </li>
                            <li class="nav-item mx-1" role="presentation">
                                <button class="nav-link rounded-pill px-4 py-3 fw-semibold" 
                                        data-bs-toggle="tab" data-bs-target="#tab-request" type="button" 
                                        role="tab" aria-selected="false">
                                    <i class="bx bx-envelope me-2 fs-5" 
                                       data-fallback-fa="fas fa-envelope" 
                                       data-fallback-emoji="✉️"
                                       style="font-size: 1.25rem !important; 
                                              line-height: 1 !important; 
                                              display: inline-block !important;"></i>
                                    <span class="d-none d-md-inline">Ajukan Izin</span>
                                    <span class="d-md-none">Izin</span>
                                </button>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab Content --}}
    <div class="tab-content">
        {{-- Tab Absensi --}}
        <div class="tab-pane fade show active" id="tab-attendance" role="tabpanel">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-6">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-5 text-center">
                            <div class="mb-4">
                                <div class="icon-circle icon-circle-primary">
                                    <i class="bx bx-fingerprint attendance-icon primary"></i>
                                    <i class="fas fa-fingerprint attendance-icon primary" style="display: none;"></i>
                                    <span class="attendance-icon primary" style="display: none; font-size: 2.5rem;">🔐</span>
                                </div>
                                <h4 class="fw-bold mb-2">Absensi Kehadiran</h4>
                                <p class="text-muted">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                            </div>

                            @if (!$todayAttendance)
                                {{-- Clock In Form --}}
                                <div class="mb-4">
                                    <p class="text-muted mb-4">Silakan scan QR code di lokasi Anda atau masukkan kode secara manual.</p>
                                    
                                    {{-- QR Scanner Area --}}
                                    <div class="position-relative mb-4">
                                        <div id="qr-reader" class="mx-auto rounded-3 overflow-hidden shadow" style="width: 300px;"></div>
                                        <div id="qr-reader-results" class="mt-3"></div>
                                    </div>

                                    <form action="{{ route('attendances.clockin') }}" method="POST" class="attendance-form">
                                        @csrf
                                        <input type="hidden" name="latitude" class="latitude-input">
                                        <input type="hidden" name="longitude" class="longitude-input">
                                        <input type="hidden" name="accuracy" class="accuracy-input">
                                        
                                        {{-- GPS Status Display --}}
                                        <div class="mb-3 text-center">
                                            <div class="gps-status">
                                                <i class="bx bx-loader-alt bx-spin text-warning"></i> Mencari GPS...
                                            </div>
                                            <div class="current-location-info"></div>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <div class="input-group input-group-lg shadow-sm">
                                                <span class="input-group-text bg-light border-end-0">
                                                    <i class="bx bx-qr-scan text-muted"></i>
                                                </span>
                                                <input type="text" id="barcode_value" name="barcode_value" 
                                                       class="form-control border-start-0" 
                                                       placeholder="Masukkan kode lokasi..." required>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-sm">
                                            <i class="bx bx-log-in me-2"></i>
                                            Absen Masuk
                                        </button>
                                    </form>
                                </div>

                            @elseif ($todayAttendance->status && $todayAttendance->status->name === 'pending')
                                {{-- Pending Leave Request --}}
                                <div class="text-center">
                                    <div class="icon-circle icon-circle-warning">
                                        <i class="bx bx-hourglass attendance-icon warning"></i>
                                        <i class="fas fa-hourglass attendance-icon warning" style="display: none;"></i>
                                        <span class="attendance-icon warning" style="display: none; font-size: 2.5rem;">⏳</span>
                                    </div>
                                    <h5 class="fw-bold text-warning mb-2">Pengajuan Menunggu Persetujuan</h5>
                                    @if($todayAttendance->request_type)
                                        <p class="text-muted">Pengajuan <strong>{{ ucfirst($todayAttendance->request_type) }}</strong> Anda sedang menunggu persetujuan dari admin.</p>
                                    @else
                                        <p class="text-muted">Pengajuan izin Anda sedang menunggu persetujuan dari admin.</p>
                                    @endif
                                    
                                    @if($todayAttendance->note)
                                        <div class="mt-3 p-3 bg-light rounded-3">
                                            <div class="text-muted small">Keterangan:</div>
                                            <div class="fw-semibold">{{ $todayAttendance->note }}</div>
                                        </div>
                                    @endif
                                </div>

                            @elseif ($todayAttendance->status && in_array($todayAttendance->status->name, ['approved', 'sick', 'excused', 'leave']))
                                {{-- Approved Leave --}}
                                <div class="text-center">
                                    @php
                                        $statusIcons = [
                                            'sick' => 'bx-plus-medical',
                                            'excused' => 'bx-info-circle', 
                                            'leave' => 'bx-calendar-minus',
                                            'approved' => 'bx-check-circle'
                                        ];
                                        $icon = $statusIcons[$todayAttendance->status->name] ?? 'bx-info-circle';
                                        $statusName = $todayAttendance->request_type ?? $todayAttendance->status->name;
                                    @endphp
                                    <div class="bg-info-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                         style="width: 80px; height: 80px;">
                                        <i class="bx {{ $icon }} text-info" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <h5 class="fw-bold text-info mb-2">{{ ucfirst($statusName) }} Disetujui</h5>
                                    <p class="text-muted">Pengajuan {{ strtolower($statusName) }} Anda telah disetujui oleh admin.</p>
                                    
                                    @if($todayAttendance->note)
                                        <div class="mt-3 p-3 bg-light rounded-3">
                                            <div class="text-muted small">Keterangan:</div>
                                            <div class="fw-semibold">{{ $todayAttendance->note }}</div>
                                        </div>
                                    @endif
                                    
                                    @if($todayAttendance->approved_at)
                                        <div class="mt-2">
                                            <small class="text-success">
                                                <i class="bx bx-check-circle me-1"></i>
                                                Disetujui pada {{ \Carbon\Carbon::parse($todayAttendance->approved_at)->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                    @endif
                                </div>

                            @elseif ($todayAttendance->status && $todayAttendance->status->name === 'rejected')
                                {{-- Rejected Leave --}}
                                <div class="text-center">
                                    <div class="bg-danger-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                         style="width: 80px; height: 80px;">
                                        <i class="bx bx-x-circle text-danger" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <h5 class="fw-bold text-danger mb-2">Pengajuan Ditolak</h5>
                                    <p class="text-muted">Pengajuan izin Anda ditolak oleh admin.</p>
                                    
                                    @if($todayAttendance->rejection_reason)
                                        <div class="mt-3 p-3 bg-danger-subtle rounded-3">
                                            <div class="text-danger small fw-semibold">Alasan Penolakan:</div>
                                            <div class="text-danger">{{ $todayAttendance->rejection_reason }}</div>
                                        </div>
                                    @endif
                                </div>

                            @elseif ($todayAttendance->time_in && !$todayAttendance->time_out)
                                {{-- Clock Out Form --}}
                                <div class="mb-4">
                                    <div class="alert alert-success border-0 rounded-3 p-4 mb-4">
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <i class="bx bx-check-circle me-2 fs-3"></i>
                                            <h5 class="mb-0 fw-bold">Absensi Masuk Berhasil!</h5>
                                        </div>
                                        <p class="mb-0">Tercatat masuk pada jam: <strong class="text-success">{{ \Carbon\Carbon::parse($todayAttendance->time_in)->format('H:i:s') }}</strong></p>
                                    </div>
                                    
                                    <hr class="my-4">
                                    
                                    <p class="text-muted mb-4">Silakan tekan tombol di bawah ini untuk melakukan absensi pulang.</p>
                                    
                                    <form action="{{ route('attendances.clockout') }}" method="POST" class="attendance-form">
                                        @csrf
                                        <input type="hidden" name="latitude" class="latitude-input">
                                        <input type="hidden" name="longitude" class="longitude-input">
                                        <input type="hidden" name="accuracy" class="accuracy-input">
                                        
                                        {{-- GPS Status Display for Checkout --}}
                                        <div class="mb-3 text-center">
                                            <div class="gps-status">
                                                <i class="bx bx-loader-alt bx-spin text-warning"></i> Mencari GPS...
                                            </div>
                                            <div class="current-location-info"></div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-danger btn-lg px-5 py-3 rounded-pill shadow-sm">
                                            <i class="bx bx-log-out me-2"></i>
                                            Absen Pulang
                                        </button>
                                    </form>
                                </div>

                            @else
                                {{-- Completed Message --}}
                                <div class="text-center">
                                    <div class="icon-circle icon-circle-success">
                                        <i class="bx bx-check-double attendance-icon success"></i>
                                        <i class="fas fa-check-double attendance-icon success" style="display: none;"></i>
                                        <span class="attendance-icon success" style="display: none; font-size: 2.5rem;">✅</span>
                                    </div>
                                    <h5 class="fw-bold text-success mb-2">Absensi Hari Ini Selesai</h5>
                                    <p class="text-muted">Terima kasih, Anda sudah menyelesaikan absensi untuk hari ini.</p>
                                    
                                    <div class="row mt-4">
                                        <div class="col-6">
                                            <div class="p-3 bg-light rounded-3">
                                                <div class="text-muted small">Masuk</div>
                                                <div class="fw-bold">{{ \Carbon\Carbon::parse($todayAttendance->time_in)->format('H:i') }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-3 bg-light rounded-3">
                                                <div class="text-muted small">Pulang</div>
                                                <div class="fw-bold">{{ \Carbon\Carbon::parse($todayAttendance->time_out)->format('H:i') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab History --}}
        <div class="tab-pane fade" id="tab-history" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom-0 p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-info-subtle rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px;">
                            <i class="bx bx-history text-info fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold">Riwayat Kehadiran</h5>
                            <p class="text-muted mb-0 small">Daftar absensi kehadiran Anda</p>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 fw-semibold px-4 py-3">Tanggal</th>
                                    <th class="border-0 fw-semibold py-3">Status</th>
                                    <th class="border-0 fw-semibold py-3">Jam Masuk</th>
                                    <th class="border-0 fw-semibold py-3">Jam Pulang</th>
                                    <th class="border-0 fw-semibold py-3">Shift</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($history as $attendance)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="fw-semibold">{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('l') }}</small>
                                    </td>
                                    <td class="py-3">
                                        @if($attendance->status)
                                            @php
                                                $statusColors = [
                                                    'present' => 'success',
                                                    'late' => 'warning', 
                                                    'absent' => 'danger',
                                                    'sick' => 'info',
                                                    'excused' => 'secondary',
                                                    'pending' => 'warning',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger'
                                                ];
                                                $statusColor = $statusColors[$attendance->status->name] ?? 'secondary';
                                            @endphp
                                            
                                            <div class="d-flex flex-column">
                                                <span class="badge bg-{{ $statusColor }} px-3 py-2 rounded-pill mb-1">
                                                    {{ ucfirst($attendance->status->name) }}
                                                </span>
                                                
                                                @if($attendance->request_type && $attendance->status->name === 'pending')
                                                    <small class="text-muted">
                                                        Pengajuan: <strong>{{ ucfirst($attendance->request_type) }}</strong>
                                                    </small>
                                                @elseif($attendance->request_type && in_array($attendance->status->name, ['approved', 'rejected']))
                                                    <small class="text-muted">
                                                        {{ ucfirst($attendance->request_type) }} - {{ $attendance->status->name === 'approved' ? 'Disetujui' : 'Ditolak' }}
                                                    </small>
                                                @endif
                                                
                                                @if($attendance->note)
                                                    <small class="text-muted mt-1">
                                                        <i class="bx bx-note me-1"></i>
                                                        {{ Str::limit($attendance->note, 50) }}
                                                    </small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2 rounded-pill">
                                                <i class="bx bx-question-mark me-1"></i>
                                                Unknown
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-semibold">{{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '-' }}</div>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-semibold">{{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('H:i') : '-' }}</div>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge bg-light text-dark rounded-pill">{{ $attendance->shift->name ?? '-' }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bx bx-calendar-x fs-1 mb-2 d-block"></i>
                                            <p class="mb-0">Belum ada riwayat absensi</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @if ($history->hasPages())
                <div class="card-footer bg-transparent border-top p-4">
                    <div class="d-flex justify-content-center">
                        {{ $history->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Tab Request --}}
        <div class="tab-pane fade" id="tab-request" role="tabpanel">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-bottom-0 p-4">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle icon-circle-warning" style="width: 50px !important; height: 50px !important;">
                                    <i class="bx bx-envelope attendance-icon warning" style="font-size: 1.5rem !important;"></i>
                                    <i class="fas fa-envelope attendance-icon warning" style="display: none; font-size: 1.5rem !important;"></i>
                                    <span class="attendance-icon warning" style="display: none; font-size: 1.5rem;">📧</span>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-1 fw-bold">Pengajuan Izin / Sakit</h5>
                                    <p class="text-muted mb-0 small">Ajukan permohonan izin atau sakit untuk hari ini</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body p-4">
                            <div class="alert alert-info border-0 rounded-3 p-4 mb-4">
                                <div class="d-flex align-items-start">
                                    <i class="bx bx-info-circle me-3 fs-4 text-info"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1">Informasi Penting</h6>
                                        <p class="mb-0 small">Jika tidak dapat masuk kerja pada hari ini, silakan isi form pengajuan di bawah ini dengan lengkap dan jelas.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <form action="{{ route('attendances.request.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="mb-4">
                                    <label for="status_id" class="form-label fw-semibold">
                                        <i class="bx bx-category me-1"></i>
                                        Jenis Pengajuan <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select form-select-lg rounded-3 @error('status_id') is-invalid @enderror" 
                                            name="status_id" required>
                                        <option value="">-- Pilih Jenis Pengajuan --</option>
                                        @foreach($availableStatuses as $status)
                                            <option value="{{ $status->id }}" @if(old('status_id') == $status->id) selected @endif>
                                                {{ ucfirst($status->name) }}
                                                @if($status->description)
                                                    - {{ $status->description }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="note" class="form-label fw-semibold">
                                        <i class="bx bx-note me-1"></i>
                                        Keterangan <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control rounded-3 @error('note') is-invalid @enderror" 
                                              name="note" rows="4" required 
                                              placeholder="Jelaskan alasan dan keterangan lengkap...">{{ old('note') }}</textarea>
                                    @error('note')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="attachment" class="form-label fw-semibold">
                                        <i class="bx bx-paperclip me-1"></i>
                                        Lampiran (Opsional)
                                    </label>
                                    <input class="form-control rounded-3 @error('attachment') is-invalid @enderror" 
                                           type="file" name="attachment" accept="image/*,.pdf,.doc,.docx">
                                    <small class="form-text text-muted">
                                        <i class="bx bx-info-circle me-1"></i>
                                        Format yang didukung: JPG, PNG, PDF, DOC, DOCX (Max: 2MB)
                                    </small>
                                    @error('attachment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-info btn-lg py-3 rounded-pill shadow-sm">
                                        <i class="bx bx-send me-2"></i>
                                        Kirim Pengajuan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-pills .nav-link {
        background-color: transparent;
        border: 2px solid transparent;
        transition: all 0.3s ease;
        color: #6c757d;
    }
    
    .nav-pills .nav-link:hover {
        background-color: rgba(13, 110, 253, 0.1);
        border-color: rgba(13, 110, 253, 0.2);
        color: #0d6efd;
    }
    
    .nav-pills .nav-link.active {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white !important;
        box-shadow: 0 0.125rem 0.25rem rgba(13, 110, 253, 0.3);
    }
    
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .btn {
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-1px);
    }
    
    .bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }
    
    .bg-success-subtle {
        background-color: rgba(25, 135, 84, 0.1) !important;
    }
    
    .bg-info-subtle {
        background-color: rgba(13, 202, 240, 0.1) !important;
    }
    
    /* Enhanced icon background styles */
    .bg-primary-icon {
        background-color: rgba(13, 110, 253, 0.1) !important;
        border: 2px solid rgba(13, 110, 253, 0.2) !important;
    }
    
    .bg-success-icon {
        background-color: rgba(25, 135, 84, 0.1) !important;
        border: 2px solid rgba(25, 135, 84, 0.2) !important;
    }
    
    .bg-warning-icon {
        background-color: rgba(255, 193, 7, 0.1) !important;
        border: 2px solid rgba(255, 193, 7, 0.2) !important;
    }
    
    .bg-danger-icon {
        background-color: rgba(220, 53, 69, 0.1) !important;
        border: 2px solid rgba(220, 53, 69, 0.2) !important;
    }
    
    /* Fallback for subtle backgrounds */
    .bg-warning-subtle {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
    
    .bg-danger-subtle {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }
    
    .bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }
    
    .bg-success-subtle {
        background-color: rgba(25, 135, 84, 0.1) !important;
    }
    
    /* Enhanced icon circle styles */
    .icon-circle {
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .icon-circle:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    /* Ensure all icon variations are properly sized */
    .icon-circle .attendance-icon {
        line-height: 1 !important;
        vertical-align: middle !important;
    }
    
    /* Force visibility for emoji fallbacks */
    .icon-circle span.attendance-icon {
        font-family: "Apple Color Emoji", "Segoe UI Emoji", "Noto Color Emoji", sans-serif !important;
        font-style: normal !important;
        text-rendering: optimizeLegibility !important;
    }
    
    #current-time {
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .input-group-text {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
    }
    
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }
    
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }
</style>

@endsection

@push('scripts')
{{-- QR Code Scanner Library --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Real-time clock
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('current-time').textContent = timeString;
    }
    
    updateClock();
    setInterval(updateClock, 1000);

    // Get and fill GPS location for all forms with enhanced monitoring
    let currentPosition = null;
    let gpsWatchId = null;
    
    function updateGPSStatus(status, accuracy = null, error = null) {
        const statusElements = document.querySelectorAll('.gps-status');
        statusElements.forEach(element => {
            if (status === 'searching') {
                element.innerHTML = '<i class="bx bx-loader-alt bx-spin text-warning"></i> Mencari GPS...';
                element.className = 'gps-status text-warning small';
            } else if (status === 'found') {
                const quality = accuracy <= 5 ? 'Sangat Baik' : 
                               accuracy <= 15 ? 'Baik' : 
                               accuracy <= 50 ? 'Cukup' : 'Buruk';
                const color = accuracy <= 15 ? 'success' : accuracy <= 50 ? 'warning' : 'danger';
                element.innerHTML = `<i class="bx bx-wifi text-${color}"></i> GPS: ${quality} (${Math.round(accuracy)}m)`;
                element.className = `gps-status text-${color} small`;
            } else if (status === 'error') {
                element.innerHTML = `<i class="bx bx-wifi-off text-danger"></i> GPS Error: ${error}`;
                element.className = 'gps-status text-danger small';
            }
        });
    }

    function getAndFillLocation() {
        if (!navigator.geolocation) {
            updateGPSStatus('error', null, 'GPS tidak tersedia');
            return;
        }

        updateGPSStatus('searching');

        // Stop previous watch if exists
        if (gpsWatchId) {
            navigator.geolocation.clearWatch(gpsWatchId);
        }

        const options = {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
        };

        gpsWatchId = navigator.geolocation.watchPosition(
            function(position) {
                currentPosition = position;
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const accuracy = position.coords.accuracy;

                // Update all latitude inputs
                document.querySelectorAll('.latitude-input').forEach(input => {
                    input.value = lat;
                });
                
                // Update all longitude inputs
                document.querySelectorAll('.longitude-input').forEach(input => {
                    input.value = lng;
                });

                // Update all accuracy inputs
                document.querySelectorAll('.accuracy-input').forEach(input => {
                    input.value = accuracy;
                });

                // Update GPS status display
                updateGPSStatus('found', accuracy);

                // Update location info display
                const locationInfoElements = document.querySelectorAll('.current-location-info');
                locationInfoElements.forEach(element => {
                    element.innerHTML = `
                        <div class="small text-muted mt-2">
                            <i class="bx bx-current-location me-1"></i>
                            Lokasi Saat Ini: ${lat.toFixed(6)}, ${lng.toFixed(6)}
                            <br>
                            <i class="bx bx-wifi me-1"></i>
                            Akurasi GPS: ${Math.round(accuracy)} meter
                        </div>
                    `;
                });

                // Show warning if accuracy is poor
                if (accuracy > 50) {
                    showGPSWarning(accuracy);
                }
            },
            function(error) {
                let errorMsg = 'Tidak diketahui';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMsg = 'Akses lokasi ditolak';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMsg = 'Lokasi tidak tersedia';
                        break;
                    case error.TIMEOUT:
                        errorMsg = 'Timeout mencari lokasi';
                        break;
                }
                updateGPSStatus('error', null, errorMsg);
            },
            options
        );
    }

    function showGPSWarning(accuracy) {
        // Remove existing warning first
        const existingWarning = document.querySelector('.gps-warning-alert');
        if (existingWarning) {
            existingWarning.remove();
        }

        // Determine warning level and message based on accuracy
        let alertClass, iconClass, warningTitle, warningMessage;
        
        if (accuracy > 200) {
            alertClass = 'alert-danger';
            iconClass = 'bx-wifi-off';
            warningTitle = 'GPS Sangat Buruk!';
            warningMessage = `Akurasi GPS ${Math.round(accuracy)} meter. Absensi mungkin gagal. Coba pindah ke tempat terbuka.`;
        } else if (accuracy > 100) {
            alertClass = 'alert-warning';
            iconClass = 'bx-wifi-1';
            warningTitle = 'GPS Kurang Baik';
            warningMessage = `Akurasi GPS ${Math.round(accuracy)} meter. Untuk hasil optimal, pindah ke area yang lebih terbuka.`;
        } else if (accuracy > 50) {
            alertClass = 'alert-info';
            iconClass = 'bx-wifi-2';
            warningTitle = 'GPS Cukup';
            warningMessage = `Akurasi GPS ${Math.round(accuracy)} meter. Sinyal GPS cukup untuk absensi.`;
        } else {
            // Good GPS, no warning needed
            return;
        }

        const warningDiv = document.createElement('div');
        warningDiv.className = `alert ${alertClass} alert-dismissible fade show border-0 shadow-sm gps-warning-alert`;
        warningDiv.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bx ${iconClass} me-2 fs-4"></i>
                <div>
                    <strong>${warningTitle}</strong> 
                    ${warningMessage}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
            
        // Insert after existing alerts
        const firstCard = document.querySelector('.card');
        if (firstCard) {
            firstCard.parentNode.insertBefore(warningDiv, firstCard);
        }
    }
    
    getAndFillLocation();

    // QR Code Scanner Logic
    function onScanSuccess(decodedText, decodedResult) {
        console.log(`Scan result: ${decodedText}`, decodedResult);
        
        // Fill input field with scan result
        const barcodeInput = document.getElementById('barcode_value');
        if (barcodeInput) {
            barcodeInput.value = decodedText;
        }
        
        // Show success notification
        const resultDiv = document.getElementById('qr-reader-results');
        if (resultDiv) {
            resultDiv.innerHTML = `
                <div class="alert alert-success border-0 rounded-3 fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-check-circle me-2 fs-4"></i>
                        <div>
                            <strong>Scan Berhasil!</strong><br>
                            <small>Kode: ${decodedText}</small>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Auto-hide notification after 3 seconds
        setTimeout(() => {
            if (resultDiv) {
                const alert = resultDiv.querySelector('.alert');
                if (alert) {
                    alert.classList.remove('show');
                    setTimeout(() => {
                        alert.remove();
                    }, 150);
                }
            }
        }, 3000);
        
        // Stop scanner after successful scan
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
        }
    }

    function onScanFailure(error) {
        // Handle scan failure silently
    }

    // Initialize scanner only if element exists (on clock-in page)
    if (document.getElementById('qr-reader')) {
        var html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", 
            { 
                fps: 10, 
                qrbox: 250,
                aspectRatio: 1.0,
                showTorchButtonIfSupported: true
            },
            /* verbose= */ false
        );
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    }

    // Handle tab activation after redirect
    @if(session('active_tab') === 'request' || $errors->any())
        const triggerEl = document.querySelector('button[data-bs-target="#tab-request"]');
        if(triggerEl) {
            const tab = new bootstrap.Tab(triggerEl);
            tab.show();
        }
    @endif

    // Form validation enhancements
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-2"></i>Memproses...';
                
                // Re-enable after 5 seconds as fallback
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitBtn.getAttribute('data-original-text') || 'Submit';
                }, 5000);
            }
        });
    });

    // Store original button text
    document.querySelectorAll('button[type="submit"]').forEach(btn => {
        btn.setAttribute('data-original-text', btn.innerHTML);
    });
    
    // Icon fallback system
    function checkAndFallbackIcons() {
        setTimeout(() => {
            // Check if Boxicons loaded
            const testIcon = document.createElement('i');
            testIcon.className = 'bx bx-fingerprint';
            testIcon.style.position = 'absolute';
            testIcon.style.left = '-9999px';
            document.body.appendChild(testIcon);
            
            const computedStyle = window.getComputedStyle(testIcon, ':before');
            const content = computedStyle.getPropertyValue('content');
            
            document.body.removeChild(testIcon);
            
            // If Boxicons didn't load properly, show Font Awesome fallback
            if (!content || content === 'none' || content === '""') {
                console.log('Boxicons not loaded, switching to Font Awesome');
                document.querySelectorAll('.bx').forEach(icon => {
                    icon.style.display = 'none';
                    const parent = icon.parentElement;
                    const faIcon = parent.querySelector('.fas, .far, .fab');
                    if (faIcon) {
                        faIcon.style.display = 'block';
                    } else {
                        // Show emoji fallback
                        const spanIcon = parent.querySelector('span.attendance-icon');
                        if (spanIcon) {
                            spanIcon.style.display = 'block';
                        }
                    }
                });
            } else {
                console.log('Boxicons loaded successfully');
            }
        }, 1000);
    }
    
    // Run icon check when page loads
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkAndFallbackIcons);
    } else {
        checkAndFallbackIcons();
    }
});
</script>
@endpush