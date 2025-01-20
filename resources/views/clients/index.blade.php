@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row">
        <h2>Clients List</h2>
        <div class="col">
            <a class="btn btn-success" href="{{ route('clients.create') }}">
                <i class="fa-solid fa-circle-plus"></i> Create New Client</a>
            <a href="{{ route('export.excel', ['entity' => 'clients']) }}" class="btn btn-success" title="Exportar Productos a Excel">
                <i class="fas fa-file-excel"></i>
            </a>
            <a href="{{ route('export.pdf', ['entity' => 'clients']) }}" class="btn btn-danger" title="Exportar Productos a PDF">
                <i class="fas fa-file-pdf"></i>
            </a>
        </div>
        <div class="col">
            <div>
                <form method="GET" action="{{ route('clients.index') }}" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search clients..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
    <div class="alert alert-success mt-2">
        <p>{{ $message }}</p>
    </div>
    @endif

    <table class="table table-bordered mt-2 table-hover" id="index_table">
        <thead>
            <tr>
                <th width="5%">No</th>

                <!-- Nombre con ordenación -->
                <th>
                    <a href="{{ route('clients.index', ['sort_by' => 'name', 'sort_order' => 
                                request('sort_order') == 'asc' ? 'desc' : 'asc', 'items' => request('items')]) }}">
                        Name
                        <i class="fa-sharp fa-solid fa-filter"></i>
                        @if (request('sort_by') == 'name')
                        @if (request('sort_order') == 'asc')
                        <i class="fas fa-arrow-up"></i>
                        @else
                        <i class="fas fa-arrow-down"></i>
                        @endif
                        @endif
                    </a>
                </th>

                <!-- Direccíon con ordenación -->
                <th>
                    <a href="{{ route('clients.index', ['sort_by' => 'address', 'sort_order' => 
                                request('sort_order') == 'asc' ? 'desc' : 'asc', 'items' => request('items')]) }}">
                        Address
                        <i class="fa-sharp fa-solid fa-filter"></i>
                        @if (request('sort_by') == 'address')
                        @if (request('sort_order') == 'asc')
                        <i class="fas fa-arrow-up"></i>
                        @else
                        <i class="fas fa-arrow-down"></i>
                        @endif
                        @endif
                    </a>
                </th>

                <!-- E-mail con ordenación -->
                <th>
                    <a href="{{ route('clients.index', ['sort_by' => 'email', 'sort_order' => 
                                request('sort_order') == 'asc' ? 'desc' : 'asc', 'items' => request('items')]) }}">
                        E-mail
                        <i class="fa-sharp fa-solid fa-filter"></i>
                        @if (request('sort_by') == 'email')
                        @if (request('sort_order') == 'asc')
                        <i class="fas fa-arrow-up"></i>
                        @else
                        <i class="fas fa-arrow-down"></i>
                        @endif
                        @endif
                    </a>
                </th>

                <th>Telephone</th>
                <th width="15%">Action</th>
            </tr>
        </thead>
        @foreach ($clients as $key => $client)
        <tr>
            <td>{{ $clients->firstItem() + $key }}</td>
            <td>{{ $client->name }}</td>
            <td>{{ $client->address }}</td>
            <td>{{ $client->email }}</td>
            <td>{{ $client->telephone }}</td>
            <td>
                <div class="container text-center">
                    <a title="Show" class="btn btn-success" href="{{ route('clients.show', $client->id) }}">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    <a title="Edit" class="btn btn-warning" href="{{ route('clients.edit', $client->id) }}">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <form action="{{ route('clients.destroy', $client->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button title="Delete" type="button" class="btn btn-danger delete-button"
                            data-url="{{ route('clients.destroy', $client->id) }}" data-redirect-url="{{ route('clients.index') }}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
    </table>

    <div class="d-flex justify-content-between align-items-center">
        <form method="GET" class="d-flex align-items-center">
            <label for="items" class="me-2">Show: </label>
            <select name="items" id="items" class="form-select w-auto" onchange="this.form.submit()">
                <option value="5" {{ $itemsPerPage == 5 ? 'selected' : '' }}>5</option>
                <option value="10" {{ $itemsPerPage == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ $itemsPerPage == 25 ? 'selected' : '' }}>25</option>
            </select>
        </form>

        <div>
            {{ $clients->links() }}
        </div>
    </div>
</div>
@endsection

<script type="module">
    document.addEventListener('DOMContentLoaded', () => window.searchDynamics());
    document.addEventListener('DOMContentLoaded', () => window.confirmDelete());
</script>