@extends('layouts.app')
@section('content')
    <div class="container-fluid">

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    Laporan Cost Karyawan Tahun {{ $year }}
                </h5>
                <a href="/payroll"><button class="btn btn-dark">Kembali ke payroll</button></a>
                <span class="badge bg-light text-dark">
                    Total Data: {{ count($payrolls) }}
                </span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm mb-0">
                        <thead class="table-dark text-center align-middle">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>No ID</th>
                                <th>Company</th>
                                <th>Directorate</th>
                                <th>Dept</th>
                                <th>Jabatan</th>
                                <th>Bruto / Thn</th>
                                <th>Lembur</th>
                                <th>Shift Malam</th>
                                <th>Bonus</th>
                                <th>Potongan</th>
                                <th>JHT Karyawan</th>
                                <th>JP Karyawan</th>
                                {{-- <th>JKK</th>
                                <th>JKM</th> --}}
                                <th>BPJS KS Karyawan</th>
                                <th>JHT Company</th>
                                <th>JP Company</th>
                                <th>JKK Company</th>
                                <th>JKM Company</th>
                                <th>BPJS KS Company</th>
                                <th>PPh21</th>
                                <th>Gaji Dibayarkan</th>
                                <th>TOTAL COST</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($payrolls as $i => $row)
                                <tr class="text-end align-middle">
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td class="text-start fw-semibold">{{ $row->nama }}</td>
                                    <td class="text-center">{{ $row->id_karyawan }}</td>

                                    <td class="text-center">{{ $row->company }}</td>
                                    <td class="text-center">{{ $row->placement }}</td>
                                    <td class="text-center">{{ $row->department }}</td>
                                    <td class="text-center">{{ $row->jabatan }}</td>

                                    <td>{{ number_format($row->bruto_thn) }}</td>
                                    <td>{{ number_format($row->total_lembur) }}</td>
                                    <td>{{ number_format($row->shift_malam) }}</td>
                                    <td>{{ number_format($row->bonus) }}</td>
                                    <td class="text-danger">{{ number_format($row->potongan) }}</td>

                                    <td>{{ number_format($row->jht_karyawan) }}</td>
                                    <td>{{ number_format($row->jp_karyawan) }}</td>
                                    {{-- <td class="text-center">
                                        <span class="badge {{ $row->jkk == 1 ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $row->jkk == 1 ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $row->jkm == 1 ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $row->jkm == 1 ? 'Yes' : 'No' }}
                                        </span>
                                    </td> --}}

                                    <td>{{ number_format($row->bpjs_ks_karyawan) }}</td>
                                    <td>{{ number_format($row->jht_company) }}</td>
                                    <td>{{ number_format($row->jp_company) }}</td>
                                    <td>{{ number_format($row->jkk_company) }}</td>
                                    <td>{{ number_format($row->jkm_company) }}</td>

                                    <td>{{ number_format($row->bpjs_ks_company) }}</td>

                                    <td>{{ number_format($row->pph21) }}</td>

                                    <td class="fw-bold text-success">
                                        {{ number_format($row->gaji_dibayarkan) }}
                                    </td>

                                    <td class="fw-bold text-danger">
                                        {{ number_format($row->total_cost) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
