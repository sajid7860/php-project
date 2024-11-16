<?php
include 'db.php';
$stmt = $conn->query("SELECT * FROM events");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
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
        <td><a href="purchase.php?event_id=<?= $event['event_id'] ?>">Purchase Ticket</a></td>
    </tr>
    <?php endforeach; ?>
</table>
