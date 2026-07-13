<?php
require_once('includes/check-auth.php');
require_once('config/db.php');

// Determine which filter mode we're in
$range = $_GET['range'] ?? 'today';
$fromDate = $_GET['from'] ?? '';
$toDate = $_GET['to'] ?? '';

$where = "";
$activeRange = $range;

if ($range === 'custom' && $fromDate && $toDate) {
    $fromDate = $conn->real_escape_string($fromDate);
    $toDate = $conn->real_escape_string($toDate);
    $where = "WHERE a.date BETWEEN '$fromDate' AND '$toDate'";
} else {
    switch ($range) {
        case 'week':
            $where = "WHERE a.date >= CURDATE() - INTERVAL 7 DAY";
            break;
        case 'month':
            $where = "WHERE a.date >= CURDATE() - INTERVAL 30 DAY";
            break;
        case 'all':
            $where = ""; // no filter, show everything
            break;
        case 'today':
        default:
            $where = "WHERE a.date = CURDATE()";
            $activeRange = 'today';
            break;
    }
}

$records = $conn->query("
    SELECT u.name, a.date, a.clock_in, a.lunch_out, a.lunch_in, a.clock_out, a.is_late, a.lunch_late_minutes
    FROM attendance a
    JOIN users u ON a.user_id = u.id
    $where
    ORDER BY a.date DESC, a.clock_in DESC
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Records</title>
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
    background:#f5f6fa;
}

/* ===========================
   NAVBAR
=========================== */

.navbar{
    background:#111 !important;
    box-shadow:0 8px 20px rgba(0,0,0,.2);
    margin-bottom:35px;
}

.navbar-brand{
    color:#FFD700 !important;
    font-size:24px;
    font-weight:700;
}

.navbar .btn{
    border-radius:10px;
    font-weight:500;
}

/* Dashboard Buttons */

.btn-outline-light{
    color:#FFD700;
    border:1px solid #FFD700;
}

.btn-outline-light:hover{
    background:#FFD700;
    color:#111;
}

/* Logout */

.btn-danger{
    border-radius:10px;
}

/* ===========================
   PAGE TITLE
=========================== */

h3{
    font-weight:700;
    color:#222;
    margin-bottom:25px;
}

/* ===========================
   FILTER BUTTONS
=========================== */

.btn-group .btn{
    border-radius:8px !important;
    margin-right:8px;
}

.btn-primary{
    background:#FFD700;
    color:#111;
    border:none;
    font-weight:600;
}

.btn-primary:hover{
    background:#ffc107;
    color:#111;
}

.btn-outline-primary{
    border-color:#FFD700;
    color:#111;
}

.btn-outline-primary:hover{
    background:#FFD700;
    color:#111;
}

/* ===========================
   CUSTOM DATE FILTER
=========================== */

form{
    background:#fff;
    padding:20px;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
    margin-bottom:25px;
}

.form-control{
    border-radius:10px;
    border:1px solid #ddd;
}

.form-control:focus{
    border-color:#FFD700;
    box-shadow:0 0 0 .2rem rgba(255,215,0,.25);
}

.btn-secondary{
    background:#111;
    border:none;
}

.btn-secondary:hover{
    background:#FFD700;
    color:#111;
}

/* ===========================
   CARD
=========================== */

.card{
    border:none;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 15px 35px rgba(0,0,0,.08);
}

/* ===========================
   TABLE
=========================== */

.table{
    margin:0;
}

.table thead{
    background:#111;
    color:#FFD700;
}

.table thead th{
    padding:18px;
    border:none;
    font-weight:600;
}

.table tbody td{
    padding:18px;
    vertical-align:middle;
}

.table tbody tr{
    transition:.3s;
}

.table tbody tr:hover{
    background:#fff9e6;
}

/* Zebra rows */

.table tbody tr:nth-child(even){
    background:#fafafa;
}

/* ===========================
   BADGES
=========================== */

.badge{
    padding:7px 10px;
    border-radius:30px;
    font-size:12px;
}

.bg-success{
    background:#198754 !important;
}

