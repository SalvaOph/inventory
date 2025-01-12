<h1>Reporte de Clientes</h1>
<table border="1">
    <thead>
        <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Direccion</th>
            <th>Telefono</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        @php
            $i = 0;
        @endphp
        @foreach ($data as $client)
        <tr>
            <td>{{ $client->id }}</td>
            <td>{{ $client->name }}</td>
            <td>{{ $client->address }}</td>
            <td>{{ $client->telephone }}</td>
            <td>{{ $client->email }}</td>            
        </tr>
        @endforeach
    </tbody>
</table>
