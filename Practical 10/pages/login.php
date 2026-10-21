
<?php
session_start();
require_once "../db.php";

// If already logged in, redirect to the correct dashboard.
if (isset($_SESSION["student_id"], $_SESSION["role"])) {
    if ($_SESSION["role"] === "admin") {
        header("Location: admin_dashboard.php");
        exit;
    } elseif ($_SESSION["role"] === "student") {
        header("Location: dashboard.php");
        exit;
    } else {
        // Invalid role: clear the session.
        $_SESSION = [];
        session_destroy();
    }
}

$message = "";

if (isset($_GET["timeout"])) {
    $message = "Session expired. Please login again.";
} elseif (isset($_GET["logout"])) {
    $message = "You have logged out successfully.";
}

// Generate CSRF token.
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["csrf_token"] ?? "";

    if (
        !is_string($token) ||
        !hash_equals($_SESSION["csrf_token"], $token)
    ) {
        $message = "Invalid form submission. Please refresh the page.";

    } else {
        $studentid = trim($_POST["studentid"] ?? "");
        $password = $_POST["password"] ?? "";

        if ($studentid === "" || $password === "") {
            $message = "Enter Student ID and password.";

        } else {
            try {
                $stmt = $conn->prepare(
                    "SELECT student_id, full_name, email, password,
                            course, year, gender, role
                     FROM students
                     WHERE student_id = :student_id
                     LIMIT 1"
                );

                $stmt->execute([
                    ":student_id" => $studentid
                ]);

                $student = $stmt->fetch(PDO::FETCH_ASSOC);

                if (
                    !$student ||
                    !password_verify($password, $student["password"])
                ) {
                    $message = "Invalid Student ID or password.";

                } elseif (
                    !in_array($student["role"], ["admin", "student"], true)
                ) {
                    $message = "This account has an invalid role.";

                } else {
                    // Prevent session fixation.
                    session_regenerate_id(true);

                    $_SESSION["student_id"] = $student["student_id"];
                    $_SESSION["full_name"] = $student["full_name"];
                    $_SESSION["email"] = $student["email"];
                    $_SESSION["course"] = $student["course"];
                    $_SESSION["year"] = $student["year"];
                    $_SESSION["gender"] = $student["gender"];
                    $_SESSION["role"] = $student["role"];
                    $_SESSION["last_activity"] = time();

                    // Refresh CSRF token.
                    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));

                    // Update last login time.
                    $update = $conn->prepare(
                        "UPDATE students
                         SET last_login = NOW()
                         WHERE student_id = :student_id"
                    );

                    $update->execute([
                        ":student_id" => $student["student_id"]
                    ]);

                    // Redirect based on account role.
                    if ($student["role"] === "admin") {
                        header("Location: admin_dashboard.php");
                        exit;
                    } else {
                        header("Location: dashboard.php");
                        exit;
                    }
                }

            } catch (PDOException $e) {
                error_log("Login error: " . $e->getMessage());
                $message = "Login failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>StudentHub - Login</title>

    <link rel="stylesheet" href="../CSS/style.css">
</head>

<body>

<header class="header">
    <div class="logo">
        <img src="../images/Charusat.png" alt="CHARUSAT Logo">
    </div>

    <div class="header-text">
        <h1>StudentHub Portal</h1>
    </div>
</header>

<main>
    <div class="form-box">
        <h2>Student / Admin Login</h2>

        <?php if ($message !== ""): ?>
            <p role="status">
                <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, "UTF-8") ?>"
            >

            <label for="studentid">Student / Admin ID</label><br>

            <input
                type="text"
                id="studentid"
                name="studentid"
                placeholder="Example: 25DCE001"
                maxlength="10"
                autocomplete="username"
                required
            >

            <br><br>

            <label for="password">Password</label><br>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter password"
                autocomplete="current-password"
                required
            >

            <br><br>

            <button type="submit">Login</button>
            <button type="reset">Reset</button>
        </form>

        <p>
            <a href="registration.php">New Student? Register Here</a>
        </p>

        <p>
            <a href="index.html">Back to Home</a>
        </p>
    </div>
</main>

</body>
</html>
