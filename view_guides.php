<?php
session_start();
include 'db.php'; // Include your database connection file

// Get all guides from the database
$stmt = $pdo->prepare("SELECT * FROM guides ORDER BY id DESC");
$stmt->execute();
$guides = $stmt->fetchAll();

// If there are no guides
if (empty($guides)) {
    $message = "No guides available.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Guides</title>
</head>
<body>

<h1>All Guides</h1>

<?php if (isset($message)) { echo "<p>$message</p>"; } ?>

<table border="1">
    <tr>
        <th>Title</th>
        <th>Content</th>
    </tr>
    <?php foreach ($guides as $guide): ?>
        <tr>
            <td><?php echo htmlspecialchars($guide['title']); ?></td>
            <td><?php echo $guide['content']; // Display raw HTML content ?></td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
