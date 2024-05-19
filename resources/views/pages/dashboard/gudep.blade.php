@extends('layouts.index')

@section('title', 'Test')

@section('content')

    <div class="row">
        <div class="d-flex align-items-center justify-content-between">
            <h3>Dashboard</h3>
            <!-- Year Filter Form -->
            <form method="GET" action="{{ route('dashboard') }}" class="mb-3">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <select name="year" class="form-select">
                        <option value="">Pilih Tahun</option>
                        @foreach (range(date('Y'), date('Y') - 10) as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column">
                        <div class="card-title mb-auto">
                            <h5 class="mb-1 text-nowrap">Laporan</h5>
                            <small>Jumlah total laporan</small>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex flex-column">
                                    <small>Gugus Depan</small>
                                    <span class="fs-1">{{ $total_laporan['gudep'] }}</span>
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
                            <div class="col-12">
                                <div class="d-flex flex-column">
                                    <small>Gugus Depan</small>
                                    <span class="fs-1">{{ $total_proposal['gudep'] }}</span>
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
                            <div class="col-12">
                                <div class="d-flex flex-column">
                                    <small>Gugus Depan</small>
                                    <span class="fs-1">{{ $total_lpj['gudep'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
