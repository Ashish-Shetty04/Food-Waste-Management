<?php
session_start();
include("connection.php");

// Check if user is logged in
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("location: signin.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $gender = trim($_POST['gender']);
    $email = $_SESSION['email'];

    // Update user information
    $query = "UPDATE login SET name = ?, gender = ? WHERE email = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "sss", $name, $gender, $email);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['name'] = $name;
        $_SESSION['gender'] = $gender;
        header("location: profile.php");
        exit();
    } else {
        $error = "Error updating profile. Please try again.";
    }
}

// Fetch current user data
$email = $_SESSION['email'];
$query = "SELECT * FROM login WHERE email = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Food Donation</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="profile.css">
    <style>
        .edit-profile-form {
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .form-group select {
            background-color: white;
        }

        .btn-save {
            background-color: #06C167;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        .btn-save:hover {
            background-color: #05a057;
        }

        .btn-cancel {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #666;
            text-decoration: none;
        }

        .btn-cancel:hover {
            color: #333;
        }

        .error-message {
            color: #ff4444;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Food <b style="color: #06C167;">Donate</b></div>
        <nav class="nav-bar">
            <ul>
                <li><a href="home.html">Home</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="profile.php" class="active">Profile</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="edit-profile-form">
            <h2>Edit Profile</h2>
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="edit_profile.php">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="male" <?php echo ($user['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo ($user['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                        <option value="other" <?php echo ($user['gender'] === 'other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <button type="submit" class="btn-save">Save Changes</button>
                <a href="profile.php" class="btn-cancel">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
