Food Waste Management System

📌 Overview

The Food Waste Management System is a web-based platform designed to connect donors, NGOs, and delivery partners to reduce food wastage. It allows users to donate leftover food, track deliveries, and manage feedback. This system is suitable for restaurants, individuals, and organizations who want to contribute to reducing hunger and food waste.

🚀 Features

User Authentication – Sign up, login, and manage profiles.
Food Donation Form – Submit details about food donations.
Admin Panel – Manage donations, users, and feedback.
Delivery Management – Track and manage food delivery.
Feedback System – Collect and store user feedback.
Chatbot – Provide quick guidance to users.
Analytics – View donation statistics.

🛠️ Tech Stack

Frontend: HTML, CSS, JavaScript
Backend: PHP
Database: MySQL

Other: SQL scripts for table setup

📂 Project Structure

perl
Copy code
food-waste-management-system-main/
│── about.html                # About page
│── connection.php            # Database connection
│── contact.html              # Contact page
│── delete_donation.php       # Remove donation record
│── delivery.html             # Delivery info page
│── edit_profile.php          # Edit user profile
│── feedback.php              # Feedback form processing
│── fooddonateform.php        # Donation form
│── home.html                 # Homepage
│── index.html                # Landing page
│── login.php                 # User login
│── logout.php                # Logout script
│── profile.php               # User profile page
│── setup_feedback_table.sql  # SQL script for feedback table
│── signin.php / signup.php   # User authentication pages
│
├── admin/                    # Admin dashboard files
├── chatbot/                  # Chatbot UI and scripts
└── assets/                   # CSS, JS, Images (if any)

⚙️ Installation

Clone the repository

bash
Copy code
git clone https://github.com/your-username/food-waste-management-system.git
Set up the database

Create a MySQL database.

Import provided .sql files (e.g., setup_feedback_table.sql).

Configure database connection

Edit connection.php and update:

php
Copy code
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "your_database_name";
Run the project

Place the project folder in htdocs (for XAMPP) or your web server’s root directory.

Start Apache and MySQL.

Visit:

perl
Copy code
http://localhost/food-waste-management-system-main/
📸 Screenshots
(Add screenshots of homepage, donation form, and admin panel.)

🤝 Contribution
Feel free to fork this repository, make improvements, and create a pull request.

📜 License
This project is open-source under the MIT License.
