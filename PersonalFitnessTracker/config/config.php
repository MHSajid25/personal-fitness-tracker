<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration
require_once __DIR__ . '/database.php';

// Base URL configuration
define('BASE_URL', 'http://localhost/project1/');

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper function to get current user type
function getUserType() {
    return $_SESSION['user_type'] ?? null;
}

// Helper function to check if user is admin
function isAdmin() {
    return getUserType() === 'admin';
}

// Helper function to check if user is trainer
function isTrainer() {
    return getUserType() === 'trainer';
}

// Helper function to check if user is regular user
function isUser() {
    return getUserType() === 'user';
}

// Redirect function
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Require login
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('index.php');
    }
}

// Require specific user type
function requireUserType($type) {
    requireLogin();
    if (getUserType() !== $type) {
        redirect('index.php');
    }
}
?>

