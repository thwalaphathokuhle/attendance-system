# Employee Attendance Management System

A modern web-based Employee Attendance Management System built with **PHP**, **MySQL**, **Bootstrap 5**, and **JavaScript**. The system enables employees to record attendance using a secure 4-digit PIN while allowing administrators to manage employees and monitor attendance records.

---

## Features

### Employee Attendance
- Secure 4-digit PIN authentication
- Clock In
- Lunch Out
- Lunch In
- Clock Out
- Automatic attendance tracking
- Late arrival detection
- Lunch duration tracking
- Lunch lateness calculation
- Welcome and goodbye messages
- Welcome audio on successful clock-in

### Employee Management
- Add new employees
- Unique PIN validation
- Prevent duplicate PINs
- Employee database management

### Admin Features
- Dashboard
- Attendance reports
- Export attendance reports
- View employee attendance history

### User Experience
- Modern glassmorphism interface
- Responsive design using Bootstrap 5
- Animated login card
- Company branding
- Custom welcome and goodbye messages
- Sound notifications
- Input validation
- Error handling

---

## Technologies Used

- PHP
- MySQL
- HTML5
- CSS3
- Bootstrap 5
- JavaScript
- XAMPP

---

## Project Structure

```
system-attendance/
│
├── config/
├── sounds/
├── images/
├── attendance.php
├── add_employee.php
├── dashboard.php
├── export_report.php
├── login.php
├── index.php
├── attendance_system.sql
└── README.md
```

---

## Installation

1. Clone this repository.

```
git clone https://github.com/yourusername/employee-attendance-system.git
```

2. Import the SQL database into phpMyAdmin.

3. Update the database connection inside:

```
config/db.php
```

4. Start Apache and MySQL using XAMPP.

5. Open the project in your browser.

---

## Future Improvements

- Employee profile pictures
- QR Code attendance
- Fingerprint integration
- Face recognition
- Email notifications
- SMS notifications
- Attendance analytics dashboard
- Monthly and yearly attendance statistics
- Mobile application
- Leave management
- Shift scheduling

---

## Screenshots

*Login Page*

*Attendance Screen*

*Admin Dashboard*

---

## Author

**Phathokuhle Siyathokoza Thwala**

Bachelor of Commerce in Management Information Systems

University of Zululand

---

## License

This project was developed for educational and portfolio purposes.
