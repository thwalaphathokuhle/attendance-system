<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vital Consultants Attendance System</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#0f0f0f,#1c1c1c,#2b2b2b);
    overflow:hidden;
}

/* Background glow */
body::before{
    content:'';
    position:absolute;
    width:500px;
    height:500px;
    background:#FFD700;
    border-radius:50%;
    filter:blur(180px);
    opacity:.15;
    top:-150px;
    left:-150px;
}

body::after{
    content:'';
    position:absolute;
    width:400px;
    height:400px;
    background:#FFD700;
    border-radius:50%;
    filter:blur(160px);
    opacity:.08;
    bottom:-150px;
    right:-150px;
}

/* Main Card */

.card{
    position:relative;
    z-index:2;
    border:none;
    border-radius:25px;
    background:#ffffff;
    box-shadow:0 25px 60px rgba(0,0,0,.35);
    animation:fadeUp .8s ease;
}

/* Logo */

.logo{
    width:90px;
    height:90px;
    margin:auto;
    margin-bottom:20px;
    border-radius:50%;
    background:#FFD700;
    color:#111;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:40px;
    box-shadow:0 10px 30px rgba(255,215,0,.5);
}

/* Title */

h2{
    font-weight:700;
    color:#111;
}

.text-muted{
    font-size:15px;
}

/* Buttons */

.btn{
    border-radius:15px;
    height:58px;
    font-size:18px;
    font-weight:600;
    transition:.35s;
}

.btn i{
    margin-right:8px;
}

/* Employee */

.btn-primary{
    background:#FFD700;
    color:#111;
    border:none;
}

.btn-primary:hover{
    background:#FFC107;
    color:#111;
    transform:translateY(-3px);
    box-shadow:0 12px 25px rgba(255,193,7,.35);
}

/* Admin */

.btn-dark{
    background:#111;
    border:none;
}

.btn-dark:hover{
    background:#333;
    transform:translateY(-3px);
}

/* Divider */

hr{
    margin:30px 0;
}

/* Footer */

small{
    color:#666;
}

/* Animation */

@keyframes fadeUp{

    from{
        opacity:0;
        transform:translateY(30px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }

}

/* Mobile */

@media(max-width:768px){

    .card{
        margin:20px;
        padding:35px !important;
    }

    h2{
        font-size:26px;
    }

}
</style>
</head>
<body class="bg-light">

<div class="container vh-100 d-flex justify-content-center align-items-center">

    <div class="card shadow-lg p-5 text-center" style="max-width:500px; width:100%; border-radius:15px;">

        <h2 class="mb-2">Vital Consultants Attendance System</h2>

        <p class="text-muted mb-4">
            Please select how you would like to continue.
        </p>

        <div class="d-grid gap-3">

          <a href="attendance.php" class="btn btn-primary btn-lg">
    <i class="bi bi-person-fill"></i>
    Employee Login
</a>

<a href="login.php" class="btn btn-dark btn-lg">
    <i class="bi bi-shield-lock-fill"></i>
    Administrator Login
</a>
        </div>

        <hr>

        <small class="text-muted">
            &copy; <?php echo date('Y'); ?> Vital Consultants. All Rights Reserved.
        </small>

    </div>

</div>

</body>
</html>