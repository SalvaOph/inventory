@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>Provider Details</h2>
            <a class="btn btn-dark" href="{{ route('providers.index') }}">
                <i class="fa-solid fa-backward-step"></i> Back</a>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3>{{ $provider->name }}</h3>
        </div>
        <div class="card-body">
            <p><strong>Address: </strong>{{ $provider->address }}</p>
            <p><strong>Telephone: </strong>{{ $provider->telephone }}</p>
            <p><strong>E-mail: </strong>{{ $provider->email }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('providers.edit', $provider->id) }}" class="btn btn-warning">
                <i class="fa-solid fa-pen-to-square"></i> Edit</a>
            <form action="{{ route('providers.destroy', $provider->id) }}" method="POST" style="display: inline-block;">
                @csrf
                @method('DELETE')
                <button title="Delete" type="button" class="btn btn-danger delete-button"
                    data-url="{{ route('providers.destroy', $provider->id) }}" data-redirect-url="{{ route('providers.index') }}"> Delete
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