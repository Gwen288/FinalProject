<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="../css/authentication.css">
</head>
<body>
    <div class="card-container">
        <!-- Logo above the card -->
        <img src="../images/image.png" alt="Logo" class="card-logo">

        <div class="card">
            <h2 id="layer">Sign Up</h2>

            <form id="signup_form" class="myform">
                <!-- First Name -->
                <div class="input-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="#333" viewBox="0 0 24 24">
                        <path d="M12 12.713l11.985-9.713H0L12 12.713zm0 2.574L0 5.999v12.002h24V5.999L12 15.287z"/>
                    </svg>
                    <input type="text" name="first_name" placeholder="First Name" id='firstname-input' required>
                </div>

                <!-- Last Name -->
                <div class="input-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="#333" viewBox="0 0 24 24">
                        <path d="M12 12.713l11.985-9.713H0L12 12.713zm0 2.574L0 5.999v12.002h24V5.999L12 15.287z"/>
                    </svg>
                    <input type="text" name="last_name" placeholder="Last Name" id='lastname-input' required>
                </div>

                <!-- Email -->
                <div class="input-wrapper">
                    <span>@</span>
                    <input type="email" name="email" placeholder="Email" id='email-input' required>
                </div>

                <!-- Password -->
                <div class="input-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="#333" viewBox="0 0 24 24">
                        <path d="M12 17a2 2 0 100-4 2 2 0 000 4zm6-7h-1V7a5 5 0 00-10 0v3H6a2 2 0 00-2 2v7a2 2 0 002 2h12a2 2 0 002-2v-7a2 2 0 00-2-2zm-8-3a3 3 0 016 0v3H10V7z"/>
                    </svg>
                    <input type="password" name="password" placeholder="Password" id='password-input' required>
                </div>

                <!-- Confirm Password -->
                <div class="input-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="#333" viewBox="0 0 24 24">
                        <path d="M12 17a2 2 0 100-4 2 2 0 000 4zm6-7h-1V7a5 5 0 00-10 0v3H6a2 2 0 00-2 2v7a2 2 0 002 2h12a2 2 0 002-2v-7a2 2 0 00-2-2zm-8-3a3 3 0 016 0v3H10V7z"/>
                    </svg>
                    <input type="password" name="C_password" placeholder="Confirm Password" id='confirm-password-input' required>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="myButton" onclick="Validate(event)">Sign Up</button>
            </form>

            <!-- Login Link -->
            <p id="paragraph">Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script  src="../js/registeration.js" defer></script>
</body>
</html>
