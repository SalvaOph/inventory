@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>Warehouse Details</h2>
            <a class="btn btn-dark" href="{{ route('warehouses.index') }}">
                <i class="fa-solid fa-backward-step"></i> Back</a>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3>{{ $warehouse->name }}</h3>
        </div>
        <div class="card-body">
            <p><strong>Address: </strong>{{ $warehouse->address }}</p>
            <p><strong>Telephone: </strong>{{ $warehouse->telephone }}</p>
            <p><strong>E-mail: </strong>{{ $warehouse->email }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('warehouses.edit', $warehouse->id) }}" class="btn btn-warning">
                <i class="fa-solid fa-pen-to-square"></i> Edit</a>
            <form action="{{ route('warehouses.destroy', $warehouse->id) }}" method="POST" style="display: inline-block;">
                @csrf
                @method('DELETE')
                <button title="Delete" type="button" class="btn btn-danger delete-button"
                    data-url="{{ route('warehouses.destroy', $warehouse->id) }}" data-redirect-url="{{ route('warehouses.index') }}"> Delete
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

<script type="module">
    document.addEventListener('DOMContentLoaded', () => window.confirmDelete());
</script>