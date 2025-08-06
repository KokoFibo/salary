<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payroll Detail Report (Non OS)</title>

</head>

<body>

    {{-- Pembukaan --}}
    <table class="table">
        <thead>
            <tr>
                <th colspan="5" style="font-size:24px;text-align:center; font-weight: bold">
                    <h2>Payroll Detail Report
                        {{ nama_bulan($month) }} {{ $year }} (Non OS)</h2>
                </th>
            </tr>
            <tr>
            <tr>
                <th colspan="5" style="font-size:20px;  text-align:center">
                </th>
            </tr>
            <tr>
                <th colspan="5" style="font-size:20px;text-align:center; font-weight: bold">
                    <h3 colspan="5">Pembayaran PT</h3>
                </th>
            </tr>
            <tr>
                <th colspan="5" style="font-size:20px;  text-align:center">
                </th>
            </tr>

            {{-- TR untuk title --}}
            <tr>
                <th
                    style="border: 1px solid black;text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    Company Name</th>
                <th
                    style="border: 1px solid black;text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    Jumlah Karyawan</th>
                <th
                    style="border: 1px solid black;text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    实拿 Total Gaji</th>
                <th
                    style="border: 1px solid black;text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    公账报 PRF</th>
                <th
                    style="border: 1px solid black;text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    大现金 Core Cash</th>


            </tr>
        </thead>
        <tbody>
            @foreach ($results as $company_name => $data)
                <tr>
                    <td style="border: 1px solid black;text-align: center;">{{ $company_name }}
                    </td>

                    <td style="border: 1px solid black;text-align: center">
                        {{ $data['count'] }}
                    </td>
                    <td style="border: 1px solid black;text-align: center">
                        {{ $data['total'] }}
                    </td>
                    <td style="border: 1px solid black;text-align: center">
                        {{ $data['prf'] }}
                    </td>
                    <td style="border: 1px solid black;text-align: center">
                        {{ $data['core_cash'] }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td style="border: 4px solid black;text-align: center; font-weight: bold; font-size: 14px;">Total</td>
                <td style="border: 4px solid black;text-align: center; font-weight: bold; font-size: 14px;">
                    {{ $total_karyawan }}</td>
                <td style="border: 4px solid black;text-align: center; font-weight: bold; font-size: 14px;">
                    {{ $count_gaji }}
                </td>
                <td style="border: 4px solid black;text-align: center; font-weight: bold; font-size: 14px;">
                    {{ $prf_total }}</td>
                <td style="border: 4px solid black;text-align: center; font-weight: bold; font-size: 14px;">
                    {{ $core_cash_total }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Pembagian PT Non OS --}}
    <table class="table">
        <thead>
            <tr>
                <th colspan="5" style="font-size:20px; text-align:center"></th>
            </tr>
            <tr>
                <th colspan="5" style="font-size:20px; text-align:center; font-weight: bold">
                    <h3>Pembagian PT Non OS</h3>
                </th>
            </tr>
            <tr>
                <th colspan="5" style="font-size:20px; text-align:center"></th>
            </tr>
            <tr>
                <th
                    style="border: 1px solid black; text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    Company Name</th>
                <th
                    style="border: 1px solid black; text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    Directorate</th>
                <th
                    style="border: 1px solid black; text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    TKA</th>
                <th
                    style="border: 1px solid black; text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    Non TKA</th>
                <th
                    style="border: 1px solid black; text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grouped = collect($result_company_placement)->groupBy('company');
            @endphp

            @foreach ($grouped as $company => $placements)
                @php
                    $rowspan = $placements->count();
                    $printed = false;
                @endphp

                @foreach ($placements as $placement)
                    <tr>
                        {{-- Company Name (merged/rowspan) --}}
                        @if (!$printed)
                            <td style="border: 1px solid black; text-align: center; vertical-align: middle;"
                                rowspan="{{ $rowspan }}">
                                {{ $company }}
                            </td>
                            @php $printed = true; @endphp
                        @endif

                        {{-- Placement --}}
                        <td style="border: 1px solid black; text-align: center;">
                            {{ $placement['placement'] }}
                        </td>

                        {{-- TKA --}}
                        <td style="border: 1px solid black; text-align: center;">
                            {{ $placement['tka_total'] }}
                        </td>

                        {{-- Non TKA --}}
                        <td style="border: 1px solid black; text-align: center;">
                            {{ $placement['non_tka_total'] }}
                        </td>

                        {{-- Keterangan --}}
                        @if ($loop->first)
                            <td style="border: 1px solid black; text-align: center; vertical-align: middle;"
                                rowspan="{{ $rowspan }}">
                                {{-- Kosong atau bisa diisi jika ada keterangan tambahan --}}
                            </td>
                        @endif
                    </tr>
                @endforeach
            @endforeach
            {{-- Baris Total --}}
            <tr>
                <td colspan="2"
                    style="border: 1px solid black; text-align: center; font-weight: bold; font-size: 14px;">
                    TOTAL
                </td>
                <td style="border: 1px solid black; text-align: center; font-weight: bold; font-size: 14px;">
                    {{ $total_tka }}
                </td>
                <td style="border: 1px solid black; text-align: center; font-weight: bold; font-size: 14px;">
                    {{ $total_non_tka }}
                </td>
                <td style="border: 1px solid black;"></td>
            </tr>
        </tbody>
    </table>

    {{-- Pembagian by Placement --}}
    <table class="table">
        <thead>
            <tr>
                <th colspan="6" style="font-size:20px;  text-align:center">
                </th>
            </tr>
            <tr>

                <th colspan="6" style="font-size:20px; text-align:center; font-weight: bold">
                    <h3>Pembagian by Directorate</h3>
                </th>

            </tr>
            <tr>

                <th colspan="5" style="font-size:20px;  text-align:center">

                </th>
            </tr>

            {{-- TR untuk title --}}
            <tr>
                <th
                    style="border: 1px solid black;text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    Directorate</th>
                <th
                    style="border: 1px solid black;text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    中方</th>
                <th
                    style="border: 1px solid black;text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    Non TKA</th>
                <th
                    style="border: 1px solid black;text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    外包</th>
                <th
                    style="border: 1px solid black;text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    Total</th>
                <th
                    style="border: 1px solid black;text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    Selisih</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Kelompokkan data berdasarkan nama perusahaan
                $grouped = collect($result3)->groupBy('company');

            @endphp

            @foreach ($grouped as $company => $placements)
                @php
                    $rowspan = $placements->count();
                    $printed = false;
                @endphp

                @foreach ($placements as $placement)
                    <tr>

                        <td style="border: 1px solid black; text-align: center;">
                            {{ $placement['placement'] }}
                        </td>

                        {{-- Jumlah TKA --}}
                        <td style="border: 1px solid black; text-align: center;">
                            {{ $placement['tka_total'] }}
                        </td>

                        {{-- Jumlah Non-TKA --}}
                        <td style="border: 1px solid black; text-align: center;">
                            {{ $placement['non_tka_total'] }}
                        </td>

                        {{-- Jumlah API --}}
                        <td style="border: 1px solid black; text-align: center;">
                            {{ $placement['api_total'] }}
                        </td>

                        {{-- Total --}}
                        <td style="border: 1px solid black; text-align: center;">
                            {{ $placement['tka_total'] + $placement['non_tka_total'] + $placement['api_total'] }}
                        </td>

                        {{-- Selisih --}}
                        <td style="border: 1px solid black; text-align: center;">
                            {{ $placement['tka_total'] + $placement['non_tka_total'] }}
                        </td>
                    </tr>
                @endforeach
            @endforeach

            {{-- Baris Total Keseluruhan --}}
            <tr>
                <td style="border: 1px solid black; text-align: center; font-weight: bold; font-size: 14px;">
                    TOTAL
                </td>
                <td style="border: 1px solid black; text-align: center; font-weight: bold; font-size: 14px;">
                    {{ $total_tka3 }}
                </td>
                <td style="border: 1px solid black; text-align: center; font-weight: bold; font-size: 14px;">
                    {{ $total_non_tka3 }}
                </td>
                <td style="border: 1px solid black; text-align: center; font-weight: bold; font-size: 14px;">
                    {{ $total_api3 }}
                </td>
                <td style="border: 1px solid black; text-align: center; font-weight: bold; font-size: 14px;">
                    {{ $total_tka3 + $total_non_tka3 + $total_api3 }}
                </td>
                <td style="border: 1px solid black; text-align: center; font-weight: bold; font-size: 14px;">
                    {{ $total_tka3 + $total_non_tka3 }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Pembayaran PT --}}
    <table class="table">
        <thead>
            <tr>
                <th colspan="5" style="font-size:20px;  text-align:center">
                </th>
            </tr>
            <tr>
                <th colspan="7" style="font-size:20px;  text-align:center; font-weight: bold">
                    <h3>Pembayaran PT</h3>
                </th>
            </tr>
            <tr>

                <th colspan="5" style="font-size:20px;  text-align:center">

                </th>
            </tr>

            {{-- TR untuk title --}}
            <tr>
                <th
                    style="border: 1px solid black;text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    Company</th>
                <th
                    style="border: 1px solid black;text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    Total Karyawan
                    {{ nama_bulan($last_month) }}
                    {{ $last_year }}</th>
                <th
                    style="border: 1px solid black;text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    Total Gaji
                    {{ nama_bulan($last_month) }}
                    {{ $last_year }}</th>
                <th
                    style="border: 1px solid black;text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    Total Karyawan
                    {{ nama_bulan($month) }}
                    {{ $year }}</th>
                <th
                    style="border: 1px solid black;text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    Total Gaji
                    {{ nama_bulan($month) }}
                    {{ $year }}</th>
                <th
                    style="border: 1px solid black;text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    Selisih</th>
                <th
                    style="border: 1px solid black;text-align: center; font-size:14px; background-color: #28A745; color: white;">
                    Keterangan</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($results as $company_name => $data)
                <tr>
                    <td style="border: 1px solid black; text-align: center;">{{ $company_name }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ $data['last_count'] }}
                        {{-- hh --}}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ $data['last_total'] }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ $data['count'] }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ $data['total'] }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ $data['selisih'] }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;"></td>
                </tr>
            @endforeach

            <tr>
                <td style="border: 4px solid black; text-align: center;font-weight: bold; font-size: 14px;">Total</td>
                <td style="border: 4px solid black; text-align: center;font-weight: bold; font-size: 14px;">
                    {{ $total_last_count }}
                </td>
                <td style="border: 4px solid black; text-align: center;font-weight: bold; font-size: 14px;">
                    {{ $total_last_gaji }}
                </td>
                <td style="border: 4px solid black; text-align: center;font-weight: bold; font-size: 14px;">
                    {{ $total_karyawan }}
                </td>
                <td style="border: 4px solid black; text-align: center;font-weight: bold; font-size: 14px;">
                    {{ $count_gaji }}
                </td>
                <td style="border: 4px solid black; text-align: center;font-weight: bold; font-size: 14px;">
                    {{ $total_last_selisih }}
                </td>
                <td style="border: 4px solid black; text-align: center;font-weight: bold;"></td>
            </tr>



        </tbody>
    </table>




</body>

</html>
