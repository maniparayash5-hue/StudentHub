<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];
    $email = $_POST["email"];
    $mobile = $_POST["mobile"];
    $password = $_POST["password"];
    $course = $_POST["course"];
    $year = $_POST["year"];
    $gender = $_POST["gender"];
    $terms = isset($_POST["terms"]) ? "Accepted" : "Not Accepted";

    $file = "students.csv";

    $isNewFile = !file_exists($file);

    $handle = fopen($file, "a");

    if ($isNewFile) {
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
    echo "<p>Student data has been saved.</p>";
    echo "<a href='registration.html'>Back to Registration</a>";
}
?>