@extends('layouts.default', ['paceTop' => true])

@section('title', 'Usuarios')

@include('includes.component.datatables')

@section('content')



    @include('centros.salas._form')



@endsection
