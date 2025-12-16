function editProfile() {
    const formData = new FormData();
    formData.append('email', document.getElementById('email').value);
    formData.append('bio', document.getElementById('bio').value);
    formData.append('major', document.getElementById('major').value);
    formData.append('year', document.getElementById('year').value);

    const profileFile = document.getElementById('profile_picture').files[0];
    if (profileFile) {
        formData.append('profile_picture', profileFile);
    }

    fetch('../php/edit_profile.php', {
        method: 'POST',
        body: formData // no Content-Type header needed; browser sets multipart/form-data automatically
    })
    .then(res => res.json())
    .then(res => {
        Swal.fire({
            icon: res.success ? 'success' : 'error',
            title: res.message
        }).then(() => {
            if(res.success) location.reload(); // refresh to show new profile image
        });
    })
    .catch(err => console.error(err));
}
