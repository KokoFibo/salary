<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payroll Detail Report (Non OS)</title>
</head>


<body>

    <table class="table">
        <thead>
            <tr>
                <th colspan="5" style="font-size:20px;  text-align:center">
                    <h3>Payroll Detail Report {{ nama_bulan($month) }} {{ $year }}(Non OS)</h3>
                </th>
            </tr>
            <tr>
            <tr>

                <th colspan="5" style="font-size:20px;  text-align:center">

                </th>
            </tr>
            <tr>

                <th colspan="5" style="font-size:20px;  text-align:center">
                    <h3 colspan="5" style="font-size:20px;  text-align:center">Pembayaran PT</h3>
                </th>

            </tr>
            <tr>

                <th colspan="5" style="font-size:20px;  text-align:center">

                </th>
            </tr>

            {{-- TR untuk title --}}
            <tr>
                <th style="border: 1px solid black;text-align: center;">Company Name</th>
                <th style="border: 1px solid black;text-align: center;">Jumlah Karyawan</th>
                <th style="border: 1px solid black;text-align: center;">实拿 Total Gaji</th>
                <th style="border: 1px solid black;text-align: center;">公账报 PRF</th>
                <th style="border: 1px solid black;text-align: center;">大现金 Core Cash</th>


            </tr>
        </thead>
        <tbody>
            @php
                $total_count = 0;
                $total_total_gaji = 0;
                $total_prf = 0;
                $total_core_cash = 0;
            @endphp
            @foreach ($companies as $company)
                <tr>
                    <td style="border: 1px solid black;text-align: center">{{ $company }}
                    </td>

                    <td style="border: 1px solid black;text-align: center">
                        {{ $results[$company]['count'] }}
                    </td>
                    <td style="border: 1px solid black;text-align: center">
                        {{ $results[$company]['total'] }}
                    </td>
                    <td style="border: 1px solid black;text-align: center">
                        {{ $results[$company]['prf'] }}
                    </td>
                    <td style="border: 1px solid black;text-align: center">
                        {{ $results[$company]['core_cash'] }}
                    </td>
                    @php
                        $total_count += $results[$company]['count'];
                        $total_total_gaji += $results[$company]['total'];
                        $total_prf += $results[$company]['prf'];
                        $total_core_cash += $results[$company]['core_cash'];
                    @endphp

                </tr>
            @endforeach
            <tr>
                <td style="border: 4px solid black;text-align: center; font-weight: bold;">Total</td>
                <td style="border: 4px solid black;text-align: center; font-weight: bold;"> {{ $total_count }}</td>
                <td style="border: 4px solid black;text-align: center; font-weight: bold;"> {{ $total_total_gaji }}
                </td>
                <td style="border: 4px solid black;text-align: center; font-weight: bold;"> {{ $total_prf }}</td>
                <td style="border: 4px solid black;text-align: center; font-weight: bold;"> {{ $total_core_cash }}</td>
            </tr>



        </tbody>
    </table>
    {{-- Pembagian PT Non OS --}}
    <table class="table">
        <thead>
            <tr>
                <th colspan="4" style="font-size:20px;  text-align:center">
                </th>
            </tr>
            <tr>

                <th colspan="4" style="font-size:20px;  text-align:center">
                    <h3>Pembagian PT Non OS</h3>
                </th>

            </tr>
            <tr>

                <th colspan="5" style="font-size:20px;  text-align:center">

                </th>
            </tr>

            {{-- TR untuk title --}}
            <tr>
                <th style="border: 1px solid black;text-align: center;">Company Name</th>
                <th style="border: 1px solid black;text-align: center;">实拿 Total Gaji</th>
                <th style="border: 1px solid black;text-align: center;">Directorate</th>
                <th style="border: 1px solid black;text-align: center;">Keterangan</th>
            </tr>
        </thead>
        <tbody>

            {{-- YAM --}}
            <tr>
                <td style="border: 1px solid black;text-align: center">YAM
                </td>
                <td style="border: 1px solid black;text-align: center">
                    {{ $YAM }}
                </td>
                <td style="border: 1px solid black;text-align: center">YAM</td>
                <td style="border: 1px solid black;text-align: center">

                </td>
            </tr>

            {{-- YSM --}}
            <tr>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">YSM</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $YSM_CF }}
                </td>
                <td style="border: 1px solid black; text-align: center;">YSM 中方</td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">

                </td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">
                    {{ $YSM }}
                </td>
                <td style="border: 1px solid black; text-align: center;">YSM</td>
            </tr>
            {{-- YIG --}}
            <tr>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">YIG</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $YIG_CF }}
                </td>
                <td style="border: 1px solid black; text-align: center;">YIG 中方</td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">

                </td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">
                    {{ $YIG }}
                </td>
                <td style="border: 1px solid black; text-align: center;">YIG</td>
            </tr>

            {{-- YCME --}}
            <tr>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="14">YCME</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $YCME_CF }}
                </td>
                <td style="border: 1px solid black; text-align: center;">YCME 中方</td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="14">

                </td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">
                    {{ $YCME }}
                </td>
                <td style="border: 1px solid black; text-align: center;">YCME</td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">
                    {{ $YCME_exim }}
                </td>
                <td style="border: 1px solid black; text-align: center;">EXIM</td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">
                    {{ $YCME_XIN_XUN }}
                </td>
                <td style="border: 1px solid black; text-align: center;">新讯</td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">
                    {{ $YCME_Pabrik1_CF }}
                </td>
                <td style="border: 1px solid black; text-align: center;">Pabrik 1 中方</td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">
                    {{ $YCME_Pabrik1 }}
                </td>
                <td style="border: 1px solid black; text-align: center;">Pabrik 1</td>
            </tr>

            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">
                    {{ $YCME_Pabrik2_CF }}
                </td>
                <td style="border: 1px solid black; text-align: center;">Pabrik 2 中方</td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">
                    {{ $YCME_Pabrik2 }}
                </td>
                <td style="border: 1px solid black; text-align: center;">Pabrik 2</td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">
                    {{ $YCME_Pabrik3_CF }}
                </td>
                <td style="border: 1px solid black; text-align: center;">Pabrik 3 中方</td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">
                    {{ $YCME_Pabrik3 }}
                </td>
                <td style="border: 1px solid black; text-align: center;">Pabrik 3</td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">
                    {{ $YCME_Pabrik4_CF }}
                </td>
                <td style="border: 1px solid black; text-align: center;">Pabrik 4 中方</td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">
                    {{ $YCME_Pabrik4 }}
                </td>
                <td style="border: 1px solid black; text-align: center;">Pabrik 4</td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">
                    {{ $YCME_Sunra_CF }}
                </td>
                <td style="border: 1px solid black; text-align: center;">YEV Sunra 中方</td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">
                    {{ $YCME_Sunra }}
                </td>
                <td style="border: 1px solid black; text-align: center;">YEV Sunra</td>
            </tr>
            <tr>
                <td style="border: 1px solid black; text-align: center;">YNE</td>
                <td style="border:
                    1px solid black; text-align: center;">
                    {{ $YNE }}
                </td>
                <td style="border: 1px solid black; text-align: center;">YNE</td>
                <td style="border: 1px solid black; text-align: center;">

                </td>
            </tr>
            <tr>
                <td style="border: 1px solid black; text-align: center;">STI</td>
                <td style="border:
                    1px solid black; text-align: center;">
                    {{ $STI }}
                </td>
                <td style="border: 1px solid black; text-align: center;">STI</td>
                <td style="border: 1px solid black; text-align: center;">

                </td>
            </tr>




        </tbody>
    </table>

    {{-- Pembagian by Placement --}}
    <table class="table">
        <thead>
            <tr>
                <th colspan="5" style="font-size:20px;  text-align:center">
                </th>
            </tr>
            <tr>

                <th colspan="5" style="font-size:20px;  text-align:center">
                    <h3>Pembagian by Directorate</h3>
                </th>

            </tr>
            <tr>

                <th colspan="5" style="font-size:20px;  text-align:center">

                </th>
            </tr>

            {{-- TR untuk title --}}
            <tr>
                <th style="border: 1px solid black;text-align: center;">Directorate</th>
                <th style="border: 1px solid black;text-align: center;">Directorate Detail</th>
                <th style="border: 1px solid black;text-align: center;">实拿 Total Gaji</th>
                <th style="border: 1px solid black;text-align: center;">Total</th>
                <th style="border: 1px solid black;text-align: center;">Selisih</th>
            </tr>
        </thead>
        <tbody>

            {{-- YAM --}}
            <tr>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">YAM
                </td>
                <td style="border: 1px solid black;text-align: center">YAM</td>
                <td style="border: 1px solid black;text-align: center">
                    {{ $placement_YAM }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">
                    {{ $YAM_WP + $placement_YAM }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">
                    {{ $placement_YAM }}
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid black;text-align: center">YAM 外包</td>
                <td style="border: 1px solid black;text-align: center">
                    {{ $YAM_WP }}
                </td>

            </tr>

            {{-- YSM --}}
            <tr>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="3">YSM
                </td>
                <td style="border: 1px solid black; text-align: center;">YSM</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $placement_YSM }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="3">
                    {{ $placement_YSM + $placement_YSM_CF + $YSM_WP }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="3">
                    {{ $placement_YSM + $placement_YSM_CF }}
                </td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">YSM 中方</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $placement_YSM_CF }}
                </td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">YSM 外包</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $YSM_WP }}
                </td>
            </tr>

            {{-- YIG --}}
            <tr>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="3">YIG
                </td>
                <td style="border: 1px solid black; text-align: center;">YIG</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $placement_YIG }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="3">
                    {{ $placement_YIG + $placement_YIG_CF + $YIG_WP }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="3">
                    {{ $placement_YIG + $placement_YIG_CF }}
                </td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">YIG 中方</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $placement_YIG_CF }}
                </td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">YIG 外包</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $YIG_WP }}
                </td>
            </tr>

            {{-- YCME --}}
            <tr>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">YCME
                </td>
                <td style="border: 1px solid black; text-align: center;">YCME</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $placement_YCME }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">
                    {{ $placement_YCME + $placement_YCME_CF }}

                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">
                    {{ $placement_YCME + $placement_YCME_CF }}
                </td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">YCME 中方</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $placement_YCME_CF }}
                </td>
            </tr>


            {{-- Pabrik 1 一厂 --}}
            <tr>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="3">一厂
                </td>
                <td style="border: 1px solid black; text-align: center;">一厂</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $placement_pabrik1 }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="3">
                    {{ $placement_pabrik1 + $placement_pabrik1_CF + $Pabrik1_WP }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="3">
                    {{ $placement_pabrik1 + $placement_pabrik1_CF }}
                </td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">一厂 中方</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $placement_pabrik1_CF }}
                </td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">一厂 外包</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $Pabrik1_WP }}
                </td>
            </tr>

            {{-- Pabrik 1 二厂 --}}
            <tr>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="3">二厂
                </td>
                <td style="border: 1px solid black; text-align: center;">二厂</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $placement_pabrik2 }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="3">
                    {{ $placement_pabrik2 + $placement_pabrik2_CF + $Pabrik2_WP }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="3">
                    {{ $placement_pabrik2 + $placement_pabrik2_CF }}
                </td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">二厂 中方</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $placement_pabrik2_CF }}
                </td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">二厂 外包</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $Pabrik2_WP }}
                </td>
            </tr>

            {{-- Pabrik 1 三厂 --}}
            <tr>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="3">三厂
                </td>
                <td style="border: 1px solid black; text-align: center;">三厂</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $placement_pabrik3 }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="3">
                    {{ $placement_pabrik3 + $placement_pabrik3_CF + $Pabrik3_WP }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="3">
                    {{ $placement_pabrik3 + $placement_pabrik3_CF }}
                </td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">三厂 中方</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $placement_pabrik3_CF }}
                </td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">三厂 外包</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $Pabrik3_WP }}
                </td>
            </tr>

            {{-- Pabrik 1 四厂 --}}
            <tr>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="3">四厂
                </td>
                <td style="border: 1px solid black; text-align: center;">四厂</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $placement_pabrik4 }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="3">
                    {{ $placement_pabrik4 + $placement_pabrik4_CF + $Pabrik4_WP }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="3">
                    {{ $placement_pabrik4 + $placement_pabrik4_CF }}
                </td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">四厂 中方</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $placement_pabrik4_CF }}
                </td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">四厂 外包</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $Pabrik4_WP }}
                </td>
            </tr>

            {{-- Sunra --}}
            <tr>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="3">SUNRA
                </td>
                <td style="border: 1px solid black; text-align: center;">SUNRA</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $placement_SUNRA }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="3">
                    {{ $placement_SUNRA + $placement_SUNRA_CF + $YEV_SUNRA_WP }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="3">
                    {{ $placement_SUNRA + $placement_SUNRA_CF }}
                </td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">SUNRA 中方</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $placement_SUNRA_CF }}
                </td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">SUNRA 外包</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $YEV_SUNRA_WP }}
                </td>
            </tr>

            {{-- YNE dahulu Pabrik 5 --}}
            <tr>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">YNE
                </td>
                <td style="border: 1px solid black;text-align: center">YNE</td>
                <td style="border: 1px solid black;text-align: center">
                    {{ $placement_Pabrik5_YNE }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">
                    {{ $Pabrik5_WP + $placement_Pabrik5_YNE }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">
                    {{ $placement_Pabrik5_YNE }}
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid black;text-align: center">YNE 外包</td>
                <td style="border: 1px solid black;text-align: center">
                    {{ $Pabrik5_WP }}
                </td>
            </tr>

            {{-- STI --}}
            <tr>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">STI
                </td>
                <td style="border: 1px solid black;text-align: center">STI</td>
                <td style="border: 1px solid black;text-align: center">
                    {{ $placement_STI }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">
                    {{ $STI_WP + $placement_STI }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">
                    {{ $placement_STI }}
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid black;text-align: center">STI 外包</td>
                <td style="border: 1px solid black;text-align: center">
                    {{ $STI_WP }}
                </td>

            </tr>

            {{-- TDB + --}}
            <tr>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">TDB
                </td>
                <td style="border: 1px solid black;text-align: center">TDB</td>
                <td style="border: 1px solid black;text-align: center">
                    {{ $placement_TDB }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">
                    {{ $TDB_WP + $placement_TDB }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">
                    {{ $placement_TDB }}
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid black;text-align: center">TDB 外包</td>
                <td style="border: 1px solid black;text-align: center">
                    {{ $TDB_WP }}
                </td>

            </tr>

            {{--  新讯  --}}
            <tr>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;">新讯
                </td>

                <td style="border: 1px solid black; text-align: center;">新讯</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $placement_YCME_XIN_XUN }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="1">
                    {{ $placement_YCME_XIN_XUN }}

                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="1">
                    {{ $placement_YCME_XIN_XUN }}
                </td>
            </tr>

            {{--   Exim --}}

            <tr>

                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">关务 Exim
                </td>
                <td style="border: 1px solid black; text-align: center;"> 关务</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $placement_YCME_EXIM }}
                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">
                    {{ $placement_YCME_EXIM + $EXIM_WP }}

                </td>
                <td style="border: 1px solid black; text-align: center;vertical-align: middle;" rowspan="2">
                    {{ $placement_YCME_EXIM }}
                </td>
            </tr>
            <tr>
                <!-- kolom pertama dan terakhir sudah digabung, jadi cukup isi kolom tengah -->
                <td style="border: 1px solid black; text-align: center;">关务外包</td>
                <td style="border: 1px solid black; text-align: center;">
                    {{ $EXIM_WP }}
                </td>
            </tr>





            {{-- Total --}}
            <tr>
                @php
                    $ttg =
                        $placement_YAM +
                        $placement_YSM +
                        $placement_YSM_CF +
                        $placement_YIG +
                        $placement_YIG_CF +
                        $placement_YCME +
                        $placement_YCME_CF +
                        $placement_pabrik1 +
                        $placement_pabrik1_CF +
                        $placement_pabrik2 +
                        $placement_pabrik2_CF +
                        $placement_pabrik3 +
                        $placement_pabrik3_CF +
                        $placement_pabrik4 +
                        $placement_pabrik4_CF +
                        $placement_SUNRA +
                        $placement_SUNRA_CF +
                        $placement_Pabrik5_YNE +
                        $placement_STI +
                        $placement_TDB +
                        $placement_YCME_XIN_XUN +
                        $placement_YCME_EXIM;

                    $ttg_wp =
                        $YAM_WP +
                        $YSM_WP +
                        $YIG_WP +
                        $Pabrik1_WP +
                        $Pabrik2_WP +
                        $Pabrik3_WP +
                        $Pabrik4_WP +
                        $Pabrik5_WP +
                        $YEV_SUNRA_WP +
                        $STI_WP +
                        $TDB_WP +
                        $EXIM_WP;

                @endphp
                <td style="border: 4px solid black; text-align: center; font-weight: bold;" colspan=2>Total
                </td>

                <td style="border: 4px solid black;text-align: center; font-weight: bold;">
                    {{ $ttg + $ttg_wp }}
                </td>
                <td style="border: 4px solid black; text-align: center; font-weight: bold;">
                    {{ $ttg + $ttg_wp }}
                </td>
                <td style="border: 4px solid black; text-align: center; font-weight: bold;">
                    {{ $ttg }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Pembayarn PT --}}
    <table class="table">
        <thead>

            <tr>

                <th colspan="5" style="font-size:20px;  text-align:center">

                </th>
            </tr>
            <tr>
                <th colspan="7" style="font-size:20px;  text-align:center">
                    <h3>Pembayaran PT</h3>
                </th>
            </tr>
            <tr>

                <th colspan="5" style="font-size:20px;  text-align:center">

                </th>
            </tr>

            {{-- TR untuk title --}}
            <tr>
                <th style="border: 1px solid black;text-align: center;">Company</th>
                <th style="border: 1px solid black;text-align: center;">Total Karyawan {{ nama_bulan($last_month) }}
                    {{ $last_year }}</th>
                <th style="border: 1px solid black;text-align: center;">Total Gaji {{ nama_bulan($last_month) }}
                    {{ $last_year }}</th>
                <th style="border: 1px solid black;text-align: center;">Total Karyawan {{ nama_bulan($month) }}
                    {{ $year }}</th>
                <th style="border: 1px solid black;text-align: center;">Total Gaji {{ nama_bulan($month) }}
                    {{ $year }}</th>
                <th style="border: 1px solid black;text-align: center;">Selisih</th>
                <th style="border: 1px solid black;text-align: center;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($companies as $company)
                <tr>
                    <td style="border: 1px solid black; text-align: center;">{{ $company }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ $last_results[$company]['count'] }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ $last_results[$company]['total'] }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ $results[$company]['count'] }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ $results[$company]['total'] }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ $selisih_results[$company]['selisih'] }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;"></td>
                </tr>
            @endforeach

            <tr>
                <td style="border: 4px solid black; text-align: center;font-weight: bold;">Total</td>
                <td style="border: 4px solid black; text-align: center;font-weight: bold;">
                    {{ $last_count }}
                </td>
                <td style="border: 4px solid black; text-align: center;font-weight: bold;">
                    {{ $last_total }}
                </td>
                <td style="border: 4px solid black; text-align: center;font-weight: bold;">
                    {{ $count }}
                </td>
                <td style="border: 4px solid black; text-align: center;font-weight: bold;">
                    {{ $total }}
                </td>
                <td style="border: 4px solid black; text-align: center;font-weight: bold;">
                    {{ $total_selisih }}
                </td>
                <td style="border: 4px solid black; text-align: center;font-weight: bold;"></td>
            </tr>



        </tbody>
    </table>

</body>

</html>
