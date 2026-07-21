@extends('layouts.admin')

@section('content')
    @php
        $tahunTab = array_unique(array_merge([2025, 2026], $daftarTahun));
        $tahunTab = array_filter($tahunTab, function ($tahun) {
            return $tahun >= 2025;
        });
        rsort($tahunTab);
    @endphp
    <div class="container-fluid py-4">
        <!-- Dropdown Filter Tahun (disamakan dengan dashboard) -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-success shadow h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title mb-2">Sudah Isi Survei</h5>
                        <h2 class="display-4 fw-bold mb-0">{{ $totalSudahIsi }}</h2>
                        <span class="text-white-50">responden</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info shadow h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title mb-2">Sudah Isi Data</h5>
                        <h2 class="display-4 fw-bold mb-0">{{ $totalSudahIsi }}</h2>
                        <span class="text-white-50">responden</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 d-flex flex-column justify-content-center">
                <div class="d-grid gap-2">
                    <a href="{{ url('admin/hasilsurvei/download-pdf?tahun=' . ($tahunFilter ?? $tahunTab[0])) }}" class="btn btn-danger btn-lg shadow">Download PDF</a>
                    <a href="{{ url('admin/hasilsurvei/download-csv?tahun=' . ($tahunFilter ?? $tahunTab[0])) }}" class="btn btn-success btn-lg shadow">Download CSV</a>
                </div>
            </div>
        </div>
        <!-- Kotak Sudah Isi Data di bawah (hapus jika ada) -->

        <!-- HAPUS CARD HASIL SURVEI -->

        <!-- Tab Tahun Bootstrap -->
        @if(!empty($tahunTab))
        <ul class="nav nav-tabs mb-4" id="tahunTab" role="tablist">
            @foreach($tahunTab as $tahun)
                <li class="nav-item" role="presentation">
                    <a class="nav-link{{ ($tahun == ($tahunFilter ?? $tahunTab[0])) ? ' active' : '' }}" href="?tahun={{ $tahun }}" role="tab">{{ $tahun }}</a>
                </li>
            @endforeach
        </ul>
        @endif

        <!-- Tabel Rekap Pertanyaan & Penjabaran Jawaban per Pendidikan (Format Kolom) -->
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Rekap Pertanyaan & Penjabaran Jawaban per Pendidikan</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 180px;" class="text-center align-middle">Pendidikan</th>
                                <th class="text-center align-middle">Pertanyaan</th>
                                @foreach($kategoriJawaban as $kategori)
                                    <th style="width: 80px;" class="text-center align-middle">{{ $kategori }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pertanyaanPerPendidikan as $pendidikan => $pertanyaanList)
                                @php $rowspan = count($pertanyaanList); $first = true; @endphp
                                @foreach($pertanyaanList as $pertanyaan)
                                    <tr>
                                        @if($first)
                                            <td rowspan="{{ $rowspan }}">{{ $pendidikan }}</td>
                                            @php $first = false; @endphp
                                        @endif
                                        <td>{{ $pertanyaan->pertanyaan }}</td>
                                        @foreach($kategoriJawaban as $kategori)
                                            <td class="text-center">{{ $jawabanPerPertanyaan[$pertanyaan->idpertanyaansurvei][$kategori] ?? 0 }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let dataTahun = @json($dataTahun);
            let dataPendidikan = @json($dataPendidikan);
            let kategoriJawaban = @json($kategoriJawaban);

            if (Object.keys(dataTahun).length > 0) {
                new Chart(document.getElementById('chartTahun'), {
                    type: 'pie',
                    data: {
                        labels: kategoriJawaban,
                        datasets: [{
                            data: kategoriJawaban.map(k =>
                                Object.values(dataTahun).reduce((sum, dt) => sum + (dt.kategori[
                                    k] || 0), 0)
                            ),
                            backgroundColor: ['#4CAF50', '#36A2EB', '#FFCE56', '#FF6384', '#9966FF']
                        }]
                    }
                });
            }

            if (Object.keys(dataPendidikan).length > 0) {
                new Chart(document.getElementById('chartPendidikan'), {
                    type: 'pie',
                    data: {
                        labels: kategoriJawaban,
                        datasets: [{
                            data: kategoriJawaban.map(k =>
                                Object.values(dataPendidikan).reduce((sum, dp) => sum + (dp
                                    .kategori[k] || 0), 0)
                            ),
                            backgroundColor: ['#4CAF50', '#36A2EB', '#FFCE56', '#FF6384', '#9966FF']
                        }]
                    }
                });
            }
        });
    </script>
@endsection
