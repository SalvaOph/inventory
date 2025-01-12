@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>{{ isset($provider) ? 'Edit Provider' : 'Create Provider' }}</h2>
            <a class="btn btn-dark" href="{{ route('providers.index') }}">
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

    <form action="{{ isset($provider) ? route('providers.update', $provider->id) : route('providers.store') }}" method="POST">
        @csrf
        @if(isset($provider)) @method('PUT') @endif

        <div class="form-group">
            <strong>Name:</strong>
            <input type="text" name="name" value="{{ isset($provider) ? $provider->name : '' }}" class="form-control">
        </div>
        <div class="form-group">
            <strong>Address:</strong>
            <textarea class="form-control" name="address">{{ isset($provider) ? $provider->address : '' }}</textarea>
        </div>
        <div class="form-group">
            <strong>Telephone:</strong>
            <input type="number" name="telephone" value="{{ isset($provider) ? $provider->telephone : '' }}" class="form-control">
        </div>
        <div class="form-group">
            <strong>E-mail:</strong>
            <input type="email" name="email" value="{{ isset($provider) ? $provider->email : '' }}" class="form-control">
        </div>
        <div class="form-group mt-2">
            <button type="submit" class="btn btn-success">
            <i class="fa-solid fa-floppy-disk"></i> {{ isset($provider) ? 'Update' : 'Create' }}</button>
        </div>
    </form>
</div>
@endsection