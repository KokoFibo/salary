<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $status }}</title>
</head>


<body>
    <table class="table">
        <thead>
            <tr>
                <th colspan="12" style="font-size:20px;  text-align:center">
                    <h3>{{ $header_text }}</h3>
                </th>
            </tr>
            <tr>
                <th style="text-align: center;">ID</th>
                <th style="text-align: center;">Nama Karyawan</th>
                <th style="text-align: center;">NIK/Passport</th>
                <th style="text-align: center;">No Identitas</th>
                <th style="text-align: center;">Tempat Lahir</th>
                <th style="text-align: center;">Tanggal Lahir</th>
                <th style="text-align: center;">Jenis Kelamin</th>
                <th style="text-align: center;">Alamat ID</th>
                <th style="text-align: center;">Alamat Tinggal</th>
                <th style="text-align: center;">Email</th>
                <th style="text-align: center;">No HP</th>
                <th style="text-align: center;">Company</th>
                <th style="text-align: center;">Directorate</th>
                <th style="text-align: center;">Jabatan</th>
                <th style="text-align: center;">Departement</th>
                <th style="text-align: center;">Tanggal Bergabung</th>
                <th style="text-align: center;">Tanggal {{ $nama_status }}</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $d)
                <tr>
                    <td style="text-align: center"> {{ $d->id_karyawan }}</td>
                    <td style="text-align: center"> {{ $d->nama }}</td>
                    <td style="text-align: center"> {{ $d->jenis_identitas }}</td>
                    <td style="text-align: center">'{{ $d->no_identitas_str }}</td>
                    <td style="text-align: center"> {{ $d->tempat_lahir }}</td>
                    <td style="text-align: center"> {{ $d->tanggal_lahir }}</td>
                    <td style="text-align: center"> {{ $d->gender }}</td>
                    <td style="text-align: center"> {{ $d->alamat_identitas }}</td>
                    <td style="text-align: center"> {{ $d->alamat_tinggal }}</td>
                    <td style="text-align: center"> {{ $d->email }}</td>
                    <td style="text-align: center"> {{ $d->hp }}</td>
                    <td style="text-align: center"> {{ $d->company->company_name }}</td>
                    <td style="text-align: center"> {{ $d->placement->placement_name }}</td>
                    <td style="text-align: center"> {{ $d->jabatan->nama_jabatan }}</td>
                    <td style="text-align: center"> {{ $d->department->nama_department }}</td>
                    <td style="text-align: center"> {{ $d->tanggal_bergabung }}</td>
                    @if ($status == 2)
                        <td style="text-align: center"> {{ $d->tanggal_resigned }}</td>
                    @endif
                    @if ($status == 3)
                        <td style="text-align: center"> {{ $d->tanggal_blacklist }}</td>
                    @endif
                </tr>
            @endforeach

        </tbody>
    </table>
</body>

</html>
