@extends('layouts.index')

@section('title', 'LPJ Pengurus - Ubah')

@section('content')
    <div class="container-xxl flex-grow-1 ">


        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">LPJ / <a class="text-muted" href="{{ route('lpj-pengurus.index') }}">Pengurus</a>
                / </span>
            Ubah
        </h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-header">Ubah LPJ Pengurus</h5>
                <div class="card-header">
                    <a class="btn btn-label-secondary" href="{{ route('lpj-pengurus.index') }}">Kembali</a>
                </div>
            </div>

            <div class="card-body mt-0 pt-0">
                <form id="form" class="mb-3" action="{{ route('lpj-pengurus.update', $lpjPengurus->id) }}"
                    enctype="multipart/form-data" , method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="proposal_pengurus_id" class="form-label">LPJ</label>
                        <select class="form-select" id="role" aria-label="Default select example"
                            name="proposal_pengurus_id">
                            <option selected>Pilih</option>
                            @foreach ($proposalOption as $item)
                                <option value="{{ $item->id }}"
                                    {{ $item->id === $lpjPengurus->proposal_pengurus_id ? 'selected' : '' }}>
                                    {{ $item->nama_kegiatan }}</option>
                            @endforeach
                        </select>
                        @error('proposal_pengurus_id')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="foto_kegiatan" class="form-label">Foto Kegiatan</label>
                        <div class="d-flex gap-3 align-items-center">
                            <a href="{{ Storage::url($lpjPengurus->foto_kegiatan) }}" target="_blank">
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
                        <label for="dokumen_lpj" class="form-label">Dokumen Laporan</label>
                        <div class="d-flex gap-3 align-items-center">
                            <a href="{{ Storage::url($lpjPengurus->dokumen_lpj) }}" target="_blank">
                                <span class="text-nowrap">
                                    Lihat File
                                </span>
                            </a>
                            <input type="file" class="form-control" id="dokumen_lpj" name="dokumen_lpj" autofocus />
                        </div>
                        @error('dokumen_lpj')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="evaluasi" class="form-label">Evaluasi</label>
                        <textarea name="evaluasi" id="evaluasi" class="form-control">{{ $lpjPengurus->evaluasi }}</textarea>
                        @error('evaluasi')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="saran" class="form-label">Saran</label>
                        <textarea name="saran" id="saran" class="form-control">{{ $lpjPengurus->saran }}</textarea>
                        @error('saran')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status Verifikasi</label>
                        <select class="form-select" id="role" aria-label="Default select example"
                            name="status_verifikasi" value="{{ $lpjPengurus->status_verifikasi }}"
                            {{ Auth::user()->role !== 'Ketua' ? 'disabled' : '' }}>
                            <option selected>Pilih</option>
                            <option value="Diterima"
                                {{ $lpjPengurus->status_verifikasi === 'Diterima' ? 'selected' : null }}>
                                Diterima
                            </option>
                            <option value="Ditolak"
                                {{ $lpjPengurus->status_verifikasi === 'Ditolak' ? 'selected' : null }}>
                                Ditolak
                            </option>
                        </select>
                        @error('status_verifikasi')
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
