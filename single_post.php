<?php
// pages/single_post.php - FINAL VERSION v3 (Features Added, Clean URL Links)

// The $post variable is already fetched and checked by index.php
if (!isset($post) || !$post) {
    echo '<div class="bg-red-100 text-red-700 p-4 rounded shadow">Error: Post data not available. Please check the URL or go back.</div>';
    return; // Stop if no post data
}

// --- Calculate Estimated Reading Time ---
$wordCount = str_word_count(strip_tags($post['content'] ?? ''));
$wordsPerMinute = 200; // Average reading speed
$readingTimeMinutes = ceil($wordCount / $wordsPerMinute);
$readingTimeText = ($readingTimeMinutes < 1) ? 'Less than 1 min read' : $readingTimeMinutes . ' min read';
// --- End Reading Time Calculation ---

// --- Fetch Related Posts ---
$relatedPosts = [];
$currentPostId = $post['id'] ?? 0;
$currentCategory = $post['category'] ?? null;

if ($currentCategory && $currentPostId > 0) {
    try {
        $db = getDbConnection();
        $sqlRelated = "SELECT id, slug, title, excerpt, image_url
                       FROM posts
                       WHERE category = :category AND id != :current_id
                       ORDER BY created_at DESC LIMIT 3";
        $stmtRelated = $db->prepare($sqlRelated);
        $stmtRelated->bindParam(':category', $currentCategory, PDO::PARAM_STR);
        $stmtRelated->bindParam(':current_id', $currentPostId, PDO::PARAM_INT);
        $stmtRelated->execute();
        $relatedPosts = $stmtRelated->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching related posts: " . $e->getMessage());
        $relatedPosts = [];
    }
}
// --- End Fetch Related Posts ---

// Set page title (already done in index.php)
$pageTitle = $post['title'] ?? 'Blog Post';

?>

<article class="bg-white rounded-lg shadow-xl overflow-hidden border border-gray-200 mb-12">
    <?php // Featured Image Section ?>
    <?php if (!empty($post['image_url'])): ?>
        <div class="w-full h-64 sm:h-80 md:h-[450px] bg-gray-200">
             <img src="<?php echo htmlspecialchars($post['image_url']); ?>"
                 alt="Featured image for <?php echo htmlspecialchars($post['title'] ?? 'Blog Post'); ?>"
                 class="w-full h-full object-cover" loading="lazy"
                 onerror="this.onerror=null; this.src='https://placehold.co/1200x600/E2E8F0/4A5568?text=Image+Not+Found'; this.style.opacity='0.5';">
        </div>
    <?php endif; ?>

    <div class="p-6 sm:p-8 lg:p-12">
        <?php // Post Header ?>
        <header class="mb-8 sm:mb-10 border-b border-gray-200 pb-6">
            <?php // Category Badge & Reading Time ?>
            <div class="mb-4 flex justify-between items-center text-sm text-gray-500 flex-wrap">
                <?php // Category Badge Link - Use clean URL with query param ?>
                 <a href="/blog?category=<?php echo urlencode($post['category'] ?? 'General'); ?>" class="font-semibold uppercase tracking-wider px-3 py-1 rounded-full text-xs mr-3 mb-1 sm:mb-0 <?php
                        $categoryLower = strtolower($post['category'] ?? '');
                        $badgeClass = 'bg-gray-100 text-gray-800 hover:bg-gray-200';
                        if ($categoryLower === 'finance') $badgeClass = 'bg-blue-100 text-blue-800 hover:bg-blue-200';
                        elseif ($categoryLower === 'travel') $badgeClass = 'bg-green-100 text-green-800 hover:bg-green-200';
                        echo $badgeClass; ?>">
                    <?php echo htmlspecialchars(ucfirst($post['category'] ?? 'Uncategorized')); ?>
                 </a>
                <span class="whitespace-nowrap mb-1 sm:mb-0"> <?php // Reading Time ?>
                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-block h-4 w-4 mr-1 relative -top-px"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <?php echo $readingTimeText; ?>
                </span>
            </div>
            <?php // Post Title ?>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight mb-4">
                <?php echo htmlspecialchars($post['title'] ?? 'Untitled Post'); ?>
            </h1>
            <?php // Meta Info: Author and Published Date ?>
            <div class="text-sm text-gray-500 flex flex-wrap items-center">
                <?php if (!empty($post['author_name'])): ?>
                    <span class="mr-3 inline-flex items-center">
                         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-block h-4 w-4 mr-1"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        By <?php echo htmlspecialchars($post['author_name']); ?>
                    </span>
                    <?php if (isset($post['created_at'])): ?>
                         <span class="hidden sm:inline mx-1"> | </span>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (isset($post['created_at'])): ?>
                    <span class="mr-3 inline-flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-block h-4 w-4 mr-1"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                        <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                    </span>
                <?php else: ?>
                     <span class="mr-3 inline-flex items-center">Unknown Date</span>
                <?php endif; ?>
             </div>
        </header>

        <?php // Main Post Content Area - Left Aligned & Wider ?>
        <div class="prose prose-lg lg:prose-xl prose-indigo
                    prose-a:text-blue-600 hover:prose-a:text-blue-800 hover:prose-a:underline
                    prose-blockquote:border-l-4 prose-blockquote:border-blue-500 prose-blockquote:pl-4 prose-blockquote:italic prose-blockquote:text-gray-600
                    prose-headings:font-semibold prose-headings:text-gray-800
                    prose-img:rounded-md prose-img:shadow-sm">
            <?php
                // Ensure code blocks use <pre><code class="language-xyz"> for Prism.js
                echo $post['content'] ?? '<p class="text-red-500">Post content is missing.</p>';
            ?>
        </div>

        <?php // Post Footer Section ?>
        <footer class="mt-10 pt-8 border-t border-gray-200">
            <?php // Social Share Links ?>
            <div class="mb-6 text-center sm:text-left">
                <span class="text-sm font-semibold text-gray-700 mr-3">Share this post:</span>
                <div class="inline-block space-x-2">
                    <?php
                        $currentPageUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/blog/" . ($post['slug'] ?? '');
                        $encodedUrl = urlencode($currentPageUrl);
                        $encodedTitle = urlencode($post['title'] ?? SITE_NAME);
                    ?>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo $encodedUrl; ?>&text=<?php echo $encodedTitle; ?>" target="_blank" rel="noopener noreferrer" class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-700 p-2 rounded-full transition duration-200" title="Share on Twitter/X"><span class="sr-only">Twitter/X</span><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24h-6.617l-5.21-6.817-6.044 6.817h-3.308l7.73-8.835-7.997-10.66h6.772l4.616 6.13z"/></svg></a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $encodedUrl; ?>" target="_blank" rel="noopener noreferrer" class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-700 p-2 rounded-full transition duration-200" title="Share on Facebook"><span class="sr-only">Facebook</span><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm3 8h-1.35c-.538 0-.65.221-.65.778v1.222h2l-.209 2h-1.791v7h-3v-7h-2v-2h2v-2.308c0-1.769.931-2.692 3.029-2.692h1.971v3z"/></svg></a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $encodedUrl; ?>" target="_blank" rel="noopener noreferrer" class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-700 p-2 rounded-full transition duration-200" title="Share on LinkedIn"><span class="sr-only">LinkedIn</span><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm-2 16h-2v-6h2v6zm-1-6.891c-.607 0-1.1-.496-1.1-1.109 0-.612.493-1.109 1.1-1.109s1.1.497 1.1 1.109c0 .613-.493 1.109-1.1 1.109zm8 6.891h-2v-3.258c0-.777-.018-1.777-1.083-1.777-1.084 0-1.25.844-1.25 1.721v3.314h-2v-6h2v.911c.277-.522.956-.911 1.932-.911 2.068 0 2.448 1.359 2.448 3.128v3.878z"/></svg></a>
                 </div>
            </div>

            <?php // Navigation Links - Use Clean URLs ?>
            <div class="flex flex-col sm:flex-row justify-between items-center pt-6 border-t border-gray-200">
                 <a href="/blog" class="text-blue-600 hover:text-blue-800 transition duration-200 mb-4 sm:mb-0">
                    &larr; Back to Blog
                 </a>
                 <?php // Admin Edit Link - Use Clean URL structure ?>
                 <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true && isset($post['id'])): ?>
                     <a href="/admin/edit/<?php echo $post['id']; ?>" class="text-sm text-gray-500 hover:text-indigo-600 underline">
                         Edit This Post
                     </a>
                 <?php endif; ?>
             </div>
        </footer>

    </div> <?php // End content padding ?>
