@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <h2>Products List</h2>
        <div class="col">
            <a class="btn btn-success" href="{{ route('products.create') }}">
                <i class="fa-solid fa-circle-plus"></i> Create New Product</a>
            <a href="{{ route('export.excel', ['entity' => 'products']) }}" class="btn btn-success" title="Exportar Productos a Excel">
                <i class="fas fa-file-excel"></i>
            </a>
            <a href="{{ route('export.pdf', ['entity' => 'products']) }}" class="btn btn-danger" title="Exportar Productos a PDF">
                <i class="fas fa-file-pdf"></i>
            </a>
        </div>
        <div class="col">
            <div>
                <form method="GET" action="{{ route('products.index') }}" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
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

    <div id="paginateButton"></div>

    <table class="table table-bordered mt-2 table-hover" id="index_table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <!-- Nombre con ordenación -->
                <th>
                    <a href="{{ route('products.index', ['sort_by' => 'name', 'sort_order' => 
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

                <!-- Precio con ordenación -->
                <th>
                    <a href="{{ route('products.index', ['sort_by' => 'price', 'sort_order' => 
                                request('sort_order') == 'asc' ? 'desc' : 'asc', 'items' => request('items')]) }}">
                        Price
                        <i class="fa-sharp fa-solid fa-filter"></i>
                        @if (request('sort_by') == 'price')
                        @if (request('sort_order') == 'asc')
                        <i class="fas fa-arrow-up"></i>
                        @else
                        <i class="fas fa-arrow-down"></i>
                        @endif
                        @endif
                    </a>
                </th>

                <!-- Precio de venta con ordenación -->
                <th>
                    <a href="{{ route('products.index', ['sort_by' => 'saleprice', 'sort_order' => 
                                request('sort_order') == 'asc' ? 'desc' : 'asc', 'items' => request('items')]) }}">
                        Sale Price
                        <i class="fa-sharp fa-solid fa-filter"></i>
                        @if (request('sort_by') == 'saleprice')
                        @if (request('sort_order') == 'asc')
                        <i class="fas fa-arrow-up"></i>
                        @else
                        <i class="fas fa-arrow-down"></i>
                        @endif
                        @endif
                    </a>
                </th>

                <th>Description</th>

                <th width="15%">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $key => $product)
            <tr>
                <td>{{ $products->firstItem() + $key }}</td>
                <td>{{ $product->name }}</td>
                <td>$ {{ $product->price }}</td>
                <td>$ {{ number_format((float) $product->saleprice, 2) }}</td>
                <td>{{ $product->description }}</td>
                <td>
                    <div class="container text-center">
                        <a title="Show" href="{{ route('products.show', $product->id) }}" class="btn btn-success">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a title="Edit" href="{{ route('products.edit', $product->id) }}" class="btn btn-warning">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button title="Delete" type="button" class="btn btn-danger delete-button"
                                data-url="{{ route('products.destroy', $product->id) }}" data-redirect-url="{{ route('products.index') }}">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
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
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection

<script type="module">
    document.addEventListener('DOMContentLoaded', () => window.searchDynamics());
    document.addEventListener('DOMContentLoaded', () => window.confirmDelete());
</script>