document.addEventListener("DOMContentLoaded", function () {
  // ======= DELETE PORTFOLIO =======
  const deleteBtn = document.getElementById("deletePortfolioBtn");
  if (deleteBtn) {
    deleteBtn.addEventListener("click", function () {
      Swal.fire({
        title: 'Are you sure?',
        text: "This will permanently delete your portfolio!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        cancelButtonColor: '#dc2626',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          const portfolioId = deleteBtn.dataset.portfolioId;
          if (!portfolioId) return;

          fetch('../php/delete_portfolio.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `portfolio_id=${encodeURIComponent(portfolioId)}`
          })
          .then(res => res.json())
          .then(response => {
            if (response.success) {
              Swal.fire('Deleted!', 'Your portfolio has been deleted.', 'success')
                .then(() => { window.location.href = "homepage.php"; });
            } else {
              Swal.fire('Error', response.message || 'Something went wrong.', 'error');
            }
          })
          .catch(() => {
            Swal.fire('Error', 'Server error. Try again later.', 'error');
          });
        }
      });
    });
  }

  // ======= EDIT PORTFOLIO =======
  const editForm = document.getElementById("editPortfolioForm");
  if (editForm) {
    editForm.addEventListener("submit", function (e) {
      e.preventDefault(); // prevent default form submission

      const formData = new FormData(editForm);

      fetch('../php/edit_portfolio.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(response => {
        if (response.success) {
          Swal.fire('Updated!', 'Your portfolio has been updated.', 'success')
            .then(() => { window.location.href = "User_Portfolio_page.php"; });
        } else {
          Swal.fire('Error', response.message || 'Failed to update portfolio.', 'error');
        }
      })
      .catch(() => {
        Swal.fire('Error', 'Server error. Try again later.', 'error');
      });
    });
  }
});
