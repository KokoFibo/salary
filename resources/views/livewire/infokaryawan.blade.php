<div>

    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .content {
            padding: 20px;
        }

        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>

    <body>



        <!-- Main Content -->
        <div class="content">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Informasi Karyawan</h2>
                <a href="/karyawanindex"><button class="btn btn-primary">Exit</button></a>
            </div>

            <!-- Cards -->
            {{-- First row --}}
            <div class="row g-4">
                <!-- Card 0 -->
                <div class="col-md-2">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Karyawan Aktif</h5>
                            <br>
                            <br>
                            <p class="display-4">{{ number_format($total_karyawan_aktif) }}</p>
                        </div>
                    </div>
                </div>
                <!-- Card 1 -->
                <div class="col-md-2">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Karyawan PKWT</h5>
                            <br>
                            <br>
                            <p class="display-4">{{ number_format($pkwt) }}</p>
                        </div>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="col-md-2">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Karyawan PKWTT</h5>
                            <br>
                            <br>
                            <p class="display-4">{{ number_format($pkwtt) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="col-md-2">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Karyawan Dirumahkan</h5>
                            <br>
                            <br>
                            <p class="display-4">{{ number_format($dirumahkan) }}</p>
                        </div>
                    </div>
                </div>
                <!-- Card 4 -->
                <div class="col-md-2">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Karyawan Resigned</h5>
                            <br>
                            <br>
                            <p class="display-4">{{ number_format($resigned) }}</p>
                        </div>
                    </div>
                </div>
                <!-- Card 5 -->
                <div class="col-md-2">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Karyawan Blacklist</h5>
                            <br>
                            <br>
                            <p class="display-4">{{ number_format($blacklist) }}</p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Second row --}}
            <div class="row g-4">
                <!-- Card 1 -->
                <div class="col-md-2">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Tanpa No Rekening Bank</h5>
                            @if ($jumlahTanpaRekening > 0)
                                <p class="display-4 text-danger">{{ number_format($jumlahTanpaRekening) }}</p>
                            @else
                                <p class="display-4">{{ number_format($jumlahTanpaRekening) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Belum upload dokumen</h5>
                            <p class="display-4">{{ number_format($jumlah_karyawan_tanpa_dokumen) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Jumlah Karyawan Perbulan</h5>
                            <p class="display-4">{{ number_format($total_karyawan_perbulan) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Jumlah Karyawan Perjam</h5>
                            <p class="display-4">{{ number_format($total_karyawan_perjam) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Jumlah Karyawan Perbulan tanpa PTKP</h5>
                            <p class="display-4">{{ number_format($karyawan_perbulan_tanpa_ptkp) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Jumlah Karyawan tanpa Email</h5>
                            <p class="display-4">{{ number_format($jumlah_karyawan_tanpa_email) }}</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Table Karyawan tanpa nomor rekening bank -->
            @if ($dataTanpaRekening->isNotEmpty())
                <div class="row g-4">
                    <div class="mt-5">
                        <h4>Karyawan tanpa nomor rekening bank</h4>
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>ID Karyawan</th>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>Status</th>
                                    <th>Bank</th>
                                    <th>No Rekening</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dataTanpaRekening as $key => $dtr)
                                    <tr>
                                        <td>{{ $key }}</td>
                                        <td>{{ $dtr->id_karyawan }}</td>
                                        <td>{{ $dtr->nama }}</td>
                                        <td>{{ nama_jabatan($dtr->jabatan_id) }}</td>
                                        <td>{{ $dtr->status_karyawan }}</td>
                                        <td>{{ $dtr->nama_bank }}</td>

                                        <td>
                                            <button class="btn btn-sm btn-danger">Kosong</button>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Table Karyawan tanpa Email -->
            @if ($karyawan_tanpa_email->isNotEmpty())
                <div class="row g-4">
                    <div class="mt-5">
                        <h4>Karyawan tanpa Email</h4>
                        <button class="btn btn-sm btn-primary mb-3" wire:click="toggleKaryawanTanpaEmail">
                            {{ $showKaryawanTanpaEmail ? 'Hide Details' : 'Show Details' }}
                        </button>

                        @if ($showKaryawanTanpaEmail)
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>ID Karyawan</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Status</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($karyawan_tanpa_email as $key => $dtr)
                                        <tr class="text-center">
                                            <td>{{ $key }}</td>
                                            <td>{{ $dtr->id_karyawan }}</td>
                                            <td>{{ $dtr->nama }}</td>
                                            <td>{{ nama_jabatan($dtr->jabatan_id) }}</td>
                                            <td>{{ $dtr->status_karyawan }}</td>

                                            <td>
                                                <button class="btn btn-sm btn-danger">Kosong</button>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Table Karyawan terlambat -->
            @if ($karyawan_telat->isNotEmpty())
                <div class="row g-4">
                    <div class="mt-5">

                        <h4>Karyawan (Perbulan) Terlambat Total {{ $total_karyawan_telat }} Orang
                            ({{ nama_bulan($month) }} {{ $year }})</h4>
                        <div class="d-flex align-items-center gap-2 mb-3">

                            <button class="btn btn-sm btn-primary" wire:click="toggleKaryawanTerlambat">
                                {{ $showKaryawanTerlambat ? 'Hide Details' : 'Show Details' }}
                            </button>

                            <select wire:model.live="selectBulan" class="form-select form-select-sm w-auto">
                                <option value="0">Bulan ini</option>
                                <option value="1">Bulan lalu</option>
                            </select>
                        </div>

                        @if ($showKaryawanTerlambat)
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>ID Karyawan</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Status</th>
                                        <th>Company</th>
                                        <th>Jumlah Hari Terlambat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($karyawan_telat as $key => $dtr)
                                        <tr class="text-center">
                                            <td>{{ $key }}</td>
                                            <td>{{ $dtr->user_id }}</td>
                                            <td>{{ $dtr->nama }}</td>
                                            <td>{{ nama_jabatan($dtr->jabatan_id) }}</td>
                                            <td>{{ $dtr->status_karyawan }}</td>
                                            <td>{{ nama_company($dtr->company_id) }}</td>
                                            <td>{{ $dtr->total_terlambat }}</td>



                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Script Bootstrap -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>

</div>
