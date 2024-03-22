@extends('layouts.index')

@section('title', 'Test')

@section('content')

    <div class="row">
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column">
                        <div class="card-title mb-auto">
                            <h5 class="mb-1 text-nowrap">Laporan</h5>
                            <small>Jumlah total laporan</small>
                        </div>

                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="d-flex flex-column">
                                    <small>Gugus Depan</small>
                                    <span class="fs-1">{{ $total_laporan['gudep'] }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex flex-column">
                                    <small>Pengurus</small>
                                    <span class="fs-1">{{ $total_laporan['pengurus'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column">
                        <div class="card-title mb-auto">
                            <h5 class="mb-1 text-nowrap">Proposal</h5>
                            <small>Jumlah total proposal</small>
                        </div>

                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="d-flex flex-column">
                                    <small>Gugus Depan</small>
                                    <span class="fs-1">{{ $total_proposal['gudep'] }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex flex-column">
                                    <small>Pengurus</small>
                                    <span class="fs-1">{{ $total_proposal['pengurus'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column">
                        <div class="card-title mb-auto">
                            <h5 class="mb-1 text-nowrap">LPJ</h5>
                            <small>Jumlah total laporan penanggungjawab (LPJ)</small>
                        </div>

                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="d-flex flex-column">
                                    <small>Gugus Depan</small>
                                    <span class="fs-1">{{ $total_lpj['gudep'] }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex flex-column">
                                    <small>Pengurus</small>
                                    <span class="fs-1">{{ $total_lpj['pengurus'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column">
                        <div class="card-title mb-auto">
                            <h5 class="mb-1 text-nowrap">Aktivasi Pengguna</h5>
                            <small>Jumlah permintaan aktivasi akun pengguna</small>
                        </div>


                        <div class="mt-3">

                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive text-nowrap">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nama Pengguna</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @if ($user->count())
                                            @foreach ($user as $item)
                                                <tr>
                                                    <td>{{ $item->fullname }}</td>
                                                    <td>{{ $item->role }}</td>
                                                    @if ($item->is_active === null)
                                                        <td><span class="badge bg-label-primary">Belum Diaktifkan</span>
                                                        </td>
                                                    @elseif ($item->is_active)
                                                        <td><span class="badge bg-label-success">Aktif</span></td>
                                                    @else
                                                        <td><span class="badge bg-label-dark">Nonaktif</span></td>
                                                    @endif
                                                    <td>{{ $item->username }}</td>
                                                    <td>{{ $item->email }}</td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button type="button"
                                                                class="btn p-0 dropdown-toggle hide-arrow"
                                                                data-bs-toggle="dropdown" aria-expanded="false"><i
                                                                    class="ti ti-dots-vertical"></i></button>
                                                            <div class="dropdown-menu" style="">
                                                                <a class="dropdown-item"
                                                                    href="{{ route('activate-user', $item->id) }}">
                                                                    Aktifkan</a>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
