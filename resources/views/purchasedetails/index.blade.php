@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>Purchases Detail List</h2>
            <a class="btn btn-success" href="{{ route('purchasedetails.create') }}"> Create New Purchase Detail</a>
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
            <th>Purchase Total</th>
            <th>Product</th>
            <th width="280px">Action</th>
        </tr>
        @php
            $i = 0;
        @endphp
        @foreach ($purchasedetails as $purchasedetail)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $purchasedetail->amount }}</td>
            <td>{{ $purchasedetail->unit_price }}</td>
            <td>{{ $purchasedetail->purchase->total }}</td>
            <td>{{ $purchasedetail->product->name }}</td>
            <td>
                <form action="{{ route('purchasedetails.destroy', $purchasedetail->id) }}" method="POST">
                    <a class="btn btn-info" href="{{ route('purchasedetails.show', $purchasedetail->id) }}">Show</a>
                    <a class="btn btn-primary" href="{{ route('purchasedetails.edit', $purchasedetail->id) }}">Edit</a>
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