<?php
session_start();
require 'config.php';

// Initialize an empty array for errors
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name       = trim($_POST['first_name']);
    $last_name        = trim($_POST['last_name']);
    $email            = trim($_POST['email']);
    $password         = trim($_POST['password']);
    $confirmPassword  = trim($_POST['confirm_password']);

    // ✅ Password match check
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    // ✅ Password complexity: exactly 8 chars, at least 1 uppercase + 1 special char
    if (!preg_match('/^(?=.*[A-Z])(?=.*[\W_]).{8}$/', $password)) {
        $errors[] = "Password must be exactly 8 characters, include at least one uppercase letter and one special symbol.";
    }

    // ✅ If no validation errors, proceed
    if (empty($errors)) {
        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $errors[] = "✘ Email already registered! Please login.";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashedPassword);

            if ($stmt->execute()) {
                // Save session data
                $_SESSION['user_id']    = $stmt->insert_id;
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name']  = $last_name;
                $_SESSION['email']      = $email;
                $_SESSION['role']       = 'user';  // ✅ important

                // Redirect to cap.php
                header("Location: cap.php");
                exit;
            } else {
                $errors[] = "✘ Registration failed. Please try again.";
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>

<!-- Registration Form -->
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body {
            background: url("download (2) (3).jpg") no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .error-container {
            background: rgba(211, 207, 207, 0.85);
            color: #101010ff;
            padding: 25px;
            width: 400px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.4);
            text-align: center;
            animation: fadeIn 0.6s ease-in-out;
        }
        .error-container h3 {
            margin: 0 0 15px;
            font-size: 20px;
        }
        .error-container ul {
            margin: 0 0 15px;
            padding-left: 20px;
            text-align: left;
        }
        .error-container button {
            background: rgba(211, 207, 207, 0.85);
            color: #0000005e;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }
        .error-container button:hover {
            background: #a09821ff;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to   { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>

    <!-- Show error messages if any -->
    <?php if (!empty($errors)): ?>
        <div class="error-container">
            <h3>⚠️ Oops! Something went wrong:</h3>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
            <button onclick="window.location.href='login.php'">Close</button>
        </div>
    <?php endif; ?>

</body>
</html>