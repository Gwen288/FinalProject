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
                     <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="#333">
                     <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Z"/>
                     </svg>
                    <input type="text" name="first_name" placeholder="First Name" id="firstname-input" required>
                </div>


                <!-- Last Name -->
                <div class="input-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="#333">
                    <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Z"/>
                     </svg>
                    <input type="text" name="last_name" placeholder="Last Name" id="lastname-input" required>
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
                <button type="submit" id="register-button" onclick="Validate(event)">Sign Up</button>
            </form>

            <!-- Login Link -->
            <p id="paragraph">Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script  src="../js/registeration.js" defer></script>
</body>
</html>
