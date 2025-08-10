<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Ubah Data Pengguna</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Nama Lengkap</label><input type="text" id="edit_name" name="name" class="form-control" value="{{ old('name') }}" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Email</label><input type="email" id="edit_email" name="email" class="form-control" value="{{ old('email') }}" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Divisi</label><select id="edit_division_id" name="division_id" class="form-select" required>@foreach($divisions as $d)<option value="{{ $d->id }}" @if(old('division_id') == $d->id) selected @endif>{{ $d->name }}</option>@endforeach</select></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Jabatan</label><select id="edit_job_title_id" name="job_title_id" class="form-select" required>@foreach($jobTitles as $j)<option value="{{ $j->id }}" @if(old('job_title_id') == $j->id) selected @endif>{{ $j->name }}</option>@endforeach</select></div>
                        
                        {{-- DROPDOWN BARU UNTUK SHIFT --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Shift Kerja</label>
                            <select id="edit_shift_id" name="shift_id" class="form-select" required>
                                <option value="">Pilih Shift</option>
                                @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" @if(old('shift_id') == $shift->id) selected @endif>{{ $shift->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3"><label class="form-label">Grup</label><select id="edit_group" name="group" class="form-select" required><option value="user" @if(old('group') == 'user') selected @endif>User</option><option value="admin" @if(old('group') == 'admin') selected @endif>Admin</option><option value="superadmin" @if(old('group') == 'superadmin') selected @endif>Superadmin</option></select></div>
                        <hr class="my-3">
                        <p class="text-muted">Isi password hanya jika ingin mengubahnya.</p>
                        <div class="col-md-6 mb-3"><label class="form-label">Password Baru</label><input type="password" id="edit_password" name="password" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Konfirmasi Password Baru</label><input type="password" id="edit_password_confirmation" name="password_confirmation" class="form-control"></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Perbarui</button></div>
            </form>
        </div>
    </div>
</div>