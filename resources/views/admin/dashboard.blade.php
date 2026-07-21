@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4 px-4">

    {{-- ===== WELCOME CARD ===== --}}
    <div class="card bg-dark text-white shadow-lg p-4 mb-4" style="border-radius:12px;">
        <div class="card-body py-2">
            <h2 class="card-title fw-bold mb-1">Selamat Datang, Admin!</h2>
            <p class="card-text mb-0">Anda berada di halaman dashboard E-Questioner Dinas Pendidikan TNI Angkatan Laut.</p>
            <p class="card-text">Gunakan menu di bawah untuk mengelola data dan survei kepuasan.</p>
        </div>
    </div>

    {{-- ===== FILTER TAHUN ===== --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-3 col-sm-6">
            <form method="GET" action="{{ url('admin') }}" id="filterForm">
                <div class="d-flex gap-2 align-items-center">
                    <select class="form-select" id="tahun" name="tahun" style="border-radius:8px;">
                        <option value="" {{ !$tahunFilter ? 'selected' : '' }}>Semua Tahun</option>
                        @foreach($daftarTahun as $tahun)
                            <option value="{{ $tahun }}" {{ $tahun == $tahunFilter ? 'selected' : '' }}>{{ $tahun }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary fw-bold px-4" style="border-radius:8px; white-space:nowrap;">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MAIN CONTENT ROW ===== --}}
    <div class="row g-4">

        {{-- ===== PIE CHARTS ===== --}}
        <div class="col-lg-8">
            <div class="card shadow-sm" style="border-radius:12px; background: rgba(255,255,255,0.93);">
                <div class="card-body p-3">
                    <h6 class="fw-bold text-center text-dark mb-3">Hasil Survei per Pendidikan</h6>
                    <div class="row row-cols-1 row-cols-md-2 g-3">
                        @foreach ($dataPerStrata as $strata => $pendidikanList)
                            @foreach ($pendidikanList as $pendidikan => $kategoriData)
                                <div class="col">
                                    <div class="card h-100 border-0 shadow-sm" style="border-radius:10px; background:#f8f9fa;">
                                        <div class="card-body p-2">
                                            <p class="text-center fw-semibold mb-1" style="font-size:0.85rem; color:#333;">{{ $strata }}</p>
                                            <p class="text-center text-muted mb-2" style="font-size:0.78rem;">{{ $pendidikan }}</p>
                                            <canvas id="pie_{{ md5($strata.'_'.$pendidikan) }}" style="max-height:200px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                    {{-- Legend --}}
                    <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
                        @php
                            $legendColors = ['#4CAF50','#36A2EB','#FFCE56','#FF6384','#9966FF'];
                            $legends = ['Sangat Puas','Puas','Netral','Tidak Puas','Sangat Tidak Puas'];
                        @endphp
                        @foreach($legends as $i => $leg)
                            <span class="d-flex align-items-center gap-1" style="font-size:0.72rem; color:#444;">
                                <span style="display:inline-block; width:12px; height:12px; border-radius:2px; background:{{ $legendColors[$i] }};"></span>
                                {{ $leg }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== STAT CARDS ===== --}}
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-3">

                {{-- Total Responden --}}
                <div class="card text-white shadow" style="border-radius:12px; background: linear-gradient(135deg,#1565C0,#1976D2);">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:48px;height:48px;background:rgba(255,255,255,0.2);">
                            <i class="fa fa-users fa-lg"></i>
                        </div>
                        <div>
                            <div class="small text-white-50 fw-semibold">Total Responden</div>
                            <div class="fw-bold" style="font-size:1.6rem; line-height:1.1;">{{ number_format($totalResponden) }}</div>
                        </div>
                    </div>
                </div>

                {{-- Survei Selesai --}}
                <div class="card text-white shadow" style="border-radius:12px; background: linear-gradient(135deg,#00897B,#26A69A);">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:48px;height:48px;background:rgba(255,255,255,0.2);">
                            <i class="fa fa-check-circle fa-lg"></i>
                        </div>
                        <div>
                            <div class="small text-white-50 fw-semibold">Survei Selesai</div>
                            <div class="fw-bold" style="font-size:1.6rem; line-height:1.1;">{{ number_format($surveiSelesai) }}</div>
                        </div>
                    </div>
                </div>

                {{-- Survei Menunggu --}}
                <div class="card text-white shadow" style="border-radius:12px; background: linear-gradient(135deg,#E65100,#FB8C00);">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:48px;height:48px;background:rgba(255,255,255,0.2);">
                            <i class="fa fa-hourglass-half fa-lg"></i>
                        </div>
                        <div>
                            <div class="small text-white-50 fw-semibold">Survei Menunggu</div>
                            <div class="fw-bold" style="font-size:1.6rem; line-height:1.1;">{{ number_format($surveiMenunggu) }}</div>
                        </div>
                    </div>
                </div>

                {{-- Kepuasan Rata-rata --}}
                <div class="card text-white shadow" style="border-radius:12px; background: linear-gradient(135deg,#6A1B9A,#8E24AA);">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:48px;height:48px;background:rgba(255,255,255,0.2);">
                            <i class="fa fa-bar-chart fa-lg"></i>
                        </div>
                        <div>
                            <div class="small text-white-50 fw-semibold">Kepuasan Rata-rata</div>
                            <div class="fw-bold" style="font-size:1.6rem; line-height:1.1;">{{ $kepuasanRataRata }}%</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ===== BOTTOM ROW: Survei Terbaru | Tren Bulanan | Kepuasan Per Departemen ===== --}}
    <div class="row g-4 mt-1">

        {{-- Survei Terbaru --}}
        <div class="col-lg-4">
            <div class="card shadow-sm h-100" style="border-radius:12px; background:rgba(255,255,255,0.93);">
                <div class="card-header fw-bold text-dark border-0 pb-0" style="background:transparent; font-size:0.95rem;">
                    <i class="fa fa-clipboard-list me-2 text-primary"></i>Survei Terbaru
                </div>
                <div class="card-body p-3">
                    @forelse($surveiTerbaru as $s)
                        <div class="d-flex align-items-center justify-content-between border-bottom py-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                     style="width:32px;height:32px;background:#e3f2fd;color:#1565C0;font-size:0.75rem;font-weight:bold;">
                                    {{ $loop->iteration }}
                                </div>
                                <div>
                                    <div class="fw-semibold" style="font-size:0.82rem;">Survei {{ $loop->iteration }}</div>
                                    <div class="text-muted" style="font-size:0.72rem;">{{ \Carbon\Carbon::parse($s->created_at)->format('d M Y') }}</div>
                                </div>
                            </div>
                            <span class="badge bg-success" style="font-size:0.7rem;">Selesai</span>
                        </div>
                    @empty
                        <p class="text-muted text-center mt-3" style="font-size:0.85rem;">Belum ada data survei.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Tren Kepuasan Bulanan --}}
        <div class="col-lg-4">
            <div class="card shadow-sm h-100" style="border-radius:12px; background:rgba(255,255,255,0.93);">
                <div class="card-header fw-bold text-dark border-0 pb-0" style="background:transparent; font-size:0.95rem;">
                    <i class="fa fa-line-chart me-2 text-success"></i>Tren Kepuasan Bulanan
                </div>
                <div class="card-body p-3">
                    <canvas id="chartTrenBulanan" style="max-height:220px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Kepuasan Per Departemen --}}
        <div class="col-lg-4">
            <div class="card shadow-sm h-100" style="border-radius:12px; background:rgba(255,255,255,0.93);">
                <div class="card-header fw-bold text-dark border-0 pb-0" style="background:transparent; font-size:0.95rem;">
                    <i class="fa fa-building me-2 text-warning"></i>Kepuasan Per Departemen
                </div>
                <div class="card-body p-3">
                    <canvas id="chartKepuasanDept" style="max-height:220px;"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const pieColors = ['#4CAF50', '#36A2EB', '#FFCE56', '#FF6384', '#9966FF'];

    // ===== PIE CHARTS =====
    @foreach ($dataPerStrata as $strata => $pendidikanList)
        @foreach ($pendidikanList as $pendidikan => $kategoriData)
            new Chart(document.getElementById('pie_{{ md5($strata.'_'.$pendidikan) }}'), {
                type: 'pie',
                data: {
                    labels: @json($kategoriJawaban),
                    datasets: [{
                        data: @json(array_values($kategoriData)),
                        backgroundColor: pieColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        @endforeach
    @endforeach

    // ===== TREN BULANAN =====
    const trenLabels = @json(array_keys($trenBulanan));
    const trenData = @json(array_values($trenBulanan));
    new Chart(document.getElementById('chartTrenBulanan'), {
        type: 'bar',
        data: {
            labels: trenLabels,
            datasets: [{
                label: 'Rata-rata Kepuasan',
                data: trenData,
                backgroundColor: 'rgba(54,162,235,0.7)',
                borderColor: '#36A2EB',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, max: 5,
                     ticks: { font: { size: 10 } } },
                x: { ticks: { font: { size: 9 }, maxRotation: 45 } }
            }
        }
    });

    // ===== KEPUASAN PER DEPARTEMEN =====
    const deptLabels = @json(array_keys($kepuasanPerDepartemen));
    const deptData = @json(array_values($kepuasanPerDepartemen));
    const deptColors = ['#4CAF50','#36A2EB','#FFCE56','#FF6384','#9966FF','#FF9800','#00BCD4','#E91E63','#8BC34A','#673AB7'];
    new Chart(document.getElementById('chartKepuasanDept'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Kepuasan',
                data: deptData,
                backgroundColor: deptColors.slice(0, deptLabels.length),
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, max: 5,
                     ticks: { font: { size: 10 } } },
                y: { ticks: { font: { size: 9 } } }
            }
        }
    });

});
</script>
@endsection
