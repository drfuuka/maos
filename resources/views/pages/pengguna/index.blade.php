@extends('layouts.index')

@section('title', 'Daftar Pengguna')

@section('content')
    <div class="container-xxl flex-grow-1 ">


        <h4 class="py-3 mb-4">
            Daftar Pengguna
        </h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-header">Daftar Pengguna</h5>
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
                                        <td><span class="badge bg-label-primary">Belum Diaktifkan</span></td>
                                    @elseif ($item->is_active)
                                        <td><span class="badge bg-label-success">Aktif</span></td>
                                    @else
                                        <td><span class="badge bg-label-dark">Nonaktif</span></td>
                                    @endif
                                    <td>{{ $item->username }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown" aria-expanded="false"><i
                                                    class="ti ti-dots-vertical"></i></button>
                                            <div class="dropdown-menu" style="">
                                                <a class="dropdown-item" href="{{ route('pengguna.edit', $item->id) }}"><i
                                                        class="ti ti-pencil me-1"></i> Edit</a>
                                                <form action="{{ route('pengguna.destroy', $item->id) }}" method="POST">
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
