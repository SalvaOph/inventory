<h1>Reporte de Proveedores</h1>
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
        @foreach ($data as $provider)
        <tr>
            <td>{{ $provider->id }}</td>
            <td>{{ $provider->name }}</td>
            <td>{{ $provider->address }}</td>
            <td>{{ $provider->telephone }}</td>
            <td>{{ $provider->email }}</td>            
        </tr>
        @endforeach
    </tbody>
</table>
