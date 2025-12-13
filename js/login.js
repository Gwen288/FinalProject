document.getElementById("login-button").addEventListener("click", async function(event) {
    event.preventDefault();

    let email = document.getElementById("email-input").value.trim();
    let password = document.getElementById("password-input").value.trim();
    let remember = document.getElementById("remember-me-checkbox").checked;


    if (!email || !password) {
        Swal.fire({ title: "Missing Information", text: "Please fill in all fields.", icon: "warning" });
        return;
    }

    //sends data to the login.php file
    try {
        let response = await fetch("../php/login_logic.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "same-origin",
            body: JSON.stringify({
                email: email,
                password: password,
                remember: remember
            })
        });

        let result = await response.json();
        console.log(result);

        if (result.success) {
            Swal.fire({
                title: "Login Successful!",
                text: `Welcome, ${result.username}`,
                icon: "success",
                timer: 1500,
                showConfirmButton: false
            });

            setTimeout(() => {
               window.location.href = result.redirect; 
            }, 1500);
        } else {
            Swal.fire({ title: "Login Failed", text: "Invalid credentials", icon: "error" });
        }

    } catch (error) {
        Swal.fire({ title: "Error", text: "Server error occurred", icon: "error" });
    }
});

