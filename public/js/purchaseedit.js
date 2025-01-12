export default function editPurchase(products, purchase) {
    const modifiedPurchase = Object.values(purchase.products).reduce(
        (acc, curr) => {
            acc[curr.id] = curr.pivot.quantity * curr.price;
            return acc;
        },
        {}
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

        handleChange(
            { target: { value: amountInput.value } },
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
