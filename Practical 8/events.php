<?php

require_once "../db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $event_name = trim($_POST["event_name"] ?? "");
    $event_date = trim($_POST["event_date"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if ($event_name == "") {

        $message = "Please enter event name.";

    }
    elseif ($event_date == "") {

        $message = "Please select event date.";

    }
    else {

        try {

            $sql = "INSERT INTO events
                    (event_name, event_date, description)
                    VALUES
                    (:event_name, :event_date, :description)";

            $stmt = $conn->prepare($sql);

            $stmt->execute([
                ":event_name" => $event_name,
                ":event_date" => $event_date,
                ":description" => $description
            ]);

            $message = "Event added successfully!";

        }
        catch (PDOException $e) {

            $message = "Error: Event could not be added.";

        }

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>StudentHub - Events</title>

    <link rel="stylesheet" href="../CSS/style.css">

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

        <h2>Add Event</h2>

        <?php

        if ($message != "") {

            echo "<p>" . htmlspecialchars($message) . "</p>";

        }

        ?>


        <form method="POST" action="events.php">

            <label for="event_name">
                Event Name
            </label>

            <input
                type="text"
                id="event_name"
                name="event_name"
                placeholder="Enter event name"
                required>


            <label for="event_date">
                Event Date
            </label>

            <input
                type="date"
                id="event_date"
                name="event_date"
                required>


            <label for="description">
                Description
            </label>

            <textarea
                id="description"
                name="description"
                placeholder="Enter event description"
                rows="4"></textarea>


            <button type="submit">
                Add Event
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