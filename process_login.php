<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    // ✅ Check admin table first
    $stmt = $conn->prepare("SELECT id, firstname, password FROM accnts WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $firstname, $hashed);
        $stmt->fetch();
        if (password_verify($password, $hashed)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['firstname'] = $firstname;
            $_SESSION['role'] = 'admin';

            header("Location: cap.php"); // from here → admin.php
            exit;
        } else {
            echo "<script>alert('❌ Incorrect password'); window.location.href='login.php';</script>";
            exit;
        }
    }

    // ✅ Otherwise check users
    $stmt = $conn->prepare("SELECT id, first_name, password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $first_name, $hashed);
        $stmt->fetch();
        if (password_verify($password, $hashed)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['role'] = 'user';

            header("Location: cap.php"); // from here → welcome.php
            exit;
        } else {
            echo "<script>alert('❌ Incorrect password'); window.location.href='login.php';</script>";
            exit;
        }
    }

    echo "<script>alert('❌ Email not found'); window.location.href='login.php';</script>";
}
?>
