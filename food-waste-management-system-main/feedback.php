<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['email'])) {
    header("location: signin.php");
    exit();
}

$success_message = '';
$error_message = '';

if (isset($_POST['feedback'])) {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    
    // Debug output
    error_log("Feedback submission - Name: $name, Email: $email, Message: $message");
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($message)) {
        $error_message = "All fields are required.";
    } else {
        // Sanitize inputs
        $name = mysqli_real_escape_string($connection, $name);
        $email = mysqli_real_escape_string($connection, $email);
        $message = mysqli_real_escape_string($connection, $message);
        
        // Insert feedback using prepared statement
        $stmt = mysqli_prepare($connection, "INSERT INTO user_feedback (name, email, message) VALUES (?, ?, ?)");
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $message);
            
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Thank you for your feedback! We appreciate your input.";
                error_log("Feedback successfully inserted");
            } else {
                $error_message = "Error saving feedback: " . mysqli_error($connection);
                error_log("Error executing feedback insert: " . mysqli_error($connection));
            }
            mysqli_stmt_close($stmt);
        } else {
            $error_message = "Error preparing statement: " . mysqli_error($connection);
            error_log("Error preparing feedback statement: " . mysqli_error($connection));
        }
    }
}

// Debug output for session data
error_log("Session data - Name: " . ($_SESSION['name'] ?? 'not set') . ", Email: " . ($_SESSION['email'] ?? 'not set'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - Food Donation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            border-radius: 8px;
            text-align: center;
        }

        .header h1 {
            color: #06C167;
            margin-bottom: 10px;
        }

        .feedback-form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .form-group textarea {
            height: 150px;
            resize: vertical;
        }

        .submit-btn {
            background: #06C167;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background 0.3s;
        }

        .submit-btn:hover {
            background: #05a057;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .nav-links {
            margin-top: 20px;
            text-align: center;
        }

        .nav-links a {
            color: #666;
            text-decoration: none;
            margin: 0 10px;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #06C167;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
                margin: 20px auto;
            }

            .feedback-form {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Feedback</h1>
            <p>We value your feedback! Let us know how we can improve.</p>
        </div>

        <?php if ($success_message): ?>
            <div class="message success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="message error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="feedback-form">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="message">Your Feedback</label>
                    <textarea id="message" name="message" required placeholder="Please share your thoughts with us..."></textarea>
                </div>

                <button type="submit" name="feedback" class="submit-btn">Submit Feedback</button>
            </form>
        </div>

        <div class="nav-links">
            <a href="home.html"><i class="fas fa-home"></i> Home</a>
            <a href="contact.html"><i class="fas fa-user"></i> Dashboard</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</body>
</html>
