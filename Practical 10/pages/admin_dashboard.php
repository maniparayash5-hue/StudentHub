<?php
require_once "auth.php";

// Allow only admin users
if (($_SESSION["role"] ?? "") !== "admin") {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Admin Dashboard</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>

<main>
    <div class="dashboard">
        <h1>Admin Dashboard</h1>

        <h2>
            Welcome,
            <?php echo htmlspecialchars($_SESSION["full_name"]); ?>!
        </h2>

        <p>
            Student ID:
            <?php echo htmlspecialchars($_SESSION["student_id"]); ?>
        </p>

        <p>Role: Admin</p>

        <h3>Administration Panel</h3>
        <p>You have administrator access.</p>

        <p>
            <a href="dashboard.php">Student Dashboard</a>
        </p>

        <p>
            <a href="logout.php">Logout</a>
        </p>
    </div>
</main>

</body>
</html>