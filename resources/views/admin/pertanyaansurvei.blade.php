@extends('layouts.admin')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header Card -->
        <div class="card bg-primary bg-gradient text-white shadow-lg mb-4 border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="display-6 fw-bold mb-2">Manajemen Pertanyaan Survei</h1>
                        <p class="card-text opacity-75 mb-0">Kelola data pertanyaan survei dalam sistem</p>
                    </div>
                    <button class="btn btn-light btn-md" data-bs-toggle="modal" data-bs-target="#addPertanyaanModal">
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
                                <th>Pendidikan</th>
                                <th>Strata</th>
                                <th>Pertanyaan</th>
                                <th style="width: 120px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (($pertanyaan ?? []) as $key => $value)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $value->pendidikan->pendidikan }}</td>
                                    <td>{{ $value->pendidikan->strata->strata }}</td>
                                    <td>{{ $value->pertanyaan }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ url('admin/pertanyaansurveiedit/' . $value->idpertanyaansurvei) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form
                                                action="{{ url('admin/pertanyaansurveihapus/' . $value->idpertanyaansurvei) }}"
                                                method="POST" class="d-inline">
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

    <!-- Modal Tambah Pertanyaan Survei -->
    <div class="modal fade" id="addPertanyaanModal" tabindex="-1" aria-labelledby="addPertanyaanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title fw-bold text-dark" id="addPertanyaanModalLabel">Tambah Pertanyaan Survei</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="pertanyaanForm" action="{{ url('admin/pertanyaansurveisimpan') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="idstrata" class="form-label text-dark">Strata</label>
                            <select class="form-control" id="idstrata" name="idstrata" required>
                                <option value="" selected disabled>Pilih Strata</option>
                                @foreach (($strata ?? []) as $s)
                                    <option value="{{ $s->idstrata }}">{{ $s->strata }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="idpendidikan" class="form-label text-dark">Pendidikan</label>
                            <select class="form-control" id="idpendidikan" name="idpendidikan" required>
                                <option value="" selected disabled>Pilih Pendidikan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark">Pertanyaan</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="pertanyaan-number">1</span>
                                <input type="text" class="form-control" id="pertanyaan-input" placeholder="Masukkan pertanyaan">
                                <button type="button" class="btn btn-secondary" id="tambah-pertanyaan">Tambah Pertanyaan</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark">Draft List Pertanyaan</label>
                            <ul class="list-group" id="draft-list"></ul>
                        </div>
                        <input type="hidden" name="draft_pertanyaan" id="draft_pertanyaan_hidden">
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
                        pendidikanSelect.append('<option value="' + item
                            .idpendidikan + '">' + item.pendidikan + '</option>'
                        );
                    });
                },
                error: function() {
                    pendidikanSelect.html(
                        '<option value="" selected disabled>Error memuat data</option>');
                }
            });
        });

        let pertanyaanDraft = [];
        function renderDraftList() {
            const ul = document.getElementById('draft-list');
            ul.innerHTML = '';
            pertanyaanDraft.forEach((val, idx) => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `<span>${idx+1}. ${val}</span><button type='button' class='btn btn-danger btn-sm' onclick='hapusPertanyaan(${idx})'>Hapus</button>`;
                ul.appendChild(li);
            });
            document.getElementById('draft_pertanyaan_hidden').value = pertanyaanDraft.join('\n');
            document.getElementById('pertanyaan-number').textContent = pertanyaanDraft.length + 1;
        }
        function hapusPertanyaan(idx) {
            pertanyaanDraft.splice(idx, 1);
            renderDraftList();
        }
        document.getElementById('tambah-pertanyaan').onclick = function() {
            const input = document.getElementById('pertanyaan-input');
            const val = input.value.trim();
            if (val !== '') {
                pertanyaanDraft.push(val);
                input.value = '';
                renderDraftList();
                input.focus();
            }
        };
        function setPertanyaanInputState() {
            const strata = document.getElementById('idstrata').value;
            const pendidikan = document.getElementById('idpendidikan').value;
            const input = document.getElementById('pertanyaan-input');
            const btn = document.getElementById('tambah-pertanyaan');
            if (!strata || !pendidikan) {
                input.disabled = true;
                btn.disabled = true;
            } else {
                input.disabled = false;
                btn.disabled = false;
            }
        }
        document.getElementById('idstrata').addEventListener('change', setPertanyaanInputState);
        document.getElementById('idpendidikan').addEventListener('change', setPertanyaanInputState);
        // Inisialisasi state saat modal dibuka
        $('#addPertanyaanModal').on('shown.bs.modal', function () {
            setPertanyaanInputState();
            renderDraftList();
            document.getElementById('pertanyaan-input').value = '';
            document.getElementById('pertanyaan-input').focus();
        });
        // Reset draft saat modal ditutup
        $('#addPertanyaanModal').on('hidden.bs.modal', function () {
            pertanyaanDraft = [];
            renderDraftList();
            document.getElementById('pertanyaan-input').value = '';
        });
        // Pastikan submit hanya kirim draft
        $('#pertanyaanForm').on('submit', function() {
            document.getElementById('draft_pertanyaan_hidden').value = pertanyaanDraft.filter(x=>x.trim()!=='').join('\n');
        });
    </script>
@endsection
