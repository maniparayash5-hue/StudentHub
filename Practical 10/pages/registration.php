<?php

session_start();

require_once "../db.php";

$message = "";

if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (
        !isset($_POST["csrf_token"]) ||
        $_POST["csrf_token"] != $_SESSION["csrf_token"]
    ) {

        $message = "Invalid form submission.";

    } else {

        $studentid = trim($_POST["studentid"] ?? "");
        $fullname = trim($_POST["fullname"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $mobile = trim($_POST["mobile"] ?? "");
        $course = trim($_POST["course"] ?? "");
        $year = trim($_POST["year"] ?? "");
        $gender = trim($_POST["gender"] ?? "");
        $terms = $_POST["terms"] ?? "";
        $password = $_POST["password"] ?? "";
        $confirm = $_POST["confirm"] ?? "";

        if ($studentid == "") {

            $message = "Enter Student ID";

        } elseif (
            !preg_match(
                "/^[0-9]{2}[A-Z]{3}[0-9]{3}$/",
                $studentid
            )
        ) {

            $message = "Student ID must be like 25DCE003";

        } elseif ($fullname == "") {

            $message = "Enter your name";

        } elseif (!preg_match("/^[A-Za-z ]{3,}$/", $fullname)) {

            $message = "Enter a valid name";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $message = "Enter a valid email";

        } elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {

            $message = "Mobile number must be 10 digits";

        } elseif ($course == "") {

            $message = "Select a course";

        } elseif ($year == "") {

            $message = "Select your year";

        } elseif ($gender == "") {

            $message = "Select your gender";

        } elseif ($terms != "Accepted") {

            $message = "Please accept the terms and conditions";

        } elseif (strlen($password) < 8) {

            $message = "Password must be at least 8 characters";

        } elseif ($password != $confirm) {

            $message = "Passwords do not match";

        } else {

            try {

                $check = $conn->prepare(
                    "SELECT student_id, email
                     FROM students
                     WHERE student_id = :student_id
                     OR email = :email"
                );

                $check->execute([
                    ":student_id" => $studentid,
                    ":email" => $email
                ]);

                if ($check->fetch()) {

                    $message = "Student ID or Email already exists";

                } else {

                    $hashedPassword = password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                    $sql = "INSERT INTO students
                            (
                                student_id,
                                full_name,
                                email,
                                mobile,
                                password,
                                course,
                                year,
                                gender,
                                terms
                            )
                            VALUES
                            (
                                :student_id,
                                :full_name,
                                :email,
                                :mobile,
                                :password,
                                :course,
                                :year,
                                :gender,
                                :terms
                            )";

                    $stmt = $conn->prepare($sql);

                    $stmt->execute([
                        ":student_id" => $studentid,
                        ":full_name" => $fullname,
                        ":email" => $email,
                        ":mobile" => $mobile,
                        ":password" => $hashedPassword,
                        ":course" => $course,
                        ":year" => $year,
                        ":gender" => $gender,
                        ":terms" => $terms
                    ]);

                    $dataFolder = "../data";

                    if (!is_dir($dataFolder)) {
                        mkdir($dataFolder, 0777, true);
                    }

                    $file = $dataFolder . "/students.csv";

                    $fileExists = file_exists($file);

                    $fp = fopen($file, "a");

                    if ($fp === false) {

                        $message =
                            "Saved in database but CSV file could not be opened.";

                    } else {

                        if (!$fileExists || filesize($file) == 0) {

                            fputcsv($fp, [
                                "Student ID",
                                "Full Name",
                                "Email",
                                "Mobile",
                                "Course",
                                "Year",
                                "Gender",
                                "Terms",
                                "Password"
                            ]);
                        }

                        fputcsv($fp, [
                            $studentid,
                            $fullname,
                            $email,
                            $mobile,
                            $course,
                            $year,
                            $gender,
                            $terms,
                            $hashedPassword
                        ]);

                        fclose($fp);

                        $message =
                            "Registration Successful! Data saved in CSV and Database.";
                    }
                }

            } catch (PDOException $e) {

                $message =
                    "Database error. Student was not registered.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>StudentHub - Register</title>

    <link
        rel="stylesheet"
        href="../CSS/style.css">

</head>

<body>

<header class="header">

    <div class="logo">

        <img
            src="../images/Charusat.png"
            alt="CHARUSAT Logo">

    </div>

    <div class="header-text">

        <h1>StudentHub Portal</h1>

    </div>

</header>

<main>

    <div class="form-box">

        <h2>Student Registration</h2>

        <?php

        if ($message != "") {

            echo "<p>";

            echo htmlspecialchars($message);

            echo "</p>";
        }

        ?>

        <form
            method="POST"
            action="registration.php">

            <input
                type="hidden"
                name="csrf_token"
                value="<?php
                    echo htmlspecialchars(
                        $_SESSION["csrf_token"]
                    );
                ?>">

            <label for="studentid">
                Student ID
            </label>

            <br>

            <input
                type="text"
                id="studentid"
                name="studentid"
                placeholder="Example: 25DCE001"
                maxlength="8"
                required>

            <br><br>

            <label for="fullname">
                Full Name
            </label>

            <br>

            <input
                type="text"
                id="fullname"
                name="fullname"
                placeholder="Enter your full name"
                required>

            <br><br>

            <label for="email">
                Email
            </label>

            <br>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your email"
                required>

            <br><br>

            <label for="mobile">
                Mobile Number
            </label>

            <br>

            <input
                type="tel"
                id="mobile"
                name="mobile"
                placeholder="Enter 10-digit mobile number"
                maxlength="10"
                required>

            <br><br>

            <label for="course">
                Course
            </label>

            <br>

            <select
                id="course"
                name="course"
                required>

                <option value="">
                    Select Course
                </option>

                <option value="B.tech">
                    B.tech
                </option>

                <option value="BSc IT">
                    BSc IT
                </option>

                <option value="BCom">
                    BCom
                </option>

                <option value="BBA">
                    BBA
                </option>

            </select>

            <br><br>

            <label for="year">
                Year
            </label>

            <br>

            <select
                id="year"
                name="year"
                required>

                <option value="">
                    Select Year
                </option>

                <option value="1">
                    First Year
                </option>

                <option value="2">
                    Second Year
                </option>

                <option value="3">
                    Third Year
                </option>

                <option value="4">
                    Fourth Year
                </option>

            </select>

            <br><br>

            <fieldset>

                <legend>Gender</legend>

                <label>

                    <input
                        type="radio"
                        name="gender"
                        value="Male"
                        required>

                    Male

                </label>

                <label>

                    <input
                        type="radio"
                        name="gender"
                        value="Female">

                    Female

                </label>

                <label>

                    <input
                        type="radio"
                        name="gender"
                        value="Other">

                    Other

                </label>

            </fieldset>

            <br>

            <label for="password">
                Password
            </label>

            <br>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter password"
                required>

            <br><br>

            <label for="confirm">
                Confirm Password
            </label>

            <br>

            <input
                type="password"
                id="confirm"
                name="confirm"
                placeholder="Confirm password"
                required>

            <br><br>

            <label>

                <input
                    type="checkbox"
                    name="terms"
                    value="Accepted"
                    required>

                I accept the terms and conditions.

            </label>

            <br><br>

            <button type="submit">
                Register
            </button>

            <button type="reset">
                Reset
            </button>

        </form>

        <p>

            <a href="login.php">
                Back to Login
            </a>

        </p>

    </div>

</main>

</body>

</html>