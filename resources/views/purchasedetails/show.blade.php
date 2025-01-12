@extends('layout.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>Purchase Detail</h2>
            <a class="btn btn-primary" href="{{ route('purchasedetails.index') }}"> Back</a>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3>{{ $purchasedetail->product->name }}</h3>
        </div>
        <div class="card-body">
            <p><strong>Amount: </strong>{{ $purchasedetail->amount }}</p>
            <p><strong>Unit Price: </strong>{{ $purchasedetail->unit_price }}</p>
            <p><strong>Amount: </strong>{{ $purchasedetail->purchase->total }}</p>
            <p><strong>Date: </strong>{{ $purchasedetail->purchase->date }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('purchasedetails.edit', $purchasedetail->id) }}" class="btn btn-warning">Edit</a>
            <form action="{{ route('purchasedetails.destroy', $purchasedetail->id) }}" method="POST" style="display: inline-block;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection