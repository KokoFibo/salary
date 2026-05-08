<table class="min-w-full border border-gray-300 text-sm">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-3 py-2 border">ID Karyawan</th>
            <th class="px-3 py-2 border">Nama</th>
            <th class="px-3 py-2 border">Status</th>
            <th class="px-3 py-2 border">Etnis</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $index => $item)
            <tr class="text-center">
                <td class="px-3 py-2 border">{{ $item->id_karyawan }}</td>
                <td class="px-3 py-2 border">{{ $item->nama }}</td>
                <td class="px-3 py-2 border">{{ $item->status_karyawan }}</td>
                <td class="px-3 py-2 border">TKA</td>
            </tr>
        @endforeach
    </tbody>
</table>
