<div>

    <table class="table">
        <thead>
            <tr>
                <th>Id</th>
                <th>Nama</th>
                <th>JkK</th>
                <th>JKM</th>
                <th>KESEHATAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $d)
                <tr>
                    <td>{{ $d->id }}</td>
                    <td>{{ $d->nama }}</td>
                    <td>{{ $d->jkk }}</td>
                    <td>{{ $d->jkm }}</td>
                    <td>{{ $d->kesehatan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
