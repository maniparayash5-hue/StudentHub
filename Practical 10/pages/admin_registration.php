<?php
require_once "../db.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $studentid = strtoupper(trim($_POST["studentid"] ?? ""));
    $fullname = trim($_POST["fullname"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm"] ?? "";

    $allowedCourses = [
        "Administration",
        "B.tech",
        "BSc IT",
        "BCom",
        "BBA"
    ];

    if (!preg_match("/^[0-9]{2}[A-Z]{3}[0-9]{3}$/", $studentid)) {
        $message = "Enter a valid ID, for example 25DCE003.";

    } elseif (
        strlen($fullname) < 3 ||
        strlen($fullname) > 100 ||
        !preg_match("/^[\p{L}][\p{L} .'-]*$/u", $fullname)
    ) {
        $message = "Enter a valid full name.";

    } elseif (
        strlen($email) > 254 ||
        !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {
        $message = "Enter a valid email address.";

    } elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {
        $message = "Mobile number must contain exactly 10 digits.";

    } elseif (!in_array($course, $allowedCourses, true)) {
        $message = "Please select a valid department.";

    } elseif (!is_string($password) || strlen($password) < 8) {
        $message = "Password must be at least 8 characters.";

    } elseif (strlen($password) > 4096) {
        $message = "Password is too long.";

    } elseif (!is_string($confirm) || $password !== $confirm) {
        $message = "Passwords do not match.";

    } else {
        try {
            // Check duplicate student ID or email.
            $check = $conn->prepare(
                "SELECT student_id
                 FROM students
                 WHERE student_id = :student_id
                    OR email = :email
                 LIMIT 1"
            );

            $check->execute([
                ":student_id" => $studentid,
                ":email" => $email
            ]);

            if ($check->fetch(PDO::FETCH_ASSOC)) {
                $message = "Student ID or email already exists.";

            } else {
                // Hash password before storing.
                $hashedPassword = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                if ($hashedPassword === false) {
                    throw new RuntimeException("Password hashing failed.");
                }

                // Create admin account.
                $sql = "INSERT INTO students
                        (
                            student_id,
                            full_name,
                            email,
                            mobile,
                            course,
                            password,
                            role
                        )
                        VALUES
                        (
                            :student_id,
                            :full_name,
                            :email,
                            :mobile,
                            :course,
                            :password,
                            'admin'
                        )";

                $stmt = $conn->prepare($sql);

                $stmt->execute([
                    ":student_id" => $studentid,
                    ":full_name" => $fullname,
                    ":email" => $email,
                    ":mobile" => $mobile,
                    ":course" => $course,
                    ":password" => $hashedPassword
                ]);

                $message = "Admin account created successfully!";
                $messageType = "success";
            }

            if ($messageType === "") {
                $messageType = "error";
            }

        } catch (PDOException $e) {
            error_log("Admin registration error: " . $e->getMessage());

            if ($e->getCode() === "23000") {
                $message = "Student ID or email already exists.";
            } else {
                $message = "Database error. Admin account was not created.";
            }

            $messageType = "error";

        } catch (RuntimeException $e) {
            error_log("Password hashing error: " . $e->getMessage());
            $message = "Could not create the account. Please try again.";
            $messageType = "error";
        }
    }

    if ($messageType === "") {
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>StudentHub - Admin Registration</title>

    <link rel="stylesheet" href="../CSS/style.css">

    <style>
        .admin-registration {
            max-width: 550px;
            margin: 30px auto;
            padding: 25px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.12);
        }

        .admin-registration h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            box-sizing: border-box;
            width: 100%;
            padding: 10px;
            border: 1px solid #cccccc;
            border-radius: 5px;
        }

        .role-display {
            display: inline-block;
            padding: 8px 12px;
            background: #eeeeee;
            border-radius: 5px;
        }

        .admin-message {
            padding: 12px;
            margin-bottom: 18px;
            text-align: center;
            border-radius: 5px;
        }

        .admin-message.success {
            background: #e7f7e9;
            color: #176329;
        }

        .admin-message.error {
            background: #fdeaea;
            color: #922020;
        }

        .form-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .form-buttons button {
            padding: 10px 16px;
            cursor: pointer;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>

<body>

    <header class="header">
        <div class="logo">
            <img src="../images/Charusat.png" alt="CHARUSAT Logo">
        </div>

        <div class="header-text">
            <h1>StudentHub Portal</h1>
            <p>Admin Registration</p>
        </div>
    </header>

    <main>
        <section class="admin-registration">

            <h2>Create Admin Account</h2>

            <?php if ($message !== ""): ?>
                <p
                    class="admin-message <?= htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8') ?>"
                    role="status"
                >
                    <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>

            <form method="POST" action="admin_registration.php">

                <div class="form-group">
                    <label for="studentid">Admin ID:</label>
                    <input
                        type="text"
                        id="studentid"
                        name="studentid"
                        maxlength="8"
                        placeholder="Example: 25DCE003"
                        pattern="[0-9]{2}[A-Z]{3}[0-9]{3}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="fullname">Full Name:</label>
                    <input
                        type="text"
                        id="fullname"
                        name="fullname"
                        minlength="3"
                        maxlength="100"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        maxlength="254"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="mobile">Mobile Number:</label>
                    <input
                        type="tel"
                        id="mobile"
                        name="mobile"
                        maxlength="10"
                        pattern="[0-9]{10}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="course">Department:</label>
                    <select id="course" name="course" required>
                        <option value="">Select Department</option>
                        <option value="Administration">Administration</option>
                        <option value="B.tech">B.tech</option>
                        <option value="BSc IT">BSc IT</option>
                        <option value="BCom">BCom</option>
                        <option value="BBA">BBA</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Role:</label>
                    <span class="role-display">Administrator</span>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        minlength="8"
                        maxlength="4096"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="confirm">Confirm Password:</label>
                    <input
                        type="password"
                        id="confirm"
                        name="confirm"
                        minlength="8"
                        maxlength="4096"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <div class="form-buttons">
                    <button type="submit">Create Admin</button>
                    <button type="reset">Reset</button>
                </div>

            </form>

            <a class="back-link" href="index.html">
                Back to Home
            </a>

        </section>
    </main>

    <footer>
        <p style="text-align:center;">
            © 2026 CHARUSAT University | StudentHub
        </p>
    </footer>

</body>
</html>


