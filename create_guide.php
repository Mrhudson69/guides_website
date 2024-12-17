<?php
session_start();
include 'db.php'; // Include your database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if user is not logged in
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID

    // Validate input
    if (empty($title) || empty($content)) {
        $error = "Title and content are required!";
    } else {
        // Insert the new guide into the database
        $stmt = $pdo->prepare("INSERT INTO guides (title, content, user_id) VALUES (?, ?, ?)");
        if ($stmt->execute([$title, $content, $user_id])) {
            // Redirect to the same page to prevent the form from being resubmitted
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit(); // Ensure no further code is executed after the redirect
        } else {
            $error = "Failed to create the guide. Please try again.";
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Guide</title>
    <script src="https://cdn.tiny.cloud/1/axvk6r0qxprai4ocxip1wlruzqgepdrpzr620k1ejeo6vjsn/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#content', // Apply TinyMCE on the textarea
            height: 500,
            plugins: 'image media link code preview',
            toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | link image media | code preview',
            image_title: true,
            automatic_uploads: true,
            images_upload_url: 'upload.php', // Path to your PHP upload script
            images_reuse_filename: true,    // Prevent renaming and reuse original filename
            images_upload_base_path: '/uploads/', // Correct base path for images
            file_picker_types: 'image',
            file_picker_callback: function(callback, value, meta) {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.click();

                input.onchange = function() {
                    var file = input.files[0];
                    var formData = new FormData();
                    formData.append('file', file);

                    fetch('upload.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.location) {
                            // Replace the blob URL with the server image URL
                            callback(result.location, { alt: file.name });
                        } else {
                            alert('Image upload failed!');
                        }
                    })
                    .catch(() => alert('Image upload failed!'));
                };
            },
            setup: function(editor) {
                editor.on('init', function() {
                    console.log('TinyMCE initialized successfully.');
                });

                // Sync TinyMCE content to the textarea on form submission
                document.querySelector('form').addEventListener('submit', function(event) {
                    var content = editor.getContent();
                    // Sync content to the textarea
                    document.getElementById('content').value = content;

                    // Manually validate content (instead of using the required attribute)
                    if (content.trim() === '') {
                        event.preventDefault();  // Prevent form submission if content is empty
                        alert("Content is required!");
                    }
                });
            }
        });
    </script>

    <style>
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
            width: 70%;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 18px;
            margin-bottom: 5px;
        }

        input, textarea {
            font-size: 16px;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .success {
            color: green;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<header>
    <h1>Create a New Guide</h1>
</header>

<div class="container">
    <?php
    // Show success or error message
    if (isset($error)) {
        echo "<p class='error'>$error</p>";
    }
    if (isset($success)) {
        echo "<p class='success'>$success</p>";
    }
    ?>

    <form method="POST" action="create_guide.php" enctype="multipart/form-data">
        <label for="title">Guide Title</label>
        <input type="text" id="title" name="title" placeholder="Enter the title of the guide" required>

        <label for="content">Guide Content</label>
        <!-- Do not hide the textarea; let TinyMCE work with it -->
        <textarea id="content" name="content" rows="10" placeholder="Enter the content of the guide"></textarea> <!-- Removed required attribute -->

        <button type="submit">Create Guide</button>
    </form>
</div>

</body>
</html>
