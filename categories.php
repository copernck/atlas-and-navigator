<?php
// pages/categories.php
// lists all unique post categories and post counts - UI POLISH

$pageTitle = 'Categories'; 
$db = getDbConnection(); 

$categories = []; 
$errorMessage = null; 

try {
    // fetch unique category names and the count of posts in each
    $sql = "SELECT category, COUNT(*) as post_count 
            FROM posts 
            GROUP BY category 
            ORDER BY category ASC";
            
    $stmt = $db->query($sql); 
    $categories = $stmt->fetchAll(); 

} catch (PDOException $e) {
    error_log("error fetching categories: " . $e->getMessage());
    $errorMessage = "sorry, couldn't load categories right now.";
}

?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"> {/* Consistent padding */}
    <h1 class="text-4xl font-bold text-gray-900 border-b border-gray-300 pb-4 mb-8"><?php echo $pageTitle; ?></h1>

    <?php if ($errorMessage): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6" role="alert">... error message ...</div>
    <?php elseif (empty($categories)): ?>
        <div class="bg-white shadow-md rounded-lg p-8 text-center text-gray-500"><p>No categories found yet. Create some posts first!</p></div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"> {/* Grid layout */}
            <?php foreach ($categories as $category): ?>
                 <?php 
                    // Determine link and color based on category
                    $categorySlug = strtolower($category['category']);
                    $link = 'index.php?page=blog&category=' . urlencode($category['category']); // Default link (we haven't built general category view)
                    $categoryColor = 'bg-gray-100 text-gray-800 hover:bg-gray-200'; // Default
                    $textColor = 'text-gray-900';
                    $countColor = 'text-gray-500';

                    if ($categorySlug === 'finance') {
                        $link = 'index.php?page=finance';
                        $categoryColor = 'bg-indigo-100 text-indigo-800 hover:bg-indigo-200';
                        $textColor = 'text-indigo-700 hover:text-indigo-900';
                        $countColor = 'text-indigo-500';
                    } elseif ($categorySlug === 'travel') {
                        $link = 'index.php?page=travel';
                         $categoryColor = 'bg-cyan-100 text-cyan-800 hover:bg-cyan-200';
                         $textColor = 'text-cyan-700 hover:text-cyan-900';
                         $countColor = 'text-cyan-500';
                    } // Add more elseifs for other specific categories if needed
                ?>
                <a href="<?php echo $link; ?>" 
                   class="block bg-white shadow-lg rounded-xl p-6 text-center transition duration-300 ease-in-out hover:shadow-xl hover:scale-105"> {/* Link card */}
                    
                    <span class="inline-block <?php echo $categoryColor; ?> text-sm font-semibold px-4 py-1 rounded-full mb-3 tracking-wide"><?php echo htmlspecialchars(ucfirst($category['category'])); ?></span>
                    
                    <p class="text-lg font-semibold <?php echo $countColor; ?>">
                        <?php echo $category['post_count']; ?> post<?php echo ($category['post_count'] != 1 ? 's' : ''); ?>
                    </p>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
