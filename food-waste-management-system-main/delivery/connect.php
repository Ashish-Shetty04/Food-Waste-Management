<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Try default MySQL port
$conn = mysqli_connect("localhost", "root", "");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Select or create database
$create_db = mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS demo");
if (!$create_db) {
    die("Could not create database: " . mysqli_error($conn));
}

$db = mysqli_select_db($conn, 'demo');
if (!$db) {
    die("Could not select database: " . mysqli_error($conn));
}

// Create delivery_persons table if it doesn't exist
$create_delivery_table = "CREATE TABLE IF NOT EXISTS delivery_persons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    city VARCHAR(255),
    status ENUM('available', 'busy') DEFAULT 'available'
)";

if (!mysqli_query($conn, $create_delivery_table)) {
    die("Error creating delivery_persons table: " . mysqli_error($conn));
}

// For backward compatibility
$connection = $conn;

// Note: This file should only handle DB connection and schema setup for the delivery module.
// Any login/signup handling must be implemented in the respective pages to avoid side effects.
?>
