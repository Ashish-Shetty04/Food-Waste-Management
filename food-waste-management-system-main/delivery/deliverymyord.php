<?php
ob_start();
session_start();
include('../connection.php');

if (!isset($_SESSION['Did'])) {
    header("location:deliverylogin.php");
    exit();
}

$id = $_SESSION['Did'];
$name = $_SESSION['name'];

// Debug output
error_log("Delivery Person ID: $id");

// Query to get assigned orders
$sql = "SELECT 
    fd.*,
    DATE_FORMAT(fd.date, '%h:%i:%s %p') as exact_time,
    l.name as donor_name,
    l.email as donor_email
FROM food_donation fd
LEFT JOIN login l ON fd.email = l.email
WHERE fd.delivery_by = ? 
AND fd.status != 'completed'
ORDER BY fd.date DESC";

$stmt = mysqli_prepare($connection, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    // Debug output
    error_log("Found " . count($orders) . " orders for delivery person $id");
} else {
    error_log("Error preparing query: " . mysqli_error($connection));
    $orders = [];
}

// Handle order status updates
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];
    
    $update_sql = "UPDATE food_donation SET status = ? WHERE Fid = ? AND delivery_by = ?";
    $update_stmt = mysqli_prepare($connection, $update_sql);
    
    if ($update_stmt) {
        mysqli_stmt_bind_param($update_stmt, "sii", $new_status, $order_id, $id);
        if (mysqli_stmt_execute($update_stmt)) {
            header("Location: deliverymyord.php");
            exit();
        } else {
            $error = "Error updating status: " . mysqli_error($connection);
            echo "<div class='alert alert-danger'>$error</div>";
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
    <title>My Orders</title>
    <link rel="stylesheet" href="delivery.css">
    <link rel="stylesheet" href="../home.css">
    <style>
        .table-container {
            padding: 20px;
            margin: 20px;
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
        .btn-primary {
            background-color: #06C167;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            color: white;
            cursor: pointer;
        }
        .btn-primary:hover {
            background-color: #059656;
        }
        .status-select {
            padding: 6px;
            margin-bottom: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
        }
        .alert {
            padding: 12px;
            margin: 12px 0;
            border-radius: 4px;
        }
        .alert-info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
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
                <li><a href="delivery.php">Home</a></li>
                <li><a href="deliverymyord.php" class="active">My Orders</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <script>
        hamburger = document.querySelector(".hamburger");
        hamburger.onclick = function() {
            navBar = document.querySelector(".nav-bar");
            navBar.classList.toggle("active");
        }
    </script>

    <div class="table-container">
        <h2>My Assigned Orders</h2>
        
        <?php if (empty($orders)): ?>
            <div class="alert alert-info">No orders assigned to you at this time.</div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Donor Name</th>
                        <th>Food Items</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Usage Time</th>
                        <th>Pickup Address</th>
                        <th>Delivery Address</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): 
                        // Format usage time display
                        $hours = intval($order['usage_hours']);
                        $minutes = intval($order['usage_minutes']);
                        
                        if ($hours > 0 && $minutes > 0) {
                            $usage_time = $hours . " hours " . $minutes . " minutes";
                        } elseif ($hours > 0) {
                            $usage_time = $hours . " hours";
                        } elseif ($minutes > 0) {
                            $usage_time = $minutes . " minutes";
                        } else {
                            $usage_time = "0 minutes";
                        }
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($order['donor_name']) ?></td>
                            <td><?= htmlspecialchars($order['food']) ?></td>
                            <td><?= htmlspecialchars($order['type']) ?></td>
                            <td><?= htmlspecialchars($order['category']) ?></td>
                            <td><?= htmlspecialchars($usage_time) ?></td>
                            <td><?= htmlspecialchars($order['address']) ?></td>
                            <td><?= htmlspecialchars($order['delivery_address'] ?? 'Not specified') ?></td>
                            <td>
                                Donor: <?= htmlspecialchars($order['donor_email']) ?><br>
                                Recipient: <?= htmlspecialchars($order['phoneno']) ?>
                            </td>
                            <td><?= htmlspecialchars(ucfirst($order['status'])) ?></td>
                            <td>
                                <?php if ($order['status'] != 'completed'): ?>
                                    <form method="post">
                                        <input type="hidden" name="order_id" value="<?= $order['Fid'] ?>">
                                        <select name="new_status" class="status-select">
                                            <option value="picked_up" <?= $order['status'] == 'picked_up' ? 'selected' : '' ?>>Picked Up</option>
                                            <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                            <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn-primary">Update Status</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>