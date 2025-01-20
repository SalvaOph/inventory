function onLoad(products, data, type = "purchase") {
    //Retrieve elements from the form
    const $productsSelect = document.getElementById("products_select");
    const $table = document.querySelector("#products_table tbody");
    const $hiddenInputs = document.getElementById("hidden_inputs");
    const $addButton = document.getElementById("add_button");

    //Get the products id and total of the purchase
    const purchase = Object.values(data.products).reduce((acc, curr) => {
        acc[curr.id] = curr.pivot.quantity * curr.price;
        return acc;
    }, {});

    //Create product rows for each product on the purchase
    data.products.forEach((product) => {
        if (type === "sale") product.price = product.saleprice;

        //Create a new product row on the table
        const $productRow = window.createProductRow(product, purchase);
        $productRow.quantity = product.pivot.quantity;
        //Append product row element to the table
        $table.appendChild($productRow);
    });

    $addButton.onclick = function addProduct() {
        //Find selected product on products list
        const product = products.find(
            (prod) => prod.id === +$productsSelect.value
        );

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
        const productIdInput = document.createElement("input");
        productIdInput.setAttribute("type", "hidden");
        productIdInput.setAttribute("name", "products_id[]");
        productIdInput.value = product.id;
        $hiddenInputs.appendChild(productIdInput);
    };
}

window.onEditLoad = onLoad;