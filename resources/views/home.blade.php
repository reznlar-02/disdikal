@extends('layouts.home')
@section('content')
    <!-- Main Content -->
    <section class="container">
        <div class="main-content">
            <h1>Selamat Datang di Website E-Questioner Dinas Pendidikan TNI Angkatan Laut</h1>

            <div class="menu-links">
                <a href="{{ url('surveikepuasan') }}">Survey Kepuasan</a>
                <a href="{{ url('strukturorganisasi') }}">Struktur Organisasi</a>
            </div>
        </div>
    </section>
@endsection
