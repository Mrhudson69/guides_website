<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlowBit</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 10px 0;
        }

        nav {
            text-align: center;
            margin: 20px 0;
        }

        nav a {
            color: #4CAF50;
            text-decoration: none;
            margin: 0 15px;
            font-size: 18px;
        }

        .container {
            width: 80%;
            margin: 0 auto;
        }

        .welcome-message {
            text-align: center;
            font-size: 24px;
            margin-top: 50px;
        }

        .guides {
            text-align: center;
            margin-top: 30px;
        }

        .guides a {
            display: inline-block;
            margin: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        footer {
            text-align: center;
            background-color: #4CAF50;
            color: white;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .header_buttons{
            color: white;
            border: 1px solid white;
            padding: 4px 9px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<header>
    <h1>Welcome to the FlowBit</h1>
    <nav>
    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- If the user is logged in, show the guides section, logout link, and profile link -->
        <a class="header_buttons" href="logout.php">Logout</a>
        <!-- <a href="guides.php">My Guides</a> -->
        <a class="header_buttons" href="profile.php">My Profile</a>  <!-- Add My Profile link -->
        <a class="header_buttons" href="view_guides.php">All Guides</a> <!-- Add All Guides link -->
    <?php else: ?>
        <!-- If the user is not logged in, show login and register links -->
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    <?php endif; ?>
</nav>
</header>



<div class="container">
    <div class="welcome-message">
        <?php if (isset($_SESSION['user_id'])): ?>
            <p>Hello, <?php echo $_SESSION['username']; ?>! Welcome back to the FlowBit.</p>
            <p>You are logged in</p>
        <?php else: ?>
            <p>Welcome to the FlowBit. Please log in to access the guides or create an account to get started!</p>
        <?php endif; ?>
    </div>

    <div class="guides">
        <h2>Explore Guides</h2>
        <p>Find helpful guides for a variety of topics, written by experts!</p>

        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Display guide creation link if the user is logged in -->
            <a href="create_guide.php">Create a New Guide</a>
        <?php endif; ?>

        <!-- Display a link to view guides -->
        <a href="view_guides.php">Explore Guides</a>
    </div>
</div>

<footer>
    <p>&copy; 2024 Guides Website. All rights reserved.</p>
</footer>

</body>
</html>
