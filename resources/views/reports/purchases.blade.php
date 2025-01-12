<h1>Reporte de Compras</h1>
<table border="1">
    <thead>
        <tr>
            <th>#</th>
            <th>Total</th>
            <th>Date</th>
            <th>Client Id</th>            
        </tr>
    </thead>
    <tbody>
        @php
            $i = 0;
        @endphp
        @foreach ($data as $sale)
        <tr>
            <td>{{ $sale->id }}</td>
            <td>{{ $sale->total }}</td>
            <td>{{ $sale->date }}</td>
            <td>{{ $sale->provider_id }}</td>                        
        </tr>
        @endforeach
    </tbody>
</table>
