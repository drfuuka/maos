@extends('layouts.index')

@section('title', 'Laporan Gugus Depan - Ubah')

@section('content')
    <div class="container-xxl flex-grow-1 ">


        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">Laporan / <a class="text-muted"
                    href="{{ route('laporan-pengurus.index') }}">Gugus
                    Depan</a> / </span>
            Ubah
        </h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-header">Ubah Laporan Gugus Depan</h5>
                <div class="card-header">
                    <a class="btn btn-label-secondary" href="{{ route('laporan-pengurus.index') }}">Kembali</a>
                </div>
            </div>

            <div class="card-body mt-0 pt-0">
                <form id="form" class="mb-3" action="{{ route('laporan-pengurus.update', $laporanPengurus->id) }}"
                    enctype="multipart/form-data" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nama_kegiatan" class="form-label">Nama Kegiatan</label>
                        <input type="text" class="form-control" id="nama_kegiatan" name="nama_kegiatan"
                            placeholder="Masukkan nama kegiatan" autofocus value="{{ $laporanPengurus->nama_kegiatan }}" />
                        @error('nama_kegiatan')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_kegiatan" class="form-label">Tanggal Kegiatan</label>
                        <input type="date" class="form-control" id="tanggal_kegiatan" name="tanggal_kegiatan"
                            placeholder="Masukkan tanggal kegiatan" autofocus
                            value="{{ $laporanPengurus->tanggal_kegiatan }}" />
                        @error('tanggal_kegiatan')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tempat_kegiatan" class="form-label">Tempat Kegiatan</label>
                        <input type="text" class="form-control" id="tempat_kegiatan" name="tempat_kegiatan"
                            placeholder="Masukkan tempat kegiatan" autofocus
                            value="{{ $laporanPengurus->tempat_kegiatan }}" />
                        @error('tempat_kegiatan')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="jumlah_peserta" class="form-label">Jumlah Peserta</label>
                        <input type="text" class="form-control" id="jumlah_peserta" name="jumlah_peserta"
                            placeholder="Masukkan jumlah peserta" autofocus
                            value="{{ $laporanPengurus->jumlah_peserta }}" />
                        @error('jumlah_peserta')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="foto_kegiatan" class="form-label">Foto Kegiatan</label>

                        <div class="d-flex gap-3 align-items-center">
                            <a href="{{ Storage::url($laporanPengurus->foto_kegiatan) }}" target="_blank">
                                <span class="text-nowrap">
                                    Lihat File
                                </span>
                            </a>
                            <input type="file" class="form-control" id="foto_kegiatan" name="foto_kegiatan" autofocus />
                        </div>
                        @error('foto_kegiatan')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="evaluasi_kegiatan" class="form-label">Evaluasi Kegiatan</label>
                        <textarea class="form-control" id="evaluasi_kegiatan" name="evaluasi_kegiatan" placeholder="Tuliskan evaluasi kegiatan"
                            autofocus>{{ $laporanPengurus->evaluasi_kegiatan }}</textarea>
                        @error('evaluasi_kegiatan')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="dokumen_pendukung" class="form-label">Dokumen Pendukung</label>
                        <div class="d-flex gap-3 align-items-center">
                            <a href="{{ Storage::url($laporanPengurus->dokumen_pendukung) }}" target="_blank">
                                <span class="text-nowrap">
                                    Lihat File
                                </span>
                            </a>
                            <input type="file" class="form-control" id="dokumen_pendukung" name="dokumen_pendukung"
                                autofocus value="{{ $laporanPengurus->dokumen_pendukung }}" />
                        </div>
                        @error('dokumen_pendukung')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    @if (Auth::user()->role === 'Pengurus')
                        <div class="d-flex justify-content-end mt-5">
                            <button class="btn btn-primary d-grid" id="btn-form">Simpan</button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
        <!--/ Basic Bootstrap Table -->

        <hr class="my-5">

    </div>
@endsection
