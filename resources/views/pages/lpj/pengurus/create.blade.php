@extends('layouts.index')

@section('title', 'LPJ Pengurus - Tambah')

@section('content')
    <div class="container-xxl flex-grow-1 ">


        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">LPJ / <a class="text-muted" href="{{ route('lpj-pengurus.index') }}">Gugus
                    Depan</a> / </span>
            Tambah
        </h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-header">Tambah LPJ Pengurus</h5>
                <div class="card-header">
                    <a class="btn btn-label-secondary" href="{{ route('lpj-pengurus.index') }}">Kembali</a>
                </div>
            </div>

            <div class="card-body mt-0 pt-0">
                <form id="form" class="mb-3" action="{{ route('lpj-pengurus.store') }}" enctype="multipart/form-data"
                    , method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="proposal_pengurus_id" class="form-label">LPJ</label>
                        <select class="form-select" id="role" aria-label="Default select example"
                            name="proposal_pengurus_id">
                            <option selected>Pilih</option>
                            @foreach ($proposalOption as $item)
                                <option value="{{ $item->id }}"
                                    {{ $item->id === old('proposal_pengurus_id') ? 'selected' : '' }}>
                                    {{ $item->nama_kegiatan }}
                                </option>
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
                        <input type="file" class="form-control" id="foto_kegiatan" name="foto_kegiatan" autofocus />
                        @error('foto_kegiatan')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="dokumen_lpj" class="form-label">Dokumen Laporan</label>
                        <input type="file" class="form-control" id="dokumen_lpj" name="dokumen_lpj" autofocus />
                        @error('dokumen_lpj')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="evaluasi" class="form-label">Evaluasi</label>
                        <textarea name="evaluasi" id="evaluasi" class="form-control" placeholder="Tuliskan evaluasi">{{ old('evaluasi') }}</textarea>
                        @error('evaluasi')
                            <small class="text-danger" role="alert">
                                {{ $message }}
                            </small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="saran" class="form-label">Saran</label>
                        <textarea name="saran" id="saran" class="form-control" placeholder="Tuliskan saran">{{ old('saran') }}</textarea>
                        @error('saran')
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
