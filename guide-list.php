<?php
include('db.php');
$stmt = $pdo->query("SELECT * FROM guides ORDER BY created_at DESC");

while ($row = $stmt->fetch()) {
    echo "<div class='guide-item'>
            <h3><a href='guide.php?id={$row['id']}'>{$row['title']}</a></h3>
            <p>" . substr($row['content'], 0, 150) . "...</p>
          </div>";
}
?>
