@extends('layouts.index')

@section('title', 'Daftar Pengguna - Ubah')

@section('content')
    <div class="container-xxl flex-grow-1 ">


        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light"> <a class="text-muted" href="{{ route('pengguna.index') }}">Daftar
                    Pengguna</a> / </span>
            Ubah
        </h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-header">Ubah Pengguna</h5>
                <div class="card-header">
                    <a class="btn btn-label-secondary" href="{{ route('pengguna.index') }}">Kembali</a>
                </div>
            </div>

            <div class="card-body mt-0 pt-0">
                <form id="form" class="mb-3" action="{{ route('pengguna.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="fullname" class="form-label">Fullname</label>
                        <input type="text" class="form-control" id="fullname" name="fullname"
                            placeholder="Enter your fullname" autofocus value="{{ $user->fullname }}" />
                        @error('fullname')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                            placeholder="Enter your username" autofocus value="{{ $user->username }}" />
                        @error('username')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email" name="email"
                            placeholder="Enter your email" value="{{ $user->email }}" />
                        @error('email')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3 form-password-toggle">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group input-group-merge">
                            <input type="password" id="password" class="form-control" name="password"
                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                aria-describedby="password" />
                            <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                        </div>
                        @error('password')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3 form-password-toggle">
                        <label class="form-label" for="password_confirmation">Password Confirmation</label>
                        <div class="input-group input-group-merge">
                            <input type="password_confirmation" id="password_confirmation" class="form-control"
                                name="password_confirmation"
                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                aria-describedby="password_confirmation" />
                            <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                        </div>
                        @error('password_confirmation')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    @if ($user->role !== 'Admin')
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" aria-label="Default select example" name="role"
                                value="{{ $user->role }}">
                                <option selected>Pilih</option>
                                <option value="Gudep" {{ $user->role === 'Gudep' ? 'selected' : null }}>Gugus Depan
                                </option>
                                <option value="Pengurus" {{ $user->role === 'Pengurus' ? 'selected' : null }}>Pengurus
                                </option>
                                <option value="Ketua" {{ $user->role === 'Ketua' ? 'selected' : null }}>Ketua
                                </option>
                            </select>
                            @error('role')
                                <small class="text-danger" role="alert">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <div class="mb-3 gudep-form" style="display: none">
                            <label for="nama_mabigus" class="form-label">Nama Mabigus</label>
                            <input type="text" class="form-control" id="nama_mabigus" name="nama_mabigus"
                                placeholder="Masukkan nama mabigus" value="{{ $user->detail->nama_mabigus }}" />
                            @error('nama_mabigus')
                                <small class="text-danger" role="alert">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <div class="mb-3 pengurus-form" style="display: none">
                            <label for="nama_pengaju" class="form-label">Nama Pengaju</label>
                            <input type="text" class="form-control" id="nama_pengaju" name="nama_pengaju"
                                placeholder="Masukkan nama pengaju" value="{{ $user->detail->nama_pengaju }}" />
                            @error('nama_pengaju')
                                <small class="text-danger" role="alert">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <div class="mb-3 pengurus-form" style="display: none">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <input type="text" class="form-control" id="jabatan" name="jabatan"
                                placeholder="Masukkan jabatan" value="{{ $user->detail->jabatan }}" />
                            @error('fullname')
                                <small class="text-danger" role="alert">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="no_hp" class="form-label">No HP</label>
                            <input type="text" class="form-control" id="no_hp" name="no_hp"
                                placeholder="Masukkan nomor HP anda" value="{{ $user->detail->no_hp }}" />
                            @error('no_hp')
                                <small class="text-danger" role="alert">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="role" aria-label="Default select example" name="is_active"
                            value="{{ $user->is_active }}">
                            <option selected>Pilih</option>
                            <option value="1" {{ $user->is_active === 1 ? 'selected' : null }}>Aktif
                            </option>
                            <option value="0" {{ $user->is_active === 0 ? 'selected' : null }}>Nonaktif
                            </option>
                        </select>
                        @error('is_active')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end mt-5">
                        <button class="btn btn-primary d-grid" id="btn-form">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        <!--/ Basic Bootstrap Table -->

        <hr class="my-5">

    </div>
@endsection

@section('scripts')
    <script>
        const role = $('#role').val()

        if (role === 'Gudep') {
            $('.gudep-form').show()
            $('.pengurus-form').hide()
        } else if (role === 'Pengurus') {
            $('.gudep-form').hide()
            $('.pengurus-form').show()
        } else {
            $('.gudep-form').hide()
            $('.pengurus-form').hide()
        }

        $(document).ready(function() {
            $('#role').on('change', function(e) {
                const role = e.target.value

                if (role === 'Gudep') {
                    $('.gudep-form').show()
                    $('.pengurus-form').hide()
                } else if (role === 'Pengurus') {
                    $('.gudep-form').hide()
                    $('.pengurus-form').show()
                } else {
                    $('.gudep-form').hide()
                    $('.pengurus-form').hide()
                }
            })
        })
    </script>
@endsection
