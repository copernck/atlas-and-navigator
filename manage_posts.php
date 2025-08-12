<?php
// pages/manage_posts.php - Admin page to list existing posts for editing/deleting

// session_start() is already called in config.php

// ** SECURITY CHECK: Ensure user is logged in as admin **
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php'); 
    exit; 
}

$pageTitle = 'Manage Posts'; 
$db = getDbConnection(); 

$posts = []; 
$errorMessage = null; 
$successMessage = null; 
$infoMessage = null; // Added info message check

// ** Check for flash messages from previous actions (like delete or update redirect) **
if (isset($_SESSION['admin_message_success'])) {
    $successMessage = $_SESSION['admin_message_success'];
    unset($_SESSION['admin_message_success']); // Clear message after retrieving
}
if (isset($_SESSION['admin_message_error'])) {
    $errorMessage = $_SESSION['admin_message_error'];
    unset($_SESSION['admin_message_error']); // Clear message
}
if (isset($_SESSION['admin_message_info'])) {
    $infoMessage = $_SESSION['admin_message_info'];
    unset($_SESSION['admin_message_info']); // Clear message
}

// Fetch posts
try {
    $sql = "SELECT id, slug, title, category, created_at 
            FROM posts 
            ORDER BY created_at DESC";
            
    $stmt = $db->query($sql); 
    $posts = $stmt->fetchAll(); 

} catch (PDOException $e) {
    error_log("error fetching posts for admin: " . $e->getMessage());
    $errorMessage = "sorry, couldn't load posts list.";
    // Ensure $posts remains an array even on error
    $posts = []; 
}

?>

<div class="bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
         <h1 class="text-3xl font-bold text-gray-800"><?php echo $pageTitle; ?></h1>
         <div>
             <a href="index.php?page=create_post" class="inline-block bg-green-500 hover:bg-green-600 text-white text-sm font-bold py-2 px-3 rounded focus:outline-none focus:shadow-outline mr-4">
                 + create new post
             </a>
             <a href="index.php?page=admin" class="text-sm text-blue-600 hover:text-blue-800">&larr; back to admin dashboard</a>
         </div>
    </div>

    <?php // ** Display flash messages ** ?>
    <?php if ($successMessage): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">success!</strong>
            <span class="block sm:inline"><?php echo htmlspecialchars($successMessage); ?></span>
        </div>
    <?php endif; ?>
     <?php if ($infoMessage): ?>
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">info:</strong>
            <span class="block sm:inline"><?php echo htmlspecialchars($infoMessage); ?></span>
        </div>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
         <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">error!</strong>
            <span class="block sm:inline"><?php echo htmlspecialchars($errorMessage); ?></span>
        </div>
    <?php endif; ?>


    <?php // Check posts *after* potential DB error message is set ?>
    <?php if (empty($posts) && !$errorMessage): ?>
        <p class="text-gray-600">no posts found yet. maybe <a href="index.php?page=create_post" class="text-indigo-600 hover:text-indigo-800">create one</a>?</p>
    <?php elseif (!empty($posts)): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">title</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">category</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">slug</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">created</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($post['title']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars(ucfirst($post['category'])); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono"><?php echo htmlspecialchars($post['slug']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('Y-m-d H:i', strtotime($post['created_at'])); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="index.php?page=edit_post&id=<?php echo $post['id']; ?>" class="text-indigo-600 hover:text-indigo-900">edit</a>
                                
                                <a href="delete_post.php?id=<?php echo $post['id']; ?>" 
                                   class="text-red-600 hover:text-red-900" 
                                   onclick="return confirm('are you fucking sure you want to delete this post? \n\ntitle: <?php echo htmlspecialchars(addslashes($post['title'])); ?>');">delete</a>
                                   
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>
