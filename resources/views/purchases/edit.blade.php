@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>{{ isset($purchase) ? 'Edit Purchase' : 'Create Purchase' }}</h2>
            <a class="btn btn-dark" href="{{ route('purchases.index') }}">
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

    <form action="{{ isset($purchase) ? route('purchases.update', $purchase->id) : route('purchases.store') }}" method="POST">
        @csrf
        @if(isset($purchase)) @method('PUT') @endif

        <!-- Proveedor -->
        <div class="form-group">
            <strong>Provider:</strong>
            <select name="provider_id" class="form-control">
                @foreach ($providers as $provider)
                <option value="{{ $provider->id }}" {{ isset($purchase) && $purchase->provider_id == $provider->id ? 'selected' : '' }}>
                    {{ $provider->name }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- Producto -->
        <div class="form-group">
            <strong>Product:</strong>
            <select id="products_select" class="form-control">
                @foreach ($all_products as $product)
                <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select><br>
            <button id="add_button" type="button" class="btn btn-primary">
                <i class="fa-solid fa-circle-plus"></i> Add</button>
        </div>

        <!-- Tabla de productos agregados -->
        <table id="products_table" class="table table-bordered mt-2">
            <thead>
                <th>Name</th>
                <th>Unit Price</th>
                <th>Amount</th>
                <th>Total</th>
                <th>Action</th>
            </thead>
            <tbody>
            </tbody>
        </table>

        <div class="form-group mt-2">
            <strong>Total</strong>
            <p id="total_p">$ {{ isset($purchase) ? number_format($purchase->total, 2) : '0' }}</p>
        </div>

        <div id="hidden_inputs" class="form-group mt-2">
            @if(isset($purchase))
            @foreach($purchase->products as $product)
            <input type="hidden" name="products_id[]" value="{{ $product->id }}">
            @endforeach
            @endif
        </div>

        <div class="form-group mt-2">
            <strong>Date:</strong>
            <input type="date" name="date" value="{{ isset($purchase) ? $purchase->date : '' }}" class="form-control">
        </div>

        <input class="form-control mt-2" type="hidden" name="total" id="total" value="{{ isset($purchase) ? $purchase->total : '0' }}">

        <!-- Alamcén -->
        <div class="form-group">
            <strong>Warehouse:</strong>
            <select name="warehouse_id" class="form-control">
                @foreach ($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" {{ (isset($purchase) && $purchase->warehouse_id == $warehouse->id) ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mt-2">
            <button type="submit" class="btn btn-success">
                <i class="fa-solid fa-floppy-disk"></i> {{ isset($purchase) ? 'Update' : 'Create' }}</button>
        </div>
    </form>
</div>

@endsection


<script type="module">
    const products = <?php echo $all_products; ?>;
    const purchase = <?php echo $purchase; ?>;

    function editPurchase(products, purchase) {
        const modifiedPurchase = Object.values(purchase.products).reduce(
            (acc, curr) => {
                acc[curr.id] = curr.pivot.quantity * curr.price;
                return acc;
            }, {}
        );

        const table = document.querySelector("#products_table tbody");
        const hiddenInputs = document.getElementById("hidden_inputs");

        purchase.products.forEach((prod) => {
            const row = document.createElement("tr");

            const name = document.createElement("td");
            name.textContent = prod.name;

            const unitPrice = document.createElement("td");
            unitPrice.textContent = "$ " + Number(prod.price).toFixed(2);

            const amount = document.createElement("td");
            const amountInput = document.createElement("input");
            amountInput.setAttribute("type", "number");
            amountInput.setAttribute("min", "0");
            amountInput.setAttribute("name", `products_quantity[]`);
            amountInput.classList.add("form-control", "mt-2");
            amountInput.value = prod.pivot.quantity;
            amount.appendChild(amountInput);

            const total = document.createElement("td");
            total.textContent =
                "$ " + (prod.pivot.quantity * prod.price).toFixed(2);

            amountInput.addEventListener("change", (e) =>
                handleChange(e, prod, total, modifiedPurchase)
            );

            const deleteProduct = document.createElement("td");

            const deleteProductBtn = document.createElement("button");
            deleteProductBtn.setAttribute("class", "btn btn-danger");

            const icon = document.createElement("i");
            icon.setAttribute("class", "fa-solid fa-trash");
            deleteProductBtn.appendChild(icon);
            deleteProductBtn.appendChild(document.createTextNode(" Delete"));

            deleteProductBtn.addEventListener("click", (e) => {
                e.preventDefault(); // Previene la recarga de la página

                // Encontrar y eliminar el input oculto correspondiente
                const hiddenInput = hiddenInputs.querySelector(
                    `input[value="${prod.id}"]`
                );
                if (hiddenInput) hiddenInputs.removeChild(hiddenInput);

                const row = e.target.closest("tr");
                if (row) row.remove();

                // Eliminar el producto del objeto 'modifiedPurchase'
                delete modifiedPurchase[prod.id];

                // Actualizar el total de la compra
                updateTotal(modifiedPurchase);
            });

            deleteProduct.appendChild(deleteProductBtn);

            row.appendChild(name);
            row.appendChild(unitPrice);
            row.appendChild(amount);
            row.appendChild(total);
            row.appendChild(deleteProduct);
            table.appendChild(row);
        });

        const addButton = document.getElementById("add_button");
        addButton.onclick = function addProduct() {
            const products_select = document.getElementById("products_select");
            const hiddenInputs = document.getElementById("hidden_inputs");

            const product = products.find(
                (prod) => prod.id === +products_select.value
            );
            const newProductRow = document.createElement("tr");

            const newProduct = document.createElement("td");
            newProduct.textContent = products_select.selectedOptions[0].textContent;

            const newProductPrice = document.createElement("td");
            newProductPrice.textContent = "$ " + Number(product.price).toFixed(2);

            const totalProduct = document.createElement("td");
            totalProduct.textContent = "$ " + Number(product.price).toFixed(2);

            const amountInput = document.createElement("input");
            amountInput.setAttribute("type", "number");
            amountInput.setAttribute("min", "0");
            amountInput.setAttribute("name", `products_quantity[]`);
            amountInput.value = 1;
            amountInput.classList.add("form-control", "mt-2");

            amountInput.addEventListener("change", (e) =>
                handleChange(e, product, totalProduct, modifiedPurchase)
            );

            const deleteProduct = document.createElement("td");
            const deleteProductBtn = document.createElement("button");
            deleteProductBtn.setAttribute("class", "btn btn-danger");

            const icon = document.createElement("i");
            icon.setAttribute("class", "fa-solid fa-trash");
            deleteProductBtn.appendChild(icon);
            deleteProductBtn.appendChild(document.createTextNode(" Delete"));

            deleteProductBtn.addEventListener("click", (e) => {
                e.preventDefault();

                // Encontrar y eliminar el input oculto correspondiente
                const hiddenInput = hiddenInputs.querySelector(
                    `input[value="${product.id}"]`
                );
                if (hiddenInput) hiddenInputs.removeChild(hiddenInput);

                const row = e.target.closest("tr");
                if (row) row.remove();

                delete modifiedPurchase[product.id];

                updateTotal(modifiedPurchase);
            });

            if (hiddenInputs.querySelector(`input[value="${product.id}"]`)) {
                alert("This product is already added.");
                return;
            }

            deleteProduct.appendChild(deleteProductBtn);

            newProductRow.appendChild(newProduct);
            newProductRow.appendChild(newProductPrice);
            newProductRow.appendChild(amountInput);
            newProductRow.appendChild(totalProduct);
            newProductRow.appendChild(deleteProduct);
            table.appendChild(newProductRow);

            const productIdInput = document.createElement("input");
            productIdInput.setAttribute("type", "hidden");
            productIdInput.setAttribute("name", "products_id[]");
            productIdInput.value = product.id;
            hiddenInputs.appendChild(productIdInput);

            handleChange({
                    target: {
                        value: amountInput.value
                    }
                },
                product,
                totalProduct,
                modifiedPurchase
            );
        };
    }

    function handleChange(e, prod, total, modifiedPurchase) {
        const quantity = parseFloat(e.target.value) || 0;
        const newTotal = quantity * prod.price;
        total.textContent = "$ " + newTotal.toFixed(2);
        modifiedPurchase[prod.id] = newTotal;
        updateTotal(modifiedPurchase);
    }

    function updateTotal(modifiedPurchase) {
        const newPurchaseTotal = Object.values(modifiedPurchase).reduce(
            (acc, curr) => acc + curr,
            0
        );
        const totalPurchaseElement = document.getElementById("total_p");
        if (totalPurchaseElement)
            totalPurchaseElement.textContent = "$ " + newPurchaseTotal.toFixed(2);
        const totalInput = document.getElementById("total");
        if (totalInput) totalInput.value = newPurchaseTotal;
    }

    window.addEventListener('load', editPurchase(products, purchase));
</script>