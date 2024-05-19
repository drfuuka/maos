@extends('auth.layouts.app')

@section('title', 'Login')

@section('content')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                <!-- Register Card -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-4 mt-2">
                            <a href="index.html" class="app-brand-link gap-2">
                                <img src="{{ asset('logo.png') }}" alt="" width="50">
                                <span class="app-brand-text demo text-body fw-bold ms-1">SIKEKWARMA</span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-1 pt-2">Selamat Datang Di</h4>
                        <p class="mb-4">Sistem Informasi Kegiatan Kwartir Ranting Maos</p>

                        <form id="form" class="mb-3" action="{{ route('login.authenticate') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Username / Email</label>
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="Enter your username / email" autofocus value="{{ old('email') }}" />
                                @error('email')
                                    <small class="text-danger" role="alert">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="mb-3 form-password-toggle">
                                <label class="form-label" for="password">Kata Sandi</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" value="{{ old('password') }}" />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                                <small class="text-primary"><a href="{{ route('forgot-password.index') }}">Lupa kata
                                        sandi?</a></small>
                                @error('password')
                                    <small class="text-danger" role="alert">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <button class="btn btn-primary d-grid w-100" id="btn-form">Masuk</button>
                        </form>

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <p class="text-center">
                            <span>Belum memiliki akun?</span>
                            <a href="{{ route('register.index') }}">
                                <span>Daftar</span>
                            </a>
                        </p>

                    </div>
                </div>
                <!-- Register Card -->
            </div>
        </div>
    </div>
@endsection
