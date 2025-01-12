@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>{{ isset($purchasedetail) ? 'Edit Purchase Detail' : 'Create Purchase Detail' }}</h2>
            <a class="btn btn-primary" href="{{ route('purchasedetails.index') }}"> Back</a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ isset($purchasedetail) ? route('purchasedetails.update', $purchasedetail->id) : route('purchasedetails.store') }}" method="POST">
        @csrf
        @if(isset($purchasedetail)) @method('PUT') @endif

        <div class="form-group">
            <strong>Amount:</strong>
            <input type="text" name="amount" value="{{ isset($purchasedetail) ? $purchasedetail->amount : '' }}" class="form-control">
        </div>
        <div class="form-group">
            <strong>Unit Price:</strong>
            <input type="text" name="unit_price" value="{{ isset($purchasedetail) ? $purchasedetail->unit_price : '' }}" class="form-control">
        </div>
        <div class="form-group">
            <strong>Purchase:</strong>
            <input type="text" name="purchase_id" value="{{ isset($purchasedetail) ? $purchasedetail->purchase_id : '' }}" class="form-control">
        </div>
        <div class="form-group">
            <strong>Product:</strong>
            <input type="text" name="product_id" value="{{ isset($purchasedetail) ? $purchasedetail->product_id : '' }}" class="form-control">
        </div>
        
        <div class="form-group mt-2">
            <button type="submit" class="btn btn-success">{{ isset($purchasedetail) ? 'Update' : 'Create' }}</button>
        </div>
    </form>
</div>
@endsection