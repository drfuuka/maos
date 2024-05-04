@extends('layouts.index')

@section('title', 'Laporan Gugus Depan')

@section('content')
    <div class="container-xxl flex-grow-1 ">


        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">Laporan /</span> Gugus Depan
        </h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-header">Laporan Gugus Depan</h5>

                <div class="card-header d-flex justify-content-end gap-3">
                    <button type="button" class="btn btn-label-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <i class="link-icon me-1" data-feather="printer" width="18"></i>
                        Cetak Laporan
                    </button>

                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Cetak Laporan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="btn-close"></button>
                                </div>
                                <form action="{{ route('laporan-gudep.export') }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" id="filter_tanggal"
                                                        name="filter_tanggal" {{ old('filter_tanggal') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="filter_tanggal">Filter
                                                        Tanggal</label>
                                                </div>
                                                @error('filter_tanggal')
                                                    <small class="text-danger" role="alert">
                                                        {{ $message }}
                                                    </small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row" id="date-filter">

                                            <div class="col-6">
                                                <label for="dari_tanggal" class="form-label">Dari Tanggal</label>
                                                <div class="input-group date datepicker" id="dari_tanggal">
                                                    <input type="date" class="form-control" name="dari_tanggal"
                                                        placeholder="Pilih tanggal" value="{{ old('dari_tanggal') }}">
                                                </div>
                                                @error('dari_tanggal')
                                                    <small class="text-danger" role="alert">
                                                        {{ $message }}
                                                    </small>
                                                @enderror
                                            </div>
                                            <div class="col-6">
                                                <label for="sampai_tanggal" class="form-label">Sampai
                                                    Tanggal</label>
                                                <div class="input-group date datepicker" id="sampai_tanggal">
                                                    <input type="date" class="form-control" name="sampai_tanggal"
                                                        placeholder="Pilih tanggal" value="{{ old('sampai_tanggal') }}">
                                                </div>
                                                @error('sampai_tanggal')
                                                    <small class="text-danger" role="alert">
                                                        {{ $message }}
                                                    </small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Cetak</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @if (Auth::user()->role === 'Gudep')
                        <a class="btn btn-primary" href="{{ route('laporan-gudep.create') }}">Tambah</a>
                    @endif
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
                            <th>Dibuat Oleh</th>
                            <th>Dibuat Tanggal</th>
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
                                    <td>{{ $item->user->fullname }}</td>
                                    <td>{{ \Carbon\Carbon::create($item->created_at)->format('d M Y') }}</td>

                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown" aria-expanded="false"><i
                                                    class="ti ti-dots-vertical"></i></button>
                                            <div class="dropdown-menu" style="">

                                                @if (Auth::user()->role === 'Gudep' || Auth::user()->role === 'Admin')
                                                    <a class="dropdown-item"
                                                        href="{{ route('laporan-gudep.edit', $item->id) }}"><i
                                                            class="ti ti-pencil me-1"></i> Edit</a>
                                                    @if (Auth::user()->role === 'Admin')
                                                        <form action="{{ route('laporan-gudep.destroy', $item->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item"><i
                                                                    class="ti ti-trash me-1"></i>
                                                                Hapus</button>
                                                        </form>
                                                    @endif
                                                @else
                                                    <a class="dropdown-item"
                                                        href="{{ route('laporan-gudep.edit', $item->id) }}"><i
                                                            class="ti ti-pencil me-1"></i> Detail</a>
                                                @endif
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

@section('scripts')
    <script>
        $(document).ready(function() {
            const isChecked = $('#filter_tanggal').prop('checked')

            if (isChecked) {
                $('#date-filter').show()
            } else {
                $('#date-filter').hide()
            }

            $('#filter_tanggal').on('change', function() {
                const isChecked = $('#filter_tanggal').prop('checked')

                if (isChecked) {
                    $('#date-filter').show()
                } else {
                    $('#date-filter').hide()
                }
            })
        })
    </script>
@endsection
