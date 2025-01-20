function updateTotal(purchase, input, text) {
    const total = Object.values(purchase).reduce(
        (acc, curr) => (acc += curr.total),
        0
    );

    input.value = total;
    text.textContent = `$ ${total.toFixed(2)}`;
}

function createProductRow(product, purchase) {
    //Retrieve elements from the form
    const $totalInput = document.getElementById("total");
    const $totalToShow = document.getElementById("total_p");
    const $hiddenInputs = document.getElementById("hidden_inputs");

    //Create a new product row element
    const $productRow = document.createElement("tr", {
        is: "product-table-row",
    });

    //Create an event that will fire when the amount input is changed
    $productRow.onChange = (totalByProduct) => {
        purchase[product.id] = { total: totalByProduct };
        updateTotal(purchase, $totalInput, $totalToShow);
    };

    //Create an event that will fire when the delete button is clicked
    $productRow.onDelete = () => {
        delete purchase[product.id];

        // Encontrar y eliminar el input oculto correspondiente
        const $hiddenInput = $hiddenInputs.querySelector(
            `input[value="${product.id}"]`
        );

        if ($hiddenInput) $hiddenInputs.removeChild($hiddenInput);

        updateTotal(purchase, $totalInput, $totalToShow);
    };

    //Add the product data to the product row element
    $productRow.product = product;

    return $productRow;
}

window.createProductRow = createProductRow;