<?php
// pages/travel.php - Fetches and displays travel posts dynamically - FINAL v4

$pageTitle = 'Travel Guides';
$db = getDbConnection();
$travelPosts = [];
$errorMessage = null;

try {
    // Fetch the latest 5 travel posts
    $sql = "SELECT id, slug, title, excerpt, created_at, image_url
            FROM posts
            WHERE LOWER(category) = 'travel' -- Case-insensitive match
            ORDER BY created_at DESC
            LIMIT 5"; // Adjust limit as needed

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $travelPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("error fetching travel posts: " . $e->getMessage());
    $errorMessage = "sorry, couldn't load travel guides right now.";
}
?>

<div class="bg-white rounded-lg shadow-xl overflow-hidden border border-gray-200">
     <div class="p-6 sm:p-8 lg:p-10">
        <header class="text-center border-b border-gray-200 pb-6 mb-8">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-800 mb-2"><?php echo htmlspecialchars($pageTitle); ?></h1>
            <p class="text-lg text-gray-600">Explore the world with our tips and destination guides.</p>
        </header>

        <?php if ($errorMessage): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6" role="alert">
                 <strong class="font-bold">Error!</strong> <span class="block sm:inline"><?php echo htmlspecialchars($errorMessage); ?></span>
            </div>
        <?php endif; ?>

        <?php // --- START DYNAMIC TRAVEL CONTENT --- ?>
        <div class="prose prose-lg max-w-none prose-indigo mb-10">
             <h2>Welcome to the Travel Section</h2>
            <p>
                Dreaming of your next getaway? Find inspiration and practical advice in our latest travel guides below.
            </p>
        </div>

        <?php if (!empty($travelPosts)): ?>
            <h3 class="text-2xl font-semibold text-gray-800 mb-6 border-t pt-8">Latest Travel Guides</h3>
             <div class="space-y-8">
                <?php foreach ($travelPosts as $post): ?>
                    <article class="border-b border-gray-100 pb-8 last:border-b-0 last:pb-0 flex flex-col sm:flex-row sm:items-start sm:space-x-6">
                        <?php if (!empty($post['image_url'])): ?>
                            <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>" class="block w-full sm:w-40 h-40 sm:h-auto sm:flex-shrink-0 mb-4 sm:mb-0 bg-gray-100 rounded overflow-hidden">
                                <img src="<?php echo htmlspecialchars($post['image_url']); ?>"
                                     alt="Thumbnail for <?php echo htmlspecialchars($post['title']); ?>"
                                     class="w-full h-full object-cover" loading="lazy"
                                     onerror="this.onerror=null; this.src='https://placehold.co/400x300/D1FAE5/065F46?text=Travel'; this.style.opacity='0.5';">
                            </a>
                        <?php endif; ?>
                        <div class="flex-grow">
                            <h4 class="font-semibold text-xl hover:text-green-700 transition duration-150 mb-1">
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
                                    } else {
                                         // echo '<span class="text-gray-500 italic">No summary available.</span>'; // Removed fallback text earlier
                                    }
                                ?>
                            </p>
                             <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>" class="text-emerald-600 hover:text-emerald-800 font-medium text-sm mt-2 inline-block">
                                Read More &rarr;
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
                 <div class="mt-8 text-center">
                    <a href="/blog?category=Travel" class="text-emerald-600 hover:text-emerald-800 font-medium">
                        View all travel guides &rarr;
                    </a>
                </div>
            </div>
        <?php elseif (!$errorMessage): ?>
             <div class="text-center text-gray-500 border-t pt-6 mt-6">
                <p>No travel guides have been published yet. Check back soon!</p>
            </div>
        <?php endif; ?>
        <?php // --- END DYNAMIC TRAVEL CONTENT --- ?>

    </div>
</div>
