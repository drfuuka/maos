@extends('layouts.index')

@section('title', 'Laporan Pengurus')

@section('content')
    <div class="container-xxl flex-grow-1 ">


        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">Laporan /</span> Pengurus
        </h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-header">Laporan Pengurus</h5>

                <div class="card-header d-flex justify-content-end gap-3">
                    <a class="btn btn-label-primary" href="{{ route('laporan-pengurus.export') }}">Export</a>
                    <a class="btn btn-primary" href="{{ route('laporan-pengurus.create') }}">Tambah</a>
                </div>
            </div>
            @if (session('success'))
                <div class="px-4">
                    <div class="alert alert-success alert-dismissible">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                        </button>
                    </div>
                </div>
            @endif
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Kegiatan</th>
                            <th>Tanggal Kegiatan</th>
                            <th>Tempat Kegiatan</th>
                            <th>Jumlah Peserta</th>
                            <th>Foto Kegiatan</th>
                            <th>Evaluas Kegiatan</th>
                            <th>Dokumen Pendukung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @if ($laporanGudep->count())
                            @foreach ($laporanGudep as $item)
                                <tr>
                                    <td>{{ $item->nama_kegiatan }}</td>
                                    <td>{{ $item->tanggal_kegiatan }}</td>
                                    <td>{{ $item->tempat_kegiatan }}</td>
                                    <td>{{ $item->jumlah_peserta }}</td>
                                    <td>
                                        <a href="{{ Storage::url($item->foto_kegiatan) }}" target="_blank">
                                            <img src="{{ Storage::url($item->foto_kegiatan) }}" alt=""
                                                width="100">
                                        </a>
                                    </td>
                                    <td>{{ $item->evaluasi_kegiatan }}</td>
                                    <td>
                                        <a href="{{ Storage::url($item->dokumen_pendukung) }}" target="_blank">
                                            <img src="{{ Storage::url($item->dokumen_pendukung) }}" alt=""
                                                width="100">
                                        </a>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown" aria-expanded="false"><i
                                                    class="ti ti-dots-vertical"></i></button>
                                            <div class="dropdown-menu" style="">
                                                <a class="dropdown-item"
                                                    href="{{ route('laporan-pengurus.edit', $item->id) }}"><i
                                                        class="ti ti-pencil me-1"></i> Edit</a>
                                                <form action="{{ route('laporan-pengurus.destroy', $item->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item"><i
                                                            class="ti ti-trash me-1"></i>
                                                        Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data ditemukan</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <!--/ Basic Bootstrap Table -->

        <hr class="my-5">


    </div>
@endsection
