<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID Karyawan</th>
            <th>Nama Karyawan</th>
            <th>Gaji BPJS</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($karyawans as $index => $karyawan)
            <tr>
                <td>{{ $karyawan->id_karyawan }}</td>
                <td>{{ $karyawan->nama }}</td>
                <td>{{ $karyawan->gaji_bpjs }}</td>
                <td>{{ $karyawan->status_karyawan }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center">
                    Tidak ada data
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
