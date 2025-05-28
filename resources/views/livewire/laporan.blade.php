<div>
    <div class="card m-3">
        <div class="card-header">
            <h5>Non OS Report</h5>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>实拿 (Total Gaji)</th>
                        <th>公账报 (PRF)</th>
                        <th>大现金 (Core Cash)</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($companies as $company)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">{{ $company }} (
                                {{ number_format($results[$company]['count'], 0, ',', '.') }})</td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ number_format($results[$company]['total'], 0, ',', '.') }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ number_format($results[$company]['prf'], 0, ',', '.') }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ number_format($results[$company]['core_cash'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="border border-gray-300 px-4 py-2">Total</td>
                        <td class="border border-gray-300 px-4 py-2">
                            {{ number_format($total, 0, ',', '.') }}
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>
    <div class="card m-3">
        <div class="card-header">
            <h5>Pembagian PT Non OS</h5>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>实拿</th>
                        <th>Placement</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>YAM</td>
                        <td>{{ number_format($YAM) }}</td>
                        <td>YAM</td>
                    </tr>

                    <tr>
                        <td>YSM</td>
                        <td>{{ number_format($YSM_CF) }}</td>
                        <td>YSM 中方</td>
                    </tr>
                    {{-- <tr>
                        <td>YSM</td>
                        <td>{{ number_format($YSM_Wanto) }}</td>
                        <td>YSM Wanto</td>
                    </tr> --}}
                    <tr>
                        <td>YSM</td>
                        <td>{{ number_format($YSM) }}</td>
                        <td>YSM</td>
                    </tr>
                    <tr>
                        <td>YIG</td>
                        <td>{{ number_format($YIG_CF) }}</td>
                        <td>YIG 中方</td>
                    </tr>
                    <tr>
                        <td>YIG</td>
                        <td>{{ number_format($YIG) }}</td>
                        <td>YIG</td>
                    </tr>
                    <tr>
                        <td>YCME</td>
                        <td>{{ number_format($YCME_CF) }}</td>
                        <td>YCME 中方</td>
                    </tr>
                    <tr>
                        <td>YCME</td>
                        <td>{{ number_format($YCME) }}</td>
                        <td>YCME</td>
                    </tr>
                    <tr>
                        <td>YCME</td>
                        <td>{{ number_format($YCME_exim) }}</td>
                        <td>EXIM</td>
                    </tr>
                    <tr>
                        <td>YCME</td>
                        <td>{{ number_format($YCME_XIN_XUN) }}</td>
                        <td>新讯</td>
                    </tr>
                    <tr>
                        <td>YCME</td>
                        <td>{{ number_format($YCME_Pabrik1_CF) }}</td>
                        <td>Pabrik 1 中方</td>
                    </tr>
                    <tr>
                        <td>YCME</td>
                        <td>{{ number_format($YCME_Pabrik1) }}</td>
                        <td>Pabrik 1</td>
                    </tr>
                    <tr>
                        <td>YCME</td>
                        <td>{{ number_format($YCME_Pabrik2_CF) }}</td>
                        <td>Pabrik 2 中方</td>
                    </tr>
                    <tr>
                        <td>YCME</td>
                        <td>{{ number_format($YCME_Pabrik2) }}</td>
                        <td>Pabrik 2</td>
                    </tr>
                    <tr>
                        <td>YCME</td>
                        <td>{{ number_format($YCME_Pabrik3_CF) }}</td>
                        <td>Pabrik 3 中方</td>
                    </tr>
                    <tr>
                        <td>YCME</td>
                        <td>{{ number_format($YCME_Pabrik3) }}</td>
                        <td>Pabrik 3</td>
                    </tr>
                    <tr>
                        <td>YCME</td>
                        <td>{{ number_format($YCME_Pabrik4_CF) }}</td>
                        <td>Pabrik 4 中方</td>
                    </tr>
                    <tr>
                        <td>YCME</td>
                        <td>{{ number_format($YCME_Pabrik4) }}</td>
                        <td>Pabrik 4</td>
                    </tr>
                    <tr>
                        <td>YCME</td>
                        <td>{{ number_format($YCME_Sunra_CF) }}</td>
                        <td>YEV Sunra 中方</td>
                    </tr>
                    <tr>
                        <td>YCME</td>
                        <td>{{ number_format($YCME_Sunra) }}</td>
                        <td>YEV Sunra</td>
                    </tr>
                    <tr>
                        <td>YNE</td>
                        <td>{{ number_format($YNE) }}</td>
                        <td>YNE</td>
                    </tr>
                    <tr>
                        <td>STI</td>
                        <td>{{ number_format($STI) }}</td>
                        <td>STI</td>
                    </tr>
                </tbody>

            </table>
        </div>
    </div>
    <div class="card m-3">
        <div class="card-header">
            <h5>Placement</h5>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Placement</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>YAM</td>
                        <td>{{ number_format($placement_YAM) }}</td>
                    </tr>
                    <tr>
                        <td>YAM 外包</td>
                        <td>{{ number_format($YAM_WP) }}</td>
                        <td>OS Placement YAM ambil dari API</td>
                    </tr>
                    <tr>
                        <td>YSM</td>
                        <td>{{ number_format($placement_YSM) }}</td>
                    </tr>
                    <tr>
                        <td>YSM 中方</td>
                        <td>{{ number_format($placement_YSM_CF) }}</td>
                    </tr>
                    <tr>
                        <td>YSM 外包</td>
                        <td>{{ number_format($YSM_WP) }}</td>
                        <td>OS Placement YSM ambil dari API</td>
                    </tr>
                    <tr>
                        <td>YIG</td>
                        <td>{{ number_format($placement_YIG) }}</td>
                    </tr>
                    <tr>
                        <td>YIG 中方</td>
                        <td>{{ number_format($placement_YIG_CF) }}</td>
                    </tr>
                    <tr>
                        <td>YIG 外包</td>
                        <td>{{ number_format($YIG_WP) }}</td>
                        <td>OS Placement YIG ambil dari API</td>
                    </tr>
                    <tr>
                        <td>YCME</td>
                        <td>{{ number_format($placement_YCME) }}</td>
                    </tr>
                    <tr>
                        <td>YCME 中方</td>
                        <td>{{ number_format($placement_YCME_CF) }}</td>
                    </tr>
                    <tr>
                        <td>新讯 Perusahaan lain nebeng gaji di YCME</td>
                        <td>{{ number_format($placement_YCME_XIN_XUN) }}</td>

                    </tr>
                    <tr>
                        <td>关务 Placement EXIM</td>
                        <td>{{ number_format($placement_YCME_EXIM) }}</td>
                    </tr>
                    <tr>
                        <td>关务外包 OS EXIM</td>
                        <td>{{ number_format($EXIM_WP) }}</td>
                    </tr>
                    <tr>
                        <td>Pabrik 1</td>
                        <td>{{ number_format($placement_pabrik1) }}</td>
                    </tr>
                    <tr>
                        <td>Pabrik 1 中方</td>
                        <td>{{ number_format($placement_pabrik1_CF) }}</td>
                    </tr>
                    <tr>
                        <td>Pabrik 1 外包</td>
                        <td>{{ number_format($Pabrik1_WP) }}</td>
                        <td>OS Placement Pabrik 1 ambil dari API</td>
                    </tr>
                    <tr>
                        <td>Pabrik 2</td>
                        <td>{{ number_format($placement_pabrik2) }}</td>
                    </tr>
                    <tr>
                        <td>Pabrik 2 中方</td>
                        <td>{{ number_format($placement_pabrik2_CF) }}</td>
                    </tr>
                    <tr>
                        <td>Pabrik 2 外包</td>
                        <td>{{ number_format($Pabrik2_WP) }}</td>
                        <td>OS Placement Pabrik 2 ambil dari API</td>
                    </tr>
                    <tr>
                        <td>Pabrik 3</td>
                        <td>{{ number_format($placement_pabrik3) }}</td>
                    </tr>
                    <tr>
                        <td>Pabrik 3 中方</td>
                        <td>{{ number_format($placement_pabrik3_CF) }}</td>
                    </tr>
                    <tr>
                        <td>Pabrik 3 外包</td>
                        <td>{{ number_format($Pabrik3_WP) }}</td>
                        <td>OS Placement Pabrik 3 ambil dari API</td>
                    </tr>
                    <tr>
                        <td>Pabrik 4</td>
                        <td>{{ number_format($placement_pabrik4) }}</td>
                    </tr>
                    <tr>
                        <td>Pabrik 4 中方</td>
                        <td>{{ number_format($placement_pabrik4_CF) }}</td>
                    </tr>
                    <tr>
                        <td>Pabrik 4 外包</td>
                        <td>{{ number_format($Pabrik4_WP) }}</td>
                        <td>OS Placement Pabrik 4 ambil dari API</td>
                    </tr>
                    <tr>
                        <td>YEV SUNRA</td>
                        <td>{{ number_format($placement_SUNRA) }}</td>
                    </tr>
                    <tr>
                        <td>YEV SUNRA 中方</td>
                        <td>{{ number_format($placement_SUNRA_CF) }}</td>
                    </tr>
                    <tr>
                        <td>YEV SUNRA 外包</td>
                        <td>{{ number_format($YEV_SUNRA_WP) }}</td>
                        <td>OS Placement YEV SUNRA ambil dari API</td>
                    </tr>
                    <tr>
                        <td>Pabrik 5 (YNE)</td>
                        <td>{{ number_format($placement_Pabrik5_YNE) }}</td>
                    </tr>
                    <tr>
                        <td>Pabrik 5 外包</td>
                        <td>{{ number_format($Pabrik5_WP) }}</td>
                        <td>OS Placement Pabrik 5 ambil dari API</td>
                    </tr>
                    <tr>
                        <td>STI</td>
                        <td>{{ number_format($placement_STI) }}</td>
                    </tr>
                    <tr>
                        <td>STI 外包</td>
                        <td>{{ number_format($STI_WP) }}</td>
                        <td>OS Placement STI ambil dari API</td>
                    </tr>

                    <tr>
                        <td>TDB 外包</td>
                        <td>{{ number_format($TDB_WP) }}</td>

                        <td>OS Placement TDB ambil dari API</td>
                    </tr>

                </tbody>
            </table>

        </div>
    </div>
    {{-- ggg --}}
    <div class="card m-3">
        <div class="card-header">
            <h5>Laporan Pembayaran PT</h5>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Jumlah Karyawan bulan lalu</th>
                        <th>bulan lalu</th>
                        <th>Jumlah Karyawan bulan Ini</th>
                        <th>Bulan ini</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($companies as $company)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">{{ $company }}</td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ number_format($last_results[$company]['count']) }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ number_format($last_results[$company]['total']) }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ number_format($results[$company]['count']) }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ number_format($results[$company]['total']) }}
                            </td>

                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
    </div>
</div>
