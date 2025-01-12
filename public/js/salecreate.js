export default function addProduct(products, purchase) {
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

        purchase[product.id] = { total: totalByProduct };

        const total = calculateTotal(purchase);
        totalInput.value = total;
        totalPurchaseElement.textContent = "$ " + total.toFixed(2);
    });

    purchase[product.id] = { total: totalByProduct };
    
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