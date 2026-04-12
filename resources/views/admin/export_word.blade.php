<html xmlns:office="urn:schemas-microsoft-com:office:office" xmlns:word="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <style>
        body { font-family: 'Times New Roman', serif; }
        .header { text-align: center; font-weight: bold; text-transform: uppercase; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 5px; text-align: center; font-size: 10pt; }
    </style>
</head>
<body>
    <div class="header">
        LAPORAN {{ strtoupper($jenis) }} PERPUSTAKAAN SMKN 3 BANJAR<br>
        PERIODE: {{ $periode }}
    </div>

    <p>Berikut adalah data {{ $jenis }} buku:</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Judul Buku</th>
                <th>Tgl Pinjam</th>
                <th>Jatuh Tempo</th>
                @if($jenis == 'pengembalian')
                    <th>Tgl Kembali</th>
                    <th>Status/Denda</th>
                @else
                    <th>Status</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td>{{ $row->id_pinjam }}</td>
                <td>{{ $row->user->name }}</td>
                <td>{{ $row->buku->judul }}</td>
                <td>{{ $row->tgl_pinjam }}</td>
                <td>{{ $row->tgl_jatuh_tempo }}</td>
                @if($jenis == 'pengembalian')
                    <td>{{ $row->tgl_kembali }}</td>
                    <td>{{ $row->denda > 0 ? 'Rp '.number_format($row->denda, 0,',','.') : '0' }}</td>
                @else
                    <td>{{ $row->status }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>