@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>{{ isset($saledetail) ? 'Edit Sale Detail' : 'Create Sale Detail' }}</h2>
            <a class="btn btn-primary" href="{{ route('saledetails.index') }}"> Back</a>
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

    <form action="{{ isset($saledetail) ? route('saledetails.update', $saledetail->id) : route('saledetails.store') }}" method="POST">
        @csrf
        @if(isset($saledetail)) @method('PUT') @endif

        <div class="form-group">
            <strong>Amount:</strong>
            <input type="text" name="amount" value="{{ isset($saledetail) ? $saledetail->amount : '' }}" class="form-control">
        </div>
        <div class="form-group">
            <strong>Unit Price:</strong>
            <input type="text" name="unit_price" value="{{ isset($saledetail) ? $saledetail->unit_price : '' }}" class="form-control">
        </div>
        <div class="form-group">
            <strong>Sale:</strong>
            <input type="text" name="sale_id" value="{{ isset($saledetail) ? $saledetail->sale_id : '' }}" class="form-control">
        </div>
        <div class="form-group">
            <strong>Product:</strong>
            <input type="text" name="product_id" value="{{ isset($saledetail) ? $saledetail->product_id : '' }}" class="form-control">
        </div>

        <div class="form-group mt-2">
            <button type="submit" class="btn btn-success">{{ isset($saledetail) ? 'Update' : 'Create' }}</button>
        </div>
    </form>
</div>
@endsection