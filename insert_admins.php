<?php
require 'config.php';

$admins = [
    ['C Admin', 'c@email.com', 'admin123'],
    ['B Admin', 'b@email.com', 'admin123'],
    ['O Admin', 'o@email.com', 'admin123']
];

foreach ($admins as $admin) {
    $firstname = $admin[0];
    $email     = $admin[1];
    $password  = password_hash($admin[2], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO accnts (firstname, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $firstname, $email, $password);
    $stmt->execute();
}

echo "✅ Admins inserted with hashed passwords.";
