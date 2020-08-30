@extends('layouts.empty', ['paceTop' => true, 'bodyExtraClass' => 'bg-white'])

@section('title', 'Login Page')

@section('content')
    <!-- begin login -->
    <div class="login login-with-news-feed">
        <!-- begin news-feed -->
        <div class="news-feed">
            <div class="news-image" style="background-color: #235B4E; background-image: url({{asset('assets/img/logo/fondo-verde.jpg')}})"></div>
            <div class="news-caption">
                <h4 class="caption-title">Conciliación</h4>
                <p>
                    Sistema de gestión del proceso de Conciliación.
                    <b>V 0.1</b>
                </p>
            </div>
        </div>
        <!-- end news-feed -->
        <!-- begin right-content -->
        <div class="right-content" style="">
            <!-- begin login-header -->
            <div class="login-header">
                <div class="brand">
                    Conciliación
                    <small>Ingrese sus datos de acceso</small>
                </div>
                <div class="icon">
                    <i class="fa fa-sign-in-alt"></i>
                </div>
            </div>
            <!-- end login-header -->
            <!-- begin login-content -->
            <div class="login-content">
                <form action="{{route('login')}}" method="POST" class="margin-bottom-0">
                    {{csrf_field()}}
                    <div class="form-group m-b-15">
                        <input type="text" id="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="Email" required autofocus />
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group m-b-15">
                        <input id="password" name="password" type="password" class="form-control form-control-lg  @error('password') is-invalid @enderror" placeholder="Contraseña" required />
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="login-buttons">
                        <button type="submit" class="btn btn-success btn-block btn-lg">Entrar</button>
                    </div>
                    @if (Route::has('password.request'))
                        <a class="btn btn-link" href="{{ route('password.request') }}">
                            ¿Olvidó su clave de acceso?
                        </a>
                    @endif
                    <hr />
                    <p class="text-center text-grey-darker mb-0">
                        &copy; 2019 - {{date("Y")}}
                    </p>
                </form>
            </div>
            <!-- end login-content -->
        </div>
        <!-- end right-container -->
    </div>
    <!-- end login -->
@endsection
