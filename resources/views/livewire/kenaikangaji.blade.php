<div>
    <div class='row m-3'>
        <div class='col-2'>

            {{-- <label class="form-label">{{ __('Tahun') }} </label> --}}
            <select class="form-select" wire:model.live='year'>
                {{-- <option selected>Tahun</option> --}}
                <option value="2023">2023</option>
                <option value="2024">2024</option>
                <option value="2025">2025</option>
            </select>
        </div>
        <div class='col-2'>
            {{-- <label class="form-label">{{ __('Etnis') }} </label> --}}
            <select wire:model.live="etnis" class="form-select wire:model.live='etnis'"
                aria-label="Default select example">
                {{-- <option value=" ">{{ __('Pilih Etnis') }}</option> --}}
                <option value="Tionghoa">{{ __('Tionghoa') }}</option>
                <option value="China">{{ __('China') }}</option>
                <option value="Lainnya">{{ __('Lainnya') }}</option>
            </select>
        </div>
        <div class='col-2'>
            <button class="btn btn-success" wire:click='excel'>Excel</button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Perubahan Gaji Karyawan Etnis {{ $etnis }} Tahun {{ $year }}</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table">
                    <tr>
                        <th>No</th>
                        <th>Nama Karyawan</th>
                        <th>ID Karyawan</th>
                        <th>Januari</th>
                        <th>Februari</th>
                        <th>Maret</th>
                        <th>April</th>
                        <th>Mei</th>
                        <th>Juni</th>
                        <th>Juli</th>
                        <th>Agustus</th>
                        <th>September</th>
                        <th>Oktober</th>
                        <th>November</th>
                        <th>Desember</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($karyawans as $index => $karyawan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $karyawan['nama'] }}</td>
                            <td>{{ $karyawan['id_karyawan'] }}</td>
                            @foreach ($karyawan['gaji_per_bulan'] as $bulan => $gaji)
                                <td>{{ $gaji !== null ? number_format($gaji, 0, ',', '.') : '-' }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="text-center">Tidak ada data karyawan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
