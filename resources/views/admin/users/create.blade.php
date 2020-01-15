@extends('layouts.default', ['paceTop' => true])

@section('title', 'Usuarios')

@include('includes.component.datatables')

@section('content')

    {!! Form::post(route('usuarios.store')) !!}

    @include('admin.users._form')

    {!! Form::close() !!}

@endsection
