<div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Company ID</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $item->id_karyawan }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ nama_company($item->company_id) }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>
    <table>
        <thead>
            <tr>
                <th>ID User</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($presensis as $item)
                <tr>
                    <td>{{ $item->user_id }}</td>

                </tr>
            @endforeach

        </tbody>
    </table>
    <button class="btn btn-primary" wire:click="deleteSTI">Delete all STI</button>
</div>
