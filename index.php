<?php
// index.php - Main Router / Front Controller - Single Post Debug Removed

// --- Configuration and Database ---
require_once __DIR__ . '/config/config.php'; // Defines constants, starts session
require_once __DIR__ . '/includes/database.php'; // Provides getDbConnection()

// --- Allowed Page Keys (Defined UP FRONT!) ---
$allowedPages = [
    'home', 'finance', 'travel', 'tools', 'blog', 'single_post',
    'donation', 'about', 'contact', 'privacy', 'qrcode',
    'admin', 'create_post', 'manage_posts', 'edit_post',
    // 'search', // Search is currently disabled
    '404'
];
// List of admin pages accessible via ?page=...
$admin_pages_get = ['admin', 'create_post', 'manage_posts', 'edit_post'];

// --- Routing Logic ---
$page = '404'; // Default
$slug = null;
$postId = null;
$filterCategory = null; // For potential category query strings
$pageData = null;
$pageTitle = 'Page Not Found';

// Check if accessing via ?page= for allowed admin pages first
$getPage = isset($_GET['page']) ? trim($_GET['page']) : null;

if ($getPage && in_array($getPage, $admin_pages_get)) {
    $page = $getPage;
    $pageTitle = ucfirst(str_replace('_', ' ', $page));
    if ($page === 'edit_post' && isset($_GET['id'])) {
        $postId = (int)$_GET['id'];
    }

} else {
    // If not an admin page via GET, proceed with Clean URL routing
    $requestUri = $_SERVER['REQUEST_URI'];
    $requestPath = strtok($requestUri, '?');
    $requestPath = trim($requestPath, '/');

    // --- Define Clean URL Routes ---
    if ($requestPath === '' || $requestPath === 'home') {
        $page = 'home'; $pageTitle = 'Welcome';
    } elseif ($requestPath === 'about') {
        $page = 'about'; $pageTitle = 'About Us';
    } elseif ($requestPath === 'contact') {
        $page = 'contact'; $pageTitle = 'Contact Us';
    } elseif ($requestPath === 'privacy') {
        $page = 'privacy'; $pageTitle = 'Privacy Policy';
    } elseif ($requestPath === 'donation') {
        $page = 'donation'; $pageTitle = 'Donate';
    } elseif ($requestPath === 'tools') {
        $page = 'tools'; $pageTitle = 'Tools';
    } elseif ($requestPath === 'finance') {
        $page = 'finance'; $pageTitle = 'Finance Insights';
    } elseif ($requestPath === 'travel') {
        $page = 'travel'; $pageTitle = 'Travel Guides';
    } elseif ($requestPath === 'blog') {
        $page = 'blog'; $pageTitle = 'Blog - All Articles';
        if (isset($_GET['category'])) { $filterCategory = trim($_GET['category']); $pageTitle = htmlspecialchars(ucfirst($filterCategory)) . ' Articles'; }
    } elseif (preg_match('/^blog\/([a-z0-9\-]+)$/i', $requestPath, $matches)) {
        $page = 'single_post';
        $slug = $matches[1];
        // ***** DEBUGGING REMOVED from this block *****
        try {
            $db = getDbConnection();
            $sql = "SELECT * FROM posts WHERE slug = :slug LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();
            $pageData = $stmt->fetch(PDO::FETCH_ASSOC);
            // ***** DEBUGGING REMOVED from this block *****
            if (!$pageData) {
                http_response_code(404); // Set 404 status here is fine, before header include
                $page = '404';
                $pageTitle = 'Post Not Found';
            } else {
                $pageTitle = $pageData['title'];
            }
        } catch (PDOException $e) {
            error_log("Error fetching post slug '{$slug}': " . $e->getMessage());
            http_response_code(500); // Set 500 status here is fine
            $page = '404';
            $pageTitle = 'Database Error';
        }
    } elseif ($requestPath === 'admin') {
        $page = 'admin'; $pageTitle = 'Admin Dashboard';
    } elseif ($requestPath === 'admin/create') {
        $page = 'create_post'; $pageTitle = 'Create New Post';
    } elseif ($requestPath === 'admin/manage') {
        $page = 'manage_posts'; $pageTitle = 'Manage Posts';
    } elseif (preg_match('/^admin\/edit\/(\d+)$/i', $requestPath, $matches)) {
        $page = 'edit_post'; $postId = (int)$matches[1]; $_GET['id'] = $postId; $pageTitle = 'Edit Post';
    } elseif (in_array($requestPath, $allowedPages)) {
        $page = $requestPath; $pageTitle = ucfirst(str_replace('_', ' ', $page));
    } else {
        $page = '404'; $pageTitle = 'Page Not Found';
    }
} // End routing logic

// --- Final validation check on determined page key ---
// If routing logic resulted in a page not in the master list, force 404
// Also handles cases where initial $page was 404
if (!in_array($page, $allowedPages)) {
     $page = '404';
     $pageTitle = 'Page Not Found';
     if (http_response_code() !== 500) http_response_code(404); // Set 404 if not already 500
}


// --- Determine Final Page File Path ---
$pageFile = __DIR__ . '/pages/' . $page . '.php';

// Final check: Does the determined PHP file actually exist?
if (!file_exists($pageFile)) {
    error_log("Error: file_exists failed for - " . $pageFile . " (Page Key: " . $page . ")");
    if (http_response_code() !== 500) http_response_code(404); // Set 404 status *before* output
    $page = '404'; $pageTitle = 'Page Not Found';
    $pageFile = __DIR__ . '/pages/404.php'; // Prepare to include 404 page

    if (!file_exists($pageFile)) { /* ... critical error handling ... */ }
}


// ******** ADMIN AUTHENTICATION CHECK (BEFORE OUTPUT) ********
$admin_pages_check = ['admin', 'create_post', 'manage_posts', 'edit_post'];
if (in_array($page, $admin_pages_check)) {
    // Ensure session started before checking $_SESSION
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        // Show 404 instead of redirecting to login
        http_response_code(404);
        $page = '404';
        $pageTitle = 'Page Not Found';
        $pageFile = __DIR__ . '/pages/404.php'; // Ensure 404 page file is set
        // Allow script to continue to load header/footer/404 content
    }
}
// ******** END ADMIN AUTHENTICATION CHECK ********


// --- Output Starts Here ---
$headerPath = __DIR__ . '/includes/header.php';
if (file_exists($headerPath)) { include $headerPath; }
else { error_log("Critical Error: header.php not found"); /* Basic fallback needed */ }

// --- Include the Main Page Content ---
if (isset($pageData) && $page === 'single_post') { $post = $pageData; }
// Make sure $pageFile is set correctly if auth check failed and set page to 404
if (!isset($pageFile)) { $pageFile = __DIR__ . '/pages/404.php'; }
include $pageFile;

// --- Include Footer ---
$footerPath = __DIR__ . '/includes/footer.php';
if (file_exists($footerPath)) { include $footerPath; }
else { error_log("Critical Error: footer.php not found"); /* Basic fallback needed */ }

?>
