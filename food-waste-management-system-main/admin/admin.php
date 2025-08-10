<?php
session_start();
require_once(__DIR__ . '/../connection.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['name']) || empty($_SESSION['name'])) {
    header("location:login.php");
    exit();
}

$msg=0;
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
    <title>Admin Dashboard Panel</title> 
    <style>
        .activity .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .activity .table th,
        .activity .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .activity .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        .activity .table tbody tr:hover {
            background-color: #f5f5f5;
        }
        .activity .table td {
            color: #666;
        }
        .no-donations {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
            background: white;
            border-radius: 8px;
            margin-top: 20px;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-completed {
            background-color: #c3e6cb;
            color: #155724;
        }
        .status-assigned {
            background-color: #b8daff;
            color: #004085;
        }
        .status-pending {
            background-color: #ffeeba;
            color: #856404;
        }
        @media (max-width: 768px) {
            .activity .table thead {
                display: none;
            }
            .activity .table, 
            .activity .table tbody, 
            .activity .table tr, 
            .activity .table td {
                display: block;
                width: 100%;
            }
            .activity .table tr {
                margin-bottom: 15px;
                border-bottom: 2px solid #ddd;
            }
            .activity .table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }
            .activity .table td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 15px;
                font-weight: 600;
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <nav>
        <div class="logo-name">
            <div class="logo-image">
            </div>
            <span class="logo_name">ADMIN</span>
        </div>

        <div class="menu-items">
            <ul class="nav-links">
                <li><a href="#" class="active">
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
                <li><a href="adminprofile.php">
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
            <p class="logo">Food <b style="color: #06C167;">Donate</b></p>
        </div>

        <div class="dash-content">
            <div class="overview">
                <div class="title">
                    <i class="uil uil-tachometer-fast-alt"></i>
                    <span class="text">Dashboard</span>
                </div>

                <div class="boxes">
                    <div class="box box1">
                        <i class="uil uil-user"></i>
                        <span class="text">Total Users</span>
                        <?php
                        try {
                            $query = "SELECT COUNT(*) as count FROM login";
                            $result = mysqli_query($connection, $query);
                            if ($result) {
                                $row = mysqli_fetch_assoc($result);
                                echo "<span class=\"number\">" . $row['count'] . "</span>";
                            } else {
                                echo "<span class=\"number\">0</span>";
                            }
                        } catch (Exception $e) {
                            echo "<span class=\"number\">0</span>";
                            error_log("Error counting users: " . $e->getMessage());
                        }
                        ?>
                    </div>
                    <div class="box box2">
                        <i class="uil uil-comments"></i>
                        <span class="text">Feedbacks</span>
                        <?php
                        try {
                            $query = "SELECT COUNT(*) as count FROM user_feedback";
                            $result = mysqli_query($connection, $query);
                            if ($result) {
                                $row = mysqli_fetch_assoc($result);
                                echo "<span class=\"number\">" . $row['count'] . "</span>";
                            } else {
                                echo "<span class=\"number\">0</span>";
                            }
                        } catch (Exception $e) {
                            echo "<span class=\"number\">0</span>";
                            error_log("Error counting feedback: " . $e->getMessage());
                        }
                        ?>
                    </div>
                    <div class="box box3">
                        <i class="uil uil-heart"></i>
                        <span class="text">Total Donations</span>
                        <?php
                        try {
                            $query = "SELECT COUNT(*) as count FROM food_donation";
                            $result = mysqli_query($connection, $query);
                            if ($result) {
                                $row = mysqli_fetch_assoc($result);
                                echo "<span class=\"number\">" . $row['count'] . "</span>";
                            } else {
                                echo "<span class=\"number\">0</span>";
                            }
                        } catch (Exception $e) {
                            echo "<span class=\"number\">0</span>";
                            error_log("Error counting donations: " . $e->getMessage());
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="activity">
                <div class="title">
                    <i class="uil uil-clock-three"></i>
                    <span class="text">Recent Donations</span>
                </div>

                <?php
                try {
                    // Get recent donations
                    $query = "SELECT 
                                d.*,
                                COALESCE(d.status, 'pending') as current_status,
                                DATE_FORMAT(COALESCE(d.date, NOW()), '%h:%i:%s %p') as exact_time,
                                d.usage_hours,
                                d.usage_minutes
                            FROM food_donation d
                            ORDER BY d.date DESC
                            LIMIT 10";
                    
                    $result = mysqli_query($connection, $query);
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        echo "<table class='table'>";
                        echo "<thead><tr>
                                <th>Name</th>
                                <th>Food Items</th>
                                <th>Category</th>
                                <th>Phone</th>
                                <th>Usage Time</th>
                                <th>Time</th>
                                <th>Address</th>
                                <th>Status</th>
                              </tr></thead><tbody>";
                        
                        while ($row = mysqli_fetch_assoc($result)) {
                            $status = $row['current_status'] ?? 'pending';
                            $statusClass = match($status) {
                                'completed' => 'status-completed',
                                'assigned' => 'status-assigned',
                                default => 'status-pending'
                            };
                            $statusText = match($status) {
                                'completed' => 'Completed',
                                'assigned' => 'In Progress',
                                default => 'Pending'
                            };

                            // Format usage time display
                            $hours = intval($row['usage_hours']);
                            $minutes = intval($row['usage_minutes']);
                            
                            if ($hours > 0 && $minutes > 0) {
                                $usage_time = $hours . " hours " . $minutes . " minutes";
                            } elseif ($hours > 0) {
                                $usage_time = $hours . " hours";
                            } elseif ($minutes > 0) {
                                $usage_time = $minutes . " minutes";
                            } else {
                                $usage_time = "0 minutes";
                            }

                            echo "<tr>";
                            echo "<td data-label='Name'>" . htmlspecialchars($row['name'] ?? '') . "</td>";
                            echo "<td data-label='Food Items'>" . htmlspecialchars($row['food'] ?? '') . "</td>";
                            echo "<td data-label='Category'>" . htmlspecialchars($row['category'] ?? '') . "</td>";
                            echo "<td data-label='Phone'>" . htmlspecialchars($row['phoneno'] ?? '') . "</td>";
                            echo "<td data-label='Usage Time'>" . htmlspecialchars($usage_time) . "</td>";
                            echo "<td data-label='Time'>" . htmlspecialchars($row['exact_time']) . "</td>";
                            echo "<td data-label='Address'>" . htmlspecialchars($row['address'] ?? '') . "</td>";
                            echo "<td data-label='Status'><span class='status-badge " . $statusClass . "'>" . $statusText . "</span></td>";
                            echo "</tr>";
                        }
                        
                        echo "</tbody></table>";
                    } else {
                        echo "<div class='no-donations'>No recent donations found.</div>";
                    }
                } catch (Exception $e) {
                    error_log("Error fetching recent donations: " . $e->getMessage());
                    echo "<div class='no-donations'>Error loading recent donations. Please try again later.</div>";
                }
                ?>
            </div>
        </div>
    </section>

    <script>
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
