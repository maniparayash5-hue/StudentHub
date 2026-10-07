<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: registration.html");
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$password = $_POST["password"] ?? "";
$course = $_POST["course"] ?? "";
$year = $_POST["year"] ?? "";
$gender = $_POST["gender"] ?? "";

$terms = isset($_POST["terms"]) ? "Accepted" : "Not Accepted";


if ($name == "" || $email == "" || $mobile == "" ||
    $password == "" || $course == "" ||
    $year == "" || $gender == "") {

    die("Please fill all fields.");
}


$dataFolder = __DIR__ . "/../data";


if (!is_dir($dataFolder)) {

    mkdir($dataFolder, 0777, true);
}


$file = $dataFolder . "/register.csv";


$isNewFile = !file_exists($file);


$handle = fopen($file, "a");


if ($handle === false) {

    die("ERROR: Cannot create register.csv");
}


if ($isNewFile || filesize($file) == 0) {

    fputcsv($handle, [
        "Name",
        "Email",
        "Mobile",
        "Password",
        "Course",
        "Year",
        "Gender",
        "Terms"
    ]);
}


fputcsv($handle, [
    $name,
    $email,
    $mobile,
    $password,
    $course,
    $year,
    $gender,
    $terms
]);


fclose($handle);


echo "<h2>Registration Successful!</h2>";

echo "<p>Student data saved successfully.</p>";

echo "<p>CSV file created at:</p>";

echo "<b>data/register.csv</b>";

echo "<br><br>";

echo "<a href='registration.html'>Back to Registration</a>";

?>