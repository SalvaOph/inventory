@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>{{ isset($product) ? 'Edit Product' : 'Create Product' }}</h2>
            <a class="btn btn-dark" href="{{ route('products.index') }}">
                <i class="fa-solid fa-backward-step"></i> Back</a>
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

    <form action="{{ isset($product) ? route('products.update', $product->id) : route('products.store') }}" method="POST">
        @csrf
        @if(isset($product)) @method('PUT') @endif

        <div class="form-group">
            <strong>Name:</strong>
            <input type="text" name="name" value="{{ isset($product) ? $product->name : '' }}" class="form-control">
        </div>
        <div class="form-group">
            <strong>Description:</strong>
            <textarea class="form-control" name="description">{{ isset($product) ? $product->description : '' }}</textarea>
        </div>
        <div class="form-group">
            <strong>Price:</strong>
            <input type="number" step="0.01" name="price" value="{{ isset($product) ? $product->price : '' }}" class="form-control">
        </div>
        <div class="form-group mt-2">
            <button type="submit" class="btn btn-success">
                <i class="fa-solid fa-floppy-disk"></i> {{ isset($product) ? 'Update' : 'Create' }}</button>
        </div>
    </form>
</div>
@endsection