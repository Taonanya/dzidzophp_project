<?php
session_start();
require_once 'db.php';

$response = ['success' => false, 'message' => ''];

$fullName = $_POST['full_name'] ?? '';
$email = $_POST['email'] ?? '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Validate inputs
if (empty($fullName) || empty($email) || empty($username) || empty($password)) {
    $response['message'] = 'All fields are required';
} else {
    // Check if username already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->fetch()) {
        $response['message'] = 'Username already taken';
    } else {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $db->prepare("INSERT INTO users (full_name, email, username, password) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$fullName, $email, $username, $hashedPassword])) {
            $response['success'] = true;
            $response['message'] = 'Registration successful';
        } else {
            $response['message'] = 'Registration failed';
        }
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>