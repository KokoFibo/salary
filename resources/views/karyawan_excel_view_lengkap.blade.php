<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Karyawan Excel View</title>
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
                <th style="text-align: center;">Nama Lengkap</th>
                <th style="text-align: center;">Tanggal Bergabung</th>
                <th style="text-align: center;">Status Karyawan</th>
                <th style="text-align: center;">PTKP</th>
                <th style="text-align: center;">Company</th>
                <th style="text-align: center;">Directorate</th>
                <th style="text-align: center;">Department</th>
                <th style="text-align: center;">Jabatan</th>
                <th style="text-align: center;">Job Grade</th>
                <th style="text-align: center;">Email</th>
                <th style="text-align: center;">No Handphone</th>
                <th style="text-align: center;">No Telepon</th>
                <th style="text-align: center;">Tempat Lahir</th>
                <th style="text-align: center;">Tanggal Lahir</th>
                <th style="text-align: center;">Gender</th>
                <th style="text-align: center;">Status Pernikahan</th>
                <th style="text-align: center;">Golongan Darah</th>
                <th style="text-align: center;">Agama</th>
                <th style="text-align: center;">Etnis</th>
                <th style="text-align: center;">Nama Kontak Darurat 1</th>
                <th style="text-align: center;">Nomor Handphone Kontak Darurat 1</th>
                <th style="text-align: center;">Hubungan Kontrak Darurat 1</th>
                <th style="text-align: center;">Nama Kontak Darurat 2</th>
                <th style="text-align: center;">Nomor Handphone Kontak Darurat 2</th>
                <th style="text-align: center;">Hubungan Kontrak Darurat 2</th>
                <th style="text-align: center;">Jenis Identitas</th>
                <th style="text-align: center;">Nomor Identitas</th>
                <th style="text-align: center;">Alamat Identitas</th>
                <th style="text-align: center;">Alamat Domisili</th>
                <th style="text-align: center;">Nomor rekening</th>
                <th style="text-align: center;">NPWP</th>
                <th style="text-align: center;">Metode Penggajian</th>
                <th style="text-align: center;">Gaji Pokok</th>
                <th style="text-align: center;">Tunjangan Bahasa</th>
                <th style="text-align: center;">Tunjangan Housing</th>
                <th style="text-align: center;">Tunjangan Jabatan</th>
                <th style="text-align: center;">Gaji Lembur</th>
                <th style="text-align: center;">Gaji BPJS</th>
                <th style="text-align: center;">Iuran Air</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $d)
                <tr>
                    <td style="text-align: center"> {{ $d->id_karyawan }}</td>
                    <td style="text-align: center"> {{ $d->nama }}</td>
                    <th style="text-align: center;">{{ $d->tanggal_bergabung }}</th>
                    <th style="text-align: center;">{{ $d->status_karyawan }}</th>
                    <th style="text-align: center;">{{ $d->ptkp }}</th>
                    <th style="text-align: center;">{{ $d->company->company_name }}</th>
                    <th style="text-align: center;">{{ $d->placement->placement_name }}</th>
                    <th style="text-align: center;">{{ $d->department->nama_department }}</th>
                    <th style="text-align: center;">{{ $d->jabatan->nama_jabatan }}</th>
                    <th style="text-align: center;">{{ getGrade($d->level_jabatan) }}</th>
                    <th style="text-align: center;">{{ $d->email }}</th>
                    <th style="text-align: center;">{{ $d->hp }}</th>
                    <th style="text-align: center;">{{ $d->telepon }}</th>
                    <th style="text-align: center;">{{ $d->tempat_lahir }}</th>
                    <th style="text-align: center;">{{ $d->tanggal_lahir }}</th>
                    <th style="text-align: center;">{{ $d->gender }}</th>
                    <th style="text-align: center;">{{ $d->status_pernikahan }}</th>
                    <th style="text-align: center;">{{ $d->golongan_darah }}</th>
                    <th style="text-align: center;">{{ $d->agama }}</th>
                    <th style="text-align: center;">{{ $d->etnis }}</th>
                    <th style="text-align: center;">{{ $d->kontak_darurat }}</th>
                    <th style="text-align: center;">{{ $d->hp1 }}</th>
                    <th style="text-align: center;">{{ $d->hubungan1 }}</th>
                    <th style="text-align: center;">{{ $d->kontak_darurat2 }}</th>
                    <th style="text-align: center;">{{ $d->hp2 }}</th>
                    <th style="text-align: center;">{{ $d->hubungan2 }}</th>
                    <th style="text-align: center;">{{ $d->jenis_identitas }}</th>
                    @php

                        // $npwp = str_pad((string) $d->no_npwp, 15, '0', STR_PAD_LEFT);
                        // $ktp = str_pad((string) trim($d->no_identitas), 16, '0', STR_PAD_LEFT);
                        // $ktp = (string) trim($d->no_identitas);

                        // // $npwp = "'" . str_pad((string) $d->no_npwp, 15, '0', STR_PAD_LEFT);
                        // // $npwp = "'" . $d->no_npwp;

                        // if ($d->id_karyawan == 1146) {
                        //     dd($ktp, $d->no_identitas, $npwp, $d->no_npwp);
                        // }
                    @endphp
                    {{-- <th style="text-align: center;">{{ $ktp }}</th> --}}
                    <th style="text-align: center;">{{ "'" . trim($d->no_identitas) }}</th>

                    <th style="text-align: center;">{{ $d->alamat_identitas }}</th>
                    <th style="text-align: center;">{{ $d->alamat_tinggal }}</th>
                    {{-- <th style="text-align: center;">{{ $d->nomor_rekening }}</th> --}}
                    <th style="text-align: center;">{{ "'" . trim($d->nomor_rekening) }}</th>

                    <th style="text-align: center;">{{ "'" . trim($d->no_npwp) }}</th>
                    {{-- <th style="text-align: center;">{{ $npwp }}</th> --}}
                    <th style="text-align: center;">{{ $d->metode_penggajian }}</th>
                    <th style="text-align: center;">{{ $d->gaji_tetap }}</th>
                    <th style="text-align: center;">{{ $d->tunjangan_bahasa }}</th>
                    <th style="text-align: center;">{{ $d->tunjangan_housing }}</th>
                    <th style="text-align: center;">{{ $d->tunjangan_jabatan }}</th>
                    <th style="text-align: center;">{{ $d->gaji_overtime }}</th>
                    <th style="text-align: center;">{{ $d->gaji_bpjs }}</th>
                    <th style="text-align: center;">{{ $d->iuran_air }}</th>
                </tr>
            @endforeach

        </tbody>
    </table>
</body>

</html>
