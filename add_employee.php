<?php
require_once('includes/check-auth.php');
require_once('config/db.php');

$message = "";
$message_type = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $pin = trim($_POST['pin'] ?? '');

    if ($name === '' || $pin === '') {
        $message = "Please fill in all fields.";
        $message_type = "error";
    } elseif (!ctype_digit($pin) || strlen($pin) !== 4) {
        $message = "PIN must be exactly 4 digits.";
        $message_type = "error";
    } else {
        // Check PIN isn't already taken
        $check = $conn->prepare("SELECT id FROM users WHERE pin = ?");
        $check->bind_param("s", $pin);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $message = "That PIN is already in use. Please choose another.";
            $message_type = "error";
        } else {
         $stmt = $conn->prepare("INSERT INTO users (name, pin, role) VALUES (?, ?, 'employee')");
            $stmt->bind_param("ss", $name, $pin);
            $stmt->execute();

            $message = "Employee '{$name}' added successfully.";
            $message_type = "success";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<head>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:linear-gradient(135deg,#111,#2b2b2b);
    min-height:100vh;
}

/* Card */

.card{
    border:none;
    border-radius:20px;
    background:#fff;
    box-shadow:0 20px 40px rgba(0,0,0,.35);
    animation:fadeIn .6s ease;
}

/* Title */

h3{
    font-weight:700;
    color:#111;
    margin-bottom:25px;
}

/* Labels */

.form-label{
    font-weight:600;
    color:#333;
}

/* Inputs */

.form-control{
    height:50px;
    border-radius:12px;
    border:2px solid #ddd;
    transition:.3s;
}

.form-control:focus{
    border-color:#FFD700;
    box-shadow:0 0 0 .25rem rgba(255,215,0,.25);
}

/* Button */

.btn-primary{
    background:#FFD700;
    color:#111;
    border:none;
    border-radius:12px;
    height:50px;
    font-size:17px;
    font-weight:600;
    transition:.3s;
}

.btn-primary:hover{
    background:#FFC107;
    color:#111;
    transform:translateY(-2px);
    box-shadow:0 10px 20px rgba(255,193,7,.35);
}

/* Alerts */

.alert-success{
    background:#d1e7dd;
    border:none;
    color:#0f5132;
    border-radius:10px;
}

.alert-danger{
    background:#f8d7da;
    border:none;
    color:#842029;
    border-radius:10px;
}

/* Optional logo circle */

.logo-circle{
    width:80px;
    height:80px;
    background:#FFD700;
    color:#111;
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:32px;
    font-weight:700;
    margin:0 auto 20px;
    box-shadow:0 10px 25px rgba(255,215,0,.4);
}

/* Animation */

@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(20px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}
</style>
</head>
<body class="bg-light">

<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="card shadow-lg p-5" style="max-width:450px; width:100%; border-radius:15px;">
        <h3 class="mb-4 text-center">Add Employee</h3>

        <?php if ($message): ?>
            <div class="alert alert-<?= $message_type === 'success' ? 'success' : 'danger' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="add_employee.php">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">4-Digit PIN</label>
                <input type="text" name="pin" class="form-control" maxlength="4" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Add Employee</button>
        </form>
    </div>
</div>

</body>
</html>