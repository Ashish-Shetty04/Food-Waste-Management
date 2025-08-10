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
    <title>Admin Dashboard - Donations</title>
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
        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin: 2px;
        }
        .assign-btn {
            background-color: #06C167;
            color: white;
        }
        .assign-btn:hover {
            background-color: #059656;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
        }
        .close {
            float: right;
            cursor: pointer;
            font-size: 24px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-pending {
            background-color: #ffeeba;
            color: #856404;
        }
        .status-assigned {
            background-color: #b8daff;
            color: #004085;
        }
        .status-completed {
            background-color: #c3e6cb;
            color: #155724;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
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
                <li><a href="#" class="active">
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
                    <i class="uil uil-heart"></i>
                    <span class="text">Donations Overview</span>
                </div>

                <div class="table-container">
                    <?php
                    // Process delivery address assignment
                    if (isset($_POST['assign_delivery'])) {
                        $donation_id = $_POST['donation_id'];
                        $delivery_address = $_POST['delivery_address'];
                        
                        // First, let's try to add the columns if they don't exist
                        $alter_table_queries = [
                            "ALTER TABLE food_donation ADD COLUMN IF NOT EXISTS delivery_address TEXT AFTER address",
                            "ALTER TABLE food_donation ADD COLUMN IF NOT EXISTS delivery_notes TEXT AFTER delivery_address",
                            "ALTER TABLE food_donation ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'pending' AFTER delivery_notes"
                        ];

                        foreach ($alter_table_queries as $query) {
                            mysqli_query($connection, $query);
                        }

                        // Now update the donation with delivery information
                        $update_query = "UPDATE food_donation SET 
                            delivery_address = ?,
                            status = 'assigned'
                            WHERE Fid = ?";
                            
                        $update_stmt = mysqli_prepare($connection, $update_query);
                        if ($update_stmt) {
                            mysqli_stmt_bind_param($update_stmt, "si", $delivery_address, $donation_id);
                            if (mysqli_stmt_execute($update_stmt)) {
                                echo "<div class='alert alert-success'>Delivery address assigned successfully!</div>";
                            } else {
                                echo "<div class='alert alert-danger'>Error assigning delivery address.</div>";
                            }
                            mysqli_stmt_close($update_stmt);
                        }
                    }

                    try {
                        // First, ensure all required columns exist
                        $required_columns = [
                            ["delivery_address", "TEXT"],
                            ["delivery_notes", "TEXT"],
                            ["status", "VARCHAR(20) DEFAULT 'pending'"]
                        ];

                        foreach ($required_columns as $column) {
                            $check_column = mysqli_query($connection, 
                                "SELECT COLUMN_NAME 
                                FROM INFORMATION_SCHEMA.COLUMNS 
                                WHERE TABLE_NAME = 'food_donation' 
                                AND COLUMN_NAME = '{$column[0]}'
                                AND TABLE_SCHEMA = 'demo'");
                            
                            if (mysqli_num_rows($check_column) == 0) {
                                mysqli_query($connection, 
                                    "ALTER TABLE food_donation 
                                    ADD COLUMN {$column[0]} {$column[1]}");
                            }
                        }

                        // Now fetch the donations with proper error handling
                        $query = "SELECT 
                                    d.*,
                                    COALESCE(d.status, 'pending') as current_status,
                                    COALESCE(d.delivery_address, 'Not assigned') as delivery_location,
                                    DATE_FORMAT(COALESCE(d.date, NOW()), '%h:%i:%s %p') as exact_time,
                                    d.usage_hours,
                                    d.usage_minutes
                                FROM food_donation d
                                ORDER BY d.date DESC";
                        
                        $result = mysqli_query($connection, $query);
                        
                        if ($result === false) {
                            throw new Exception(mysqli_error($connection));
                        }
                        
                        if (mysqli_num_rows($result) > 0) {
                            echo "<table class='table'>";
                            echo "<thead><tr>
                                    <th>Name</th>
                                    <th>Food Items</th>
                                    <th>Category</th>
                                    <th>Phone</th>
                                    <th>Usage Time</th>
                                    <th>Time</th>
                                    <th>Pickup Address</th>
                                    <th>Delivery Address</th>
                                    <th>Status</th>
                                    <th>Action</th>
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
                                echo "<td data-label='Pickup Address'>" . htmlspecialchars($row['address'] ?? '') . "</td>";
                                echo "<td data-label='Delivery Address'>" . htmlspecialchars($row['delivery_location']) . "</td>";
                                echo "<td data-label='Status'><span class='status-badge " . $statusClass . "'>" . $statusText . "</span></td>";
                                echo "<td data-label='Action'>";
                                if ($status !== 'completed') {
                                    echo "<button class='action-btn assign-btn' onclick='openDeliveryModal(" . $row['Fid'] . ")'>Assign Delivery</button>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                            
                            echo "</tbody></table>";
                        } else {
                            echo "<div class='alert alert-info'>No donations found.</div>";
                        }
                    } catch (Exception $e) {
                        echo "<div class='alert alert-danger'>An error occurred while fetching donations: " . htmlspecialchars($e->getMessage()) . "</div>";
                        error_log("Exception in donate.php: " . $e->getMessage());
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Delivery Assignment Modal -->
    <div id="deliveryModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDeliveryModal()">&times;</span>
            <h2>Assign Delivery Address</h2>
            <form method="POST" action="">
                <input type="hidden" name="donation_id" id="donation_id">
                <div class="form-group">
                    <label for="delivery_address">Delivery Address:</label>
                    <textarea name="delivery_address" id="delivery_address" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="delivery_notes">Delivery Notes (Optional):</label>
                    <textarea name="delivery_notes" id="delivery_notes" rows="2"></textarea>
                </div>
                <button type="submit" name="assign_delivery" class="action-btn assign-btn">Assign Delivery</button>
            </form>
        </div>
    </div>

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

        // Delivery modal functions
        function openDeliveryModal(donationId) {
            document.getElementById('deliveryModal').style.display = 'block';
            document.getElementById('donation_id').value = donationId;
        }

        function closeDeliveryModal() {
            document.getElementById('deliveryModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            let modal = document.getElementById('deliveryModal');
            if (event.target == modal) {
                closeDeliveryModal();
            }
        }
    </script>
</body>
</html>
