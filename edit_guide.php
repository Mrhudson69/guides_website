<?php
if (!isset($_GET['id'])) {
    die("No guide specified.");
}
$guide_id = intval($_GET['id']);
echo "Edit guide with ID: " . $guide_id;
?>
