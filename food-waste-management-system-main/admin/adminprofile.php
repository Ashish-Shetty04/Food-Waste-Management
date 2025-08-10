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

// Get admin details
$admin_id = $_SESSION['Aid'] ?? 0;
$admin_details = [];

try {
    $query = "SELECT * FROM admin WHERE Aid = ?";
    $stmt = mysqli_prepare($connection, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $admin_id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if ($result && $row = mysqli_fetch_assoc($result)) {
                $admin_details = $row;
            } else {
                error_log("No admin found with ID: " . $admin_id);
            }
        } else {
            error_log("Error executing admin query: " . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt);
    } else {
        error_log("Error preparing admin query: " . mysqli_error($connection));
    }
} catch (Exception $e) {
    error_log("Error fetching admin details: " . $e->getMessage());
}

// Ensure admin_details has default values if empty
$admin_details = array_merge([
    'name' => 'N/A',
    'email' => 'N/A',
    'location' => 'N/A',
    'address' => 'N/A'
], $admin_details);

// Debug information
error_log("Admin ID: " . $admin_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <title>Admin Profile</title>
    <style>
        .profile-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 20px;
            padding: 20px;
        }
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 60px;
            background: #f0f0f0;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: #666;
        }
        .profile-name {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin: 10px 0;
        }
        .profile-role {
            color: #666;
            font-size: 16px;
        }
        .profile-details {
            margin-top: 30px;
        }
        .detail-item {
            display: flex;
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .detail-label {
            flex: 0 0 120px;
            color: #666;
            font-weight: 500;
        }
        .detail-value {
            flex: 1;
            color: #333;
        }
        .assigned-donations {
            margin-top: 30px;
        }
        .assigned-donations h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
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
            .detail-item {
                flex-direction: column;
            }
            .detail-label {
                margin-bottom: 5px;
            }
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
                    <span class="link-name">Dashboard</span>
                </a></li>
                <li><a href="analytics.php">
                    <i class="uil uil-chart"></i>
                    <span class="link-name">Analytics</span>
                </a></li>
                <li><a href="donate.php">
                    <i class="uil uil-heart"></i>
                    <span class="link-name">Donates</span>
                </a></li>
                <li><a href="feedback.php">
                    <i class="uil uil-comments"></i>
                    <span class="link-name">Feedbacks</span>
                </a></li>
                <li><a href="#" class="active">
                    <i class="uil uil-user"></i>
                    <span class="link-name">Profile</span>
                </a></li>
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
            <p class="logo">Admin <b style="color: #06C167;">Profile</b></p>
        </div>

        <div class="dash-content">
            <div class="profile-container">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="uil uil-user"></i>
                    </div>
                    <h1 class="profile-name"><?php echo htmlspecialchars($admin_details['name'] ?? 'N/A'); ?></h1>
                    <div class="profile-role">Administrator</div>
                </div>

                <div class="profile-details">
                    <div class="detail-item">
                        <div class="detail-label">Email</div>
                        <div class="detail-value"><?php echo htmlspecialchars($admin_details['email'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Location</div>
                        <div class="detail-value"><?php echo htmlspecialchars($admin_details['location'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Role</div>
                        <div class="detail-value">System Administrator</div>
                    </div>
                </div>

                <div class="assigned-donations">
                    <h2>Recent Donations</h2>
                    <?php
                    try {
                        // Get all donations, not just assigned ones
                        $query = "SELECT * FROM food_donation ORDER BY date DESC LIMIT 10";
                        $result = mysqli_query($connection, $query);
                        
                        if ($result && mysqli_num_rows($result) > 0) {
                            echo "<table class='table'>";
                            echo "<thead><tr>
                                    <th>Name</th>
                                    <th>Food Items</th>
                                    <th>Category</th>
                                    <th>Phone</th>
                                    <th>Date</th>
                                    <th>Address</th>
                                    <th>Quantity</th>
                                  </tr></thead><tbody>";

                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td data-label='Name'>" . htmlspecialchars($row['name'] ?? 'N/A') . "</td>";
                                echo "<td data-label='Food Items'>" . htmlspecialchars($row['food'] ?? 'N/A') . "</td>";
                                echo "<td data-label='Category'>" . htmlspecialchars($row['category'] ?? 'N/A') . "</td>";
                                echo "<td data-label='Phone'>" . htmlspecialchars($row['phoneno'] ?? 'N/A') . "</td>";
                                echo "<td data-label='Date'>" . htmlspecialchars($row['date'] ?? 'N/A') . "</td>";
                                echo "<td data-label='Address'>" . htmlspecialchars($row['address'] ?? 'N/A') . "</td>";
                                echo "<td data-label='Quantity'>" . htmlspecialchars($row['quantity'] ?? '0') . "</td>";
                                echo "</tr>";
                            }
                            
                            echo "</tbody></table>";
                        } else {
                            echo "<div class='no-data'>No donations found in the system.</div>";
                        }
                    } catch (Exception $e) {
                        error_log("Error fetching donations: " . $e->getMessage());
                        echo "<div class='no-data'>Error loading donations. Please try again later.</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>

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