<h1>Reporte de Ventas</h1>
<table border="1">
    <thead>
        <tr>
            <th>#</th>
            <th>Total</th>
            <th>Date</th>
            <th>Client Id</th>
            <th>Product Id</th>
        </tr>
    </thead>
    <tbody>
        @php
            $i = 0;
        @endphp
        @foreach ($data as $sale)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $sale->total }}</td>
            <td>{{ $sale->date }}</td>
            <td>{{ $sale->client_id }}</td>
            <td>{{ $sale->product_id }}</td>            
        </tr>
        @endforeach
    </tbody>
</table>
