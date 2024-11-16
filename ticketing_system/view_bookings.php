<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user bookings
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT 
        tickets.ticket_id, 
        events.name AS event_name, 
        events.date AS event_date, 
        events.location AS event_location, 
        seats.seat_number 
    FROM tickets
    INNER JOIN events ON tickets.event_id = events.event_id
    INNER JOIN seats ON tickets.seat_id = seats.seat_id
    WHERE tickets.user_id = :user_id
    ORDER BY events.date ASC
");
$stmt->execute(['user_id' => $user_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>My Bookings</h1>
    </header>

    <div class="container">
        <?php if (count($bookings) > 0): ?>
            <table>
                <tr>
                    <th>Ticket ID</th>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Seat Number</th>
                </tr>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['ticket_id']) ?></td>
                        <td><?= htmlspecialchars($booking['event_name']) ?></td>
                        <td><?= htmlspecialchars($booking['event_date']) ?></td>
                        <td><?= htmlspecialchars($booking['event_location']) ?></td>
                        <td><?= htmlspecialchars($booking['seat_number']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>You have no bookings.</p>
        <?php endif; ?>
    </div>
</body>
</html>
