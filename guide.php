<?php
include('db.php');
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM guides WHERE id = :id");
$stmt->execute(['id' => $id]);
$guide = $stmt->fetch();

if ($guide) {
    echo "<h1>{$guide['title']}</h1>";
    echo "<div>" . $guide['content'] . "</div>";
} else {
    echo "Guide not found.";
}
?>
