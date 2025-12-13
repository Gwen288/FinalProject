<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="../css/authentication.css">
</head>
<body>
    <div class="card-container">
        <!-- Logo above the card -->
        <img src="../images/logo.png" alt="Logo" class="card-logo">

        <div class="card">
            <h2 id="layer">Login</h2>

            <form id="l_form" class="myform">

                <!-- Email input with icon -->
                <div class="input-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="#333" viewBox="0 0 24 24">
                        <path d="M12 12.713l11.985-9.713H0L12 12.713zm0 2.574L0 5.999v12.002h24V5.999L12 15.287z"/>
                    </svg>
                    <input type="email" name="email" placeholder="Email" id='email-input' required>
                </div>

                <!-- Password input with icon -->
                <div class="input-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="#333" viewBox="0 0 24 24">
                        <path d="M12 17a2 2 0 100-4 2 2 0 000 4zm6-7h-1V7a5 5 0 00-10 0v3H6a2 2 0 00-2 2v7a2 2 0 002 2h12a2 2 0 002-2v-7a2 2 0 00-2-2zm-8-3a3 3 0 016 0v3H10V7z"/>
                    </svg>
                    <input type="password" name="password" placeholder="Password" id='password-input' required>
                </div>

                <!-- Remember me checkbox -->
                <div class="Check">
                    <input type="checkbox" id="remember-me-checkbox">
                    <label for="remember-me-checkbox">Remember me</label>
                </div>

                <!-- Login button -->
                <button type="submit" id="login-button">Login</button>
            </form>

            <!-- Signup link -->
            <p id="paragraph">Don't have an account? <a href="register.php">Signup</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/login.js"></script>
</body>
</html>
