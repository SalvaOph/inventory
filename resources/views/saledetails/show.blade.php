@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>Sale Detail</h2>
            <a class="btn btn-primary" href="{{ route('saledetails.index') }}"> Back</a>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3>{{ $saledetail->product->name }}</h3>
        </div>
        <div class="card-body">
            <p><strong>Amount: </strong>{{ $saledetail->amount }}</p>
            <p><strong>Unit Price: </strong>{{ $saledetail->unit_price }}</p>
            <p><strong>Amount: </strong>{{ $saledetail->sale->total }}</p>
            <p><strong>Date: </strong>{{ $saledetail->sale->date }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('saledetails.edit', $saledetail->id) }}" class="btn btn-warning">Edit</a>
            <form action="{{ route('saledetails.destroy', $saledetail->id) }}" method="POST" style="display: inline-block;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection