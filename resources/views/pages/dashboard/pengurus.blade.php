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
                            <div class="col-12">
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
                            <div class="col-12">
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
                            <div class="col-12">
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
    </div>
@endsection
