<h1>Reporte de Inventarios</h1>
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
        @foreach ($data as $warehouse)
        <tr>
            <td>{{ $warehouse->id }}</td>
            <td>{{ $warehouse->name }}</td>
            <td>{{ $warehouse->address }}</td>
            <td>{{ $warehouse->telephone }}</td>
            <td>{{ $warehouse->email }}</td>            
        </tr>
        @endforeach
    </tbody>
</table>
