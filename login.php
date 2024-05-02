<?php
require 'config.php';

// Check if user is already logged in
if (!empty($_SESSION["id"])) {
    header("Location: index.php");
    exit(); // Terminate script execution after redirection
}

// Initialize errors array
$errors = [];

// Check if form is submitted
if (isset($_POST["submit"])) {
    $usernameemail = $_POST["usernameemail"];
    $password = $_POST["password"];

    // Fetch user data by username or email
    $result = mysqli_query($conn, "SELECT * FROM users  WHERE username = '$usernameemail' OR email = '$usernameemail'");
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // Check if provided password matches the stored password
        if ($password === $row["password"]) { // Assuming password is stored as plain text
            $_SESSION["login"] = true;
            $_SESSION["id"] = $row["id"];
            header("Location: index.php");
            exit(); // Terminate script execution after successful login
        } else {
            $errors[] = "Wrong Password";
        }
    } else {
        $errors[] = "This account is not registered";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/logow.png" type="image/x-icon">
    <link rel="stylesheet" href="style/loginn.css">
    <title>Login</title>
</head>
<body>
    <div class="container">
        <div class="login-form">
            <form class="form" action="" method="post" autocomplete="off">
                <h1 class="form-title">Sign in to your account</h1>
                <?php if (!empty($errors)) : ?>
                    <div class="error-message">
                        <?php foreach ($errors as $error) : ?>
                            <p><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div class="input-container">
                    <label for="usernameemail">USERNAME</label>
                    <input type="text" name="usernameemail" id="usernameemail" placeholder="Enter username or email" required class="input-field">
                </div>
                <div class="input-container">
                    <label for="password">PASSWORD</label>
                    <input type="password" name="password" id="password" placeholder="Enter password" required class="input-field">
                    <label class="show-password-label" for="togglePassword">
                        <input type="checkbox" id="togglePassword">
                        Show Password
                    </label>
                </div>
                <button class="submit" type="submit" name="submit">Login</button>
                <h3 class="signup-link">No account? <a class="regis" href="registration.php">Signup</a></h3>
            </form>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const toggleCheckbox = document.getElementById('togglePassword');

        toggleCheckbox.addEventListener('change', function() {
            if (this.checked) {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        });
    </script>
</body>
</html>