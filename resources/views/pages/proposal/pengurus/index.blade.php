@extends('layouts.index')

@section('title', 'Proposal Pengurus')

@section('content')
    <div class="container-xxl flex-grow-1 ">


        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">Proposal /</span> Pengurus
        </h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-header">Proposal Pengurus</h5>

                <div class="card-header d-flex justify-content-end gap-3">
                    <a class="btn btn-label-primary" href="{{ route('proposal-pengurus.export') }}">Export</a>
                    <a class="btn btn-primary" href="{{ route('proposal-pengurus.create') }}">Tambah</a>
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
                            <th>Jenis Proposal</th>
                            <th>Dasar Kegiatan</th>
                            <th>Nama Kegiatan</th>
                            <th>Tanggal Kegiatan</th>
                            <th>Status Verifikasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @if ($proposalPengurus->count())
                            @foreach ($proposalPengurus as $item)
                                <tr>
                                    <td>{{ $item->jenis_proposal }}</td>
                                    <td>{{ $item->dasar_kegiatan }}</td>
                                    <td>{{ $item->nama_kegiatan }}</td>
                                    <td>{{ $item->tanggal_kegiatan }}</td>
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
                                                    href="{{ route('proposal-pengurus.edit', $item->id) }}"><i
                                                        class="ti ti-pencil me-1"></i> Edit</a>
                                                <form action="{{ route('proposal-pengurus.destroy', $item->id) }}"
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
