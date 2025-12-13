async function Validate(event){

    event.preventDefault();

    let f_name = document.getElementById("firstname-input").value.trim(); 
    let l_name = document.getElementById("lastname-input").value.trim();
    let email = document.getElementById("email-input").value.trim();
    let password = document.getElementById("password-input").value.trim();
    let c_password = document.getElementById("confirm-password-input").value.trim();

    let e_pattern = /^[A-Za-z][A-Za-z0-9._]*@ashesi\.edu\.gh$/;
    let s_pass = /([A-Za-z])\w*(?=\d{1,})/;

    if (!f_name || !l_name || !email || !password || !c_password) {
        Swal.fire({ title: "Missing Information", text: "Please fill in all fields.", icon: "warning" });
        return;
    }

    if(!e_pattern.test(email)){
        Swal.fire({ title: "Invalid Email", text: "Must be your institutional email.", icon: "error" });
        return;
    }

    if(!s_pass.test(password) || password.length < 6){
        Swal.fire({ title: "Weak Password", text:"Password must start with a letter, include a number and be at least 6 characters.", icon: "warning" });
        return;
    }

    if(password !== c_password){
        Swal.fire({ title: "Password Mismatch!", text:"Try again", icon: "error", timer:2000, timerProgressBar:true });
        return;

    } 

    // try {
        // sends data to SignUp.php file 
        let response = await fetch("../php/registeration_logic.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                f_name: f_name,
                l_name: l_name,
                email: email,
                password: password
            })
        });

        let result = await response.json();

        // handles success from server
        if (result.status === "success") {
            Swal.fire({
                title: "Success!",
                text: result.message,
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            });

            // Redirect after 2 seconds
            setTimeout(() => {
                window.location.href = "../Views/login.php";
            }, 2000);

        } else {
            Swal.fire("Error", result.message, "error");
        }

    // } catch (error) {
    //     Swal.fire("Error", "Server error occurred", "error");
    // }
}
