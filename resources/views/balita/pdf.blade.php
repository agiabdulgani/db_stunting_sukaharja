<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Balita & Stunting Singaparna</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 14pt; }
        .header h3 { margin: 2px 0; font-size: 12pt; }
        .header p { margin: 2px 0; font-size: 10pt; color: #555; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PEMERINTAH KABUPATEN TASIKMALAYA</h2>
        <h3>KANTOR KECAMATAN SINGAPARNA</h3>
        <p>Laporan Data Balita dan Status Penimbangan Stunting</p>
        <hr>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="22%">NIK</th>
                <th width="20%">Nama Balita</th>
                <th width="5%">L/P</th>
                <th width="20%">Nama Orang Tua</th>
                <th width="13%">Posyandu</th>
                <th width="15%">Status Gizi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($balitas as $index => $b)
            @php
                $status = $b->penimbanganTerakhir->status_gizi ?? $b->status_gizi ?? 'Belum Ditimbang';
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $b->nik }}</td>
                <td>{{ ucwords(strtolower($b->nama)) }}</td>
                <td class="text-center">{{ $b->jenis_kelamin }}</td>
                <td>{{ ucwords(strtolower($b->nama_orang_tua ?? '-')) }}</td>
                <td>{{ $b->posyandu ?? '-' }}</td>
                <td>{{ $status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>