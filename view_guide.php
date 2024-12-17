<?php
session_start();
include 'db.php'; // Include your database connection file

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $guide_id = $_GET['id'];

    // Get the guide details from the database using the ID
    $stmt = $pdo->prepare("SELECT * FROM guides WHERE id = ?");
    $stmt->execute([$guide_id]);
    $guide = $stmt->fetch();

    // If no guide is found
    if (!$guide) {
        $error = "Guide not found!";
    }
} else {
    $error = "Invalid guide ID!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($guide['title']); ?> - Guide</title>
    <link rel="stylesheet" href="style.css"> <!-- Link your CSS -->
</head>
<body>

<header>
    <h1>Guide Details</h1>
    <nav>
        <a href="view_guides.php">Back to All Guides</a>
    </nav>
</header>

<main>
    <?php if (isset($error)) { echo "<p class='no-guides'>$error</p>"; } ?>

    <?php if (isset($guide)): ?>
        <div class="guide-item">
            <h2><?php echo htmlspecialchars($guide['title']); ?></h2>
            <div class="guide-content">
                <!-- Display the full content (raw HTML) -->
                <?php echo $guide['content']; ?>
            </div>
        </div>
    <?php endif; ?>
</main>

</body>
</html>
