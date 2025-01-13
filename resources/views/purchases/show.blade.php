@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>Purchase Details</h2>
            <a class="btn btn-dark" href="{{ route('purchases.index') }}">
                <i class="fa-solid fa-backward-step"></i> Back</a>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3>{{ $purchase->provider->name }}</h3>
        </div>
        <div class="card-body">
            <p><strong>Date: </strong>{{ \Carbon\Carbon::parse($purchase->date)->translatedFormat('D, d M Y') }}</p>
            <p><strong>Total: </strong>$ {{ number_format($purchase->total, 2) }}</p>
            <p><strong>Warehouse: </strong>{{ isset($warehouse) ? $warehouse->name : "There is no warehouse assigned" }}</p>

            <h4>Products</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $i = 0;
                    @endphp
                    @foreach ($products as $product)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $product->name }}</td>
                        <td>$ {{ number_format((float) $product->price, 2) }}</td>
                        <td>{{ $product->pivot->quantity }}</td>
                        <td>$ {{ number_format((float) $product->price * (float) $product->pivot->quantity, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn btn-warning">
                <i class="fa-solid fa-pen-to-square"></i> Edit</a>
            <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" style="display: inline-block;">
                @csrf
                @method('DELETE')
                <button title="Delete" type="button" class="btn btn-danger delete-button"
                    data-url="{{ route('purchases.destroy', $purchase->id) }}" data-redirect-url="{{ route('purchases.index') }}"> Delete
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