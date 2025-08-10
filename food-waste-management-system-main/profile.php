<?php
session_start();
include("connection.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in with all required session variables
if (!isset($_SESSION['email']) || empty($_SESSION['email']) || !isset($_SESSION['name']) || empty($_SESSION['name'])) {
    header("location: signin.php?error=not_logged_in");
    exit();
}

// Fetch user details from database
$email = $_SESSION['email'];
$query = "SELECT l.*, COUNT(fd.Fid) as donation_count 
          FROM login l 
          LEFT JOIN food_donation fd ON l.email = fd.email 
          WHERE l.email = ? 
          GROUP BY l.id";

$stmt = mysqli_prepare($connection, $query);
if (!$stmt) {
    error_log("Error preparing statement: " . mysqli_error($connection));
    header("location: signin.php?error=database_error");
    exit();
}

mysqli_stmt_bind_param($stmt, "s", $email);
if (!mysqli_stmt_execute($stmt)) {
    error_log("Error executing statement: " . mysqli_error($connection));
    header("location: signin.php?error=database_error");
    exit();
}

$result = mysqli_stmt_get_result($stmt);
if (!$result) {
    error_log("Error getting result: " . mysqli_error($connection));
    header("location: signin.php?error=database_error");
    exit();
}

$user = mysqli_fetch_assoc($result);
if (!$user) {
    // Log the error and session data for debugging
    error_log("User not found in database. Email: " . $email);
    error_log("Session data: " . print_r($_SESSION, true));
    
    // Clear session and redirect
    session_unset();
    session_destroy();
    header("location: signin.php?error=user_not_found");
    exit();
}

// Update session with latest user data
$_SESSION['name'] = $user['name'];
$_SESSION['gender'] = $user['gender'] ?? 'Not specified';
$_SESSION['id'] = $user['id'];

// Close the statement
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Food Donation</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .info {
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .info p {
            margin: 10px 0;
            font-size: 16px;
            color: #333;
        }
        .info strong {
            color: #06C167;
            margin-right: 10px;
        }
        .actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        .btn-logout, .btn-edit {
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn-logout {
            background-color: #ff4444;
            color: white;
        }
        .btn-edit {
            background-color: #06C167;
            color: white;
        }
        .btn-logout:hover {
            background-color: #cc0000;
        }
        .btn-edit:hover {
            background-color: #05a057;
        }
        .delete-btn {
            background-color: #ff4444;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .delete-btn:hover {
            background-color: #cc0000;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .time-ago {
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <header>
    <div class="logo">Food <b style="color: #06C167;">Donate</b></div>
        <div class="hamburger">
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
        </div>
        <nav class="nav-bar">
            <ul>
                <li><a href="home.html">Home</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="profile.php" class="active">Profile</a></li>
            </ul>
        </nav>
    </header>
    <script>
        hamburger=document.querySelector(".hamburger");
        hamburger.onclick =function(){
            navBar=document.querySelector(".nav-bar");
            navBar.classList.toggle("active");
        }
    </script>

    <div class="profile">
        <div class="profilebox">
            <?php
            if (isset($_GET['msg'])) {
                if ($_GET['msg'] === 'deleted') {
                    echo '<div class="alert alert-success">Donation deleted successfully!</div>';
                } else if ($_GET['msg'] === 'error') {
                    echo '<div class="alert alert-error">Error deleting donation. Please try again.</div>';
                }
            }
            ?>
            <h2 class="headingline">Profile Information</h2>
            <div class="info">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name'] ?? 'Not specified'); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? 'Not specified'); ?></p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender'] ?? 'Not specified'); ?></p>
                <div class="actions">
                    <a href="edit_profile.php" class="btn-edit">Edit Profile</a>
                    <a href="logout.php" class="btn-logout">Logout</a>
                </div>
            </div>
            <br>
            <br>

            <hr>
            <br>
            <p class="heading">Your donations</p>
            <div class="table-container">
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Food</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Usage Time (hrs)</th>
                                <th>Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $email=$_SESSION['email'];
                            
                            $query = "SELECT 
                                    Fid,
                                    food,
                                    type,
                                    category,
                                    usage_hours,
                                    usage_minutes,
                                    DATE_FORMAT(COALESCE(date, NOW()), '%h:%i:%s %p') as exact_time
                                    FROM food_donation 
                                    WHERE email=? 
                                    ORDER BY date DESC";
                            
                            $stmt = mysqli_prepare($connection, $query);
                            if($stmt) {
                                mysqli_stmt_bind_param($stmt, "s", $email);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                
                                if(mysqli_num_rows($result) > 0) {
                                    while($row = mysqli_fetch_assoc($result)) {
                                        // Set default values for null fields
                                        $food = $row['food'] !== null ? $row['food'] : '';
                                        $type = $row['type'] !== null ? $row['type'] : '';
                                        $category = $row['category'] !== null ? $row['category'] : '';
                                        $hours = intval($row['usage_hours']);
                                        $minutes = intval($row['usage_minutes']);
                                        
                                        // Format the time display
                                        if ($hours > 0 && $minutes > 0) {
                                            $usage_time = $hours . " hrs " . $minutes . " min";
                                        } elseif ($hours > 0) {
                                            $usage_time = $hours . " hrs";
                                        } elseif ($minutes > 0) {
                                            $usage_time = $minutes . " min";
                                        } else {
                                            $usage_time = "0 minutes";
                                        }
                                        
                                        $exact_time = $row['exact_time'] !== null ? $row['exact_time'] : 'Time not recorded';
                                        
                                        echo "<tr>";
                                        echo "<td>".htmlspecialchars($food)."</td>";
                                        echo "<td>".htmlspecialchars($type)."</td>";
                                        echo "<td>".htmlspecialchars($category)."</td>";
                                        echo "<td>".htmlspecialchars($usage_time)."</td>";
                                        echo "<td>".htmlspecialchars($exact_time)."</td>";
                                        echo "<td><button class='delete-btn' onclick='deleteDonation(".$row['Fid'].")'>Delete</button></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' style='text-align: center;'>No donations found</td></tr>";
                                }
                                mysqli_stmt_close($stmt);
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <script>
            function deleteDonation(id) {
                if(confirm('Are you sure you want to delete this donation?')) {
                    window.location.href = 'delete_donation.php?id=' + id;
                }
            }
            </script>

            <script>
            // Function to refresh the page every minute to update timestamps
            setInterval(function() {
                location.reload();
            }, 60000); // 60000 milliseconds = 1 minute
            </script>
        </div>
    </div>
</body>
</html>