<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get event ID from the query string
$event_id = $_GET['event_id'] ?? null;
if (!$event_id) {
    echo "Event not found!";
    exit;
}

// Fetch event details
$stmt = $conn->prepare("SELECT * FROM events WHERE event_id = :event_id");
$stmt->execute(['event_id' => $event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo "Event not found!";
    exit;
}

// Fetch seats for the event
$seats_stmt = $conn->prepare("SELECT * FROM seats WHERE event_id = :event_id");
$seats_stmt->execute(['event_id' => $event_id]);
$seats = $seats_stmt->fetchAll(PDO::FETCH_ASSOC);

// Process booking if seat is selected
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $seat_id = $_POST['seat_id'];
    
    // Check if seat is available
    $seat_check = $conn->prepare("SELECT is_reserved FROM seats WHERE seat_id = :seat_id AND event_id = :event_id");
    $seat_check->execute(['seat_id' => $seat_id, 'event_id' => $event_id]);
    $seat = $seat_check->fetch(PDO::FETCH_ASSOC);

    if ($seat && !$seat['is_reserved']) {
        // Reserve the seat and create the ticket
        $conn->beginTransaction();
        $conn->prepare("UPDATE seats SET is_reserved = 1 WHERE seat_id = :seat_id")->execute(['seat_id' => $seat_id]);
        $conn->prepare("INSERT INTO tickets (user_id, event_id, seat_id) VALUES (:user_id, :event_id, :seat_id)")
            ->execute(['user_id' => $_SESSION['user_id'], 'event_id' => $event_id, 'seat_id' => $seat_id]);
        $conn->commit();

        echo "Ticket booked successfully!";
        exit;
    } else {
        echo "Seat is already reserved. Please select another seat.";
    }
}
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">Ticketing System</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="view_events.php">View Events</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Ticket</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>
<body>
    <header>
        <h1>Book Ticket for <?= htmlspecialchars($event['name']) ?></h1>
    </header>

    <div class="container">
        <h2>Select a Seat</h2>
        <form method="POST">
            <div class="seat-selection">
                <?php foreach ($seats as $seat): ?>
                    <label class="seat <?= $seat['is_reserved'] ? 'reserved' : '' ?>">
                        <input type="checkbox" name="seat_id" value="<?= $seat['seat_id'] ?>" <?= $seat['is_reserved'] ? 'disabled' : '' ?>>
                        <?= htmlspecialchars($seat['seat_number']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="btn">Book Now</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
