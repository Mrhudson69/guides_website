<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  // Redirect to login if not logged in
    exit;
}

// Fetch user data from the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

// Handle the profile update form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    
    // Handle file upload for profile picture
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $file = $_FILES['profile_pic'];
        $fileName = basename($file['name']);
        $filePath = 'uploads/' . $fileName;
        
        // Move the uploaded file to the 'uploads' directory
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Update database with new profile picture
            $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email, profile_pic = :profile_pic WHERE id = :id");
            $stmt->execute(['username' => $username, 'email' => $email, 'profile_pic' => $fileName, 'id' => $_SESSION['user_id']]);
        }
    } else {
        // If no new profile picture, update only username and email
        $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
        $stmt->execute(['username' => $username, 'email' => $email, 'id' => $_SESSION['user_id']]);
    }

    // Refresh the page after update
    header("Location: profile.php");
    exit;
}

// Handle the profile picture deletion
if (isset($_GET['delete_pic'])) {
    // Delete the profile picture from the database
    $stmt = $pdo->prepare("UPDATE users SET profile_pic = NULL WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    
    // Optionally, you can delete the file from the server (if it exists)
    $currentPic = $user['profile_pic'];
    if ($currentPic && file_exists("uploads/$currentPic")) {
        unlink("uploads/$currentPic");  // Delete the file from the server
    }

    // Redirect to the profile page after deletion
    header("Location: profile.php");
    exit;
}

// Fetch the guides created by the logged-in user
$guidesStmt = $pdo->prepare("SELECT * FROM guides WHERE user_id = :user_id");
$guidesStmt->execute(['user_id' => $_SESSION['user_id']]);
$guides = $guidesStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <style>
        /* Add some basic styling for the profile page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 10px 0;
        }

        .container {
            width: 80%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-pic-container {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
        }

        .profile-pic {
            max-width: 150px;
            border-radius: 50%;
        }

        .delete-btn {
            position: absolute;
            top: 0;
            right: 0;
            background-color: rgba(255, 0, 0, 0.7);
            color: white;
            border: none;
            border-radius: 50%;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 14px;
        }

        .delete-btn:hover {
            background-color: red;
        }

        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="file"] {
            margin: 10px 0;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .back-btn {
            background-color: #f44336;
            padding: 10px 20px;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 20px;
        }

        .back-btn:hover {
            background-color: #d32f2f;
        }

        .guides-section {
            margin-top: 40px;
        }

        .guides-section h3 {
            margin-bottom: 15px;
        }

        .guide-item {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .guide-item a {
            color: #4CAF50;
            text-decoration: none;
            font-size: 18px;
        }

        .guide-item a:hover {
            text-decoration: underline;
        }

        .guide-item .action-btns {
            margin-top: 10px;
        }

        .guide-item .action-btns button {
            background-color: #f44336;
            color: white;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }

        .guide-item .action-btns button.edit-btn {
            background-color: #2196F3;
        }

        .guide-item .action-btns button:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>

<header>
    <h1>My Profile</h1>
</header>

<div class="container">
    <a href="index.php" class="back-btn">Back to Home</a>

    <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?></h2>

    <!-- Profile Picture Section -->
    <div class="profile-pic-container">
        <?php
        // Check if the user has a profile picture, otherwise show a default one
        $profilePic = $user['profile_pic'] ? 'uploads/' . htmlspecialchars($user['profile_pic']) : 'default-profile-pic.png';
        ?>
        <img class="profile-pic" src="<?php echo $profilePic; ?>" alt="Profile Picture">
        
        <!-- Delete Profile Picture Button -->
        <?php if ($user['profile_pic']): ?>
            <a href="?delete_pic=true">
                <button class="delete-btn">X</button>
            </a>
        <?php endif; ?>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <!-- Input fields for username and email -->
        <label for="username">Username:</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label for="profile_pic">Profile Picture (optional):</label>
        <input type="file" name="profile_pic">

        <button type="submit">Save Changes</button>
    </form>

    <!-- Section for showing user-created guides -->
    <div class="guides-section">
    <h3>My Created Guides</h3>
    <?php if (count($guides) > 0): ?>
        <?php foreach ($guides as $guide): ?>
            <div class="guide-item">
                <a href="guide.php?id=<?php echo $guide['id']; ?>">
                    <?php echo htmlspecialchars($guide['title']); ?>
                </a>
                <div class="action-btns" style="margin-top: 10px; display: flex; gap: 10px;">
                    <!-- Edit Button -->
                    <a href="edit_guide.php?id=<?php echo $guide['id']; ?>">
                        <button type="button" class="edit-btn" style="background-color: #2196F3; color: white; border: none; padding: 5px 15px; border-radius: 5px; cursor: pointer;">
                            Edit
                        </button>
                    </a>

                    <!-- Delete Button -->
                    <button type="button" onclick="confirmDelete(<?php echo $guide['id']; ?>)" style="background-color: #f44336; color: white; border: none; padding: 5px 15px; border-radius: 5px; cursor: pointer;">
                        Delete
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>You haven't created any guides yet.</p>
    <?php endif; ?>
</div>

<script>
    function confirmDelete(guideId) {
        if (confirm("Are you sure you want to delete this guide?")) {
            // Redirect to delete_guide.php with the guide ID
            window.location.href = "delete_guide.php?id=" + guideId;
        }
    }
</script>


</body>
</html>
