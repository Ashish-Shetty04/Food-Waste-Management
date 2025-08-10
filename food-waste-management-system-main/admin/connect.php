<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the main connection file
require_once(__DIR__ . '/../connection.php');

$msg = 0;
if (isset($_POST['sign'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sanitized_emailid = mysqli_real_escape_string($connection, $email);
    $sanitized_password = mysqli_real_escape_string($connection, $password);

    $sql = "SELECT * FROM admin WHERE email=?";
    $stmt = mysqli_prepare($connection, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $sanitized_emailid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $num = mysqli_num_rows($result);

        if ($num == 1) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($sanitized_password, $row['password'])) {
                $_SESSION['email'] = $email;
                $_SESSION['name'] = $row['name'];
                $_SESSION['location'] = $row['location'];
                $_SESSION['Aid'] = $row['Aid'];
                $_SESSION['admin'] = true;
                header("location:admin.php");
                exit();
            } else {
                $msg = 1;
            }
        } else {
            $msg = 1;
        }
        mysqli_stmt_close($stmt);
    } else {
        $msg = 1;
    }
}

if ($msg == 1) {
    echo '<script>alert("Invalid Credentials")</script>';
}
?>
