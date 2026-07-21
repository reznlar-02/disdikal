@php
// Helper untuk tabel
function renderKategoriTable($kategoriJawaban, $kategoriData) {
    $html = '<tr>';
    foreach ($kategoriJawaban as $k) {
        $html .= '<th style="padding:4px 8px;">' . $k . '</th>';
    }
    $html .= '</tr><tr>';
    foreach ($kategoriJawaban as $k) {
        $html .= '<td style="padding:4px 8px;">' . ($kategoriData[$k] ?? 0) . '</td>';
    }
    $html .= '</tr>';
    return $html;
}
@endphp
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        h2 { margin-bottom: 0.5em; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 1em; }
        th, td { border: 1px solid #333; padding: 4px 8px; text-align: center; }
    </style>
</head>
<body>
    <h2>Rekapitulasi Hasil Survei Kepuasan Tahun {{ $tahunFilter }}</h2>
    <p><strong>Jumlah Responden Sudah Isi:</strong> {{ $totalSudahIsi }}</p>
    <h3>Rekap Pertanyaan & Penjabaran Jawaban per Pendidikan Tahun {{ $tahunFilter }}</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 180px; text-align:center;">Pendidikan</th>
                <th style="text-align:center;">Pertanyaan</th>
                @foreach($kategoriJawaban as $kategori)
                    <th style="width: 80px; text-align:center;">{{ $kategori }}</th>
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
                            <td style="text-align:center;">{{ $jawabanPerPertanyaan[$pertanyaan->idpertanyaansurvei][$kategori] ?? 0 }}</td>
                        @endforeach
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html> 