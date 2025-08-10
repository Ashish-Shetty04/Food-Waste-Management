<?php
include("login.php"); 
if($_SESSION['name']==''){
	header("location: signin.php");
}
// include("login.php"); 
$emailid= $_SESSION['email'];

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Improve database connection
$connection = mysqli_connect("localhost", "root", "");
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$db = mysqli_select_db($connection, 'demo');
if (!$db) {
    die("Database selection failed: " . mysqli_error($connection));
}

// Remove old food_donations table if it exists
mysqli_query($connection, "DROP TABLE IF EXISTS food_donations");

// Check if table exists and get structure
$table_check = mysqli_query($connection, "SHOW TABLES LIKE 'food_donation'");
if (mysqli_num_rows($table_check) == 0) {
    // Table doesn't exist, create it
    $create_table = "CREATE TABLE IF NOT EXISTS food_donation (
        Fid INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255),
        food VARCHAR(255),
        type VARCHAR(50),
        category VARCHAR(50),
        phoneno VARCHAR(15),
        usage_hours INT,
        usage_minutes INT,
        address TEXT,
        name VARCHAR(255),
        quantity VARCHAR(50),
        status VARCHAR(50) DEFAULT 'pending',
        date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($connection, $create_table)) {
        die("Error creating table: " . mysqli_error($connection));
    }
}

if(isset($_POST['submit']))
{
    try {
        $foodname = mysqli_real_escape_string($connection, $_POST['foodname']);
        $meal = mysqli_real_escape_string($connection, $_POST['meal']);
        $category = $_POST['image-choice'];
        $quantity = mysqli_real_escape_string($connection, $_POST['quantity']);
        $phoneno = mysqli_real_escape_string($connection, $_POST['phoneno']);
        $hours = intval($_POST['usage_hours']);
        $minutes = intval($_POST['usage_minutes']);
        $address = mysqli_real_escape_string($connection, $_POST['address']);
        $name = mysqli_real_escape_string($connection, $_POST['name']);

        // Insert into food_donation table
        $direct_query = "INSERT INTO food_donation(email, food, type, category, phoneno, usage_hours, usage_minutes, address, name, quantity) 
                        VALUES('$emailid', '$foodname', '$meal', '$category', '$phoneno', $hours, $minutes, '$address', '$name', '$quantity')";
        
        if(mysqli_query($connection, $direct_query)) {
            echo '<script type="text/javascript">
                alert("Food donation saved successfully");
                window.location.href = "delivery.html";
            </script>';
        } else {
            throw new Exception("Error saving donation. Please try again.");
        }
        
    } catch (Exception $e) {
        echo '<div style="color: red; background-color: #ffe6e6; padding: 10px; margin: 10px; border: 1px solid red;">
            Error: ' . htmlspecialchars($e->getMessage()) . 
        '</div>';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Donate</title>
    <link rel="stylesheet" href="loginstyle.css">
</head>
<body style="    background-color: #06C167;">
    <div class="container">
        <div class="regformf" >
    <form action="" method="post">
        <p class="logo">Food <b style="color: #06C167; ">Donate</b></p>
        
       <div class="input">
        <label for="foodname"  > Food Name:</label>
        <input type="text" id="foodname" name="foodname" required/>
        </div>
      
      
        <div class="radio">
        <label for="meal" >Meal type :</label> 
        <br><br>

        <input type="radio" name="meal" id="veg" value="veg" required/>
        <label for="veg" style="padding-right: 40px;">Veg</label>
        <input type="radio" name="meal" id="Non-veg" value="Non-veg" >
        <label for="Non-veg">Non-veg</label>
    
        </div>
        <br>
        <div class="input">
        <label for="food">Select the Category:</label>
        <br><br>
        <div class="image-radio-group">
            <input type="radio" id="raw-food" name="image-choice" value="raw-food">
            <label for="raw-food">
              <img src="img/raw-food.png" alt="raw-food" >
            </label>
            <input type="radio" id="cooked-food" name="image-choice" value="cooked-food"checked>
            <label for="cooked-food">
              <img src="img/cooked-food.png" alt="cooked-food" >
            </label>
            <input type="radio" id="packed-food" name="image-choice" value="packed-food">
            <label for="packed-food">
              <img src="img/packed-food.png" alt="packed-food" >
            </label>
          </div>
          <br>
        <!-- <input type="text" id="food" name="food"> -->
        </div>
        <div class="input">
        <label for="quantity">Quantity:(number of person /kg)</label>
        <input type="text" id="quantity" name="quantity" required/>
        </div>
       <b><p style="text-align: center;">Contact Details</p></b>
        <div class="input">
          <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo"". $_SESSION['name'] ;?>" required/>
          </div>
          <div>
            <label for="phoneno">Phone No:</label>
            <input type="text" id="phoneno" name="phoneno" maxlength="10" pattern="[0-9]{10}" required/>
          </div>
        </div>
        <div class="input">
            <label for="usage_time">How much time can this food be used:</label>
            <div style="display: flex; gap: 10px; align-items: center;">
                <div>
                    <input type="number" id="usage_hours" name="usage_hours" min="0" max="72" value="0" required style="width: 70px;"/>
                    <label for="usage_hours">Hours</label>
                </div>
                <div>
                    <input type="number" id="usage_minutes" name="usage_minutes" min="0" max="59" value="0" required style="width: 70px;"/>
                    <label for="usage_minutes">Minutes</label>
                </div>
            </div>
            <small style="color: #666; margin-top: 5px;">Please specify how long the food will remain usable (maximum 72 hours)</small>
        </div>
        <div class="input">
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required/><br>
        </div>
        <div class="btn">
            <button type="submit" name="submit"> submit</button>
     
        </div>
     </form>
     </div>
   </div>
     
    <script>
        // Get the time input elements
        const hoursInput = document.getElementById('usage_hours');
        const minutesInput = document.getElementById('usage_minutes');

        // Validate hours input
        hoursInput.addEventListener('change', function() {
            let hours = parseInt(this.value);
            if (hours < 0) this.value = 0;
            if (hours > 72) this.value = 72;
            
            // If hours is 72, minutes must be 0
            if (hours === 72) {
                minutesInput.value = 0;
                minutesInput.disabled = true;
            } else {
                minutesInput.disabled = false;
            }
        });

        // Validate minutes input
        minutesInput.addEventListener('change', function() {
            let minutes = parseInt(this.value);
            if (minutes < 0) this.value = 0;
            if (minutes > 59) this.value = 59;
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const hours = parseInt(hoursInput.value);
            const minutes = parseInt(minutesInput.value);
            
            // Check if at least 15 minutes total time is specified
            if (hours === 0 && minutes < 15) {
                e.preventDefault();
                alert('Please specify at least 15 minutes of usage time.');
                return;
            }
            
            // Check if total time doesn't exceed 72 hours
            if (hours > 72 || (hours === 72 && minutes > 0)) {
                e.preventDefault();
                alert('Total time cannot exceed 72 hours.');
                return;
            }
        });
    </script>
</body>
</html>