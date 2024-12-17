<?php
session_start();
include('db.php');

// Check if the user ID is provided in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}

$profile_id = intval($_GET['id']); // ID of the profile being viewed
$logged_in_user_id = $_SESSION['user_id'] ?? null; // Logged-in user ID (if logged in)

// Fetch the user profile
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $profile_id]);
$user = $stmt->fetch();

if (!$user) {
    die("User profile not found.");
}

// Fetch guides created by this user
$stmt_guides = $pdo->prepare("SELECT * FROM guides WHERE user_id = :user_id");
$stmt_guides->execute(['user_id' => $profile_id]);
$guides = $stmt_guides->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['username']); ?>'s Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        .profile-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
            background-color: #eee;
        }
        .profile-info h2 {
            margin: 0;
            color: #333;
        }
        .profile-info p {
            color: #555;
            margin: 5px 0;
        }
        .guide-card {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .guide-card h3 {
            margin: 0 0 10px;
            color: #333;
        }
        .guide-card p {
            margin: 0 0 10px;
            color: #555;
        }
        .buttons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .buttons a, .buttons button {
            text-decoration: none;
            background: #4CAF50;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .buttons .delete {
            background: #f44336;
        }
        .buttons a:hover, .buttons button:hover {
            opacity: 0.9;
        }
        .empty-guides {
            color: #888;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Profile Header -->
        <div class="profile-header">
            <!-- Profile Photo -->
            <img src="<?php echo $user['profile_pic'] ? htmlspecialchars($user['profile_pic']) : 'https://via.placeholder.com/120'; ?>" 
                 alt="Profile Photo" class="profile-photo">
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div>

        <!-- Guides Section -->
        <h3>Guides Created:</h3>
        <?php if (count($guides) > 0): ?>
            <?php foreach ($guides as $guide): ?>
                <div class="guide-card">
                    <h3><?php echo htmlspecialchars($guide['title']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars(substr($guide['content'], 0, 150))); ?>...</p>
                    
                    <?php if ($logged_in_user_id === $profile_id): ?>
                        <div class="buttons">
                            <a href="edit_guide.php?id=<?php echo $guide['id']; ?>">Edit</a>
                            <form action="delete_guide.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this guide?');">
                                <input type="hidden" name="guide_id" value="<?php echo $guide['id']; ?>">
                                <button type="submit" class="delete">Delete</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty-guides">No guides created yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
