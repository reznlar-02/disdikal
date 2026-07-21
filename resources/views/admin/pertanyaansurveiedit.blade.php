@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header Card -->
        <div class="card bg-primary bg-gradient text-white shadow-lg mb-4 border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="display-6 fw-bold mb-2">Edit Pertanyaan Survei</h1>
                        <p class="card-text opacity-75 mb-0">Perbarui data pertanyaan survei dalam sistem</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Edit Pertanyaan Survei -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form id="editPertanyaanForm"
                    action="{{ url('admin/pertanyaansurveiupdate/' . $pertanyaan->idpertanyaansurvei) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="idstrata" class="form-label text-dark">Strata</label>
                        <select class="form-control" id="idstrata" name="idstrata" required>
                            <option value="" disabled>Pilih Strata</option>
                            @foreach ($strata as $s)
                                <option value="{{ $s->idstrata }}"
                                    {{ $s->idstrata == $pertanyaan->pendidikan->idstrata ? 'selected' : '' }}>
                                    {{ $s->strata }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="idpendidikan" class="form-label text-dark">Pendidikan</label>
                        <select class="form-control" id="idpendidikan" name="idpendidikan" required>
                            <option value="" disabled>Pilih Pendidikan</option>
                            @foreach ($pendidikan as $p)
                                <option value="{{ $p->idpendidikan }}"
                                    {{ $p->idpendidikan == $pertanyaan->idpendidikan ? 'selected' : '' }}>
                                    {{ $p->pendidikan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pertanyaan" class="form-label text-dark">Pertanyaan</label>
                        <input type="text" class="form-control" id="pertanyaan" name="pertanyaan"
                            value="{{ old('pertanyaan', $pertanyaan->pertanyaan) }}" required>
                        @error('pertanyaan')
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

@section('script')
    <script>
        $('#idstrata').on('change', function() {
            let idStrata = $(this).val();
            let pendidikanSelect = $('#idpendidikan');
            pendidikanSelect.html('<option value="" selected disabled>Memuat...</option>');

            $.ajax({
                type: "GET",
                url: "{{ url('api/getpendidikanbystrata') }}/" + idStrata,
                dataType: "json",
                success: function(data) {
                    pendidikanSelect.html(
                        '<option value="" selected disabled>Pilih Pendidikan</option>');
                    $.each(data, function(index, item) {
                        pendidikanSelect.append('<option value="' + item.idpendidikan + '">' +
                            item.pendidikan + '</option>');
                    });
                },
                error: function() {
                    pendidikanSelect.html(
                        '<option value="" selected disabled>Error memuat data</option>');
                }
            });
        });
    </script>
@endsection
