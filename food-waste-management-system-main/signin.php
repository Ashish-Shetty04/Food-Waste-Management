<?php
session_start();
include 'connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$msg = 0;

function generateStrongPassword($length = 12) {
  $lowercase = 'abcdefghijklmnopqrstuvwxyz';
  $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $numbers = '0123456789';
  $specialChars = '!@#$%^&*()-_=+<>?';
  
  // Ensure the password contains at least one of each character type
  $password = '';
  $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
  $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
  $password .= $numbers[random_int(0, strlen($numbers) - 1)];
  $password .= $specialChars[random_int(0, strlen($specialChars) - 1)];

  // Fill the rest of the password length with random characters
  $allCharacters = $lowercase . $uppercase . $numbers . $specialChars;
  for ($i = 4; $i < $length; $i++) {
      $password .= $allCharacters[random_int(0, strlen($allCharacters) - 1)];
  }

  // Shuffle the password to ensure randomness
  return str_shuffle($password);
}

if (isset($_POST['sign'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use prepared statement for secure login
    $sql = "SELECT * FROM login WHERE email = ?";
    $stmt = mysqli_prepare($connection, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row['password'])) {
                // Set session variables
                $_SESSION['email'] = $email;
                $_SESSION['name'] = $row['name'];
                $_SESSION['id'] = $row['id'];
                $_SESSION['gender'] = $row['gender'];
                
                header("location:home.html");
                exit();
            } else {
                $msg = 1;
                echo "<div class='alert alert-danger'>Invalid password</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Account does not exist</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Database error. Please try again.</div>";
    }
}

// Handle error messages from redirects
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'not_logged_in':
            echo "<div class='alert alert-danger'>Please log in to access your profile.</div>";
            break;
        case 'user_not_found':
            echo "<div class='alert alert-danger'>Session expired. Please log in again.</div>";
            break;
        case 'database_error':
            echo "<div class='alert alert-danger'>A database error occurred. Please try again.</div>";
            break;
        default:
            echo "<div class='alert alert-danger'>An error occurred. Please try again.</div>";
    }
}

$generatedPassword = '';
if (isset($_POST['generate'])) {
    $generatedPassword = generateStrongPassword(12);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="loginstyle.css">
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />

    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">

</head>

<body>
    <style>
    .uil {

        top: 42%;
    }
    </style>
    <div class="container">
        <div class="regform">

            <form action=" " method="post">

                <p class="logo" style="">Food <b style="color:#06C167; ">Donate</b></p>
                <p id="heading" style="padding-left: 1px;"> Welcome back ! <img src="" alt=""> </p>

                <div class="input">
                    <input type="email" placeholder="Email address" name="email" value="" required />
                </div>
                <div class="password">
                    <input type="password" placeholder="Password" name="password" id="password" required />

                  
                    <i class="uil uil-eye-slash showHidePw"></i>
                  
                    <?php
                    if($msg==1){
                        echo ' <i class="bx bx-error-circle error-icon"></i>';
                        echo '<p class="error">Password not match.</p>';
                    }
                    ?>
                
                </div>


                <div class="btn">
                    <button type="submit" name="sign"> Sign in</button>
                </div>
                <div class="signin-up">
                    <p id="signin-up">Don't have an account? <a href="signup.php">Register</a></p>
                </div>
            </form>
        </div>


    </div>
    <script src="login.js"></script>
    <script src="admin/login.js"></script>
</body>

</html>
