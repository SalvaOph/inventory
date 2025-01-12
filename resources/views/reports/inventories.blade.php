<h1>Reporte de Inventario</h1>
<table border="1">
    <thead>
        <tr>
            <th>#</th>
            <th>Product Id</th>
            <th>Stock</th>
            <th>Warehouse Location</th>            
        </tr>
    </thead>
    <tbody>
        @php
            $i = 0;
        @endphp
        @foreach ($data as $Inventory)
        <tr>
            <td>{{ $Inventory->id }}</td>
            <td>{{ $Inventory->product_id }}</td>
            <td>{{ $Inventory->stock }}</td>
            <td>{{ $Inventory->warehouse_id}}</td>                   
        </tr>
        @endforeach
    </tbody>
</table>
