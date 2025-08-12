<?php
// pages/404.php - Page Not Found Error Page - FINAL v4 (Header Call Removed)

// Set the page title (index.php should already set this)
$pageTitle = 'Page Not Found';

// ** REMOVED redundant http_response_code(404) call **
// index.php now handles setting the 404 status code *before* including header.php
/*
if (http_response_code() !== 404 && http_response_code() !== 500) {
    http_response_code(404); // This line is commented out!
}
*/
?>

<div class="text-center py-16 sm:py-24 px-4">
    <h1 class="text-6xl sm:text-8xl font-extrabold text-indigo-600 mb-4">404</h1>
    <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-3">Page Not Found</h2>
    <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
        sorry, we couldn't find the page you were looking for. maybe it was moved, deleted, or you mistyped the url.
    </p>
    <div class="space-x-4">
        <?php // Use clean URL for home link ?>
        <a href="/" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg shadow transition duration-300 ease-in-out">
            go back home
        </a>
        <?php // Use clean URL for contact link ?>
        <a href="/contact" class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg shadow-sm transition duration-300 ease-in-out">
            contact us
        </a>
    </div>
</div>
