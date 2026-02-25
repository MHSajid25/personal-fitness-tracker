<?php
/**
 * Password Hash Generator
 * Use this script to generate password hashes for the database
 * 
 * Usage: php helpers/generate-password.php your_password
 */

if ($argc < 2) {
    echo "Usage: php generate-password.php <password>\n";
    echo "Example: php generate-password.php admin123\n";
    exit(1);
}

$password = $argv[1];
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: $password\n";
echo "Hash: $hash\n";
echo "\nSQL Update Query:\n";
echo "UPDATE users SET password = '$hash' WHERE username = 'your_username';\n";
?>

