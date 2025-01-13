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

    document.addEventListener('DOMContentLoaded', confirmDelete);
</script>