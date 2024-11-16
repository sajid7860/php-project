<?php
include 'db.php';
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$stmt = $conn->query("SELECT * FROM events");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<html>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
    <body>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">Ticketing System</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <h1>Event List</h1>
<table>
    <tr>
        <th>Name</th>
        <th>Date</th>
        <th>Location</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($events as $event): ?>
    <tr>
        <td><?= htmlspecialchars($event['name']) ?></td>
        <td><?= htmlspecialchars($event['date']) ?></td>
        <td><?= htmlspecialchars($event['location']) ?></td>
        <td>
            <a href="edit_event.php?id=<?= $event['event_id'] ?>">Edit</a> | 
            <a href="delete_event.php?id=<?= $event['event_id'] ?>">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<a href="add_event.php">
    <button type="button" class="btn btn-primary mt-3 d-block mx-auto">
        Add Event
    
</button>
</a>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>
