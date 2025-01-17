<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>


<body>
    <table class="table">
        <thead>
            <tr>
                <th colspan="13" style="font-size:20px;  text-align:center">
                    <h3>{{ $header_text }}</h3>
                </th>
            </tr>

            <tr>
                <th style="text-align: center;">No</th>
                <th style="text-align: center;">Nama Karyawan</th>
                <th style="text-align: center;">ID Karyawan</th>
                <th style="text-align: center;">Januari</th>
                <th style="text-align: center;">Februari</th>
                <th style="text-align: center;">Maret</th>
                <th style="text-align: center;">April</th>
                <th style="text-align: center;">Mei</th>
                <th style="text-align: center;">Juni</th>
                <th style="text-align: center;">Juli</th>
                <th style="text-align: center;">Agustus</th>
                <th style="text-align: center;">September</th>
                <th style="text-align: center;">Oktober</th>
                <th style="text-align: center;">November</th>
                <th style="text-align: center;">Desember</th>
            </tr>

        </thead>
        <tbody>
            @forelse ($karyawans as $index => $karyawan)
                <tr>
                    <td style="text-align: center">{{ $index + 1 }}</td>
                    <td style="text-align: center">{{ $karyawan['nama'] }}</td>
                    <td style="text-align: center">{{ $karyawan['id_karyawan'] }}</td>
                    @foreach ($karyawan['gaji_per_bulan'] as $bulan => $gaji)
                        <td style="text-align: right">{{ $gaji !== null ? number_format($gaji, 0, ',', '.') : '-' }}
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="14" class="text-center">Tidak ada data karyawan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
