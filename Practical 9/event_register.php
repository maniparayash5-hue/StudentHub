<?php

require_once "../db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_id = trim($_POST["student_id"] ?? "");
    $event_id = trim($_POST["event_id"] ?? "");

    if ($student_id == "") {

        $message = "Please enter Student ID.";

    }
    elseif ($event_id == "") {

        $message = "Please select an event.";

    }
    else {

        try {

            // Check if student exists

            $student = $conn->prepare(
                "SELECT student_id
                 FROM students
                 WHERE student_id = :student_id"
            );

            $student->execute([
                ":student_id" => $student_id
            ]);


            if (!$student->fetch()) {

                $message = "Student ID not found.";

            }
            else {

                // Check if already registered

                $check = $conn->prepare(
                    "SELECT registration_id
                     FROM registrations
                     WHERE student_id = :student_id
                     AND event_id = :event_id"
                );

                $check->execute([
                    ":student_id" => $student_id,
                    ":event_id" => $event_id
                ]);


                if ($check->fetch()) {

                    $message =
                        "Student is already registered for this event.";

                }
                else {

                    // Register student

                    $sql = "INSERT INTO registrations
                            (student_id, event_id)
                            VALUES
                            (:student_id, :event_id)";

                    $stmt = $conn->prepare($sql);

                    $stmt->execute([
                        ":student_id" => $student_id,
                        ":event_id" => $event_id
                    ]);

                    $message =
                        "Student registered successfully!";

                }

            }

        }
        catch (PDOException $e) {

            $message = "Database error.";

        }

    }

}


// Get events from database

try {

    $events = $conn->query(
        "SELECT event_id, event_name, event_date, description
         FROM events
         ORDER BY event_date"
    );

}
catch (PDOException $e) {

    $events = false;

    $message = "Unable to load events.";

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>StudentHub - Event Registration</title>

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

        <h2>Event Registration</h2>


        <?php

        if ($message != "") {

            echo "<p>";
            echo htmlspecialchars($message);
            echo "</p>";

        }

        ?>


        <form
            method="POST"
            action="event_register.php">


            <label for="student_id">

                Student ID

            </label>

            <input
                type="text"
                id="student_id"
                name="student_id"
                placeholder="Example: 25DCE003"
                maxlength="20"
                required>


            <label for="event_id">

                Select Event

            </label>

            <select
                id="event_id"
                name="event_id"
                required>

                <option value="">

                    Select Event

                </option>


                <?php

                if ($events !== false) {

                    while (
                        $event =
                        $events->fetch(PDO::FETCH_ASSOC)
                    ) {

                        echo "<option value='"
                            . htmlspecialchars(
                                $event["event_id"]
                            )
                            . "'>";

                        echo htmlspecialchars(
                            $event["event_name"]
                        );

                        echo " - ";

                        echo htmlspecialchars(
                            $event["event_date"]
                        );

                        echo "</option>";

                    }

                }

                ?>

            </select>


            <br><br>


            <button type="submit">

                Register for Event

            </button>


            <button type="reset">

                Reset

            </button>

        </form>

    </div>

</main>


<footer>

    <p>@2026 StudentHub</p>

</footer>

</body>

</html>