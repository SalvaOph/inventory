class DeleteButton extends HTMLButtonElement {
    constructor() {
        super();

        const icon = document.createElement("i");
        icon.setAttribute("class", "fa-solid fa-trash");
        icon.style.pointerEvents = "none";

        this.appendChild(icon);
        this.setAttribute("class", "btn btn-danger");
        this.appendChild(document.createTextNode(" Delete"));

        this.onclick = function () {
            this.closest("tr").remove();
        };
    }
}

class AmountInput extends HTMLInputElement {
    constructor() {
        super();

        this.setAttribute("type", "number");
        this.setAttribute("name", "products_quantity[]");
        this.setAttribute("min", "0");
        this.classList.add("form-control", "mt-2");
    }
}

class ProductTableRow extends HTMLTableRowElement {
    total = 0; // Valor por defecto
    amount = 1; // Valor por defecto

    constructor() {
        super();

        const nameColumn = document.createElement("td");
        const priceColumn = document.createElement("td");
        const amountColumn = document.createElement("td");
        const totalColumn = document.createElement("td");
        const actionsColumn = document.createElement("td");

        this.append(
            nameColumn,
            priceColumn,
            amountColumn,
            totalColumn,
            actionsColumn
        );
    }

    set product(product) {
        const amountInput = document.createElement("input", {
            is: "amount-input",
        });

        amountInput.value = this.amount;
        amountInput.addEventListener("change", (e) => {
            this.amount = e.target.value;
            this.total = this.amount * product.price;
            this.children[3].textContent = `$ ${this.total.toFixed(2)}`;
            this.onChange(this.total);
        });

        const deleteButton = document.createElement("button", {
            is: "delete-button",
        });

        deleteButton.addEventListener("click", this.onDelete);

        this.productData = product;
        this.total = product.price * this.amount;
        this.children[0].textContent = product.name;
        this.children[1].textContent = `$ ${product.price}`;
        this.children[2].appendChild(amountInput);
        this.children[3].textContent = `$ ${this.total.toFixed(2)}`;
        this.children[4].appendChild(deleteButton);

        this.onChange(this.total);
    }

    set quantity(amount) {
        this.amount = amount;
        this.total = this.productData.price * this.amount;

        this.children[2].children[0].value = amount;
        this.children[3].textContent = `$ ${this.total.toFixed(2)}`;

        this.onChange(this.total);
    }
}

// Define the new element
customElements.define("amount-input", AmountInput, { extends: "input" });
customElements.define("delete-button", DeleteButton, { extends: "button" });
customElements.define("product-table-row", ProductTableRow, { extends: "tr" });