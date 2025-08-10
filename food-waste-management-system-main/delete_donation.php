<?php
session_start();
include 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: signin.php");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: profile.php");
    exit();
}

$donation_id = $_GET['id'];
$user_email = $_SESSION['email'];

// Use prepared statement to delete donation (only if it belongs to the current user)
$query = "DELETE FROM food_donations WHERE Fid = ? AND email = ?";
$stmt = mysqli_prepare($connection, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "is", $donation_id, $user_email);
    
    if (mysqli_stmt_execute($stmt)) {
        // Deletion successful
        header("Location: profile.php?msg=deleted");
    } else {
        // Deletion failed
        header("Location: profile.php?msg=error");
    }
    mysqli_stmt_close($stmt);
} else {
    // Statement preparation failed
    header("Location: profile.php?msg=error");
}
exit();
?> 