</article>


<?php // --- Related Posts Section --- ?>
<?php if (!empty($relatedPosts)): ?>
<section class="mt-16 pt-10 border-t border-gray-200">
    <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-8 text-center">you might also like...</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
        <?php foreach ($relatedPosts as $relatedPost): ?>
            <article class="bg-white shadow-lg rounded-xl overflow-hidden transition duration-300 ease-in-out hover:shadow-xl flex flex-col group">
                 <?php // Related Post Image - Use clean URL ?>
                 <?php if (!empty($relatedPost['image_url'])): ?>
                    <a href="/blog/<?php echo htmlspecialchars($relatedPost['slug']); ?>" class="block h-40 overflow-hidden bg-gray-100">
                        <img src="<?php echo htmlspecialchars($relatedPost['image_url']); ?>"
                             alt="Thumbnail for <?php echo htmlspecialchars($relatedPost['title']); ?>"
                             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                             loading="lazy"
                             onerror="this.onerror=null; this.src='https://placehold.co/400x250/E2E8F0/4A5568?text=No+Image';">
                    </a>
                 <?php endif; ?>
                 <?php // Related Post Content ?>
                 <div class="p-4 flex flex-col flex-grow">
                    <?php // Related Post Title - Use clean URL ?>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2 leading-tight flex-grow group-hover:text-indigo-600 transition duration-200">
                        <a href="/blog/<?php echo htmlspecialchars($relatedPost['slug']); ?>">
                            <?php echo htmlspecialchars($relatedPost['title']); ?>
                        </a>
                    </h3>
                    <div class="mt-auto pt-2">
                        <?php // Related Post Read More - Use clean URL ?>
                        <a href="/blog/<?php echo htmlspecialchars($relatedPost['slug']); ?>" class="inline-block text-indigo-600 hover:text-indigo-800 font-medium text-sm group-hover:underline">
                            Read More &rarr;
                        </a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
<?php // --- End Related Posts Section --- ?>

