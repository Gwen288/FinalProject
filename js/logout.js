document.querySelector('a[href="../php/logout.php"]').addEventListener('click', async function(e) {
    e.preventDefault(); // prevent default navigation

    // SweetAlert confirmation
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: "You will be logged out!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, logout',
        cancelButtonText: 'Cancel'
    });

    if (result.isConfirmed) {
        try {
            let response = await fetch("../php/logout.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                credentials: "same-origin",
                body: JSON.stringify({ action: "logout" })
            });

            let data = await response.json();

            if (data.success) {
                // SweetAlert success
                await Swal.fire({
                    title: 'Logged Out!',
                    text: 'You have been successfully logged out.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
                window.location.href = "../Views/login.php";
            } else {
                Swal.fire('Error', 'Logout failed. Please try again.', 'error');
            }
        } catch (error) {
            console.error("Error:", error);
            Swal.fire('Error', 'An error occurred during logout.', 'error');
        }
    }
});
