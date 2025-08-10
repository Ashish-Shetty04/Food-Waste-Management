# Food Waste Management System

## 📌 Description
The **Food Waste Management System** is a web-based platform designed to connect food donors, delivery partners, and receivers to reduce food wastage. It allows users to donate excess food, track deliveries, and provide feedback. The system includes an admin dashboard for managing users, donations, and feedback.

---

## 🚀 Features
- User authentication (Sign in / Sign up)
- Food donation form with details
- Admin dashboard for managing records
- Feedback system
- Chatbot assistance
- Profile management
- Contact and About pages
- Delivery information management

---

## 📂 Project Structure
```plaintext
food-waste-management-system-main/
│── about.html                # About page
│── connection.php            # Database connection
│── contact.html              # Contact page
│── delete_donation.php       # Remove donation record
│── delivery.html              # Delivery info page
│── edit_profile.php           # Edit user profile
│── feedback.php               # Feedback form processing
│── fooddonateform.php         # Donation form
│── home.html                  # Homepage
│── index.html                 # Landing page
│── login.php                  # User login
│── logout.php                 # Logout script
│── profile.php                # User profile page
│── setup_feedback_table.sql   # SQL script for feedback table
│── signin.php / signup.php    # User authentication pages
│
├── admin/                     # Admin dashboard files
├── chatbot/                   # Chatbot UI and scripts
└── assets/                    # CSS, JS, Images (if any)
```

---

## 🛠️ Installation (Using XAMPP)
1. Download and install [XAMPP](https://www.apachefriends.org/)
2. Place the `food-waste-management-system-main` folder inside the `htdocs` directory of XAMPP
3. Start **Apache** and **MySQL** from the XAMPP Control Panel
4. Import the SQL files in `phpMyAdmin` to set up the database
5. Open the project in your browser:
   ```
   http://localhost/food-waste-management-system-main/
   ```

---

## 📜 License
This project is licensed under the [MIT License](LICENSE).
