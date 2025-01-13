@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <h2>Inventories List</h2>
        <div class="col">
            <a class="btn btn-success" href="{{ route('inventories.create') }}">
                <i class="fa-solid fa-circle-plus"></i> Create New Inventory</a>
            <a href="{{ route('export.excel', ['entity' => 'inventories']) }}" class="btn btn-success" title="Exportar Productos a Excel">
                <i class="fas fa-file-excel"></i>
            </a>
            <a href="{{ route('export.pdf', ['entity' => 'inventories']) }}" class="btn btn-danger" title="Exportar Productos a PDF">
                <i class="fas fa-file-pdf"></i>
            </a>
        </div>
        <div class="col">
            <div>
                <form method="GET" action="{{ route('inventories.index') }}" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search inventories..." value="{{ request('search') }}">
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


    <div class="accordion accordion-flush" id="accordionFlushExample">
        @foreach ($products as $product)
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-{{ $product->id }}" aria-expanded="false" aria-controls="flush-collapse-{{ $product->id }}">
                    {{ $product->name }} || Total de unidades: {{ $totalInventories[$product->id] ?? 0 }}
                </button>
            </h2>
            <div id="flush-collapse-{{ $product->id }}" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body">
                    @if ($product->inventories->isEmpty())
                    <p>No stock available for this product.</p>
                    @else
                    <table class="table table-bordered mt-2 table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>
                                    Warehouse
                                </th>
                                <th>
                                    Stock
                                    </a>
                                </th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($product->inventories as $key => $inventory)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $inventory->warehouse->name }}</td>
                                <td>{{ $inventory->stock }}</td>
                                <td>
                                    <div class="container text-center">
                                        <a title="Show" class="btn btn-success" href="{{ route('inventories.show', $inventory->id) }}">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a title="Edit" class="btn btn-warning" href="{{ route('inventories.edit', $inventory->id) }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('inventories.destroy', $inventory->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button title="Delete" type="button" class="btn btn-danger delete-button"
                                                data-url="{{ route('inventories.destroy', $inventory->id) }}" data-redirect-url="{{ route('inventories.index') }}">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
        @endforeach

        <div class="d-flex justify-content-between align-items-center mt-3">
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
        function confirmDelete() {
            const deleteButtons = document.querySelectorAll('.delete-button');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const url = button.getAttribute('data-url'); // Obtiene la URL desde el atributo data-url
                    const urlRedirect = button.getAttribute('data-redirect-url');

                    Swal.fire({
                        title: "Are you sure?",
                        text: "You won't be able to revert this!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, delete it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(url, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'Content-Type': 'application/json',
                                    },
                                })
                                .then(async response => {
                                    const contentType = response.headers.get('Content-Type');
                                    let data;

                                    if (contentType && contentType.includes('application/json')) {
                                        data = await response.json();
                                    } else {
                                        throw new Error("Invalid JSON response");
                                    }

                                    if (response.ok) {
                                        Swal.fire("Deleted!", data.message || "The record has been deleted.", "success")
                                            .then(() => {
                                                location.replace(urlRedirect);
                                            });
                                    } else {
                                        Swal.fire("Error!", data.message || "There was a problem deleting the record.", "error");
                                    }
                                })
                                .catch(error => {
                                    Swal.fire("Error!", "There was a problem with the request.<br><br>" + error.message, "error");
                                });
                        }
                    });
                });
            });
        }

        function searchDynamics() {
            const searchInput = document.querySelector('input[name="search"]');
            const tableRows = document.querySelectorAll('table tbody tr');

            searchInput.addEventListener('input', function() {
                const filter = searchInput.value.toLowerCase();

                tableRows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    let match = false;

                    cells.forEach(cell => {
                        if (cell.textContent.toLowerCase().includes(filter)) {
                            match = true;
                        }
                    });

                    if (match) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        };

        document.addEventListener('DOMContentLoaded', searchDynamics());
        document.addEventListener('DOMContentLoaded', confirmDelete);
    </script>