@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header Card -->
        <div class="card bg-primary bg-gradient text-white shadow-lg mb-4 border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="display-6 fw-bold mb-2">Edit Strata</h1>
                        <p class="card-text opacity-75 mb-0">Perbarui data strata dalam sistem</p>
                    </div>
                    <a href="{{ url('admin/strata') }}" class="btn btn-light btn-md">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Edit Form Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ url('admin/strataupdate/' . $strata->idstrata) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="strata" class="form-label text-dark">Strata</label>
                        <input type="text" class="form-control" id="strata" name="strata"
                            value="{{ old('strata', $strata->strata) }}" required>
                        @error('strata')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
