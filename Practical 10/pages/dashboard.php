
<?php
session_start();

// Check whether the user is logged in
if (!isset($_SESSION["student_id"])) {
    header("Location: login.php");
    exit;
}

// Session timeout: 15 minutes
$timeout = 900;

if (
    !isset($_SESSION["last_activity"]) ||
    (time() - $_SESSION["last_activity"]) > $timeout
) {
    $_SESSION = [];
    session_destroy();

    header("Location: login.php?timeout=1");
    exit;
}

// Update last activity time
$_SESSION["last_activity"] = time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>StudentHub - Dashboard</title>

    <link rel="stylesheet" href="../CSS/style.css">
    <script src="../js/script.js" defer></script>
</head>

<body>
    <main>
        <div class="dashboard">

            <h1>
                Welcome,
                <?php echo htmlspecialchars($_SESSION["full_name"]); ?>!
            </h1>

            <p>
                Student ID:
                <?php echo htmlspecialchars($_SESSION["student_id"]); ?>
            </p>

            <p>
                Role:
                <?php echo htmlspecialchars($_SESSION["role"]); ?>
            </p>

            <button id="menuButton" type="button">☰ Menu</button>

            <div class="dashboard-menu">

                <a href="profile.html">
                    <button type="button">Profile</button>
                </a>

                <a href="courses.html">
                    <button type="button">Course Selection</button>
                </a>

                <a href="result.html">
                    <button type="button">Result</button>
                </a>

                <a href="assignment.html">
                    <button type="button">Assignment</button>
                </a>

                <a href="attendance.html">
                    <button type="button">Attendance</button>
                </a>

                <a href="contact.html">
                    <button type="button">Contact</button>
                </a>

                <a href="FAQ.html">
                    <button type="button">FAQ</button>
                </a>

                <a href="event_register.php">
                    <button type="button">Event Register</button>
                </a>

                <a href="events.php">
                    <button type="button">Add Event</button>
                </a>

            </div>

            <p>
                <a href="logout.php">Logout</a>
            </p>

        </div>
    </main>
</body>
</html>
