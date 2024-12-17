<?php
session_start();
include 'db.php'; // Include your database connection file

// Get the search term (if any) from the query string
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Prepare the query based on whether a search term is provided
if ($searchTerm) {
    // Split the search term into individual words
    $searchWords = explode(' ', $searchTerm);

    // Construct the SQL query with multiple LIKE conditions for each word
    $sql = "SELECT * FROM guides WHERE";
    $params = [];

    // For each word in the search term, add a LIKE condition
    foreach ($searchWords as $word) {
        $sql .= " (title LIKE ? OR content LIKE ?) AND";
        $params[] = "%$word%";
        $params[] = "%$word%";
    }

    // Remove the last 'AND' from the query string
    $sql = rtrim($sql, 'AND');

    // Prepare and execute the query
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
} else {
    // If no search term is provided, show all guides
    $stmt = $pdo->prepare("SELECT * FROM guides ORDER BY id DESC");
    $stmt->execute();
}

$guides = $stmt->fetchAll();

// If there are no guides
if (empty($guides)) {
    $message = "No guides available.";
}

// Function to remove the first image and limit to text content
function strip_first_image($content) {
    // Remove any <img> tags (and the first one) from the content
    $content = preg_replace('/<img[^>]*>/i', '', $content, 1);
    return $content;
}

// Function to get a snippet (first few lines) of the content
function get_guide_snippet($content, $limit = 300) {
    // First, strip out the images and unwanted tags
    $content = strip_first_image($content);
    
    // Remove all other tags, but keep the text content
    $content = strip_tags($content);

    // Limit to the specified number of characters
    return substr($content, 0, $limit) . (strlen($content) > $limit ? '...' : '');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Guides</title>
    <link rel="stylesheet" href="style.css"> <!-- Link your CSS file -->
</head>
<body>

<header>
    <h1>All Guides</h1>
    <nav>
        <a href="index.php">Back to Dashboard</a>
    </nav>
</header>

<main>
    <!-- Search Bar -->
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Search guides..." value="<?php echo htmlspecialchars($searchTerm); ?>">
        <button type="submit">Search</button>
    </form>

    <?php if (isset($message)) { echo "<p class='no-guides'>$message</p>"; } ?>

    <div class="guides-grid">
        <?php foreach ($guides as $guide): ?>
            <div class="guide-item">
                <h3>
                    <!-- Make each guide title clickable and open in a new tab -->
                    <a href="view_guide.php?id=<?php echo $guide['id']; ?>" target="_blank">
                        <?php echo htmlspecialchars($guide['title']); ?>
                    </a>
                </h3>
                <div class="guide-snippet">
                    <!-- Get a cleaned snippet of the content -->
                    <?php echo get_guide_snippet($guide['content']); ?>
                </div>
                <a href="view_guide.php?id=<?php echo $guide['id']; ?>" target="_blank" class="view-btn">View Full Guide</a>
            </div>
        <?php endforeach; ?>
    </div>
</main>

</body>
</html>
