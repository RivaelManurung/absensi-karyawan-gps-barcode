@extends('User.Layout.single-page')

@section('title', 'Ubah Password')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Ubah Password</h4>
            <p class="text-muted mb-0">Pastikan akun Anda tetap aman dengan password yang kuat</p>
        </div>
        <a href="{{ route('user.profile.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i>Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-lock me-2"></i>Ganti Password
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Security Notice -->
                    <div class="alert alert-info d-flex align-items-start mb-4">
                        <i class="bx bx-info-circle fs-4 me-3 mt-1"></i>
                        <div>
                            <h6 class="alert-heading mb-2">Tips Keamanan</h6>
                            <ul class="mb-0 ps-3">
                                <li>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol</li>
                                <li>Minimal 8 karakter panjang</li>
                                <li>Jangan gunakan informasi personal yang mudah ditebak</li>
                                <li>Gunakan password yang unik dan berbeda dari akun lain</li>
                            </ul>
                        </div>
                    </div>

                    <form action="{{ route('user.profile.update-password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Current Password -->
                        <div class="mb-3">
                            <label class="form-label" for="current_password">
                                Password Saat Ini <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                    <i class="bx bx-show" id="current_password_icon"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label class="form-label" for="new_password">
                                Password Baru <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                       id="new_password" name="new_password" required minlength="8"
                                       onkeyup="checkPasswordStrength(this.value)">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                    <i class="bx bx-show" id="new_password_icon"></i>
                                </button>
                            </div>
                            @error('new_password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @endif
                            
                            <!-- Password Strength Indicator -->
                            <div class="mt-2">
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar" id="password_strength_bar" style="width: 0%"></div>
                                </div>
                                <small class="text-muted" id="password_strength_text">Minimal 8 karakter</small>
                            </div>
                        </div>

                        <!-- Confirm New Password -->
                        <div class="mb-4">
                            <label class="form-label" for="new_password_confirmation">
                                Konfirmasi Password Baru <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" 
                                       id="new_password_confirmation" name="new_password_confirmation" required
                                       onkeyup="checkPasswordMatch()">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_confirmation')">
                                    <i class="bx bx-show" id="new_password_confirmation_icon"></i>
                                </button>
                            </div>
                            <div class="mt-1">
                                <small class="text-muted" id="password_match_text"></small>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('user.profile.index') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-x me-1"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary" id="submit_btn" disabled>
                                <i class="bx bx-save me-1"></i>Ubah Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('bx-show');
        icon.classList.add('bx-hide');
    } else {
        field.type = 'password';
        icon.classList.remove('bx-hide');
        icon.classList.add('bx-show');
    }
}

function checkPasswordStrength(password) {
    const strengthBar = document.getElementById('password_strength_bar');
    const strengthText = document.getElementById('password_strength_text');
    
    let strength = 0;
    let feedback = '';
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    switch (strength) {
        case 0:
        case 1:
            strengthBar.style.width = '20%';
            strengthBar.className = 'progress-bar bg-danger';
            feedback = 'Sangat Lemah';
            break;
        case 2:
            strengthBar.style.width = '40%';
            strengthBar.className = 'progress-bar bg-warning';
            feedback = 'Lemah';
            break;
        case 3:
            strengthBar.style.width = '60%';
            strengthBar.className = 'progress-bar bg-info';
            feedback = 'Sedang';
            break;
        case 4:
            strengthBar.style.width = '80%';
            strengthBar.className = 'progress-bar bg-primary';
            feedback = 'Kuat';
            break;
        case 5:
            strengthBar.style.width = '100%';
            strengthBar.className = 'progress-bar bg-success';
            feedback = 'Sangat Kuat';
            break;
    }
    
    strengthText.textContent = feedback;
    checkPasswordMatch();
}

function checkPasswordMatch() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('new_password_confirmation').value;
    const matchText = document.getElementById('password_match_text');
    const submitBtn = document.getElementById('submit_btn');
    
    if (confirmPassword === '') {
        matchText.textContent = '';
        submitBtn.disabled = true;
        return;
    }
    
    if (newPassword === confirmPassword && newPassword.length >= 8) {
        matchText.textContent = '✓ Password cocok';
        matchText.className = 'text-success';
        submitBtn.disabled = false;
    } else {
        matchText.textContent = '✗ Password tidak cocok';
        matchText.className = 'text-danger';
        submitBtn.disabled = true;
    }
}
</script>
@endsection
