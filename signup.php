<?php
session_start(); 
include 'db.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST["first_name"]);
    $lastName = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    // Added password complexity validation
    if (!preg_match('/^(?=.*[A-Z])(?=.*[\W_]).{8,}$/', $password)) {
        $errors[] = "Password must be at least 8 characters long, include at least one uppercase letter, and one special symbol.";
    }

    $role = ($email === "janecuritao25@gmail.com") ? "admin" : "user";

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO user (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $firstName, $lastName, $email, $hashedPassword, $role);

        if ($stmt->execute()) {
            $userId = $stmt->insert_id;
            
            $_SESSION["user_id"] = $userId;
            $_SESSION["user_email"] = $email;
            $_SESSION["user_first_name"] = $firstName;
            $_SESSION["user_last_name"] = $lastName;
            $_SESSION["user_role"] = $role;

            header("Location: login.php");
            exit();
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
    }
}
?>
<link rel="stylesheet" href="style.css">

<div class="container">
    <form method="post" id="signupForm">
        <h2>Sign Up</h2>

        <?php if (!empty($errors)): ?>
            <ul style="color:red;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        First Name:
        <input type="text" name="first_name" required>

        Last Name:
        <input type="text" name="last_name" required>

        Email:
        <input type="text" name="email" required>

        Password:
        <div style="position: relative;">
            <input type="password" name="password" id="password" required style="padding-right: 35px; width: 100%; box-sizing: border-box;">
            <span onclick="togglePassword('password', this)" 
                  style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #666;">
                <!-- Eye open SVG -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" 
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" 
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 
                          0 8.268 2.943 9.542 7-1.274 4.057-5.065 
                          7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </span>
        </div>

        Confirm Password:
        <div style="position: relative;">
            <input type="password" name="confirm_password" id="confirm_password" required style="padding-right: 35px; width: 100%; box-sizing: border-box;">
            <span onclick="togglePassword('confirm_password', this)" 
                  style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #666;">
                <!-- Eye open SVG -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" width="24" height="24">
                    <path stroke-linecap="round" stroke-linejoin="round" 
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" 
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 
                          0 8.268 2.943 9.542 7-1.274 4.057-5.065 
                          7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </span>
        </div>

        <button type="submit">Register</button>
    </form>
    <a href="login.php">Already have an account? Log in</a>
</div>

<script>
function togglePassword(fieldId, iconSpan) {
    const input = document.getElementById(fieldId);
    const svgOpen = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" 
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" 
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 
                              0 8.268 2.943 9.542 7-1.274 4.057-5.065 
                              7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>`;

    const svgClosed = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                           stroke="currentColor" stroke-width="2" width="24" height="24">
                        <path stroke-linecap="round" stroke-linejoin="round" 
                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 
                              0-8.268-2.943-9.542-7a10.05 10.05 0 
                              012.327-4.424m3.653-2.647A9.956 9.956 0 
                              0112 5c4.477 0 8.268 2.943 9.542 
                              7a9.96 9.96 0 01-1.68 3.19M15 
                              12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                    </svg>`;

    if (input.type === "password") {
        input.type = "text";
        iconSpan.innerHTML = svgClosed;
    } else {
        input.type = "password";
        iconSpan.innerHTML = svgOpen;
    }
}

// Added client-side validation for password complexity
document.getElementById('signupForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordPattern = /^(?=.*[A-Z])(?=.*[\W_]).{8,}$/;

    if (!passwordPattern.test(password)) {
        e.preventDefault();
        alert("Password must be at least 8 characters long, include at least one uppercase letter, and one special symbol.");
    }
});
</script>