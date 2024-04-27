@extends('auth.layouts.app')

@section('title', 'Forgot Password')

@section('content')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                <!-- Register Card -->
                <div class="card">
                    <div class="card-body">
                        <!-- /Logo -->
                        <form id="form" class="mb-3" action="{{ route('forgot-password.send') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Masukkan email kamu</label>
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="Masukkan email kamu" autofocus value="{{ old('email') }}" />
                                @error('email')
                                    <small class="text-danger" role="alert">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <button class="btn btn-primary d-grid w-100" id="btn-form">Kirim Email</button>
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
                            <a href="{{ route('login.index') }}">
                                <span>Kembali</span>
                            </a>
                        </p>

                    </div>
                </div>
                <!-- Register Card -->
            </div>
        </div>
    </div>
@endsection
