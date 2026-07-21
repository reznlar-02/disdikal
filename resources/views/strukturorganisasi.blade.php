@extends('layouts.home')

@section('content')
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white text-center">
                <h2>Struktur Organisasi</h2>
            </div>
            <div class="card-body text-center">
                <div class="image-wrapper">
                    <img src="{{ asset('assets/images/so.jpg') }}" alt="Struktur Organisasi"
                        class="img-fluid border rounded p-2">
                </div>
            </div>
        </div>
    </div>
@endsection