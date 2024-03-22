@extends('layouts.index')

@section('title', 'LPJ Pengurus')

@section('content')
    <div class="container-xxl flex-grow-1 ">


        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">LPJ /</span> Pengurus
        </h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-header">LPJ Pengurus</h5>
                <div class="card-header d-flex justify-content-end gap-3">
                    <a class="btn btn-label-primary" href="{{ route('lpj-pengurus.export') }}">Export</a>
                    <a class="btn btn-primary" href="{{ route('lpj-pengurus.create') }}">Tambah</a>
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
                            <th>Nama LPJ</th>
                            <th>Foto Kegiatan</th>
                            <th>Dokumen Laporan</th>
                            <th>Evaluasi</th>
                            <th>Saran</th>
                            <th>Status Verifikasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @if ($lpjPengurus->count())
                            @foreach ($lpjPengurus as $item)
                                <tr>
                                    <td><a
                                            href="{{ url('proposal-pengurus/' . $item->proposal->id . '/edit') }}">{{ $item->proposal->nama_kegiatan }}</a>
                                    </td>
                                    <td>
                                        <a href="{{ Storage::url($item->foto_kegiatan) }}" target="_blank">
                                            <img src="{{ Storage::url($item->foto_kegiatan) }}" alt=""
                                                width="100">
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ Storage::url($item->dokumen_lpj) }}" target="_blank">
                                            <img src="{{ Storage::url($item->dokumen_lpj) }}" alt="" width="100">
                                        </a>
                                    </td>
                                    <td>{{ $item->evaluasi }}</td>
                                    <td>{{ $item->saran }}</td>
                                    @if ($item->status_verifikasi === null)
                                        <td><span class="badge bg-label-dark">Belum Terverifikasi</span></td>
                                    @elseif ($item->status_verifikasi === 'Diterima')
                                        <td><span class="badge bg-label-success">Diterima</span></td>
                                    @else
                                        <td><span class="badge bg-label-danger">Ditolak</span></td>
                                    @endif
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown" aria-expanded="false"><i
                                                    class="ti ti-dots-vertical"></i></button>
                                            <div class="dropdown-menu" style="">
                                                <a class="dropdown-item"
                                                    href="{{ route('lpj-pengurus.edit', $item->id) }}"><i
                                                        class="ti ti-pencil me-1"></i> Edit</a>
                                                <form action="{{ route('lpj-pengurus.destroy', $item->id) }}"
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
