@extends('auth.layouts.app')

@section('title', 'Register')

@section('content')
    <style>
        .signature {
            margin-bottom: 10px
        }

        .wrapper {
            position: relative;
            width: 100%;
            height: 150px;
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
            border-radius: 6px;
            margin-bottom: 10px
        }

        .signature-pad {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 150px;
            border-radius: 6px;
        }
    </style>

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                <!-- Register Card -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-4 mt-2">
                            <a href="index.html" class="app-brand-link gap-2">
                                <img src="{{ asset('logo.png') }}" alt="" width="50">
                                <span class="app-brand-text demo text-body fw-bold ms-1">SIKEKWARMA</span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-1 pt-2">Selamat Datang Di</h4>
                        <p class="mb-4">Sistem Informasi Kegiatan Kwartir Ranting Maos</p>

                        <form id="form" class="mb-3" action="{{ route('register.create') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="fullname" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="fullname" name="fullname"
                                    placeholder="Enter your fullname" autofocus value="{{ old('fullname') }}" />
                                @error('fullname')
                                    <small class="text-danger" role="alert">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    placeholder="Enter your username" autofocus value="{{ old('username') }}" />
                                @error('username')
                                    <small class="text-danger" role="alert">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="Enter your email" value="{{ old('email') }}" />
                                @error('email')
                                    <small class="text-danger" role="alert">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="mb-3 form-password-toggle">
                                <label class="form-label" for="password">Kata Sandi</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" value="{{ old('password') }}" />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                                @error('password')
                                    <small class="text-danger" role="alert">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="mb-3 form-password-toggle">
                                <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password_confirmation" class="form-control"
                                        name="password_confirmation"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password_confirmation"
                                        value="{{ old('password_confirmation') }}" />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                                @error('password_confirmation')
                                    <small class="text-danger" role="alert">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" aria-label="Default select example"
                                    name="role" value="{{ old('role') }}">
                                    <option selected>Pilih</option>
                                    <option value="Gudep" {{ old('role') === 'Gudep' ? 'Selected' : null }}>Gugus Depan
                                    </option>
                                    <option value="Pengurus" {{ old('role') === 'Pengurus' ? 'Selected' : null }}>Pengurus
                                    </option>
                                    <option value="Ketua" {{ old('role') === 'Ketua' ? 'Selected' : null }}>Ketua
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
                                    placeholder="Masukkan nama mabigus" value="{{ old('nama_mabigus') }}" />
                                @error('nama_mabigus')
                                    <small class="text-danger" role="alert">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="mb-3 pengurus-form" style="display: none">
                                <label for="nama_pengaju" class="form-label">Nama Pengaju</label>
                                <input type="text" class="form-control" id="nama_pengaju" name="nama_pengaju"
                                    placeholder="Masukkan nama pengaju" value="{{ old('nama_pengaju') }}" />
                                @error('nama_pengaju')
                                    <small class="text-danger" role="alert">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="mb-3 pengurus-form" style="display: none">
                                <label for="jabatan" class="form-label">Jabatan</label>
                                <input type="text" class="form-control" id="jabatan" name="jabatan"
                                    placeholder="Masukkan jabatan" value="{{ old('jabatan') }}" />
                                @error('fullname')
                                    <small class="text-danger" role="alert">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="no_hp" class="form-label">No HP</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp"
                                    placeholder="Masukkan nomor HP anda" value="{{ old('no_hp') }}" />
                                @error('no_hp')
                                    <small class="text-danger" role="alert">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="ttd" class="form-label">TTD</label>
                                <div class="signature">
                                    <div class="wrapper">
                                        <canvas id="signature-pad" class="signature-pad"></canvas>
                                    </div>
                                    <button class="btn btn-label-secondary btn-small p-1 px-2" id="undo"
                                        type="button">Undo</button>
                                    <button class="btn btn-label-secondary btn-small p-1 px-2" id="clear"
                                        type="button">Clear</button>
                                </div>
                                <small><i>Gambar tanda tangan anda pada kanvas</i></small>
                                <br>
                                @error('ttd')
                                    <small class="text-danger" role="alert">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <button class="btn btn-primary d-grid w-100" id="btn-form">Daftar</button>
                        </form>

                        <p class="text-center">
                            <span>Sudah memiliki akun?</span>
                            <a href="{{ route('login.index') }}">
                                <span>Login disini</span>
                            </a>
                        </p>

                    </div>
                </div>
                <!-- Register Card -->
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var canvas = document.getElementById('signature-pad');
            // Adjust canvas coordinate space taking into account pixel ratio,
            // to make it look crisp on mobile devices.
            // This also causes canvas to be cleared.
            function resizeCanvas() {
                // When zoomed out to less than 100%, for some very strange reason,
                // some browsers report devicePixelRatio as less than 1
                // and only part of the canvas is cleared then.
                var ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
            }

            window.onresize = resizeCanvas;
            resizeCanvas();

            var signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)'
            });

            document.getElementById('clear').addEventListener('click', function() {
                signaturePad.clear();
            });

            document.getElementById('undo').addEventListener('click', function() {
                var data = signaturePad.toData();
                if (data) {
                    data.pop(); // remove the last dot or line
                    signaturePad.fromData(data);
                }
            });


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

            // Example of how to use the getSignatureImage function
            $('#btn-form').on('click', function(e) {
                e.preventDefault();

                var fileInput = $('<input>').attr({
                    type: 'hidden',
                    name: 'ttd',
                    value: signaturePad.isEmpty() ? null : signaturePad.toDataURL()
                });

                $('#form').append(fileInput);
                $('#form').submit();
            });
        })
    </script>
@endsection
