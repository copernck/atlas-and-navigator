<?php
// pages/admin.php - Simple admin dashboard area - FINAL v5 (Use ?page= Links)

// ** Auth check removed - Handled by index.php **

$pageTitle = 'Admin Dashboard';
// Need session for username, ensure started
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$adminUsername = $_SESSION['admin_username'] ?? 'Admin';

?>

<div class="bg-white shadow rounded-lg p-6 lg:p-8">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 border-b border-gray-200 pb-4 gap-4">
        <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($pageTitle); ?></h1>
        <div class="flex items-center flex-wrap justify-center sm:justify-end">
            <span class="text-sm text-gray-600 mr-4 whitespace-nowrap">Logged in as: <strong><?php echo htmlspecialchars($adminUsername); ?></strong></span>
            <a href="logout.php" class="inline-block bg-red-500 hover:bg-red-700 text-white text-sm font-bold py-2 px-3 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                Logout
            </a>
        </div>
    </div>

    <p class="text-gray-700 mb-8">
        welcome to the admin area. from here you can manage site content.
    </p>

    <h2 class="text-xl font-semibold text-gray-800 mb-4">manage content:</h2>
    <ul class="list-disc list-inside space-y-3 text-lg">
        <li>
            <?php // ** Use ?page= Link for Create Post ** ?>
            <a href="index.php?page=create_post" class="text-indigo-600 hover:text-indigo-800 hover:underline font-medium">
                create new blog post
            </a>
        </li>
        <li>
            <?php // ** Use ?page= Link for Manage Posts ** ?>
            <a href="index.php?page=manage_posts" class="text-indigo-600 hover:text-indigo-800 hover:underline font-medium">
                manage existing posts
            </a>
        </li>
         <li>
            <span class="text-gray-500 italic">(manage tools - coming soon)</span>
        </li>
    </ul>

</div>
