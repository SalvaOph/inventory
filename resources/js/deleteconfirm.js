function confirmDelete() {
    const deleteButtons = document.querySelectorAll(".delete-button");

    deleteButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const url = button.getAttribute("data-url"); // Obtiene la URL desde el atributo data-url
            const urlRedirect = button.getAttribute("data-redirect-url");

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(url, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                            "Content-Type": "application/json",
                        },
                    })
                        .then(async (response) => {
                            const contentType =
                                response.headers.get("Content-Type");
                            let data;

                            if (
                                contentType &&
                                contentType.includes("application/json")
                            ) {
                                data = await response.json();
                            } else {
                                throw new Error("Invalid JSON response");
                            }

                            if (response.ok) {
                                Swal.fire(
                                    "Deleted!",
                                    data.message ||
                                        "The record has been deleted.",
                                    "success"
                                ).then(() => {
                                    location.replace(urlRedirect);
                                });
                            } else {
                                Swal.fire(
                                    "Error!",
                                    data.message ||
                                        "There was a problem deleting the record.",
                                    "error"
                                );
                            }
                        })
                        .catch((error) => {
                            Swal.fire(
                                "Error!",
                                "There was a problem with the request.<br><br>" +
                                    error.message,
                                "error"
                            );
                        });
                }
            });
        });
    });
}

window.confirmDelete = confirmDelete;