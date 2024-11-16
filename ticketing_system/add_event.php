<?php
session_start();
include 'db.php';

// Check if the user is logged in as an admin
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

// Process event creation form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = $_POST['name'];
    $event_date = $_POST['date'];
    $event_location = $_POST['location'];
    $num_seats = (int)$_POST['num_seats'];  // Number of seats for this event

    // Insert the event into the events table
    $stmt = $conn->prepare("INSERT INTO events (name, date, location) VALUES (:name, :date, :location)");
    $stmt->execute(['name' => $event_name, 'date' => $event_date, 'location' => $event_location]);

    // Get the ID of the newly created event
    $event_id = $conn->lastInsertId();

    // Insert seats for the event into the seats table
    $conn->beginTransaction();
    $seat_stmt = $conn->prepare("INSERT INTO seats (event_id, seat_number, is_reserved) VALUES (:event_id, :seat_number, 0)");

    for ($i = 1; $i <= $num_seats; $i++) {
        $seat_stmt->execute(['event_id' => $event_id, 'seat_number' => $i]);
    }

    $conn->commit();

    echo "Event and seats created successfully!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Event</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Create Event</h1>
    </header>

    <div class="container">
        <form method="POST">
            <label for="name">Event Name</label>
            <input type="text" name="name" id="name" required>

            <label for="date">Date</label>
            <input type="date" name="date" id="date" required>

            <label for="location">Location</label>
            <input type="text" name="location" id="location" required>

            <label for="num_seats">Number of Seats</label>
            <input type="number" name="num_seats" id="num_seats" min="1" required>

            <button type="submit" class="btn">Create Event</button>
        </form>
    </div>

    <button onclick="location.href='logout.php'">logout</button>
</body>
</html>
