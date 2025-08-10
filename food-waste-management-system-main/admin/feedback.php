<?php
session_start();
require_once(__DIR__ . '/../connection.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['name']) || empty($_SESSION['name'])) {
    header("location:signin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <title>Admin Dashboard - Feedback</title>
    <style>
        .table-container {
            margin: 20px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .table tr:hover {
            background-color: #f5f5f5;
        }
        @media screen and (max-width: 768px) {
            .table thead {
                display: none;
            }
            .table, .table tbody, .table tr, .table td {
                display: block;
                width: 100%;
            }
            .table tr {
                margin-bottom: 15px;
                border-bottom: 2px solid #ddd;
            }
            .table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }
            .table td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 15px;
                font-weight: 600;
                text-align: left;
            }
        }
        .table td[data-label="Message"] {
            max-width: 300px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .table td {
            padding: 15px;
            vertical-align: top;
        }
        @media screen and (max-width: 768px) {
            .table td[data-label="Message"] {
                text-align: left;
                padding-left: 15px;
            }
            .table td[data-label="Message"]::before {
                display: none;
            }
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            text-align: center;
        }
    </style>
</head>
<body>
    <nav>
        <div class="logo-name">
            <div class="logo-image">
                <!--<img src="images/logo.png" alt="">-->
            </div>

            <span class="logo_name">ADMIN</span>
        </div>

        <div class="menu-items">
            <ul class="nav-links">
                <li><a href="admin.php">
                    <i class="uil uil-estate"></i>
                    <span class="link-name">Dahsboard</span>
                </a></li>
                <!-- <li><a href="#">
                    <i class="uil uil-files-landscapes"></i>
                    <span class="link-name">Content</span>
                </a></li> -->
                <li><a href="analytics.php">
                    <i class="uil uil-chart"></i>
                    <span class="link-name">Analytics</span>
                </a></li>
                <li><a href="donate.php">
                    <i class="uil uil-heart"></i>
                    <span class="link-name">Donates</span>
                </a></li>
                <li><a href="#">
                    <i class="uil uil-comments"></i>
                    <span class="link-name">Feedbacks</span>
                </a></li>
                <li><a href="adminprofile.php">
                    <i class="uil uil-user"></i>
                    <span class="link-name">Profile</span>
                </a></li>
                <!-- <li><a href="#">
                    <i class="uil uil-share"></i>
                    <span class="link-name">Share</span>
                </a></li> -->
            </ul>
            
            <ul class="logout-mode">
                <li><a href="../logout.php">
                    <i class="uil uil-signout"></i>
                    <span class="link-name">Logout</span>
                </a></li>

                <li class="mode">
                    <a href="#">
                        <i class="uil uil-moon"></i>
                    <span class="link-name">Dark Mode</span>
                </a>

                <div class="mode-toggle">
                  <span class="switch"></span>
                </div>
            </li>
            </ul>
        </div>
    </nav>

    <section class="dashboard">
        
        <div class="top">
            <i class="uil uil-bars sidebar-toggle"></i>
            <!-- <p>Food Donate</p> -->
            <p  class ="logo" >Feed<b style="color: #06C167; ">back</b></p>
             <p class="user"></p>
            <!-- <div class="search-box">
                <i class="uil uil-search"></i>
                <input type="text" placeholder="Search here...">
            </div> -->
            
            <!--<img src="images/profile.jpg" alt="">-->
        </div>
       <br>
       <br>
       <br>

            <div class="activity">
                <div class="table-container">
                    <div class="table-wrapper">
                        <?php
                        // Enable error reporting
                        error_reporting(E_ALL);
                        ini_set('display_errors', 1);
                        
                        // Debug database connection
                        if (!$connection) {
                            echo "<div class='error'>Database connection failed: " . mysqli_connect_error() . "</div>";
                            exit;
                        }
                        
                        // Check if table exists
                        $table_check = mysqli_query($connection, "SHOW TABLES LIKE 'user_feedback'");
                        if (!$table_check) {
                            echo "<div class='error'>Error checking table: " . mysqli_error($connection) . "</div>";
                            exit;
                        }
                        
                        if (mysqli_num_rows($table_check) == 0) {
                            echo "<div class='error'>Feedback table does not exist</div>";
                        } else {
                            // Get all feedback
                            $query = "SELECT * FROM user_feedback";
                            $result = mysqli_query($connection, $query);
                            
                            if (!$result) {
                                echo "<div class='error'>Error fetching feedback: " . mysqli_error($connection) . "</div>";
                            } else {
                                ?>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Message</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<tr>";
                                                echo "<td data-label='Name'>" . htmlspecialchars($row['name']) . "</td>";
                                                echo "<td data-label='Email'>" . htmlspecialchars($row['email']) . "</td>";
                                                echo "<td data-label='Message'>" . htmlspecialchars($row['message']) . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='3' style='text-align: center;'>No feedback available</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="admin.js"></script>
    <script>
        // Dark mode toggle
        const body = document.querySelector("body"),
        modeToggle = body.querySelector(".mode-toggle"),
        sidebar = body.querySelector("nav"),
        sidebarToggle = body.querySelector(".sidebar-toggle");

        let getMode = localStorage.getItem("mode");
        if(getMode && getMode === "dark") {
            body.classList.toggle("dark");
        }

        modeToggle.addEventListener("click", () => {
            body.classList.toggle("dark");
            localStorage.setItem("mode", body.classList.contains("dark") ? "dark" : "light");
        });

        sidebarToggle.addEventListener("click", () => {
            sidebar.classList.toggle("close");
        });
    </script>
</body>
</html>
