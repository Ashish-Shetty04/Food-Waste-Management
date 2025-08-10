<?php
ob_start(); 
 // $connection = mysqli_connect("localhost:3307", "root", "");
// $db = mysqli_select_db($connection, 'demo');
include("connect.php"); 
include '../connection.php';
if($_SESSION['name']==''){
	header("location:deliverylogin.php");
}
$name=$_SESSION['name'];
$city=$_SESSION['city'];
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,"http://ip-api.com/json");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$result=curl_exec($ch);
$result=json_decode($result);
// $city= $result->city;
// echo $city;

$id=$_SESSION['Did'];



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script language="JavaScript" src="http://www.geoplugin.net/javascript.gp" type="text/javascript"></script>
    <link rel="stylesheet" href="../home.css">

    <link rel="stylesheet" href="delivery.css">
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
                <li><a href="#home" class="active">Home</a></li>
                <li><a href="deliverymyord.php" >Myorders</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <br>
    <script>
        hamburger=document.querySelector(".hamburger");
        hamburger.onclick =function(){
            navBar=document.querySelector(".nav-bar");
            navBar.classList.toggle("active");
        }
    </script>
<?php

// echo var_export(unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip=103.113.190.19')));
 //echo "Your city: {$city}\n";

// $city = "<script language=javascript> document.write(geoplugin_city());</script>"; 
// $scity=$city;
?>
    <style>
        .itm{
            background-color: white;
            display: grid;
        }
        .itm img{
            width: 400px;
            height: 400px;
            margin-left: auto;
            margin-right: auto;
        }
        p{
            text-align: center; font-size: 30PX;color: black; margin-top: 50px;
        }
        a{
            /* text-decoration: underline; */
        }
        @media (max-width: 767px) {
            .itm{
                /* float: left; */
                
            }
            .itm img{
                width: 350px;
                height: 350px;
            }
        }
    </style>
         <h2><center>Welcome <?php echo"$name";?></center></h2>

        <div class="itm" >

            <img src="../img/delivery.gif" alt="" width="400" height="400"> 
          
        </div>
        
        <div class="get">
            <?php


// First, ensure the delivery_by column exists
$check_column = mysqli_query($connection, 
    "SELECT COLUMN_NAME 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'food_donation' 
    AND COLUMN_NAME = 'delivery_by'
    AND TABLE_SCHEMA = DATABASE()");

if (mysqli_num_rows($check_column) == 0) {
    // Add delivery_by column without foreign key first
    $add_column = "ALTER TABLE food_donation ADD COLUMN delivery_by INT DEFAULT NULL";
    if (!mysqli_query($connection, $add_column)) {
        echo "<div class='alert alert-danger'>Error adding delivery_by column: " . mysqli_error($connection) . "</div>";
    }
}

// Define the SQL query to fetch unassigned orders
$sql = "SELECT 
    fd.Fid,
    fd.name,
    fd.food,
    fd.type,
    fd.category,
    fd.phoneno,
    fd.address as pickup_address,
    fd.delivery_address,
    fd.usage_hours,
    fd.usage_minutes,
    DATE_FORMAT(COALESCE(fd.date, NOW()), '%h:%i:%s %p') as exact_time
FROM food_donation fd
WHERE fd.delivery_by IS NULL 
    AND fd.address LIKE ?
    AND fd.status != 'completed'
ORDER BY fd.date DESC";

// Prepare and execute the query with city parameter
$stmt = mysqli_prepare($connection, $sql);
if ($stmt) {
    $city_pattern = '%' . $city . '%';
    mysqli_stmt_bind_param($stmt, "s", $city_pattern);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch the data as an associative array
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing query: " . mysqli_error($connection);
}

// If the delivery person has taken an order
if (isset($_POST['food']) && isset($_POST['order_id']) && isset($_POST['delivery_person_id'])) {
    $order_id = $_POST['order_id'];
    $delivery_person_id = $_POST['delivery_person_id'];
    
    // Debug output
    error_log("Attempting to assign order - Order ID: $order_id, Delivery Person ID: $delivery_person_id");
    
    // Update the order status and assign to delivery person
    $update_sql = "UPDATE food_donation SET delivery_by = ?, status = 'assigned' WHERE Fid = ? AND delivery_by IS NULL";
    $update_stmt = mysqli_prepare($connection, $update_sql);
    
    if ($update_stmt) {
        mysqli_stmt_bind_param($update_stmt, "ii", $delivery_person_id, $order_id);
        
        if (mysqli_stmt_execute($update_stmt)) {
            error_log("Successfully assigned order - Order ID: $order_id to Delivery Person ID: $delivery_person_id");
            // Redirect to my orders page
            header('Location: deliverymyord.php');
            ob_end_flush();
            exit();
        } else {
            $error = "Error assigning order: " . mysqli_error($connection);
            echo "<div class='alert alert-danger'>$error</div>";
            error_log($error);
        }
        mysqli_stmt_close($update_stmt);
    } else {
        $error = "Error preparing update: " . mysqli_error($connection);
        echo "<div class='alert alert-danger'>$error</div>";
        error_log($error);
    }
}
?>
<div class="log">
<!-- <button type="submit" name="food" onclick="">My orders</button> -->
<a href="deliverymyord.php">My orders</a>

</div>
  

<!-- Display the orders in an HTML table -->
<div class="table-container">
          <p id="heading">Available Food Donations</p> 
         <div class="table-wrapper">
        <table class="table">
        <thead>
        <tr>
            <th>Name</th>
            <th>Food</th>
            <th>Type</th>
            <th>Category</th>
            <th>Phone No</th>
            <th>Usage Time</th>
            <th>Time</th>
            <th>Pickup Address</th>
            <th>Delivery Address</th>
            <th>Action</th>
        </tr>
        </thead>
       <tbody>
        <?php 
        if (!empty($data)) {
            foreach ($data as $row) { 
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
        ?>
            <tr>
                <td data-label="Name"><?= htmlspecialchars($row['name']) ?></td>
                <td data-label="Food"><?= htmlspecialchars($row['food']) ?></td>
                <td data-label="Type"><?= htmlspecialchars($row['type']) ?></td>
                <td data-label="Category"><?= htmlspecialchars($row['category']) ?></td>
                <td data-label="Phone"><?= htmlspecialchars($row['phoneno']) ?></td>
                <td data-label="Usage Time"><?= htmlspecialchars($usage_time) ?></td>
                <td data-label="Time"><?= htmlspecialchars($row['exact_time']) ?></td>
                <td data-label="Pickup Address"><?= htmlspecialchars($row['pickup_address']) ?></td>
                <td data-label="Delivery Address"><?= htmlspecialchars($row['delivery_address'] ?? 'Not specified') ?></td>
                <td data-label="Action">
                    <form method="post">
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($row['Fid']) ?>">
                        <input type="hidden" name="delivery_person_id" value="<?= htmlspecialchars($id) ?>">
                        <button type="submit" name="food">Take Order</button>
                    </form>
                </td>
            </tr>
        <?php 
            }
        } else {
            echo '<tr><td colspan="10" style="text-align: center;">No food donations available in your city at this time.</td></tr>';
        }
        ?>
        </tbody>
        </table>
        </div>

        
     
        

   <br>
   <br>
</body>
</html>