<?php
require_once('includes/check-auth.php');
require_once('config/db.php');

$today = date('Y-m-d');

// Total employees
$totalEmployees = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'employee'")->fetch_assoc()['total'];

// Clocked in today (clock_in filled, clock_out still empty)
$clockedInToday = $conn->query("SELECT COUNT(*) AS total FROM attendance WHERE date = '$today' AND clock_out IS NULL")->fetch_assoc()['total'];

// Total attendance records today (clocked in at some point today, whether or not they've clocked out)
$totalTodayRecords = $conn->query("SELECT COUNT(*) AS total FROM attendance WHERE date = '$today'")->fetch_assoc()['total'];

// Recent activity - last 5 clock events today
$recent = $conn->query("
    SELECT u.name, a.clock_in, a.lunch_out, a.lunch_in, a.clock_out
    FROM attendance a
    JOIN users u ON a.user_id = u.id
    WHERE a.date = '$today'
    ORDER BY a.id DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#f5f5f5;
}

/* Navbar */
.navbar{
    background:#111111 !important;
    margin-bottom:30px;
    box-shadow:0 8px 20px rgba(0,0,0,.25);
}

.navbar-brand,
.nav-link{
    color:#FFD700 !important;
    font-weight:600;
}

.nav-link:hover{
    color:#ffffff !important;
}

/* Dashboard Cards */
.stat-card{
    border-radius:18px;
    padding:30px;
    color:white;
    transition:.35s;
    position:relative;
    overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,.2);
}

.stat-card:hover{
    transform:translateY(-8px);
    box-shadow:0 20px 40px rgba(0,0,0,.3);
}

.stat-card h2{
    font-size:3rem;
    font-weight:700;
    margin:0;
}

.stat-card p{
    margin-top:8px;
    opacity:.9;
    font-size:1rem;
}

/* Decorative Circle */
.stat-card::before{
    content:'';
    position:absolute;
    width:180px;
    height:180px;
    background:rgba(255,255,255,.08);
    border-radius:50%;
    top:-70px;
    right:-70px;
}

/* Card Colours */

.bg-blue{
    background:linear-gradient(135deg,#111111,#FFD700);
}

.bg-green{
    background:linear-gradient(135deg,#1c1c1c,#FFC107);
}

.bg-orange{
    background:linear-gradient(135deg,#000000,#FFB300);
}

/* General Cards */
.card{
    border:none;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,.12);
}

/* Tables */
.table{
    background:#fff;
    border-radius:12px;
    overflow:hidden;
}

.table thead{
    background:#111111;
    color:#FFD700;
}

/* Buttons */
.btn-primary{
    background:#FFD700;
    color:#111;
    border:none;
    font-weight:600;
}

.btn-primary:hover{
    background:#FFC107;
    color:#000;
}

/* Export Card */
.export-card{
    background:rgba(255,255,255,.95);
    border-radius:15px;
    padding:20px 25px;
    max-width:500px;
    box-shadow:0 8px 20px rgba(0,0,0,.15);
}

.export-card h5{
    font-weight:700;
    color:#111;
    margin-bottom:15px;
}

.export-card .form-label{
    font-size:13px;
    color:#555;
    font-weight:600;
}

.export-card .btn-primary{
    background:#FFD700;
    color:#111;
    border:none;
    font-weight:600;
}

.export-card .btn-primary:hover{
    background:#FFC107;
    color:#111;
}

/* Scrollbar */
::-webkit-scrollbar{
    width:10px;
}

::-webkit-scrollbar-thumb{
    background:#FFD700;
    border-radius:20px;
}

::-webkit-scrollbar-track{
    background:#222;
}
.footer{
    background:#111;
    color:#FFD700;
    padding:20px 0;
    margin-top:50px;
    border-top:4px solid #FFD700;
    box-shadow:0 -5px 20px rgba(0,0,0,.15);
}

.footer h5{
    margin:0;
    font-weight:600;
    color:#FFD700;
}

.footer small,
.footer p{
    color:#ddd;
    margin:0;
}

.footer strong{
    color:#FFD700;
}
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Attendance System</a>
        <div>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
            <a href="records.php" class="btn btn-outline-light btn-sm me-2">Records</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <h3 class="mb-4">Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h3>

    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stat-card bg-blue">
                <p>Total Employees</p>
                <h2><?= $totalEmployees ?></h2>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card bg-green">
                <p>Currently Clocked In</p>
                <h2><?= $clockedInToday ?></h2>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card bg-orange">
                <p>Total Check-ins Today</p>
                <h2><?= $totalTodayRecords ?></h2>
            </div>
        </div>
    </div>

    <!-- Export Report Card -->
    <div class="export-card mb-4">
        <h5>Export Attendance Report</h5>
        <form method="GET" action="export_report.php" target="_blank">
            <div class="row g-2 align-items-end">
                <div class="col-auto">
                    <label for="start_date" class="form-label">From</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" required>
                </div>
                <div class="col-auto">
                    <label for="end_date" class="form-label">To</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Download PDF</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            Recent Activity (Today)
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Clock In</th>
                        <th>Lunch Out</th>
                        <th>Lunch In</th>
                        <th>Clock Out</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recent->num_rows === 0): ?>
                        <tr><td colspan="5" class="text-center text-muted">No activity yet today.</td></tr>
                    <?php else: ?>
                        <?php while ($row = $recent->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= $row['clock_in'] ? date('h:i A', strtotime($row['clock_in'])) : '-' ?></td>
                                <td><?= $row['lunch_out'] ? date('h:i A', strtotime($row['lunch_out'])) : '-' ?></td>
                                <td><?= $row['lunch_in'] ? date('h:i A', strtotime($row['lunch_in'])) : '-' ?></td>
                                <td><?= $row['clock_out'] ? date('h:i A', strtotime($row['clock_out'])) : '-' ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-md-4 text-center text-md-start">
                <h5>Vital Consultants</h5>
                <small>Attendance Management System</small>
            </div>

            <div class="col-md-4 text-center">
                <p class="mb-0">
                    &copy; <?php echo date("Y"); ?> Vital Consultants.
                    All Rights Reserved.
                </p>
            </div>

            <div class="col-md-4 text-center text-md-end">
                <small>
                    Developed by <a href="mailto:thwalaphathokuhle141@gmail.com">Phathokuhle Thwala</a>
                </small>
            </div>

        </div>
    </div>
</footer>

</body>
</html>