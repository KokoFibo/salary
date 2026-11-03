<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
</head>

<body>
    <table class="table">
        <thead>
            {{-- Header Utama / Judul --}}
            <tr>
                <th colspan="60" style="font-size: 20px; text-align: center;">
                    <h3>{{ $header_text }}</h3>
                </th>
            </tr>

            {{-- Sub-header Kelompok (Karyawan vs Company) --}}
            <tr>
                <th colspan="37"></th>
                <th colspan="5" style="text-align: center; background-color: #28a745; color: #ffffff;">
                    <h4>Karyawan</h4>
                </th>
                <th colspan="5" style="text-align: center; background-color: #007bff; color: #ffffff;">
                    <h4>Company</h4>
                </th>
                <th colspan="13"></th>
            </tr>

            {{-- Header Kolom Data --}}
            <tr>
                <th style="text-align: center;">ID Karyawan</th>
                <th style="text-align: center;">Nama Karyawan</th>
                <th style="text-align: center;">Bank</th>
                <th style="text-align: center;">No. Rekening</th>
                <th style="text-align: center;">Jabatan</th>
                <th style="text-align: center;">Company</th>
                <th style="text-align: center;">Directorate</th>
                <th style="text-align: center;">Department</th>
                <th style="text-align: center;">Metode Penggajian</th>
                <th style="text-align: center;">Total Hari Kerja</th>
                <th style="text-align: center;">Total Jam Kerja (Bersih)</th>
                <th style="text-align: center;">Total Jam Lembur</th>
                <th style="text-align: center;">Total Jam Kerja Libur</th>
                <th style="text-align: center;">Total Jam Lembur Libur</th>
                <th style="text-align: center;">Jumlah Jam Terlambat</th>
                <th style="text-align: center;">Tambahan Shift Malam</th>
                <th style="text-align: center;">Gaji</th>
                <th style="text-align: center;">Tunjangan Bahasa</th>
                <th style="text-align: center;">Tunjangan Housing</th>
                <th style="text-align: center;">Tunjangan Jabatan</th>
                <th style="text-align: center;">Gaji Per Hari/Jam</th>
                <th style="text-align: center;">Gaji Bulan Ini</th>
                <th style="text-align: center;">Gaji Lembur</th>
                <th style="text-align: center;">Total Gaji Lembur</th>
                <th style="text-align: center;">Total Gaji Lembur Libur</th>
                <th style="text-align: center;">Gaji Libur</th>
                <th style="text-align: center;">Bonus/U.Makan</th>
                <th style="text-align: center;">THR</th>
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
                <th style="text-align: center;">PTKP</th>
                <th style="text-align: center;">TER</th>
                <th style="text-align: center;">Rate</th>
                <th style="text-align: center;">Pph21</th>
                <th style="text-align: center;">NIK</th>
                <th style="text-align: center;">No NPWP</th>
                <th style="text-align: center;">Total</th>
                <th style="text-align: center; background-color: #63BDF6;">PRF Salary</th>
                <th style="text-align: center; background-color: #63BDF6;">Other Deductions</th>
                <th style="text-align: center; background-color: #63BDF6;">BPJS Employee</th>
                <th style="text-align: center; background-color: #63BDF6;">PPH21</th>
                <th style="text-align: center; background-color: #63BDF6;">PRF</th>
                <th style="text-align: center; background-color: #63BDF6;">Core Cash</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $d)
                @php
                    $karyawan = get_data_karyawan($d->id_karyawan);

                    // BPJS Capping calculation
                    $gajiBpjsMax = min($d->gaji_bpjs, 12000000);
                    $gajiJpMax = min($d->gaji_bpjs, 11086300);

                    // Company BPJS Contributions
                    $kesehatanCompany = $d->kesehatan != 0 ? $gajiBpjsMax * 0.04 : 0;
                    $jkkRate = $d->company_id == 101 ? 0.0089 : 0.0024;
                    $jkkCompany = $d->jkk ? $d->gaji_bpjs * $jkkRate : 0;
                    $jkmCompany = $d->jkm ? $d->gaji_bpjs * 0.003 : 0;
                    $jpCompany = $d->jp != 0 ? $gajiJpMax * 0.02 : 0;
                    $jhtCompany = $d->gaji_bpjs * 0.037;

                    // Total Taxable Income Base
                    $totalTax =
                        $d->bpjs_adjustment +
                        $jkkCompany +
                        $jkmCompany +
                        $kesehatanCompany +
                        $d->gaji_lembur * ($d->jam_lembur + $d->jam_lembur_libur) +
                        $d->gaji_libur +
                        $d->bonus1x +
                        $d->thr +
                        $d->tambahan_shift_malam;

                    // TER Mapping lookup
                    $terMap = [
                        'TK0' => 'A',
                        'TK1' => 'A',
                        'K0' => 'A',
                        'TK2' => 'B',
                        'TK3' => 'B',
                        'K1' => 'B',
                        'K2' => 'B',
                        'K3' => 'C',
                    ];
                    $ter = $terMap[$d->ptkp] ?? '';
                    $ratePph21 = get_rate_ter_pph21($d->ptkp, $totalTax);

                    // Calculation for Daily/Hourly base rate
                    $gajiPerUnit =
                        $d->metode_penggajian === 'Perjam'
                            ? $d->gaji_pokok / 198
                            : $d->gaji_pokok / ($total_n_hari_kerja ?? 1);
                @endphp

                <tr>
                    <td style="text-align: center;">{{ $d->id_karyawan }}</td>
                    <td>{{ $d->nama }}</td>
                    <td style="text-align: center;">{{ $d->nama_bank }}</td>
                    <td style="text-align: center;">{{ strval($d->nomor_rekening) }}</td>
                    <td style="text-align: center;">{{ nama_jabatan($d->jabatan_id) }}</td>
                    <td style="text-align: center;">{{ nama_company($d->company_id) }}</td>
                    <td style="text-align: center;">{{ nama_placement($d->placement_id) }}</td>
                    <td style="text-align: center;">{{ nama_department($d->department_id) }}</td>
                    <td style="text-align: center;">{{ $d->metode_penggajian }}</td>
                    <td>{{ $d->hari_kerja }}</td>
                    <td>{{ $d->jam_kerja }}</td>
                    <td>{{ $d->jam_lembur }}</td>
                    <td>{{ $d->jam_kerja_libur }}</td>
                    <td>{{ $d->jam_lembur_libur }}</td>
                    <td>{{ $d->jumlah_jam_terlambat }}</td>
                    <td style="text-align: right;">{{ $d->tambahan_shift_malam }}</td>

                    {{-- Data Tunjangan Karyawan --}}
                    <td style="text-align: right;">{{ $karyawan?->gaji_tetap ?? 0 }}</td>
                    <td style="text-align: right;">{{ $karyawan?->tunjangan_bahasa ?? 0 }}</td>
                    <td style="text-align: right;">{{ $karyawan?->tunjangan_housing ?? 0 }}</td>
                    <td style="text-align: right;">{{ $karyawan?->tunjangan_jabatan ?? 0 }}</td>

                    <td style="text-align: right;">{{ $gajiPerUnit }}</td>
                    <td style="text-align: right;">{{ $d->gaji_bulan_ini }}</td>
                    <td style="text-align: right;">{{ $d->gaji_lembur }}</td>
                    <td style="text-align: right;">{{ $d->gaji_lembur * $d->jam_lembur }}</td>
                    <td style="text-align: right;">{{ $d->gaji_lembur * $d->jam_lembur_libur }}</td>
                    <td style="text-align: right;">{{ $d->gaji_libur }}</td>
                    <td style="text-align: right;">{{ $d->bonus1x }}</td>
                    <td style="text-align: right;">{{ $d->thr }}</td>
                    <td style="text-align: right;">{{ $d->potongan1x }}</td>
                    <td>{{ $d->total_noscan }}</td>
                    <td style="text-align: right;">{{ $d->denda_lupa_absen }}</td>
                    <td style="text-align: right;">{{ $d->denda_resigned }}</td>
                    <td>{{ $d->tanggungan }}</td>
                    <td style="text-align: right;">{{ $d->iuran_air }}</td>
                    <td style="text-align: right;">{{ $d->iuran_locker }}</td>
                    <td style="text-align: center;">{{ $d->status_karyawan }}</td>

                    {{-- BPJS Karyawan --}}
                    <td style="text-align: right;">{{ $d->bpjs_adjustment }}</td>
                    <td style="text-align: right;">{{ $d->gaji_bpjs }}</td>
                    <td style="text-align: right;">{{ $d->jht }}</td>
                    <td style="text-align: right;">{{ $d->jp }}</td>
                    <td style="text-align: right;">{{ $d->jkk == 1 ? 'Yes' : 'No' }}</td>
                    <td style="text-align: right;">{{ $d->jkm == 1 ? 'Yes' : 'No' }}</td>
                    <td style="text-align: right;">{{ $d->kesehatan }}</td>

                    {{-- BPJS Company --}}
                    <td style="text-align: right;">{{ $d->jht > 0 ? $jhtCompany : '' }}</td>
                    <td style="text-align: right;">{{ $d->jp > 0 ? $jpCompany : '' }}</td>
                    <td style="text-align: right;">{{ $d->jkk == 1 ? $jkkCompany : '' }}</td>
                    <td style="text-align: right;">{{ $d->jkm == 1 ? $jkmCompany : '' }}</td>
                    <td style="text-align: right;">{{ $d->kesehatan > 0 ? $kesehatanCompany : '' }}</td>

                    {{-- Tax & Identitas --}}
                    <td style="text-align: right;">{{ $totalTax }}</td>
                    <td style="text-align: right;">{{ $d->ptkp }}</td>
                    <td style="text-align: right;">{{ $ter }}</td>
                    <td style="text-align: right;">{{ $ratePph21 }}</td>
                    <td style="text-align: right;">{{ $d->pph21 }}</td>
                    <td style="text-align: right;">'{{ trim(strval(nik($d->id_karyawan))) }}</td>
                    <td style="text-align: right;">'{{ trim(strval(no_npwp($d->id_karyawan))) }}</td>
                    <td style="text-align: right;">{{ $d->total }}</td>

                    {{-- Summary Final --}}
                    <td style="text-align: right;">{{ $d->prf_salary }}</td>
                    <td style="text-align: right;">{{ $d->other_deduction }}</td>
                    <td style="text-align: right;">{{ $d->bpjs_employee }}</td>
                    <td style="text-align: right;">{{ $d->pph21 }}</td>
                    <td style="text-align: right;">{{ $d->prf }}</td>
                    <td style="text-align: right;">{{ $d->core_cash }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
