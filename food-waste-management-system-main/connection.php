<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to MySQL using the correct port
$connection = mysqli_connect("localhost:3306", "root", "");
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Select or create database
$create_db = mysqli_query($connection, "CREATE DATABASE IF NOT EXISTS demo");
if (!$create_db) {
    die("Could not create database: " . mysqli_error($connection));
}

$db = mysqli_select_db($connection, 'demo');
if (!$db) {
    die("Could not select database: " . mysqli_error($connection));
}

// Set charset to ensure proper encoding
if (!mysqli_set_charset($connection, "utf8mb4")) {
    die("Error setting charset: " . mysqli_error($connection));
}

// Check if admin table exists and has address column
$admin_table_exists = mysqli_query($connection, "SHOW TABLES LIKE 'admin'");
if (mysqli_num_rows($admin_table_exists) > 0) {
    $admin_columns = mysqli_query($connection, "SHOW COLUMNS FROM admin LIKE 'address'");
    if (mysqli_num_rows($admin_columns) == 0) {
        // Add address column if it doesn't exist
        mysqli_query($connection, "ALTER TABLE admin ADD COLUMN address TEXT NOT NULL AFTER location");
    }
}

// Create admin table if it doesn't exist
$create_admin_table = "CREATE TABLE IF NOT EXISTS admin (
    Aid INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    address TEXT NOT NULL
)";

if (!mysqli_query($connection, $create_admin_table)) {
    die("Error creating admin table: " . mysqli_error($connection));
}

// Check if login table exists and has gender column
$table_exists = mysqli_query($connection, "SHOW TABLES LIKE 'login'");
$needs_update = false;

if (mysqli_num_rows($table_exists) > 0) {
    $columns = mysqli_query($connection, "SHOW COLUMNS FROM login LIKE 'gender'");
    if (mysqli_num_rows($columns) == 0) {
        // Add gender column if it doesn't exist
        mysqli_query($connection, "ALTER TABLE login ADD COLUMN gender VARCHAR(10) NOT NULL");
    }
} else {
    // Create login table if it doesn't exist
    $create_login_table = "CREATE TABLE IF NOT EXISTS login (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        gender VARCHAR(10) NOT NULL
    )";

    if (!mysqli_query($connection, $create_login_table)) {
        die("Error creating login table: " . mysqli_error($connection));
    }
}

// Check if food_donation table exists and has required columns
$food_table_exists = mysqli_query($connection, "SHOW TABLES LIKE 'food_donation'");
if (mysqli_num_rows($food_table_exists) > 0) {
    // Check for usage_hours and usage_minutes columns
    $check_columns = mysqli_query($connection, "SHOW COLUMNS FROM food_donation LIKE 'usage_hours'");
    if (mysqli_num_rows($check_columns) == 0) {
        mysqli_query($connection, "ALTER TABLE food_donation ADD COLUMN usage_hours INT NOT NULL DEFAULT 0");
        mysqli_query($connection, "ALTER TABLE food_donation ADD COLUMN usage_minutes INT NOT NULL DEFAULT 0");
    }
}

// Create food_donation table if it doesn't exist
$create_donations_table = "CREATE TABLE IF NOT EXISTS food_donation (
    Fid INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    food VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    category VARCHAR(255),
    phoneno VARCHAR(20),
    usage_hours INT NOT NULL DEFAULT 0,
    usage_minutes INT NOT NULL DEFAULT 0,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    address TEXT,
    quantity VARCHAR(50),
    delivery_by INT NULL,
    status VARCHAR(20) DEFAULT 'pending'
)";

if (!mysqli_query($connection, $create_donations_table)) {
    die("Error creating food_donation table: " . mysqli_error($connection));
}

// Check if user_feedback table exists
$feedback_table_exists = mysqli_query($connection, "SHOW TABLES LIKE 'user_feedback'");
if (mysqli_num_rows($feedback_table_exists) == 0) {
    // Create user_feedback table if it doesn't exist
    $create_feedback_table = "CREATE TABLE user_feedback (
        feedback_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if (!mysqli_query($connection, $create_feedback_table)) {
        die("Error creating user_feedback table: " . mysqli_error($connection));
    }
}

// Function to delete expired donations
function deleteExpiredDonations($connection) {
    $query = "DELETE FROM food_donation 
              WHERE DATE_ADD(date, INTERVAL (usage_hours + (usage_minutes/60)) HOUR) < NOW()";
    
    if (!mysqli_query($connection, $query)) {
        error_log("Error deleting expired donations: " . mysqli_error($connection));
    }
}

// Call the function to clean up expired donations
deleteExpiredDonations($connection);
?>
