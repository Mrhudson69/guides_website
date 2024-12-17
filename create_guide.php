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
    $user_id = $_SESSION['user_id'];

    // Validate input
    if (empty($title) || empty($content)) {
        $error = "Title and content are required!";
    } else {
        // Insert the new guide into the database
        $stmt = $pdo->prepare("INSERT INTO guides (title, content, user_id) VALUES (?, ?, ?)");
        if ($stmt->execute([$title, $content, $user_id])) {
            $success = "Guide successfully created!";
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
    selector: '#content',
    height: 500,
    plugins: 'image media link code preview',
    toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | link image media | code preview',
    image_title: true,
    automatic_uploads: true,
    images_upload_url: 'upload.php', // Path to your PHP upload script
    images_reuse_filename: true,    // Prevent renaming and reuse original filename
    file_picker_types: 'image',
    image_class_list: [
        { title: 'Small', value: 'small' },
        { title: 'Medium', value: 'medium' },
        { title: 'Large', value: 'large' }
    ],
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

        .preview {
            background-color: #f9f9f9;
            padding: 15px;
            margin-top: 30px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .preview h3 {
            color: #4CAF50;
        }

        /* Add this CSS to your page to scale images */
.mce-content-body img {
    max-width: 100%;    /* Ensure image doesn't exceed container width */
    height: auto;       /* Maintain aspect ratio */
}

/* Optionally add custom classes for smaller sizes */
.mce-content-body img.small {
    max-width: 150px;  /* Smaller image size */
}

.mce-content-body img.medium {
    max-width: 300px;  /* Medium image size */
}

.mce-content-body img.large {
    max-width: 500px;  /* Large image size */
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
        <textarea id="content" name="content" rows="10" placeholder="Enter the content of the guide" required></textarea>

        <button type="submit">Create Guide</button>
    </form>
<!-- 
    <div class="preview">
        <h3>Preview</h3>
        <div id="guide-preview">
            <h4><strong>Title: </strong><span id="preview-title">Your guide title will appear here.</span></h4>
            <div id="preview-content">Your content will appear here.</div>
        </div>
    </div> -->
</div>

<!-- <script>
    // Live preview of title and content
    document.getElementById('title').addEventListener('input', function () {
        document.getElementById('preview-title').textContent = this.value;
    });

    tinymce.get('content').on('input', function () {
        document.getElementById('preview-content').innerHTML = tinymce.get('content').getContent();
    });
</script> -->

</body>
</html>
