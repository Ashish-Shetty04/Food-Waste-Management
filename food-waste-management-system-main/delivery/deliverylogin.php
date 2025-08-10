<?php
session_start();
include 'connect.php'; 
$msg=0;
if (isset($_POST['sign'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $sql = "SELECT * FROM delivery_persons WHERE email = ?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "s", $email);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    if (password_verify($password, $row['password'])) {
      $_SESSION['email'] = $email;
      $_SESSION['name'] = $row['name'];
      $_SESSION['Did']=$row['id'];
      $_SESSION['city']=$row['city'];
      header("location:delivery.php");
      exit;
    } else {
      $msg = 1;
      // echo '<style type="text/css">
      // {
      //     .password input{
                
      //         border:.5px solid red;
                
                
      //       }

      // }
      // </style>';
      // echo "<h1><center> Login Failed incorrect password</center></h1>";
    }
  } else {
    echo "<h1><center>Account does not exists </center></h1>";
  }




  // $query="select * from login where email='$email'and password='$password'";
  // $qname="select name from login where email='$email'and password='$password'";


  // if(mysqli_num_rows($query_run)==1)
  // {
  // //   $_SESSION['name']=$name;

  //   // echo "<h1><center> Login Sucessful  </center></h1>". $name['gender'] ;

  //   $_SESSION['email']=$email;
  //   $_SESSION['name']=$name['name'];
  //   $_SESSION['gender']=$name['gender'];
  //   header("location:home.html");

  // }
  // else{
  //   echo "<h1><center> Login Failed</center></h1>";
  // }
}
?>



<!DOCTYPE html>

<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Login Form</title>
    <link rel="stylesheet" href="deliverycss.css">
  </head>
  <body>
    <div class="center">
      <h1>Delivery Login</h1>
      <form method="post">
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
        <?php
        if($msg==1){
                        // echo ' <i class="bx bx-error-circle error-icon"></i>';
                        echo '<p class="error">Password not match.</p>';
                    }
                    ?>
                    <br>
       <div class="pass">Forgot Password?</div> 
        <input type="submit" value="Login" name="sign">
        <div class="signup_link">
          Not a member? <a href="deliverysignup.php">Signup</a>
        </div>
      </form>
    </div>
    

  </body>
</html>
