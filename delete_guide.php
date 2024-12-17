<?php
session_start();
include('db.php'); // Ensure the database connection is included

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if the guide ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid guide ID.");
}

$guide_id = intval($_GET['id']); // Sanitize the guide ID
$user_id = $_SESSION['user_id']; // Current logged-in user ID

try {
    // Verify that the guide belongs to the logged-in user
    $stmt = $pdo->prepare("SELECT * FROM guides WHERE id = :id AND user_id = :user_id");
    $stmt->execute(['id' => $guide_id, 'user_id' => $user_id]);
    $guide = $stmt->fetch();

    if (!$guide) {
        die("Guide not found or you don't have permission to delete it.");
    }

    // Perform the deletion
    $deleteStmt = $pdo->prepare("DELETE FROM guides WHERE id = :id AND user_id = :user_id");
    $deleteStmt->execute(['id' => $guide_id, 'user_id' => $user_id]);

    // Redirect back to the profile page after deletion
    header("Location: profile.php?message=GuideDeleted");
    exit;

} catch (PDOException $e) {
    // Display any database errors
    die("Error deleting guide: " . $e->getMessage());
}
?>
