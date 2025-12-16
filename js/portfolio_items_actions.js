document.addEventListener("DOMContentLoaded", () => {

    // =======================
    // DELETE ITEM
    // =======================
    document.querySelectorAll(".icon-btn.delete").forEach(btn => {
        btn.addEventListener("click", async (e) => {
            const card = e.target.closest(".card");
            const title = card.querySelector(".card-body h3").innerText;
            const itemId = card.dataset.id;
            if (!itemId) return;

            const result = await Swal.fire({
                title: `Delete "${title}"?`,
                text: "This action cannot be undone!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch("../php/delete_portfolio_item.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ item_id: itemId })
                    });
                    const res = await response.json();
                    if (res.success) {
                        card.remove();
                        Swal.fire("Deleted!", "Item has been deleted.", "success");
                    } else {
                        Swal.fire("Error!", res.message || "Failed to delete item.", "error");
                    }
                } catch (err) {
                    console.error(err);
                    Swal.fire("Error!", "Something went wrong.", "error");
                }
            }
        });
    });

    // =======================
    // EDIT ITEM ON CARD (SweetAlert modal + AJAX)
    // =======================
    document.querySelectorAll(".icon-btn.edit").forEach(btn => {
        btn.addEventListener("click", async (e) => {
            const card = e.target.closest(".card");
            const itemId = card.dataset.id;
            if (!itemId) return;

            const currentTitle = card.querySelector(".card-body h3").innerText;
            const currentDesc = card.querySelector(".card-desc").innerText;
            const currentLocation = card.querySelector(".card-meta")?.innerText || "";

            const { value: formValues } = await Swal.fire({
                title: 'Edit Portfolio Item',
                html:
                    `<input id="swal-title" class="swal2-input" placeholder="Title" value="${currentTitle}">` +
                    `<textarea id="swal-desc" class="swal2-textarea" placeholder="Description">${currentDesc}</textarea>` +
                    `<input id="swal-location" class="swal2-input" placeholder="Location" value="${currentLocation}">`,
                focusConfirm: false,
                showCancelButton: true,
                preConfirm: () => ({
                    title: document.getElementById('swal-title').value,
                    description: document.getElementById('swal-desc').value,
                    location: document.getElementById('swal-location').value
                })
            });

            if (!formValues) return;

            try {
                const response = await fetch("../php/edit_portfolio_item.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        item_id: itemId,
                        title: formValues.title,
                        description: formValues.description,
                        location: formValues.location
                    })
                });

                const res = await response.json();
                if (res.success) {
                    card.querySelector(".card-body h3").innerText = formValues.title;
                    card.querySelector(".card-desc").innerText = formValues.description;
                    card.querySelector(".card-meta").innerText = formValues.location;

                    Swal.fire("Updated!", "Item has been updated.", "success");
                } else {
                    Swal.fire("Error!", res.message || "Failed to update item.", "error");
                }
            } catch (err) {
                console.error(err);
                Swal.fire("Error!", "Something went wrong.", "error");
            }
        });
    });

    // =======================
    // EDIT FORM SUBMISSION (edit_portfolio_item.php page)
    // =======================
    const editForm = document.getElementById("editForm");
    if (editForm) {
        editForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const formData = new FormData(editForm);

            try {
                const response = await fetch("../php/edit_portfolio_item.php", {
                    method: "POST",
                    body: formData
                });

                const res = await response.json();
                if (res.success) {
                    Swal.fire("Updated!", "Portfolio item has been updated.", "success");
                } else {
                    Swal.fire("Error!", res.message || "Failed to update item.", "error");
                }
            } catch (err) {
                console.error(err);
                Swal.fire("Error!", "Something went wrong.", "error");
            }
        });
    }

});
