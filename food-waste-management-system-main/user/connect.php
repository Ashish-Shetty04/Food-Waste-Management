<?php
$conn = mysqli_connect("localhost", "root", "");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$db = mysqli_select_db($conn, 'demo');
if (!$db) {
    die("Database selection failed: " . mysqli_error($conn));
}
?>
