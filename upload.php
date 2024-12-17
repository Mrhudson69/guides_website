<?php
// Check if a file is uploaded
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // Check for errors
    if ($file['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/'; // Directory to save uploaded files
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); // Create directory if it does not exist
        }

        // Generate a unique filename
        $filename = uniqid() . '-' . basename($file['name']);
        $targetFile = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            // Return the correct relative path for the uploaded image
            echo json_encode(['location' => $targetFile]);
            exit;
        } else {
            echo json_encode(['location' => $targetFile]);
            exit;
        }
    } else {
        echo json_encode(['error' => 'File upload error.']);
        exit;
    }
} else {
    echo json_encode(['error' => 'No file uploaded.']);
    exit;
}
?>
