@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>{{ isset($warehouse) ? 'Edit Warehouse' : 'Create Warehouse' }}</h2>
            <a class="btn btn-dark" href="{{ route('warehouses.index') }}">
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

    <form action="{{ isset($warehouse) ? route('warehouses.update', $warehouse->id) : route('warehouses.store') }}" method="POST">
        @csrf
        @if(isset($warehouse)) @method('PUT') @endif

        <div class="form-group">
            <strong>Name:</strong>
            <input type="text" name="name" value="{{ isset($warehouse) ? $warehouse->name : '' }}" class="form-control">
        </div>
        <div class="form-group">
            <strong>Address:</strong>
            <textarea class="form-control" name="address">{{ isset($warehouse) ? $warehouse->address : '' }}</textarea>
        </div>
        <div class="form-group">
            <strong>Telephone:</strong>
            <input type="number" name="telephone" value="{{ isset($warehouse) ? $warehouse->telephone : '' }}" class="form-control">
        </div>
        <div class="form-group">
            <strong>E-mail:</strong>
            <input type="email" name="email" value="{{ isset($warehouse) ? $warehouse->email : '' }}" class="form-control">
        </div>

        <div class="form-group mt-2">
            <button type="submit" class="btn btn-success">
                <i class="fa-solid fa-floppy-disk"></i> {{ isset($warehouse) ? 'Update' : 'Create' }}</button>
        </div>
    </form>
</div>
@endsection