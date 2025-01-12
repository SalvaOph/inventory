@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>Sales Detail List</h2>
            <a class="btn btn-success" href="{{ route('saledetails.create') }}"> Create New Sale Detail</a>
        </div>
    </div>
    
    @if ($message = Session::get('success'))
        <div class="alert alert-success mt-2">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-bordered mt-2">
        <tr>
            <th>No</th>
            <th>Amount</th>
            <th>Unit Price</th>
            <th>Sale Total</th>
            <th>Product</th>
            <th width="280px">Action</th>
        </tr>
        @php
            $i = 0;
        @endphp
        @foreach ($saledetails as $saledetail)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $saledetail->amount }}</td>
            <td>{{ $saledetail->unit_price }}</td>
            <td>{{ $saledetail->sale->total }}</td>
            <td>{{ $saledetail->product->name }}</td>
            <td>
                <form action="{{ route('saledetails.destroy', $saledetail->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('saledetails.show', $saledetail->id) }}">Show</a>
                    <a class="btn btn-primary" href="{{ route('saledetails.edit', $saledetail->id) }}">Edit</a>
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection