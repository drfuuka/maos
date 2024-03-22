@extends('layouts.index')

@section('title', 'Proposal Pengurus - Tambah')

@section('content')
    <div class="container-xxl flex-grow-1 ">


        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">Proposal / <a class="text-muted"
                    href="{{ route('proposal-pengurus.index') }}">Pengurus</a> / </span>
            Tambah
        </h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-header">Tambah Proposal Pengurus</h5>
                <div class="card-header">
                    <a class="btn btn-label-secondary" href="{{ route('proposal-pengurus.index') }}">Kembali</a>
                </div>
            </div>

            <div class="card-body mt-0 pt-0">
                <form id="form" class="mb-3" action="{{ route('proposal-pengurus.store') }}"
                    enctype="multipart/form-data" , method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="jenis_proposal" class="form-label">Jenis Proposal</label>
                        <input type="text" class="form-control" id="jenis_proposal" name="jenis_proposal"
                            placeholder="Masukkan jenis proposal" autofocus value="{{ old('jenis_proposal') }}" />
                        @error('jenis_proposal')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nama_kegiatan" class="form-label">Nama Kegiatan</label>
                        <input type="text" class="form-control" id="nama_kegiatan" name="nama_kegiatan"
                            placeholder="Masukkan nama kegiatan" autofocus value="{{ old('nama_kegiatan') }}" />
                        @error('nama_kegiatan')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tema_kegiatan" class="form-label">Tema Kegiatan</label>
                        <input type="text" class="form-control" id="tema_kegiatan" name="tema_kegiatan"
                            placeholder="Masukkan tema kegiatan" autofocus value="{{ old('tema_kegiatan') }}" />
                        @error('tema_kegiatan')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="dasar_kegiatan" class="form-label">Dasar Kegiatan</label>
                        <input type="text" class="form-control" id="dasar_kegiatan" name="dasar_kegiatan"
                            placeholder="Masukkan dasar kegiatan" autofocus value="{{ old('dasar_kegiatan') }}" />
                        @error('dasar_kegiatan')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="maksud_tujuan" class="form-label">Maksud dan Tujuan</label>
                        <input type="text" class="form-control" id="maksud_tujuan" name="maksud_tujuan"
                            placeholder="Masukkan dasar kegiatan" autofocus value="{{ old('maksud_tujuan') }}" />
                        @error('maksud_tujuan')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_kegiatan" class="form-label">Tanggal Kegiatan</label>
                        <input type="date" class="form-control" id="tanggal_kegiatan" name="tanggal_kegiatan"
                            placeholder="Masukkan tanggal kegiatan" autofocus value="{{ old('tanggal_kegiatan') }}" />
                        @error('tanggal_kegiatan')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="jadwal_kegiatan" class="form-label">Jadwal Kegiatan</label>
                        <textarea id="jadwal_kegiatan" class="form-control" name="jadwal_kegiatan" placeholder="Tuliskan kata jadwal_kegiatan">{{ old('jadwal_kegiatan') }}</textarea>
                        @error('jadwal_kegiatan')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="kepanitiaan" class="form-label">Kepanitiaan</label>
                        <input type="text" class="form-control" id="kepanitiaan" name="kepanitiaan"
                            placeholder="Masukkan jadwal kegiatan" autofocus value="{{ old('kepanitiaan') }}" />
                        @error('kepanitiaan')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="rincian_dana" class="form-label">Rincian Dana</label>
                        <textarea id="rincian_dana" class="form-control" name="rincian_dana" placeholder="Tuliskan kata rincian_dana">{{ old('rincian_dana') }}</textarea>
                        @error('rincian_dana')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="penutup" class="form-label">Penutup</label>
                        <textarea id="penutup" class="form-control" name="penutup" placeholder="Tuliskan kata penutup">{{ old('penutup') }}</textarea>
                        @error('penutup')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="dokumen_proposal" class="form-label">Dokumen Proposal</label>
                        <input type="file" class="form-control" id="dokumen_proposal" name="dokumen_proposal"
                            autofocus />
                        @error('dokumen_proposal')
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
