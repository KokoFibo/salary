<div>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>First in</th>
                <th>First out</th>
                <th>Second in</th>
                <th>Second out</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $d)
                <tr>
                    <td>{{ $d->user_id }}</td>
                    <td>{{ $d->first_in }}</td>
                    <td>{{ $d->first_out }}</td>
                    <td>{{ $d->second_in }}</td>
                    <td>{{ $d->second_out }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
