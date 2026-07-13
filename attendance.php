<?php
require_once('config/db.php');

$message = "";
$message_type = ""; // "success" or "error"
$playWelcomeSound = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pin = trim($_POST['pin'] ?? '');

    if ($pin === '') {
        $message = "Please enter your PIN.";
        $message_type = "error";
    } else {
        // Look up the employee by PIN
        $stmt = $conn->prepare("SELECT id, name FROM users WHERE pin = ?");
        $stmt->bind_param("s", $pin);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $message = "Invalid PIN. Please try again.";
            $message_type = "error";
        } else {
            $user = $result->fetch_assoc();
            $user_id = $user['id'];
            $name = $user['name'];
            $today = date('Y-m-d');
            $now = date('H:i:s');


            // Check today's attendance record
            $check = $conn->prepare("SELECT id, clock_in, lunch_out, lunch_in, clock_out FROM attendance WHERE user_id = ? AND date = ? ORDER BY id DESC LIMIT 1");
            $check->bind_param("is", $user_id, $today);
            $check->execute();
            $checkResult = $check->get_result();

            if ($checkResult->num_rows > 0) {
                $record = $checkResult->fetch_assoc();

                if ($record['lunch_out'] === null) {
                    // Clocked in, haven't gone to lunch yet -> LUNCH OUT
                    $update = $conn->prepare("UPDATE attendance SET lunch_out = ? WHERE id = ?");
                    $update->bind_param("si", $now, $record['id']);
                    $update->execute();

                    $message = "{$name}, enjoy your lunch! Lunch started at " . date('h:i A', strtotime($now));
                    $message_type = "success";

                } elseif ($record['lunch_in'] === null) {
                    // On lunch, hasn't returned yet -> LUNCH IN
                    $lunchOutTime = strtotime($record['lunch_out']);
                    $lunchInTime = strtotime($now);
                    $minutesOnLunch = round(($lunchInTime - $lunchOutTime) / 60);
                    $lunchLateMinutes = max(0, $minutesOnLunch - 45);

                    $update = $conn->prepare("UPDATE attendance SET lunch_in = ?, lunch_late_minutes = ? WHERE id = ?");
                    $update->bind_param("sii", $now, $lunchLateMinutes, $record['id']);
                    $update->execute();

                    if ($lunchLateMinutes > 0) {
                        $message = "Welcome back {$name}! Lunch ended at " . date('h:i A', strtotime($now)) . " — you're {$lunchLateMinutes} min late from lunch.";
                        $message_type = "error";
                    } else {
                        $message = "Welcome back {$name}! Lunch ended at " . date('h:i A', strtotime($now)) . " ({$minutesOnLunch} min lunch)";
                        $message_type = "success";
                    }

                } elseif ($record['clock_out'] === null) {
                    // Back from lunch, hasn't clocked out -> CLOCK OUT
                    $update = $conn->prepare("UPDATE attendance SET clock_out = ? WHERE id = ?");
                    $update->bind_param("si", $now, $record['id']);
                    $update->execute();

                    $goodbyeMessages = [
                        "Bye {$name}, hv a great evening! Come prepared tomorrow.",
                        "Sharp sharp {$name}, enjoy your evening! See you tomorrow.",
                        "Later {$name}! Enjoy your evening and come through ready tomorrow.",
                        "Bye {$name}, hv a lekker evening! Rest up for tomorrow.",
                        "Great work today, {$name}! Catch you tomorrow.",
                        "Thanks for today, {$name}! Go rest, izobonana.",
                    ];

                    $message = $goodbyeMessages[array_rand($goodbyeMessages)] . " Clocked out at " . date('h:i A', strtotime($now));
                    $message_type = "success";

                } else {
                    // Already completed the full cycle today
                    $message = "{$name}, you've already completed your shift today.";
                    $message_type = "error";
                }
            } else {
                // No record today -> CLOCK IN
                $cutoff = "07:00:00";
                $isLate = ($now > $cutoff) ? 1 : 0;

                $insert = $conn->prepare("INSERT INTO attendance (user_id, date, clock_in, is_late) VALUES (?, ?, ?, ?)");
                $insert->bind_param("issi", $user_id, $today, $now, $isLate);
                $insert->execute();

                if ($isLate) {
                    $minutesLate = round((strtotime($now) - strtotime($cutoff)) / 60);
                    $message = "Welcome {$name}, clocked in at " . date('h:i A', strtotime($now)) . " — you're {$minutesLate} min late.";
                    $message_type = "error";
                } else {
                    $message = "Welcome {$name}, clocked in at " . date('h:i A', strtotime($now));
                    $message_type = "success";
                }
                $playWelcomeSound = true;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Clock In/Out</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

html, body{
    height:100%;
    margin:0;
}

body{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#0d0d0d,#1a1a1a,#111);
    position:relative;
    overflow:hidden;
}

/* Background glow */

body::before{
    content:'';
    position:absolute;
    width:450px;
    height:450px;
    background:#FFD700;
    border-radius:50%;
    filter:blur(180px);
    opacity:.12;
    top:-150px;
    left:-150px;
}

body::after{
    content:'';
    position:absolute;
    width:350px;
    height:350px;
    background:#FFD700;
    border-radius:50%;
    filter:blur(160px);
    opacity:.08;
    bottom:-120px;
    right:-120px;
}
.pin-input{

transition:.3s;

}

.pin-input:focus{

transform:scale(1.03);

box-shadow:0 0 25px rgba(255,215,0,.4);

}
.btn-primary{
    transition:.3s;
}

.btn-primary:hover{

transform:
translateY(-3px)
scale(1.03);

box-shadow:0 12px 30px rgba(255,215,0,.45);

}

/* Card */

.clock-card{
    width:100%;
    max-width:450px;
    padding:45px;
    border-radius:25px;
    background:rgba(255,255,255,.95);
    backdrop-filter:blur(10px);
    text-align:center;
    position:relative;
    z-index:10;

    transition:all .4s ease;
    transform-style:preserve-3d;

    animation:
        float 4s ease-in-out infinite,
        glow 2.5s ease-in-out infinite alternate;
}
.clock-card:hover{

transform:
translateY(-8px)
rotateX(6deg)
rotateY(-6deg)
scale(1.02);

}
@keyframes float{

0%{
transform:translateY(0px);
}

50%{
transform:translateY(-12px);
}

100%{
transform:translateY(0px);
}

}
@keyframes shake{

0%{transform:translateX(0);}
20%{transform:translateX(-8px);}
40%{transform:translateX(8px);}
60%{transform:translateX(-8px);}
80%{transform:translateX(8px);}
100%{transform:translateX(0);}

}

.shake{

animation:shake .4s;

}
/* Logo */

.logo{
    width:90px;
    height:90px;
    margin:auto;
    margin-bottom:20px;
    border-radius:50%;
    background:#FFD700;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:40px;
    color:#111;
    box-shadow:0 10px 25px rgba(255,215,0,.35);
}

/* Heading */

.clock-card h3{
    font-weight:700;
    color:#111;
}

.clock-card p{
    color:#777;
}

/* PIN */

.pin-input{
    height:60px;
    font-size:30px;
    text-align:center;
    letter-spacing:12px;
    border-radius:15px;
    border:2px solid #ddd;
}

.pin-input:focus{
    border-color:#FFD700;
    box-shadow:0 0 0 .25rem rgba(255,215,0,.25);
}

/* Button */

.btn-primary{
    height:55px;
    background:#FFD700;
    color:#111;
    border:none;
    border-radius:15px;
    font-weight:600;
    font-size:18px;
}

.btn-primary:hover{
    background:#FFC107;
    color:#111;
    transform:translateY(-3px);
}

/* Alerts */

.alert-success{
    background:#d1e7dd;
    border:none;
}

.alert-danger{
    border:none;
}

/* Animation */

@keyframes fadeUp{

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
<body>

<div class="clock-card <?= $message_type === 'error' ? 'shake' : '' ?>">
    <div class="logo"><img src="logo.png" class="logo"></div>
<h3 class="mb-4"></h3>


    <?php if ($message): ?>
        <div class="alert alert-<?= $message_type === 'success' ? 'success' : 'danger' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="attendance.php">
        <input
            type="password"
            name="pin"
            class="form-control pin-input mb-3"
            maxlength="4"
            placeholder="----"
            autofocus
            required
        >
        <button type="submit" class="btn btn-primary btn-lg w-100">Clock In / Out</button>
    </form>
</div>
<?php if ($playWelcomeSound): ?>
<audio src="sounds/hello.mp3" autoplay></audio>
<?php endif; ?>

</body>
</html>