.bg-secondary{
    background:#6c757d !important;
}

.bg-danger{
    background:#dc3545 !important;
}

.bg-warning{
    background:#FFD700 !important;
    color:#111 !important;
}

/* ===========================
   SCROLLBAR
=========================== */

::-webkit-scrollbar{
    width:10px;
}

::-webkit-scrollbar-thumb{
    background:#FFD700;
    border-radius:20px;
}

::-webkit-scrollbar-track{
    background:#eee;
}

/* ===========================
   RESPONSIVE
=========================== */

@media(max-width:768px){

    .navbar .container{
        flex-direction:column;
        gap:15px;
    }

    .btn-group{
        display:flex;
        flex-wrap:wrap;
        gap:8px;
    }

    .table{
        font-size:14px;
    }

}
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Vital Consultants</a>
        <div>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
            <a href="records.php" class="btn btn-outline-light btn-sm me-2">Records</a>
            <a href="add_employee.php" class="btn btn-outline-light btn-sm me-2">Add Employee</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <h3 class="mb-4">Attendance Records</h3>

    <!-- Quick filter buttons -->
    <div class="btn-group mb-3">
        <a href="records.php?range=today" class="btn btn-sm <?= $activeRange === 'today' ? 'btn-primary' : 'btn-outline-primary' ?>">Today</a>
        <a href="records.php?range=week" class="btn btn-sm <?= $activeRange === 'week' ? 'btn-primary' : 'btn-outline-primary' ?>">Last 7 Days</a>
        <a href="records.php?range=month" class="btn btn-sm <?= $activeRange === 'month' ? 'btn-primary' : 'btn-outline-primary' ?>">Last 30 Days</a>
        <a href="records.php?range=all" class="btn btn-sm <?= $activeRange === 'all' ? 'btn-primary' : 'btn-outline-primary' ?>">All Time</a>
    </div>

    <!-- Custom date range -->
    <form method="GET" action="records.php" class="row g-2 mb-4 align-items-center">
        <input type="hidden" name="range" value="custom">
        <div class="col-auto">
            <label class="form-label mb-0 me-1">From</label>
            <input type="date" name="from" value="<?= htmlspecialchars($fromDate) ?>" class="form-control" required>
        </div>
        <div class="col-auto">
            <label class="form-label mb-0 me-1">To</label>
            <input type="date" name="to" value="<?= htmlspecialchars($toDate) ?>" class="form-control" required>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-secondary">Apply Custom Range</button>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Clock In</th>
                        <th>Launch Out</th>
                        <th>Launch In</th>
                        <th>Clock Out</th>
                        <th>Status</th>
                        
                    </tr>
                    
                    
                    
                </thead>
                <tbody>
                    <?php if ($records->num_rows === 0): ?>
                        <tr><td colspan="5" class="text-center text-muted">No records found for this period.</td></tr>
                    <?php else: ?>
                       <?php while ($row = $records->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= date('d M Y', strtotime($row['date'])) ?></td>
        <td>
            <?= $row['clock_in'] ? date('h:i A', strtotime($row['clock_in'])) : '-' ?>
            <?php if ($row['is_late']): ?>
                <span class="badge bg-danger ms-1">Late</span>
                   <?php endif; ?>
             </td>
             <td><?= $row['lunch_out'] ? date('h:i A', strtotime($row['lunch_out'])) : '-' ?></td>
        <td>
            <?= $row['lunch_in'] ? date('h:i A', strtotime($row['lunch_in'])) : '-' ?>
            <?php if ($row['lunch_late_minutes'] > 0): ?>
                <span class="badge bg-warning text-dark ms-1"><?= $row['lunch_late_minutes'] ?> min late</span>
            <?php endif; ?>
        </td>
        <td><?= $row['clock_out'] ? date('h:i A', strtotime($row['clock_out'])) : '-' ?></td>
        <td>
            <?php if ($row['clock_out'] === null): ?>
                <span class="badge bg-success">Still In</span>
            <?php else: ?>
                <span class="badge bg-secondary">Completed</span>
            <?php endif; ?>
        </td>
    </tr>
<?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>