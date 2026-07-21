@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header Card -->
        <div class="card bg-primary bg-gradient text-white shadow-lg mb-4 border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="display-6 fw-bold mb-2">Manajemen Strata</h1>
                        <p class="card-text opacity-75 mb-0">Kelola data strata dalam sistem</p>
                    </div>
                    <button class="btn btn-light btn-md" data-bs-toggle="modal" data-bs-target="#addStrataModal">
                        <i class="fas fa-plus me-2"></i>Tambah
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-striped align-middle" id="datatable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px">No</th>
                                <th>Strata</th>
                                <th style="width: 120px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($strata as $key => $value)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $value->strata }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ url('admin/strataedit/' . $value->idstrata) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ url('admin/stratahapus/' . $value->idstrata) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger btn-delete-confirm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Strata -->
    <div class="modal fade" id="addStrataModal" tabindex="-1" aria-labelledby="addStrataModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title fw-bold text-dark" id="addStrataModalLabel">Tambah Strata</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="strataForm" action="{{ url('admin/stratasimpan') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="strata" class="form-label text-dark">Strata</label>
                            <input type="text" class="form-control" id="strata" name="strata" required>
                            @error('strata')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    @error('strata')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new bootstrap.Modal(document.getElementById('addStrataModal')).show();
            });
        </script>
    @enderror
@endsection
