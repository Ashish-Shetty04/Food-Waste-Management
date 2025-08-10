<?php
session_start();
include 'connect.php';

$msg = 0;
if (isset($_POST['sign'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $location = $_POST['district'];

    // Hash the password
    $pass = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists using prepared statement
    $check_sql = "SELECT * FROM delivery_persons WHERE email = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "s", $email);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($result) > 0) {
        echo "<h1><center>Account already exists</center></h1>";
    } else {
        // Insert new delivery person using prepared statement
        $insert_sql = "INSERT INTO delivery_persons (name, email, password, city) VALUES (?, ?, ?, ?)";
        $insert_stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($insert_stmt, "ssss", $username, $email, $pass, $location);
        
        if (mysqli_stmt_execute($insert_stmt)) {
            echo "<h1><center>Registration successful!</center></h1>";
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'deliverylogin.php';
                }, 2000);
            </script>";
        } else {
            echo "<h1><center>Registration failed. Please try again.</center></h1>";
            echo "<script>console.log('Error: " . mysqli_error($conn) . "');</script>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">


  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Animated Login Form | CodingNepal</title>
    <link rel="stylesheet" href="deliverycss.css">
  </head>
  <body>
    <div class="center">
      <h1>Register</h1>
      <form method="post" action="deliverysignup.php">
        <div class="txt_field">
          <input type="text" name="username" required/>
          <span></span>
          <label>Username</label>
        </div>
        <div class="txt_field">
            <input type="email" name="email" required/>
            <span></span>
            <label>Email</label>
          </div>
          <div class="txt_field">
          <input type="password" name="password" required/>
          <span></span>
          <label>Password</label>
        </div>
        <div class="txt_field">
          <input type="text" name="district" required/>
          <span></span>
          <label>City / District</label>
        </div>
        <br>
        
        <input type="submit" name="sign" value="Register">
        <div class="signup_link">
          Already a member? <a href="deliverylogin.php">Sign in</a>
        </div>
      </form>
    </div>

  </body>
</html>
