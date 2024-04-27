@extends('layouts.index')

@section('title', 'Profil Pengguna')

@section('content')
    <style>
        .signature {
            margin-bottom: 10px;
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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column mb-3">
                        <div class="card-title mb-auto">
                            <h5 class="mb-1 text-nowrap">Profil</h5>
                            <small>Detail user profil</small>
                        </div>
                    </div>

                    <form id="form" class="mb-3" action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="fullname" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="fullname" name="fullname"
                                placeholder="Enter your fullname" autofocus value="{{ Auth::user()->fullname }}" />
                            @error('fullname')
                                <small class="text-danger" role="alert">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                placeholder="Enter your username" autofocus value="{{ Auth::user()->username }}" />
                            @error('username')
                                <small class="text-danger" role="alert">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control" id="email" name="email"
                                placeholder="Enter your email" value="{{ Auth::user()->email }}" />
                            @error('email')
                                <small class="text-danger" role="alert">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <div class="mb-3 form-password-toggle">
                            <label class="form-label" for="password">Kata Sandi <small>(Kosongkan jika tidak ubah
                                    password)</small></label>
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
                            <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password_confirmation" class="form-control"
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


                        @if (Auth::user()->role === 'Gudep')
                            <div class="mb-3 gudep-form" style="display: none">
                                <label for="nama_mabigus" class="form-label">Nama Mabigus</label>
                                <input type="text" class="form-control" id="nama_mabigus" name="nama_mabigus"
                                    placeholder="Masukkan nama mabigus" value="{{ Auth::user()->nama_mabigus }}" />
                                @error('nama_mabigus')
                                    <small class="text-danger" role="alert">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                        @endif

                        @if (Auth::user()->role === 'Pengurus')
                            <div class="mb-3 pengurus-form" style="display: none">
                                <label for="nama_pengaju" class="form-label">Nama Pengaju</label>
                                <input type="text" class="form-control" id="nama_pengaju" name="nama_pengaju"
                                    placeholder="Masukkan nama pengaju" value="{{ Auth::user()->nama_pengaju }}" />
                                @error('nama_pengaju')
                                    <small class="text-danger" role="alert">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="mb-3 pengurus-form" style="display: none">
                                <label for="jabatan" class="form-label">Jabatan</label>
                                <input type="text" class="form-control" id="jabatan" name="jabatan"
                                    placeholder="Masukkan jabatan" value="{{ Auth::user()->jabatan }}" />
                                @error('fullname')
                                    <small class="text-danger" role="alert">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                        @endif

                        @if (Auth::user()->role !== 'Admin')
                            <div class="mb-3">
                                <label for="no_hp" class="form-label">No HP</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp"
                                    placeholder="Masukkan nomor HP anda" value="{{ Auth::user()->detail->no_hp }}" />
                                @error('no_hp')
                                    <small class="text-danger" role="alert">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="d-flex flex-column">
                                    <label for="ttd" class="form-label">TTD</label>
                                    <div class="mt-2 d-flex align-items-center">
                                        <img src="{{ Storage::url(Auth::user()->detail->ttd) }}" alt=""
                                            width="100" style="border-radius: 4px">
                                        <span class="ms-3 text-primary" role="button" id="ttd-button">Ubah</span>
                                    </div>
                                    <div id="signature" class="mt-3">
                                        <small><i>Gambar tanda tangan baru hanya jika ingin ubah tanda tangan</i></small>
                                        <div class="signature mt-2" style="max-width: 350px">
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

                                    </div>
                                </div>
                                @error('ttd')
                                    <small class="text-danger" role="alert">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>
                        @endif

                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary d-grid" id="btn-form">Ubah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@if (Auth::user()->role !== 'Admin')
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

                $('#ttd-button').on('click', function() {
                    if ($('#signature').is(':hidden')) {
                        $('#signature').show()
                        $('#ttd-button').text('Batal')
                    } else {
                        $('#signature').hide()
                        $('#ttd-button').text('Ubah')
                    }
                });
            })
        </script>
    @endsection
@endif
