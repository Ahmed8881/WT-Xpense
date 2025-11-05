<?php
// Generate password hash for admin123
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: " . $password . "\n";
echo "Hash: " . $hash . "\n";
echo "\nCopy this SQL command:\n";
echo "INSERT INTO users (username, password) VALUES ('admin', '$hash');\n";
?>
