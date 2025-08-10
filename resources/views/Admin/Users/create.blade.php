<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pengguna Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Nama Lengkap</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Divisi</label><select name="division_id" class="form-select" required><option value="">Pilih Divisi</option>@foreach($divisions as $d)<option value="{{ $d->id }}" @if(old('division_id') == $d->id) selected @endif>{{ $d->name }}</option>@endforeach</select></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Jabatan</label><select name="job_title_id" class="form-select" required><option value="">Pilih Jabatan</option>@foreach($jobTitles as $j)<option value="{{ $j->id }}" @if(old('job_title_id') == $j->id) selected @endif>{{ $j->name }}</option>@endforeach</select></div>
                        
                        {{-- DROPDOWN BARU UNTUK SHIFT --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Shift Kerja</label>
                            <select name="shift_id" class="form-select" required>
                                <option value="">Pilih Shift</option>
                                @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" @if(old('shift_id') == $shift->id) selected @endif>{{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3"><label class="form-label">Grup</label><select name="group" class="form-select" required><option value="user" @if(old('group') == 'user') selected @endif>User</option><option value="admin" @if(old('group') == 'admin') selected @endif>Admin</option></select></div>
                        <hr class="my-3">
                        <div class="col-md-6 mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Konfirmasi Password</label><input type="password" name="password_confirmation" class="form-control" required></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>