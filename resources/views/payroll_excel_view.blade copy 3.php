<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> --}}

    <title>Payroll Excel View</title>
</head>


<body>
    <table class="table">
        <thead>
            <tr>
                <th colspan="5" style="font-size:20px;  text-align:center">
                    <h3>{{ $header_text }}</h3>
                </th>
            </tr>
            {{-- TR baris atas utk colspan --}}
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th colspan="5" style="text-align: center; background-color: green; color:black">
                    <h4>Karyawan</h4>
                </th>

                <th colspan="5" style="text-align: center; background-color: blue; color:white">
                    <h4>Company</h4>
                </th>

                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            {{-- TR untuk title --}}
            <tr>
                <th style="text-align: center;">ID Karyawan</th>
                <th style="text-align: center;">Nama Karyawan</th>
                <th style="text-align: center;">Bank</th>
                <th style="text-align: center;">No. Rekening</th>
                <th style="text-align: center;">Jabatan</th>
                <th style="text-align: center;">Company</th>
                <th style="text-align: center;">Placement</th>
                <th style="text-align: center;">Department</th>
                <th style="text-align: center;">Metode Penggajian</th>
                <th style="text-align: center;">Total Hari Kerja</th>
                <th style="text-align: center;">Total Jam Kerja (Bersih)</th>
                <th style="text-align: center;">Total Jam Lembur</th>
                <th style="text-align: center;">Jumlah Jam Terlambat</th>
                <th style="text-align: center;">Tambahan Shift Malam</th>
                <th style="text-align: center;">Gaji Pokok</th>
                <th style="text-align: center;">Gaji Per Hari/Jam</th>
                <th style="text-align: center;">Gaji Bulan Ini</th>
                <th style="text-align: center;">Gaji Lembur</th>
                <th style="text-align: center;">Total Gaji Lembur</th>
                <th style="text-align: center;">Gaji Libur</th>
                <th style="text-align: center;">Bonus/U.Makan</th>
                <th style="text-align: center;">Potongan 1X</th>
                <th style="text-align: center;">Total NoScan</th>
                <th style="text-align: center;">Denda Lupa Absen</th>
                <th style="text-align: center;">Denda Resigned</th>
                <th style="text-align: center;">Tanggungan</th>
                <th style="text-align: center;">Iuran Air</th>
                <th style="text-align: center;">Iuran Locker</th>
                <th style="text-align: center;">Status Karyawan</th>
                <th style="text-align: center;">Gaji BPJS with adjustment</th>
                <th style="text-align: center;">Gaji BPJS</th>
                <th style="text-align: center;">JHT</th>
                <th style="text-align: center;">JP</th>
                <th style="text-align: center;">JKK</th>
                <th style="text-align: center;">JKM</th>
                <th style="text-align: center;">Kesehatan</th>
                <th style="text-align: center;">JHT</th>
                <th style="text-align: center;">JP</th>
                <th style="text-align: center;">JKK</th>
                <th style="text-align: center;">JKM</th>
                <th style="text-align: center;">Kesehatan</th>
                <th style="text-align: center;">Total TAX</th>
                {{-- <th style="text-align: center;">Perhitungan Tax Manual</th> --}}
                <th style="text-align: center;">PTKP</th>
                <th style="text-align: center;">TER</th>
                <th style="text-align: center;">Rate</th>
                <th style="text-align: center;">Pph21</th>
                <th style="text-align: center;">No NPWP</th>
                <th style="text-align: center;">Total</th>

            </tr>
        </thead>
        <tbody>

            @foreach ($data as $key => $d)
                <tr>
                    <td style="text-align: center"> {{ $d->id_karyawan }}</td>
                    <td> {{ $d->nama }}</td>
                    <td style="text-align: center"> {{ $d->nama_bank }}</td>
                    <td style="text-align: center"> {{ strval($d->nomor_rekening) }}</td>
                    <td style="text-align: center"> {{ nama_jabatan($d->jabatan_id) }}</td>
                    <td style="text-align: center"> {{ nama_company($d->company_id) }}</td>
                    <td style="text-align: center"> {{ nama_placement($d->placement_id) }}</td>
                    <td style="text-align: center"> {{ nama_department($d->department_id) }}</td>

                    <td style="text-align: center"> {{ $d->metode_penggajian }}</td>
                    <td> {{ $d->hari_kerja }}</td>
                    <td> {{ $d->jam_kerja }}</td>
                    <td> {{ $d->jam_lembur }}</td>
                    <td> {{ $d->jumlah_jam_terlambat }}</td>
                    <td style="text-align: right"> {{ $d->tambahan_shift_malam }}</td>
                    <td style="text-align: right"> {{ $d->gaji_pokok }}</td>

                    @php
                        if ($d->metode_penggajian == 'Perjam') {
                            $gaji_bulan_ini = total_gaji_perjam($d->gaji_pokok, $d->jam_kerja);
                        } else {
                            $gaji_bulan_ini = total_gaji_bulanan(
                                $d->gaji_pokok,
                                $d->hari_kerja,
                                $total_n_hari_kerja,
                                $jumlah_libur_nasional,
                                $d->date,
                                $d->id_karyawan,
                                $d->status_karyawan,
                            );
                        }
                        if ($d->gaji_pokok != 0) {
                            // $gaji_bpjs_adjust = $d->gaji_bpjs * $total_gaji_sebelum_tax / $d->gaji_pokok;
                            $gaji_bpjs_adjust = ($d->gaji_bpjs * $gaji_bulan_ini) / $d->gaji_pokok;
                        } else {
                            // Handle the case where gaji_pokok is zero (e.g., log an error or assign a default value)
                            $gaji_bpjs_adjust = 0; // or another fallback value
                            error_log('Division by zero error: gaji_pokok is zero for karyawan ID: ' . $d->id);
                        }

                        if ($d->gaji_bpjs >= 12000000) {
                            $gaji_bpjs_max = 12000000;
                        } else {
                            $gaji_bpjs_max = $d->gaji_bpjs;
                        }

                        if ($d->gaji_bpjs >= 10042300) {
                            $gaji_jp_max = 10042300;
                        } else {
                            $gaji_jp_max = $d->gaji_bpjs;
                        }
                        if ($d->kesehatan != 0) {
                            $kesehatan_company = ($gaji_bpjs_max * 4) / 100;
                        } else {
                            $kesehatan_company = 0;
                        }

                        if ($d->jkk) {
                            $jkk_company = ($d->gaji_bpjs * 0.24) / 100;
                        } else {
                            $jkk_company = 0;
                        }

                        if ($d->jkm) {
                            $jkm_company = ($d->gaji_bpjs * 0.3) / 100;
                        } else {
                            $jkm_company = 0;
                        }

                        if ($d->jp != 0) {
                            $jp_company = ($gaji_jp_max * 2) / 100;
                        } else {
                            $jp_company = 0;
                        }

                        if ($d->jht != 0) {
                            $jht_company = ($d->gaji_bpjs * 3.7) / 100;
                        } else {
                            $jht_company = ($d->gaji_bpjs * 3.7) / 100;
                        }

                        $total_bpjs_company = 0;
                        $total_bpjs_company =
                            $gaji_bpjs_adjust +
                            // $d->gaji_bpjs +
                            $jkk_company +
                            $jkm_company +
                            $kesehatan_company +
                            $d->gaji_lembur * $d->jam_lembur +
                            $d->gaji_libur +
                            $d->bonus1x +
                            $d->tambahan_shift_malam;

                        $ter = '';
                        switch ($d->ptkp) {
                            case 'TK0':
                                $ter = 'A';
                                break;
                            case 'TK1':
                                $ter = 'A';
                                break;
                            case 'TK2':
                                $ter = 'B';
                                break;
                            case 'TK3':
                                $ter = 'B';
                                break;
                            case 'K0':
                                $ter = 'A';
                                break;
                            case 'K1':
                                $ter = 'B';
                                break;
                            case 'K2':
                                $ter = 'B';
                                break;
                            case 'K3':
                                $ter = 'C';
                                break;
                        }

                        $rate_pph21 = get_rate_ter_pph21($d->ptkp, $total_bpjs_company);
                        $pph21 = ($total_bpjs_company * $rate_pph21) / 100;
                        // if ($d->id_karyawan == 1662) {
                        //     dd(
                        //         $total_bpjs_company,
                        //         $d->tambahan_shift_malam,
                        //         $d->gaji_lembur * $d->jam_lembur,
                        //         $d->gaji_libur,
                        //         $d->bonus1x,
                        //         $gaji_bpjs_adjust,
                        //         $jkk_company,
                        //         $jkm_company,
                        //         $kesehatan_company,
                        //         $d->kesehatan,
                        //         $rate_pph21,
                        //         $pph21,
                        //     );
                        // }
                    @endphp

                    @if ($d->metode_penggajian == 'Perjam')
                        <td style="text-align: right"> {{ $d->gaji_pokok / 198 }}</td>
                    @else
                        <td style="text-align: right"> {{ $d->gaji_pokok / $total_n_hari_kerja }}</td>
                    @endif
                    <td style="text-align: right"> {{ $gaji_bulan_ini }}</td>
                    <td style="text-align: right"> {{ $d->gaji_lembur }}</td>
                    <td style="text-align: right"> {{ $d->gaji_lembur * $d->jam_lembur }}</td>
                    <td style="text-align: right"> {{ $d->gaji_libur }}</td>
                    <td style="text-align: right"> {{ $d->bonus1x }}</td>
                    <td style="text-align: right"> {{ $d->potongan1x }}</td>
                    <td> {{ $d->total_noscan }}</td>
                    <td style="text-align: right"> {{ $d->denda_lupa_absen }}</td>
                    <td style="text-align: right"> {{ $d->denda_resigned }}</td>

                    <td> {{ $d->tanggungan }}</td>
                    <td style="text-align: right"> {{ $d->iuran_air }}</td>
                    <td style="text-align: right"> {{ $d->iuran_locker }}</td>
                    <td style="text-align: center"> {{ $d->status_karyawan }}</td>


                    <td style="text-align: right"> {{ $gaji_bpjs_adjust }}</td>
                    <td style="text-align: right"> {{ $d->gaji_bpjs }}</td>
                    <td style="text-align: right"> {{ $d->jht }}</td>
                    <td style="text-align: right"> {{ $d->jp }}</td>
                    @if ($d->jkk == 1)
                        <td style="text-align: right">Yes</td>
                    @else
                        <td style="text-align: right">No</td>
                    @endif
                    @if ($d->jkm == 1)
                        <td style="text-align: right">Yes</td>
                    @else
                        <td style="text-align: right">No</td>
                    @endif
                    {{-- <td style="text-align: right"> {{ $d->jkk }}</td>
                    <td style="text-align: right"> {{ $d->jkm }}</td> --}}
                    <td style="text-align: right"> {{ $d->kesehatan }}</td>

                    @if ($d->jht > 0)
                        <td style="text-align: right"> {{ $jht_company }}</td>
                    @else
                        <td style="text-align: right"></td>
                    @endif
                    @if ($d->jp > 0)
                        <td style="text-align: right"> {{ $jp_company }}</td>
                    @else
                        <td style="text-align: right"></td>
                    @endif

                    @if ($d->jkk == 1)
                        <td style="text-align: right"> {{ $jkk_company }}</td>
                    @else
                        <td style="text-align: right"></td>
                    @endif

                    @if ($d->jkm == 1)
                        <td style="text-align: right"> {{ $jkm_company }}</td>
                    @else
                        <td style="text-align: right"></td>
                    @endif

                    @if ($d->kesehatan > 0)
                        <td style="text-align: right"> {{ $kesehatan_company }}</td>
                    @else
                        <td style="text-align: right"></td>
                    @endif


                    {{-- <td style="text-align: right"> {{ $d->total_bpjs }}</td> --}}
                    <td style="text-align: right"> {{ $total_bpjs_company }}</td>
                    {{-- <td style="text-align: right">
                        {{ $d->gaji_lembur * $d->jam_lembur + $d->gaji_libur + $d->bonus1x + $d->tambahan_shift_malam + $gaji_bpjs_adjust + $jkk_company + $jkm_company + $kesehatan_company }}
                    </td> --}}
                    <td style="text-align: right"> {{ $d->ptkp }}</td>
                    <td style="text-align: right"> {{ $ter }}</td>
                    <td style="text-align: right"> {{ $rate_pph21 }}</td>
                    {{-- <td style="text-align: right"> {{ $d->pph21 }}</td> --}}
                    <td style="text-align: right"> {{ $pph21 }}</td>

                    <td style="text-align: right"> {{ no_npwp($d->id_karyawan) }}</td>
                    <td style="text-align: right"> {{ $d->total }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>
</body>

</html>
