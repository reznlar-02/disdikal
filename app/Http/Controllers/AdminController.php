<?php

namespace App\Http\Controllers;

use App\Models\JawabansurveiModel;
use App\Models\PendidikanModel;
use App\Models\PertanyaansurveiModel;
use App\Models\StrataModel;
use Illuminate\Http\Request;
use App\Models\KunjunganModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    const KATEGORI_JAWABAN = ['Sangat Puas', 'Puas', 'Netral', 'Tidak Puas', 'Sangat Tidak Puas'];

    // Pengaman terakhir terhadap race condition: kalau dua request paralel
    // lolos bareng dari validasi unique (yang cuma SELECT sesaat sebelum
    // insert/update), unique index di database akan menolak salah satunya.
    // Tangkap itu di sini jadi pesan error yang rapi, bukan crash 500.
    private function saveOrDuplicateError(\Closure $save, string $field, string $duplicateMessage)
    {
        try {
            $save();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() !== '23000') {
                throw $e;
            }
            return back()->withInput()->withErrors([$field => $duplicateMessage]);
        }
        return null;
    }

    public function dashboard(Request $request)
    {
        $kategoriJawaban = self::KATEGORI_JAWABAN;
        $dataPerStrata = [];
        $strataList = StrataModel::all();

        // Ambil daftar tahun dari data survei
        $daftarTahun = JawabansurveiModel::selectRaw('YEAR(created_at) as tahun')
            ->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        $tahunFilter = $request->input('tahun', null); // null = semua tahun

        foreach ($strataList as $strata) {
            $pendidikanList = PendidikanModel::where('idstrata', $strata->idstrata)->get();
            foreach ($pendidikanList as $pendidikan) {
                $kategoriData = array_fill_keys($kategoriJawaban, 0);
                $jawabanQuery = JawabansurveiModel::whereHas('pertanyaansurvei', function ($q) use ($pendidikan) {
                    $q->where('idpendidikan', $pendidikan->idpendidikan);
                });
                if ($tahunFilter) {
                    $jawabanQuery->whereYear('created_at', $tahunFilter);
                }
                $jawaban = $jawabanQuery->get();
                foreach ($jawaban as $item) {
                    if (in_array($item->jawaban, $kategoriJawaban)) {
                        $kategoriData[$item->jawaban]++;
                    }
                }
                $dataPerStrata[$strata->strata][$pendidikan->pendidikan] = $kategoriData;
            }
        }

        // --- Statistik Kartu ---
        $totalResponden = KunjunganModel::when($tahunFilter, fn($q) => $q->whereYear('created_at', $tahunFilter))->count();
        $surveiSelesai  = $totalResponden;
        $surveiMenunggu = 0;

        // Kepuasan rata-rata (bobot: Sangat Puas=5, Puas=4, Netral=3, Tidak Puas=2, Sangat Tidak Puas=1)
        $bobotMap = ['Sangat Puas' => 5, 'Puas' => 4, 'Netral' => 3, 'Tidak Puas' => 2, 'Sangat Tidak Puas' => 1];
        $totalBobot  = 0;
        $totalJawaban = 0;
        foreach ($dataPerStrata as $strataData) {
            foreach ($strataData as $kData) {
                foreach ($kData as $kat => $jml) {
                    $totalBobot   += ($bobotMap[$kat] ?? 0) * $jml;
                    $totalJawaban += $jml;
                }
            }
        }
        $kepuasanRataRata = $totalJawaban > 0 ? round($totalBobot / $totalJawaban, 2) : 0;

        // --- Survei Terbaru (5 kunjungan terakhir) ---
        $surveiTerbaru = KunjunganModel::when($tahunFilter, fn($q) => $q->whereYear('created_at', $tahunFilter))
            ->orderBy('created_at', 'desc')->limit(5)->get();

        // --- Tren Kepuasan Bulanan (12 bulan terakhir) ---
        $trenBulanan = [];
        for ($i = 11; $i >= 0; $i--) {
            $bulan      = now()->subMonths($i);
            $bulanLabel = $bulan->format('M Y');
            $jawabanBulan = JawabansurveiModel::whereYear('created_at', $bulan->year)
                ->whereMonth('created_at', $bulan->month)->get();
            $totalB    = $jawabanBulan->count();
            $bobotTotal = 0;
            foreach ($jawabanBulan as $j) {
                $bobotTotal += $bobotMap[$j->jawaban] ?? 0;
            }
            $trenBulanan[$bulanLabel] = $totalB > 0 ? round($bobotTotal / $totalB, 2) : 0;
        }

        // --- Kepuasan Per Departemen/Pendidikan ---
        $kepuasanPerDepartemen = [];
        foreach ($dataPerStrata as $strataName => $pList) {
            foreach ($pList as $pendidikanName => $kData) {
                $totalD = array_sum($kData);
                $bobotD = 0;
                foreach ($kData as $kat => $jml) {
                    $bobotD += ($bobotMap[$kat] ?? 0) * $jml;
                }
                $kepuasanPerDepartemen[$pendidikanName] = $totalD > 0 ? round($bobotD / $totalD, 2) : 0;
            }
        }

        return view('admin.dashboard', compact(
            'kategoriJawaban', 'dataPerStrata', 'daftarTahun', 'tahunFilter',
            'totalResponden', 'surveiSelesai', 'surveiMenunggu', 'kepuasanRataRata',
            'surveiTerbaru', 'trenBulanan', 'kepuasanPerDepartemen'
        ));
    }

    public function strata()
    {
        $data['strata'] = StrataModel::all();
        return view('admin.strata', $data);
    }

    public function stratasimpan(Request $request)
    {
        $request->validate(['strata' => ['required', 'unique:strata,strata']]);
        if ($response = $this->saveOrDuplicateError(
            fn() => StrataModel::create(['strata' => $request->strata]),
            'strata',
            'Strata ini sudah ada.'
        )) {
            return $response;
        }
        return back()->with('success', 'Data berhasil disimpan');
    }

    public function strataedit($id)
    {
        $data['strata'] = StrataModel::find($id);
        return view('admin.strataedit', $data);
    }

    public function strataupdate(Request $request, $id)
    {
        $request->validate([
            'strata' => ['required', Rule::unique('strata', 'strata')->ignore($id, 'idstrata')],
        ]);
        if ($response = $this->saveOrDuplicateError(
            fn() => StrataModel::where('idstrata', $id)->update(['strata' => $request->strata]),
            'strata',
            'Strata ini sudah ada.'
        )) {
            return $response;
        }
        return redirect('admin/strata')->with('success', 'Data berhasil diperbarui');
    }

    public function stratahapus($id)
    {
        if (PendidikanModel::where('idstrata', $id)->exists()) {
            return back()->with('error', 'Strata tidak bisa dihapus karena masih memiliki data Pendidikan.');
        }
        StrataModel::where('idstrata', $id)->delete();
        return back()->with('success', 'Data berhasil dihapus');
    }

    // pendidikan
    public function pendidikan()
    {
        $data['pendidikan'] = PendidikanModel::with('strata')
            ->join('strata', 'pendidikan.idstrata', '=', 'strata.idstrata')
            ->orderByRaw("CASE 
                WHEN strata.strata = 'PERWIRA' THEN 1 
                WHEN strata.strata = 'BINTARA' THEN 2 
                WHEN strata.strata = 'TAMTAMA' THEN 3 
                ELSE 4 END")
            ->orderBy('pendidikan.pendidikan')
            ->select('pendidikan.*')
            ->get();
        $data['strata'] = StrataModel::all();
        return view('admin.pendidikan', $data);
    }

    public function pendidikansimpan(Request $request)
    {
        $request->validate([
            'idstrata' => 'required|numeric',
            'pendidikan' => [
                'required',
                Rule::unique('pendidikan', 'pendidikan')->where('idstrata', $request->idstrata),
            ],
        ]);
        if ($response = $this->saveOrDuplicateError(
            fn() => PendidikanModel::create(['idstrata' => $request->idstrata, 'pendidikan' => $request->pendidikan]),
            'pendidikan',
            'Pendidikan ini sudah ada untuk strata yang dipilih.'
        )) {
            return $response;
        }
        return back()->with('success', 'Data berhasil disimpan');
    }

    public function pendidikanedit($id)
    {
        $data['pendidikan'] = PendidikanModel::find($id);
        $data['strata']     = StrataModel::all();
        return view('admin.pendidikanedit', $data);
    }

    public function pendidikanupdate(Request $request, $id)
    {
        $request->validate([
            'idstrata' => 'required|numeric',
            'pendidikan' => [
                'required',
                Rule::unique('pendidikan', 'pendidikan')->where('idstrata', $request->idstrata)->ignore($id, 'idpendidikan'),
            ],
        ]);
        if ($response = $this->saveOrDuplicateError(
            fn() => PendidikanModel::where('idpendidikan', $id)->update([
                'idstrata'   => $request->idstrata,
                'pendidikan' => $request->pendidikan,
            ]),
            'pendidikan',
            'Pendidikan ini sudah ada untuk strata yang dipilih.'
        )) {
            return $response;
        }
        return redirect('admin/pendidikan')->with('success', 'Data berhasil diperbarui');
    }

    public function pendidikanhapus($id)
    {
        if (PertanyaansurveiModel::where('idpendidikan', $id)->exists()) {
            return back()->with('error', 'Pendidikan tidak bisa dihapus karena masih memiliki data Pertanyaan Survei.');
        }
        if (KunjunganModel::where('idpendidikan', $id)->exists()) {
            return back()->with('error', 'Pendidikan tidak bisa dihapus karena sudah ada responden yang mengisi survei untuk pendidikan ini.');
        }
        PendidikanModel::where('idpendidikan', $id)->delete();
        return back()->with('success', 'Data berhasil dihapus');
    }

    // pertanyaansurvei
    public function pertanyaansurvei()
    {
        $data['strata']    = StrataModel::all();
        $data['pertanyaan'] = PertanyaansurveiModel::with(['pendidikan', 'pendidikan.strata'])
            ->join('pendidikan', 'pertanyaansurvei.idpendidikan', '=', 'pendidikan.idpendidikan')
            ->join('strata', 'pendidikan.idstrata', '=', 'strata.idstrata')
            ->orderBy('strata.strata')
            ->orderBy('pendidikan.pendidikan')
            ->orderBy('pertanyaansurvei.pertanyaan')
            ->select('pertanyaansurvei.*')
            ->get();
        return view('admin.pertanyaansurvei', $data);
    }

    public function getpendidikanbystrata($id)
    {
        $data = PendidikanModel::where('idstrata', $id)->get();
        return response()->json($data);
    }

    public function pertanyaansurveisimpan(Request $request)
    {
        $request->validate(['idpendidikan' => 'required|numeric', 'draft_pertanyaan' => 'required']);
        $pertanyaanInput = $request->input('draft_pertanyaan');
        $idpendidikan    = $request->input('idpendidikan');
        $pertanyaanList  = preg_split('/\r?\n/', $pertanyaanInput);

        $existing = PertanyaansurveiModel::where('idpendidikan', $idpendidikan)
            ->pluck('pertanyaan')->map(fn($p) => mb_strtolower(trim($p)))->all();

        $inserted = 0;
        $duplikat = 0;
        foreach ($pertanyaanList as $pertanyaan) {
            $pertanyaan = trim($pertanyaan);
            if ($pertanyaan === '') {
                continue;
            }
            if (in_array(mb_strtolower($pertanyaan), $existing)) {
                $duplikat++;
                continue;
            }
            try {
                PertanyaansurveiModel::create(['idpendidikan' => $idpendidikan, 'pertanyaan' => $pertanyaan]);
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() !== '23000') {
                    throw $e;
                }
                // Race condition: baris ini baru saja dibuat oleh request lain
                // di antara pengecekan $existing di atas dan create() ini.
                $duplikat++;
                continue;
            }
            $existing[] = mb_strtolower($pertanyaan);
            $inserted++;
        }

        $message = $inserted . ' pertanyaan berhasil disimpan';
        if ($duplikat > 0) {
            $message .= ', ' . $duplikat . ' dilewati karena sudah ada';
        }

        return back()->with('success', $message);
    }

    public function pertanyaansurveiedit($id)
    {
        $data['strata']    = StrataModel::all();
        $data['pendidikan'] = PendidikanModel::all();
        $data['pertanyaan'] = PertanyaansurveiModel::where('idpertanyaansurvei', $id)->first();
        return view('admin.pertanyaansurveiedit', $data);
    }

    public function pertanyaansurveiupdate(Request $request, $id)
    {
        $request->validate([
            'idpendidikan' => 'required|numeric',
            'pertanyaan' => [
                'required',
                Rule::unique('pertanyaansurvei', 'pertanyaan')->where('idpendidikan', $request->idpendidikan)->ignore($id, 'idpertanyaansurvei'),
            ],
        ]);
        if ($response = $this->saveOrDuplicateError(
            fn() => PertanyaansurveiModel::where('idpertanyaansurvei', $id)->update([
                'idpendidikan' => $request->idpendidikan,
                'pertanyaan'   => $request->pertanyaan,
            ]),
            'pertanyaan',
            'Pertanyaan ini sudah ada untuk pendidikan yang dipilih.'
        )) {
            return $response;
        }
        return redirect('admin/pertanyaansurvei')->with('success', 'Data berhasil diperbarui');
    }

    public function pertanyaansurveihapus($id)
    {
        if (JawabansurveiModel::where('idpertanyaansurvei', $id)->exists()) {
            return back()->with('error', 'Pertanyaan tidak bisa dihapus karena sudah ada jawaban survei untuk pertanyaan ini.');
        }
        PertanyaansurveiModel::where('idpertanyaansurvei', $id)->delete();
        return back()->with('success', 'Data berhasil dihapus');
    }

    // hasilsurvei
    public function hasilsurvei(Request $request)
    {
        $tahunFilter      = $request->input('tahun');
        $pendidikanFilter = $request->input('pendidikan');

        $tahunJawaban  = JawabansurveiModel::selectRaw('YEAR(created_at) as tahun')->distinct()->pluck('tahun')->toArray();
        $tahunKunjungan = KunjunganModel::selectRaw('YEAR(created_at) as tahun')->distinct()->pluck('tahun')->toArray();
        $daftarTahun   = array_unique(array_merge($tahunJawaban, $tahunKunjungan));
        $daftarTahun   = array_map('intval', $daftarTahun);
        $daftarTahun   = array_filter($daftarTahun, fn($t) => $t >= 2025);
        rsort($daftarTahun);
        $daftarTahun = array_values($daftarTahun);
        if (!$tahunFilter && $daftarTahun) {
            $tahunFilter = $daftarTahun[0];
        }

        $query = JawabansurveiModel::with('pertanyaansurvei.pendidikan');
        if ($tahunFilter) {
            $query->whereYear('created_at', $tahunFilter);
        }
        if ($pendidikanFilter) {
            $query->whereHas('pertanyaansurvei.pendidikan', fn($q) => $q->where('pendidikan', $pendidikanFilter));
        }
        $hasilSurvei     = $query->get();
        $kategoriJawaban = self::KATEGORI_JAWABAN;

        $dataTahun = $hasilSurvei->groupBy(fn($i) => \Carbon\Carbon::parse($i->created_at)->format('Y'))
            ->map(function ($items) use ($kategoriJawaban) {
                $kategoriData = array_fill_keys($kategoriJawaban, 0);
                foreach ($items as $item) {
                    if (in_array($item->jawaban, $kategoriJawaban)) $kategoriData[$item->jawaban]++;
                }
                return ['total' => $items->count(), 'kategori' => $kategoriData];
            });

        $dataPendidikan = $hasilSurvei->groupBy(fn($i) => $i->pertanyaansurvei->pendidikan->pendidikan ?? 'Tidak Diketahui')
            ->map(function ($items) use ($kategoriJawaban) {
                $kategoriData = array_fill_keys($kategoriJawaban, 0);
                foreach ($items as $item) {
                    if (in_array($item->jawaban, $kategoriJawaban)) $kategoriData[$item->jawaban]++;
                }
                return ['total' => $items->count(), 'kategori' => $kategoriData];
            });

        $tahunSekarang   = date('Y');
        $daftarTahun     = range($tahunSekarang - 9, $tahunSekarang);
        $daftarPendidikan = PendidikanModel::pluck('pendidikan')->toArray();

        $totalSudahIsi = KunjunganModel::when($tahunFilter, fn($q) => $q->whereYear('created_at', $tahunFilter))->count();

        [$pertanyaanPerPendidikan, $jawabanPerPertanyaan] = $this->buildRekapPertanyaan($tahunFilter);

        return view('admin.hasilsurvei', compact(
            'dataTahun', 'dataPendidikan', 'kategoriJawaban', 'daftarTahun',
            'daftarPendidikan', 'tahunFilter', 'pendidikanFilter',
            'totalSudahIsi', 'pertanyaanPerPendidikan', 'jawabanPerPertanyaan'
        ));
    }

    private function buildRekapPertanyaan(?string $tahunFilter): array
    {
        $kategoriJawaban = self::KATEGORI_JAWABAN;
        $pertanyaanPerPendidikan = [];
        $jawabanPerPertanyaan    = [];
        $pendidikanList          = PendidikanModel::with('strata')->get();
        foreach ($pendidikanList as $pendidikan) {
            $pertanyaanList = PertanyaansurveiModel::where('idpendidikan', $pendidikan->idpendidikan)->get();
            $pertanyaanPerPendidikan[$pendidikan->pendidikan] = $pertanyaanList;
            foreach ($pertanyaanList as $pertanyaan) {
                $jawabanPerPertanyaan[$pertanyaan->idpertanyaansurvei] = [];
                foreach ($kategoriJawaban as $kategori) {
                    $jawabanPerPertanyaan[$pertanyaan->idpertanyaansurvei][$kategori] =
                        JawabansurveiModel::where('idpertanyaansurvei', $pertanyaan->idpertanyaansurvei)
                            ->where('jawaban', $kategori)
                            ->when($tahunFilter, fn($q) => $q->whereYear('created_at', $tahunFilter))
                            ->count();
                }
            }
        }

        return [$pertanyaanPerPendidikan, $jawabanPerPertanyaan];
    }

    public function downloadHasilSurveiPdf(Request $request)
    {
        $tahunFilter      = $request->input('tahun');
        $pendidikanFilter = $request->input('pendidikan');

        $query = JawabansurveiModel::with('pertanyaansurvei.pendidikan');
        if ($tahunFilter) $query->whereYear('created_at', $tahunFilter);
        if ($pendidikanFilter) {
            $query->whereHas('pertanyaansurvei.pendidikan', fn($q) => $q->where('pendidikan', $pendidikanFilter));
        }
        $hasilSurvei     = $query->get();
        $kategoriJawaban = self::KATEGORI_JAWABAN;

        $dataTahun = $hasilSurvei->groupBy(fn($i) => \Carbon\Carbon::parse($i->created_at)->format('Y'))
            ->map(function ($items) use ($kategoriJawaban) {
                $kategoriData = array_fill_keys($kategoriJawaban, 0);
                foreach ($items as $item) {
                    if (in_array($item->jawaban, $kategoriJawaban)) $kategoriData[$item->jawaban]++;
                }
                return ['total' => $items->count(), 'kategori' => $kategoriData];
            });

        $dataPendidikan = $hasilSurvei->groupBy(fn($i) => $i->pertanyaansurvei->pendidikan->pendidikan ?? 'Tidak Diketahui')
            ->map(function ($items) use ($kategoriJawaban) {
                $kategoriData = array_fill_keys($kategoriJawaban, 0);
                foreach ($items as $item) {
                    if (in_array($item->jawaban, $kategoriJawaban)) $kategoriData[$item->jawaban]++;
                }
                return ['total' => $items->count(), 'kategori' => $kategoriData];
            });

        $totalSudahIsi = KunjunganModel::when($tahunFilter, fn($q) => $q->whereYear('created_at', $tahunFilter))->count();

        [$pertanyaanPerPendidikan, $jawabanPerPertanyaan] = $this->buildRekapPertanyaan($tahunFilter);

        $pdf = Pdf::loadView('admin.hasilsurvei_pdf', [
            'dataTahun'               => $dataTahun,
            'dataPendidikan'          => $dataPendidikan,
            'kategoriJawaban'         => $kategoriJawaban,
            'totalSudahIsi'           => $totalSudahIsi,
            'tahunFilter'             => $tahunFilter,
            'pertanyaanPerPendidikan' => $pertanyaanPerPendidikan,
            'jawabanPerPertanyaan'    => $jawabanPerPertanyaan,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('hasil_survei.pdf');
    }

    public function downloadHasilSurveiCsv(Request $request)
    {
        $tahunFilter = $request->input('tahun');
        [$pertanyaanPerPendidikan, $jawabanPerPertanyaan] = $this->buildRekapPertanyaan($tahunFilter);
        $kategoriJawaban = self::KATEGORI_JAWABAN;

        $filename = 'hasil_survei' . ($tahunFilter ? "_{$tahunFilter}" : '') . '.csv';

        return response()->streamDownload(function () use ($pertanyaanPerPendidikan, $jawabanPerPertanyaan, $kategoriJawaban) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // BOM, supaya Excel membaca karakter UTF-8 dengan benar
            fputcsv($handle, array_merge(['Pendidikan', 'Pertanyaan'], $kategoriJawaban));

            foreach ($pertanyaanPerPendidikan as $pendidikan => $pertanyaanList) {
                foreach ($pertanyaanList as $pertanyaan) {
                    $row = [$pendidikan, $pertanyaan->pertanyaan];
                    foreach ($kategoriJawaban as $kategori) {
                        $row[] = $jawabanPerPertanyaan[$pertanyaan->idpertanyaansurvei][$kategori] ?? 0;
                    }
                    fputcsv($handle, $row);
                }
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
