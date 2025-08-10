 <?php
session_start();
include '../connection.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$acc=0;
$msg=0;
if(isset($_POST['Login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Debug information
    echo "<script>console.log('Login attempt with email: " . $email . "');</script>";
    
    $sql = "SELECT * FROM admin WHERE email = ?";
    $stmt = mysqli_prepare($connection, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row['password'])) {
                $_SESSION['email'] = $email;
                $_SESSION['name'] = $row['name'];
                $_SESSION['location'] = $row['location'];
                
                echo "<script>
                    console.log('Login successful! Redirecting...');
                    window.location.href = 'admin.php';
                </script>";
                exit();
            } else {
                echo "<script>
                    console.log('Invalid password');
                    alert('Invalid password. Please try again.');
                </script>";
            }
        } else {
            echo "<script>
                console.log('Account not found');
                alert('Account does not exist. Please register first.');
            </script>";
        }
    } else {
        echo "<script>
            console.log('Database error: " . mysqli_error($connection) . "');
            alert('Database error. Please try again.');
        </script>";
    }
}

if(isset($_POST['signup']))
{
    $username = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $location = $_POST['district'];

    // Debug registration data
    echo "<script>console.log('Registration attempt with email: " . $email . "');</script>";

    $pass = password_hash($password, PASSWORD_DEFAULT);
    
    // First check if email exists
    $check_sql = "SELECT * FROM admin WHERE email = '" . mysqli_real_escape_string($connection, $email) . "'";
    $check_result = mysqli_query($connection, $check_sql);
    
    if (!$check_result) {
        echo "<script>console.log('Database error: " . mysqli_error($connection) . "');</script>";
        die("Database error: " . mysqli_error($connection));
    }

    if(mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('Account already exists with this email!');</script>";
    } else {
        // Insert new admin
        $insert_sql = "INSERT INTO admin (name, email, password, location) VALUES (
            '" . mysqli_real_escape_string($connection, $username) . "',
            '" . mysqli_real_escape_string($connection, $email) . "',
            '" . mysqli_real_escape_string($connection, $pass) . "',
            '" . mysqli_real_escape_string($connection, $location) . "'
        )";
        
        echo "<script>console.log('Executing SQL: " . str_replace("'", "\\'", $insert_sql) . "');</script>";
        
        $insert_result = mysqli_query($connection, $insert_sql);
        
        if($insert_result) {
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $username;
            $_SESSION['location'] = $location;
            
            echo "<script>
                console.log('Registration successful');
                alert('Registration successful! Redirecting to admin page...');
                window.location.href = 'admin.php';
            </script>";
            exit();
        } else {
            echo "<script>
                console.log('Registration failed: " . mysqli_error($connection) . "');
                alert('Registration failed: " . mysqli_error($connection) . "');
            </script>";
        }
    }
}

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login & Registration</title>
    <!-- CSS -->
    <link rel="stylesheet" href="login.css">
    <!-- Iconscout CSS -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
</head>
<body>
    <div class="container">
        <div class="forms">
            <div class="form login">
                <span class="title">Login</span>
                <form action="" method="post">
                    <div class="input-field">
                        <input type="email" name="email" placeholder="Enter your email" required autocomplete="email">
                        <i class="uil uil-envelope icon"></i>
                    </div>
                    <div class="input-field">
                        <input type="password" name="password" id="password" placeholder="Enter your password" required autocomplete="current-password">
                        <i class="uil uil-lock icon"></i>
                        <i class="uil uil-eye-slash showHidePw"></i>
                    </div>
                    <div class="input-field button">
                        <button type="submit" name="Login">Login</button>
                    </div>
                </form>

                <div class="login-signup">
                    <span class="text">Not a member?
                        <a href="#" class="text signup-link">Signup Now</a>
                    </span>
                </div>
            </div>

            <!-- Registration Form -->
            <div class="form signup">
                <span class="title">Registration</span>
                <form action="" method="post">
                    <div class="input-field">
                        <input type="text" name="name" placeholder="Enter your name" required autocomplete="name">
                        <i class="uil uil-user"></i>
                    </div>
                    <div class="input-field">
                        <input type="email" name="email" placeholder="Enter your email" required autocomplete="email">
                        <i class="uil uil-envelope icon"></i>
                    </div>
                    <div class="input-field">
                        <select id="district" name="district" style="padding:10px; padding-left: 20px;" autocomplete="address-level1">
                          <option value="chennai">Chennai</option>
                          <option value="kancheepuram">Kancheepuram</option>
                          <option value="thiruvallur">Thiruvallur</option>
                          <option value="vellore">Vellore</option>
                          <option value="tiruvannamalai">Tiruvannamalai</option>
                          <option value="tiruvallur">Tiruvallur</option>
                          <option value="tiruppur">Tiruppur</option>
                          <option value="coimbatore">Coimbatore</option>
                          <option value="erode">Erode</option>
                          <option value="salem">Salem</option>
                          <option value="namakkal">Namakkal</option>
                          <option value="tiruchirappalli">Tiruchirappalli</option>
                          <option value="thanjavur">Thanjavur</option>
                          <option value="pudukkottai">Pudukkottai</option>
                          <option value="karur">Karur</option>
                          <option value="ariyalur">Ariyalur</option>
                          <option value="perambalur">Perambalur</option>
                          <option value="madurai" selected>Madurai</option>
                          <option value="virudhunagar">Virudhunagar</option>
                          <option value="dindigul">Dindigul</option>
                          <option value="ramanathapuram">Ramanathapuram</option>
                          <option value="sivaganga">Sivaganga</option>
                          <option value="thoothukkudi">Thoothukkudi</option>
                          <option value="tirunelveli">Tirunelveli</option>
                          <option value="tiruppur">Tiruppur</option>
                          <option value="tenkasi">Tenkasi</option>
                          <option value="kanniyakumari">Kanniyakumari</option>
                        </select> 
                        

                        <!-- <input type="password" class="password" placeholder="Create a password" required> -->
                        <i class="uil uil-map-marker icon"></i>
                    </div>
                    <div class="input-field">
                        <input type="password" name="password" id="password" placeholder="Create a password" required autocomplete="new-password">
                        <i class="uil uil-lock icon"></i>
                        <i class="uil uil-eye-slash showHidePw"></i>
                    </div>
                   
                    <div class="input-field button">
                       <button type="submit" name="signup">Signup</button>
                        <!-- <input type="button" value="signup" name="signup"> -->
                    </div>
                </form>

                <div class="login-signup">
                    <span class="text">Already a member?
                        <a href="#" class="text login-link">Login Now</a>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <script src="login.js"></script>
</body>
</html>