<h4 class='text-center'>Yifang Non OS head count for {{ monthname($bulan) }} {{ $tahun }}</h4>
<table class="table">
    <thead>
        <tr>
            <th>Department</th>
            <th>YIG</th>
            <th>YCME</th>
            <th>YSM</th>
            <th>YAM</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>HR</td>
            <td>{{ $yig_hr }}</td>
            <td>{{ $ycme_hr }}</td>
            <td>{{ $ysm_hr }}</td>
            <td>{{ $yam_hr }}</td>

        </tr>
        <tr>
            <td>Finance</td>
            <td>{{ $yig_finance }}</td>
            <td>{{ $ycme_finance }}</td>
            <td>{{ $ysm_finance }}</td>
            <td>{{ $yam_finance }}</td>

        </tr>
        <tr>
            <td>Procurement</td>
            <td>{{ $yig_procurement }}</td>
            <td>{{ $ycme_procurement }}</td>
            <td>{{ $ysm_procurement }}</td>
            <td>{{ $yam_procurement }}</td>

        </tr>
        <tr>
            <td>GA</td>
            <td>{{ $yig_ga }}</td>
            <td>{{ $ycme_ga }}</td>
            <td>{{ $ysm_ga }}</td>
            <td>{{ $yam_ga }}</td>

        </tr>
        <tr>
            <td>Legal</td>
            <td>{{ $yig_legal }}</td>
            <td>{{ $ycme_legal }}</td>
            <td>{{ $ysm_legal }}</td>
            <td>{{ $yam_legal }}</td>

        </tr>
        <tr>
            <td>EXIM</td>
            <td>{{ $yig_exim }}</td>
            <td>{{ $ycme_exim }}</td>
            <td>{{ $ysm_exim }}</td>
            <td>{{ $yam_exim }}</td>

        </tr>

        <tr>
            <td>Mechanical Engineer</td>
            <td>{{ $yig_me }}</td>
            <td>{{ $ycme_me }}</td>
            <td>{{ $ysm_me }}</td>
            <td>{{ $yam_me }}</td>

        </tr>
        <tr>
            <td>BD</td>
            <td>{{ $yig_bd }}</td>
            <td>{{ $ycme_bd }}</td>
            <td>{{ $ysm_bd }}</td>
            <td>{{ $yam_bd }}</td>

        </tr>
        <tr>
            <td>Quality Control</td>
            <td>{{ $yig_qc }}</td>
            <td>{{ $ycme_qc }}</td>
            <td>{{ $ysm_qc }}</td>
            <td>{{ $yam_qc }}</td>

        </tr>
        <tr>
            <td>Production</td>
            <td>{{ $yig_production }}</td>
            <td>{{ $ycme_production }}</td>
            <td>{{ $ysm_production }}</td>
            <td>{{ $yam_production }}</td>

        </tr>
        <tr>
            <td>Warehouse</td>
            <td>{{ $yig_warehouse }}</td>
            <td>{{ $ycme_warehouse }}</td>
            <td>{{ $ysm_warehouse }}</td>
            <td>{{ $yam_warehouse }}</td>

        </tr>

        <tr>
            <td>Board of Director</td>
            <td>{{ $yig_bod }}</td>
            <td>{{ $ycme_bod }}</td>
            <td>{{ $ysm_bod }}</td>
            <td>{{ $yam_bod }}</td>

        </tr>
        <tr>
            <th>Total</th>
            <th>{{ $yig_total }}</th>
            <th>{{ $ycme_total }}</th>
            <th>{{ $ysm_total }}</th>
            <th>{{ $yam_total }}</th>

        </tr>
        {{-- <tr>
            <th>Total</th>
            <th>{{ $yig_total1 }}</th>
            <th>{{ $ycme_total1 }}</th>
            <th>{{ $ysm_total1 }}</th>
            <th>{{ $yam_total1 }}</th>

        </tr> --}}
    </tbody>
</table>
