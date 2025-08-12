<?php
// delete_post.php - Handles deleting a post

// Include config to start session and get DB connection function path
require_once __DIR__ . '/config/config.php'; 
require_once __DIR__ . '/includes/database.php';

// ** SECURITY CHECK: Ensure user is logged in as admin **
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // Set error message maybe?
    $_SESSION['admin_message_error'] = 'access denied: you must be logged in to delete posts.';
    header('Location: login.php'); 
    exit; 
}

// ** CSRF Protection WARNING **
// This script currently uses GET requests for deletion via a simple link.
// This is vulnerable to Cross-Site Request Forgery (CSRF).
// A proper implementation should use POST requests with unique CSRF tokens generated
// in the manage_posts.php form and validated here.
// For simplicity in this example, we are omitting CSRF protection, but DO NOT use this in production without it.


// --- Get Post ID from URL ---
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// --- Validate ID ---
if ($postId <= 0) {
    $_SESSION['admin_message_error'] = 'invalid post id provided for deletion.';
    header('Location: index.php?page=manage_posts');
    exit;
}

// --- Attempt Deletion ---
try {
    $db = getDbConnection();

    // Prepare SQL delete statement
    $sql = "DELETE FROM posts WHERE id = ?";
    $stmt = $db->prepare($sql);
    
    // Execute deletion
    $success = $stmt->execute([$postId]);

    if ($success && $stmt->rowCount() > 0) {
        // Deletion successful (and at least one row was affected)
        $_SESSION['admin_message_success'] = "post (id: " . $postId . ") deleted successfully.";
    } elseif ($success) {
         // Query executed but no rows affected (post ID didn't exist)
         $_SESSION['admin_message_error'] = "post (id: " . $postId . ") not found or already deleted.";
    } else {
        // Execution failed
        $_SESSION['admin_message_error'] = "database error: failed to delete post (id: " . $postId . ").";
    }

} catch (PDOException $e) {
    error_log("error deleting post (id: " . $postId . "): " . $e->getMessage());
    $_SESSION['admin_message_error'] = "database error occurred during deletion.";
}

// --- Redirect back to the manage posts page ---
// The manage posts page will display the success/error message from the session
header('Location: index.php?page=manage_posts');
exit;

?>
