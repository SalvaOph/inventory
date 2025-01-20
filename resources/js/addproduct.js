function addProduct(products, purchase, type = "purchase") {
    //Retrieve elements from the form
    const $productsSelect = document.getElementById("products_select");
    const $table = document.querySelector("#products_table tbody");
    const $hiddenInputs = document.getElementById("hidden_inputs");

    //Find selected product on products list
    const product = products.find((prod) => prod.id === +$productsSelect.value);

    if ($hiddenInputs.querySelector(`input[value="${product.id}"]`)) {
        alert("This product is already added.");
        return;
    }

    if (type === "sale") product.price = product.saleprice;

    //Create a new product row on the table
    const $productRow = window.createProductRow(product, purchase);

    //Append product row element to the table
    $table.appendChild($productRow);

    // Agregar el ID del producto al array hidden
    const $IdInput = document.createElement("input");
    $IdInput.setAttribute("type", "hidden");
    $IdInput.setAttribute("name", "products_id[]"); // IDs como array
    $IdInput.value = product.id;
    $hiddenInputs.appendChild($IdInput);
}

window.addProduct = addProduct;