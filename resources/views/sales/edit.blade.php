@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2>{{ isset($sale) ? 'Edit Sale' : 'Create Sale' }}</h2>
            <a class="btn btn-dark" href="{{ route('sales.index') }}">
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

    <form action="{{ isset($sale) ? route('sales.update', $sale->id) : route('sales.store') }}" method="POST">
        @csrf
        @if(isset($sale)) @method('PUT') @endif

        <!-- Cliente -->
        <div class="form-group">
            <strong>Client:</strong>
            <select name="client_id" class="form-control">
                @foreach ($clients as $client)
                <option value="{{ $client->id }}" {{ isset($sale) && $sale->client_id == $client->id ? 'selected' : '' }}>
                    {{ $client->name }}
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
            <p id="total_p">$ {{ isset($sale) ? number_format($sale->total, 2) : '0' }}</p>
        </div>

        <div id="hidden_inputs" class="form-group mt-2">
            @if(isset($sale))
            @foreach($sale->products as $product)
            <input type="hidden" name="products_id[]" value="{{ $product->id }}">
            @endforeach
            @endif
        </div>

        <div class="form-group mt-2">
            <strong>Date:</strong>
            <input type="date" name="date" value="{{ isset($sale) ? $sale->date : '' }}" class="form-control">
        </div>

        <input class="form-control mt-2" type="hidden" name="total" id="total" value="{{ isset($sale) ? $sale->total : '0' }}">

        <!-- Almacén -->
        <div class="form-group">
            <strong>Warehouse:</strong>
            <select name="warehouse_id" class="form-control" required>
                @foreach ($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mt-2">
            <button type="submit" class="btn btn-success">
                <i class="fa-solid fa-floppy-disk"></i> {{ isset($sale) ? 'Update' : 'Create' }}</button>
        </div>
    </form>
</div>
@endsection

<script type="module">
    const products = <?php echo $all_products; ?>;
    const sale = <?php echo $sale; ?>;

    function onLoad(sale, products) {
        const modifiedPurchase = Object.values(sale.products).reduce(
            (acc, curr) => {
                acc[curr.id] = curr.pivot.quantity * curr.saleprice;
                return acc;
            }, {}
        );

        const table = document.querySelector("#products_table tbody");
        const hiddenInputs = document.getElementById("hidden_inputs");

        // Cargar productos existentes
        sale.products.forEach((prod) => {
            const row = document.createElement("tr");

            const name = document.createElement("td");
            name.textContent = prod.name;

            const unitPrice = document.createElement("td");
            unitPrice.textContent = "$ " + Number(prod.saleprice).toFixed(2);

            const amount = document.createElement("td");
            const amountInput = document.createElement("input");
            amountInput.setAttribute("type", "number");
            amountInput.setAttribute("min", "0");
            amountInput.setAttribute("name", `products_quantity[]`);
            amountInput.setAttribute("class", "form-control");
            amountInput.value = prod.pivot.quantity;
            amount.appendChild(amountInput);

            const total = document.createElement("td");
            total.textContent =
                "$ " + (prod.pivot.quantity * prod.saleprice).toFixed(2);

            // Botón de borrar
            const deleteBtn = document.createElement("td");
            const deleteProductBtn = document.createElement("button");
            deleteProductBtn.classList.add("btn", "btn-danger");

            const icon = document.createElement("i");
            icon.setAttribute("class", "fa-solid fa-trash");
            deleteProductBtn.appendChild(icon);
            deleteProductBtn.appendChild(document.createTextNode(" Delete"));

            deleteProductBtn.addEventListener("click", (e) => {
                e.preventDefault();

                // Encontrar y eliminar el input oculto correspondiente
                const hiddenInput = hiddenInputs.querySelector(
                    `input[value="${prod.id}"]`
                );
                if (hiddenInput) hiddenInputs.removeChild(hiddenInput);

                const row = e.target.closest("tr");
                if (row) row.remove();

                delete modifiedPurchase[prod.id];
                updateTotal(modifiedPurchase);
            });

            deleteBtn.appendChild(deleteProductBtn);

            amountInput.addEventListener("change", (e) => {
                handleChange(e, prod, total, modifiedPurchase);
            });

            row.appendChild(name);
            row.appendChild(unitPrice);
            row.appendChild(amount);
            row.appendChild(total);
            row.appendChild(deleteBtn);
            table.appendChild(row);
        });

        // Botón de agregar producto
        const addButton = document.getElementById("add_button");
        addButton.onclick = function addProduct() {
            const products_select = document.getElementById("products_select");
            const selectedProductId = +products_select.value;

            // Buscar el producto seleccionado en la lista de productos
            const product = products.find((prod) => prod.id === selectedProductId);

            if (!product) {
                alert("Producto no encontrado.");
                return;
            }

            const newProductRow = document.createElement("tr");

            const newProduct = document.createElement("td");
            const newProductPrice = document.createElement("td");
            const totalProduct = document.createElement("td");

            newProduct.textContent = product.name;
            newProductPrice.textContent =
                "$ " + Number(product.saleprice).toFixed(2);
            totalProduct.textContent = "$ " + Number(product.saleprice).toFixed(2);

            const amountInput = document.createElement("input");
            amountInput.setAttribute("type", "number");
            amountInput.setAttribute("min", "0");
            amountInput.setAttribute("name", `products_quantity[]`);
            amountInput.value = 1; // Valor por defecto

            amountInput.setAttribute("class", "form-control");

            amountInput.addEventListener("change", (e) =>
                handleChange(e, product, totalProduct, modifiedPurchase)
            );

            newProductRow.appendChild(newProduct);
            newProductRow.appendChild(newProductPrice);
            newProductRow.appendChild(amountInput);
            newProductRow.appendChild(totalProduct);

            // Agregar el botón de borrar
            const deleteBtn = document.createElement("td");
            const deleteProductBtn = document.createElement("button");
            deleteProductBtn.classList.add("btn", "btn-danger");

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

            deleteBtn.appendChild(deleteProductBtn);
            newProductRow.appendChild(deleteBtn);
            table.appendChild(newProductRow);

            // Agregar el ID del producto al array hidden
            const productIdInput = document.createElement("input");
            productIdInput.setAttribute("type", "hidden");
            productIdInput.setAttribute("name", "products_id[]");
            productIdInput.value = product.id;
            hiddenInputs.appendChild(productIdInput);

            // Actualizar el total de la compra
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

    function handleChange(e, product, total, modifiedPurchase) {
        const quantity = e.target.value ? parseFloat(e.target.value) : 0;
        const newTotal = quantity * product.saleprice;
        total.textContent = "$ " + newTotal.toFixed(2);
        modifiedPurchase[product.id] = newTotal;

        // Actualizar el total de la compra
        updateTotal(modifiedPurchase);
    }

    function updateTotal(modifiedPurchase) {
        const newPurchaseTotal = Object.values(modifiedPurchase).reduce(
            (acc, curr) => acc + curr,
            0
        );

        const totalPurchase = document.getElementById("total_p");
        totalPurchase.textContent = "$ " + newPurchaseTotal.toFixed(2);
        document.getElementById("total").value = newPurchaseTotal;
    }

    window.addEventListener('load', onLoad(sale, products));
</script>