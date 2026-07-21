@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header Card -->
        <div class="card bg-primary bg-gradient text-white shadow-lg mb-4 border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div> 
                        <h1 class="display-6 fw-bold mb-2">Edit Pendidikan</h1>
                        <p class="card-text opacity-75 mb-0">Perbarui data pendidikan dalam sistem</p>
                    </div>
                    <a href="{{ url('admin/pendidikan') }}" class="btn btn-light btn-md">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Edit Form Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ url('admin/pendidikanupdate/' . $pendidikan->idpendidikan) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="idstrata" class="form-label text-dark">Strata</label>
                        <select class="form-control" id="idstrata" name="idstrata" required>
                            <option value="">Pilih Strata</option>
                            @foreach ($strata as $s)
                                <option value="{{ $s->idstrata }}"
                                    {{ $s->idstrata == $pendidikan->idstrata ? 'selected' : '' }}>{{ $s->strata }}
                                </option>
                            @endforeach
                        </select>
                        @error('idstrata')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="pendidikan" class="form-label text-dark">Pendidikan</label>
                        <input type="text" class="form-control" id="pendidikan" name="pendidikan"
                            value="{{ old('pendidikan', $pendidikan->pendidikan) }}" required>
                        @error('pendidikan')
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
