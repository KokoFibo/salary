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
                            <p class="display-4">{{ $total_karyawan_aktif }}</p>
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
                            <p class="display-4">{{ $pkwt }}</p>
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
                            <p class="display-4">{{ $pkwtt }}</p>
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
                            <p class="display-4">{{ $dirumahkan }}</p>
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
                            <p class="display-4">{{ $resigned }}</p>
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
                            <p class="display-4">{{ $blacklist }}</p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Second row --}}
            <div class="row g-4">
                <!-- Card 1 -->
                <div class="col-md-4 d-flex gap-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Tanpa No Rekening Bank</h5>
                            @if ($jumlahTanpaRekening > 0)
                                <p class="display-4 text-danger">{{ $jumlahTanpaRekening }}</p>
                            @else
                                <p class="display-4">{{ $jumlahTanpaRekening }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Belum upload dokumen</h5>
                            <p class="display-4">{{ $total_karyawan_aktif - $karyawan_berdokumen }}</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Karyawan Gaji 0</h5>
                            <p class="display-4">{{ $karyawan_gaji_0_count }}</p>
                        </div>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="col-md-4 d-none">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Karyawan</h5>
                            <p class="display-4">{{ $total_karyawan_aktif }}</p>
                        </div>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="col-md-4 d-none">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Hadir Hari Ini</h5>
                            <p class="display-4">{{ $total_karyawan_hadir_hari_ini }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="row g-4">

            <!-- Table -->
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
            <!-- Table -->
            <div class="mt-5">
                <h4>Karyawan Gaji 0</h4>
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>ID Karyawan</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Status</th>
                            <th>Gaji</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($karyawan_gaji_0 as $key => $dtr)
                            <tr>
                                <td>{{ $key }}</td>
                                <td>{{ $dtr->id_karyawan }}</td>
                                <td>{{ $dtr->nama }}</td>
                                <td>{{ nama_jabatan($dtr->jabatan_id) }}</td>
                                <td>{{ $dtr->status_karyawan }}</td>
                                <td>{{ $dtr->gaji_tetap }}</td>


                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>

        <!-- Script Bootstrap -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>

</div>
