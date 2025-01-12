@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>Client Details</h2>
            <a class="btn btn-dark" href="{{ route('clients.index') }}">
                <i class="fa-solid fa-backward-step"></i> Back</a>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3>{{ $client->name }}</h3>
        </div>
        <div class="card-body">
            <p><strong>Address: </strong>{{ $client->address }}</p>
            <p><strong>Telephone: </strong>{{ $client->telephone }}</p>
            <p><strong>E-mail: </strong>{{ $client->email }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-warning">
                <i class="fa-solid fa-pen-to-square"></i> Edit
            </a>
            <form action="{{ route('clients.destroy', $client->id) }}" method="POST" style="display: inline-block;">
                @csrf
                @method('DELETE')
                <button title="Delete" type="button" class="btn btn-danger delete-button"
                    data-url="{{ route('clients.destroy', $client->id) }}" data-redirect-url="{{ route('clients.index') }}"> Delete
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

<script type="module">
    import confirmDelete from '/js/deleteconfirm.js';

    document.addEventListener('DOMContentLoaded', confirmDelete);
</script>