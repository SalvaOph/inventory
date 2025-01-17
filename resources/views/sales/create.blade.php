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

    <!-- Mostrar mensajes de éxito o error -->
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

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
                <option value="{{ $client->id }}" {{ isset($sale) && $sale->client_id == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Producto -->
        <div class="form-group">
            <strong>Product:</strong>
            <select id="products_select" class="form-control">
                @foreach ($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select><br>
            <button id="add_product_btn" type="button" class="btn btn-primary">
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
            <tbody></tbody>
        </table>

        <!-- Total de la compra -->
        <div class="form-group">
            <strong>Total</strong>
            <p id="total_p">$ 0.00</p>
        </div>

        <!-- Inputs ocultos para enviar los arrays -->
        <div id="hidden_inputs" class="form-group"></div>

        <!-- Fecha -->
        <div class="form-group mt-2">
            <strong>Date:</strong>
            <input type="date" name="date" value="{{ isset($sale) ? $sale->date : '' }}" class="form-control">
        </div>

        <!-- Total -->
        <input class="form-control mt-2" type="hidden" name="total" id="total">

        <!-- Almacén -->
        <div class="form-group">
            <strong>Warehouse:</strong>
            <select name="warehouse_id" class="form-control" required>
                @foreach ($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Botón de submit -->
        <div class="form-group mt-2">
            <button type="submit" class="btn btn-success">
                <i class="fa-solid fa-floppy-disk"></i> {{ isset($sale) ? 'Update' : 'Create' }}</button>
        </div>
    </form>
</div>
@endsection

<script type="module">
    const products = <?php echo $products; ?>;
    const purchase = {};

    function addProduct(products, purchase) {
        const products_select = document.getElementById("products_select");
        const table = document.querySelector("#products_table tbody");
        const hiddenInputs = document.getElementById("hidden_inputs");
        const totalInput = document.getElementById("total");
        const totalPurchaseElement = document.getElementById("total_p");

        const product = products.find((prod) => prod.id === +products_select.value);

        const newProductRow = document.createElement("tr");
        const newProduct = document.createElement("td");
        const newProductPrice = document.createElement("td");
        const totalProduct = document.createElement("td");
        const deleteProduct = document.createElement("td");

        const deleteProductBtn = document.createElement("button");
        deleteProductBtn.setAttribute("class", "btn btn-danger");

        const icon = document.createElement("i");
        icon.setAttribute("class", "fa-solid fa-trash");
        icon.style.pointerEvents = "none";
        deleteProductBtn.appendChild(icon);
        deleteProductBtn.appendChild(document.createTextNode(" Delete"));

        deleteProductBtn.addEventListener("click", (e) => {
            delete purchase[product.id];

            // Encontrar y eliminar el input oculto correspondiente
            const hiddenInput = hiddenInputs.querySelector(
                `input[value="${product.id}"]`
            );
            if (hiddenInput) hiddenInputs.removeChild(hiddenInput);

            const total = calculateTotal(purchase);
            totalInput.value = total;
            totalPurchaseElement.textContent = "$ " + total.toFixed(2);
            e.target.parentNode.parentNode.remove();
        });

        if (hiddenInputs.querySelector(`input[value="${product.id}"]`)) {
            alert("This product is already added.");
            return;
        }

        const amountInput = document.createElement("input");
        amountInput.setAttribute("type", "number");
        amountInput.setAttribute("name", `products_quantity[]`); // Cantidad como array
        amountInput.setAttribute("min", "0");
        amountInput.value = 1; // Valor por defecto
        amountInput.classList.add("form-control", "mt-2");

        const totalByProduct = amountInput.value * product.saleprice;
        totalProduct.textContent = "$ " + totalByProduct.toFixed(2);

        amountInput.addEventListener("change", (e) => {
            const totalByProduct = e.target.value * product.saleprice;
            totalProduct.textContent = "$ " + totalByProduct.toFixed(2);

            purchase[product.id] = {
                total: totalByProduct
            };

            const total = calculateTotal(purchase);
            totalInput.value = total;
            totalPurchaseElement.textContent = "$ " + total.toFixed(2);
        });

        purchase[product.id] = {
            total: totalByProduct
        };

        const total = calculateTotal(purchase);
        totalInput.value = total;
        totalPurchaseElement.textContent = "$ " + total.toFixed(2);

        newProduct.textContent = products_select.selectedOptions[0].textContent;
        newProductPrice.textContent = "$ " + Number(product.saleprice).toFixed(2);

        deleteProduct.appendChild(deleteProductBtn);

        newProductRow.appendChild(newProduct);
        newProductRow.appendChild(newProductPrice);
        newProductRow.appendChild(amountInput);
        newProductRow.appendChild(totalProduct);
        newProductRow.appendChild(deleteProduct);
        table.appendChild(newProductRow);

        // Agregar el ID del producto al array hidden
        const productIdInput = document.createElement("input");
        productIdInput.setAttribute("type", "hidden");
        productIdInput.setAttribute("name", "products_id[]"); // IDs como array
        productIdInput.value = product.id;
        hiddenInputs.appendChild(productIdInput);
    }

    function calculateTotal(purchase) {
        return Object.values(purchase).reduce(
            (acc, curr) => (acc += curr.total),
            0
        );
    }
    
    document.getElementById('add_product_btn').addEventListener('click', () => addProduct(products, purchase));
</script>