@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <h2>Warehouses List</h2>
        <div class="col">
            <a class="btn btn-success" href="{{ route('warehouses.create') }}">
                <i class="fa-solid fa-circle-plus"></i> Create New Warehouse</a>
            <a href="{{ route('export.excel', ['entity' => 'warehouses']) }}" class="btn btn-success" title="Exportar Productos a Excel">
                <i class="fas fa-file-excel"></i>
            </a>
            <a href="{{ route('export.pdf', ['entity' => 'warehouses']) }}" class="btn btn-danger" title="Exportar Productos a PDF">
                <i class="fas fa-file-pdf"></i>
            </a>
        </div>
        <div class="col">
            <div>
                <form method="GET" action="{{ route('warehouses.index') }}" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search warehouses..." value="{{ request('search') }}">
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
                    <a href="{{ route('warehouses.index', ['sort_by' => 'name', 'sort_order' => 
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

                <!-- Dirección con ordenación -->
                <th>
                    <a href="{{ route('warehouses.index', ['sort_by' => 'address', 'sort_order' => 
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
                    <a href="{{ route('warehouses.index', ['sort_by' => 'email', 'sort_order' => 
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

        @foreach ($warehouses as $key => $warehouse)
        <tr>
            <td>{{ $warehouses->firstItem() + $key }}</td>
            <td>{{ $warehouse->name }}</td>
            <td>{{ $warehouse->address }}</td>
            <td>{{ $warehouse->email }}</td>
            <td>{{ $warehouse->telephone }}</td>
            <td>
                <div class="container text-center">
                    <a title="Show" class="btn btn-success" href="{{ route('warehouses.show', $warehouse->id) }}">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    <a title="Edit" class="btn btn-warning" href="{{ route('warehouses.edit', $warehouse->id) }}">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <form action="{{ route('warehouses.destroy', $warehouse->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button title="Delete" type="button" class="btn btn-danger delete-button"
                            data-url="{{ route('warehouses.destroy', $warehouse->id) }}" data-redirect-url="{{ route('warehouses.index') }}">
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
            {{ $warehouses->links() }}
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