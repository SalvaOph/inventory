function searchDynamics() {
    const searchInput = document.querySelector('input[name="search"]');
    const tableRows = document.querySelectorAll("table tbody tr");

    searchInput.addEventListener("input", function () {
        const filter = searchInput.value.toLowerCase();

        tableRows.forEach((row) => {
            const cells = row.querySelectorAll("td");
            let match = false;

            cells.forEach((cell) => {
                if (cell.textContent.toLowerCase().includes(filter)) {
                    match = true;
                }
            });

            if (match) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });
}

window.searchDynamics = searchDynamics;