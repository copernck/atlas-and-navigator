<?php
// pages/finance.php - Fetches and displays finance posts dynamically - FINAL v4

$pageTitle = 'Finance Insights'; // This might be overridden by index.php logic now
$db = getDbConnection();
$financePosts = [];
$errorMessage = null;

try {
    // Fetch the latest 5 finance posts
    $sql = "SELECT id, slug, title, excerpt, created_at, image_url
            FROM posts
            WHERE LOWER(category) = 'finance' -- Case-insensitive match
            ORDER BY created_at DESC
            LIMIT 5"; // Adjust limit as needed

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $financePosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("error fetching finance posts: " . $e->getMessage());
    $errorMessage = "sorry, couldn't load finance articles right now.";
}
?>

<div class="bg-white rounded-lg shadow-xl overflow-hidden border border-gray-200">
    <div class="p-6 sm:p-8 lg:p-10">
        <header class="text-center border-b border-gray-200 pb-6 mb-8">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-800 mb-2"><?php echo htmlspecialchars($pageTitle); // Use title set by index.php ?></h1>
            <p class="text-lg text-gray-600">Your guide to managing money effectively.</p>
        </header>

        <?php if ($errorMessage): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6" role="alert">
                 <strong class="font-bold">Error!</strong> <span class="block sm:inline"><?php echo htmlspecialchars($errorMessage); ?></span>
            </div>
        <?php endif; ?>

        <?php // --- START DYNAMIC FINANCE CONTENT --- ?>
        <div class="prose prose-lg max-w-none prose-indigo mb-10">
             <h2>Welcome to the Finance Section</h2>
            <p>
                Explore valuable insights and practical advice on personal finance. Check out our latest articles below.
            </p>
        </div>

        <?php if (!empty($financePosts)): ?>
            <h3 class="text-2xl font-semibold text-gray-800 mb-6 border-t pt-8">Latest Finance Articles</h3>
            <div class="space-y-8">
                <?php foreach ($financePosts as $post): ?>
                    <article class="border-b border-gray-100 pb-8 last:border-b-0 last:pb-0 flex flex-col sm:flex-row sm:items-start sm:space-x-6">
                        <?php if (!empty($post['image_url'])): ?>
                            <?php // Use clean URL for link ?>
                            <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>" class="block w-full sm:w-40 h-40 sm:h-auto sm:flex-shrink-0 mb-4 sm:mb-0 bg-gray-100 rounded overflow-hidden">
                                <img src="<?php echo htmlspecialchars($post['image_url']); ?>"
                                     alt="Thumbnail for <?php echo htmlspecialchars($post['title']); ?>"
                                     class="w-full h-full object-cover" loading="lazy"
                                     onerror="this.onerror=null; this.src='https://placehold.co/400x300/E0E7FF/4338CA?text=Finance'; this.style.opacity='0.5';">
                            </a>
                        <?php endif; ?>
                        <div class="flex-grow">
                            <h4 class="font-semibold text-xl hover:text-blue-700 transition duration-150 mb-1">
                                <?php // Use clean URL for link ?>
                                <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </h4>
                             <div class="text-xs text-gray-500 mb-2">
                                Published on <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                            </div>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                <?php
                                    $rawExcerpt = trim($post['excerpt'] ?? '');
                                    if (!empty($rawExcerpt)) {
                                        $sanitizedExcerpt = htmlspecialchars($rawExcerpt, ENT_QUOTES, 'UTF-8');
                                        echo substr($sanitizedExcerpt, 0, 150) . (strlen($sanitizedExcerpt) > 150 ? '...' : '');
                                    } // No fallback text needed here if blog list handles it
                                ?>
                            </p>
                             <?php // Use clean URL for link ?>
                             <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm mt-2 inline-block">
                                Read More &rarr;
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
                 <div class="mt-8 text-center">
                     <?php // Link to filtered blog page ?>
                    <a href="/blog?category=Finance" class="text-indigo-600 hover:text-indigo-800 font-medium">
                        View all finance articles &rarr;
                    </a>
                </div>
            </div>
        <?php elseif (!$errorMessage): ?>
             <div class="text-center text-gray-500 border-t pt-6 mt-6">
                <p>No finance articles published yet. Check back soon!</p>
            </div>
        <?php endif; ?>
        <?php // --- END DYNAMIC FINANCE CONTENT --- ?>

    </div>
</div>
