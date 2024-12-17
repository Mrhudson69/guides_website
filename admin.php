<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.html');
    exit();
}

include('db.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'], $_POST['content'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Upload files (images/gifs/videos)
    $uploadDir = 'uploads/';
    $filePaths = [];

    if (isset($_FILES['media'])) {
        foreach ($_FILES['media']['tmp_name'] as $key => $tmpName) {
            $fileName = basename($_FILES['media']['name'][$key]);
            $filePath = $uploadDir . $fileName;

            if (move_uploaded_file($tmpName, $filePath)) {
                $filePaths[] = $filePath;
            }
        }
    }

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO guides (title, content) VALUES (:title, :content)");
    $stmt->execute(['title' => $title, 'content' => $content]);

    // Redirect after successful submission
    header('Location: index.html');
}
?>
