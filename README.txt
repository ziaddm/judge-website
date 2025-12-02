GRADING SYSTEM - SETUP INSTRUCTIONS
====================================

1. START XAMPP
   - Open XAMPP Control Panel
   - Start Apache
   - Start MySQL

2. CREATE DATABASE
   - Go to: http://localhost/phpmyadmin
   - Click "SQL" tab
   - Copy and paste everything from setup_database.sql
   - Click "Go"

3. ACCESS THE SYSTEM
   - Login page: http://localhost/login.php

4. LOGIN CREDENTIALS
   Judges:
   - Username: judge1, Password: 123
   - Username: judge2, Password: 123
   - Username: judge3, Password: 123
   - Username: judge4, Password: 123

   Admin:
   - Username: admin, Password: 123

5. HOW IT WORKS
   - Judges login and fill out grading form
   - System auto-calculates total
   - Grades saved to database
   - Admin can view all grades and averages

FILES:
- login.php = Login page for judges and admin
- grade.php = Grading form (judges only)
- admin.php = View averages (admin only)
- db.php = Database connection
- logout.php = Logout functionality
- setup_database.sql = Database setup script
