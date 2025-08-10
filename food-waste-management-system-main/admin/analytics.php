<?php
session_start();
require_once(__DIR__ . '/../connection.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['name']) || empty($_SESSION['name'])) {
    header("location:login.php");
    exit();
}

// Debug information
echo "<script>console.log('Analytics page accessed by: " . $_SESSION['name'] . "');</script>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="admin.css">
    
    <!-- Iconscout CSS -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">

    <title>Admin Analytics</title>
    <style>
        .charts {
            display: flex;
            justify-content: space-between;
            margin: 20px;
            flex-wrap: wrap;
            gap: 20px;
        }
        .chart {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .chart h2 {
            margin-bottom: 15px;
            color: #333;
        }
        #categoryChart {
            max-width: 300px !important;
            max-height: 300px !important;
        }
        #monthlyChart {
            width: 100% !important;
            max-width: 800px !important;
            height: 400px !important;
        }
        @media (max-width: 768px) {
            .charts {
                flex-direction: column;
            }
            .chart {
                width: 100%;
            }
            #categoryChart, #monthlyChart {
                width: 100% !important;
            }
        }
    </style>
</head>
<body>
    <?php
    // Initialize variables
    $total_users = 0;
    $total_donations = 0;
    $total_feedbacks = 0;
    $donation_categories = [];
    $monthly_donations = [];

    try {
        // Total Users Query
        $users_query = "SELECT COUNT(*) as total_users FROM login";
        $stmt = mysqli_prepare($connection, $users_query);
        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                $total_users = $row['total_users'];
            }
            mysqli_stmt_close($stmt);
        }

        // Total Donations Query
        $donations_query = "SELECT COUNT(*) as total_donations FROM food_donation";
        $stmt = mysqli_prepare($connection, $donations_query);
        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                $total_donations = $row['total_donations'];
            }
            mysqli_stmt_close($stmt);
        }

        // Total Feedbacks Query
        $feedbacks_query = "SELECT COUNT(*) as total_feedbacks FROM user_feedback";
        $stmt = mysqli_prepare($connection, $feedbacks_query);
        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                $total_feedbacks = $row['total_feedbacks'];
            }
            mysqli_stmt_close($stmt);
        }

        // Donation Categories Query
        $categories_query = "SELECT category, COUNT(*) as count FROM food_donation GROUP BY category";
        $stmt = mysqli_prepare($connection, $categories_query);
        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $donation_categories[$row['category']] = $row['count'];
                }
            }
            mysqli_stmt_close($stmt);
        }

        // Monthly Donations Query
        $monthly_query = "SELECT DATE_FORMAT(date, '%Y-%m') as month, COUNT(*) as count 
                         FROM food_donation 
                         GROUP BY DATE_FORMAT(date, '%Y-%m') 
                         ORDER BY month DESC 
                         LIMIT 12";
        $stmt = mysqli_prepare($connection, $monthly_query);
        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $monthly_donations[$row['month']] = $row['count'];
                }
            }
            mysqli_stmt_close($stmt);
        }
    } catch (Exception $e) {
        error_log("Error in analytics: " . $e->getMessage());
    }
    ?>

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
                <li><a href="#" class="active">
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
                    <i class="uil uil-chart"></i>
                    <span class="text">Analytics Overview</span>
                </div>

                <div class="boxes">
                    <div class="box box1">
                        <i class="uil uil-user"></i>
                        <span class="text">Total Users</span>
                        <span class="number"><?php echo $total_users; ?></span>
                    </div>
                    <div class="box box2">
                        <i class="uil uil-heart"></i>
                        <span class="text">Total Donations</span>
                        <span class="number"><?php echo $total_donations; ?></span>
                    </div>
                    <div class="box box3">
                        <i class="uil uil-comments"></i>
                        <span class="text">Total Feedbacks</span>
                        <span class="number"><?php echo $total_feedbacks; ?></span>
                    </div>
                </div>
            </div>

            <div class="charts">
                <div class="chart">
                    <h2>Donation Categories</h2>
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="chart">
                    <h2>Monthly Donations</h2>
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Category Chart
        var categoryCtx = document.getElementById('categoryChart').getContext('2d');
        var categoryChart = new Chart(categoryCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_keys($donation_categories)); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($donation_categories)); ?>,
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12
                    }
                }
            }
        });

        // Monthly Chart
        var monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        var monthlyChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_keys($monthly_donations)); ?>,
                datasets: [{
                    label: 'Donations per Month',
                    data: <?php echo json_encode(array_values($monthly_donations)); ?>,
                    borderColor: '#06C167',
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

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