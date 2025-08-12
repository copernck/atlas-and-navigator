<?php
// pages/blog.php - FINAL VERSION v3 (Category Filtering & Clean URL Links)

$db = getDbConnection();

// --- Category Filtering ---
$filterCategory = isset($_GET['category']) ? trim($_GET['category']) : null;
$pageTitle = 'Blog'; // Default title
if ($filterCategory) {
    $pageTitle = htmlspecialchars(ucfirst($filterCategory)) . ' Articles';
} else {
     $pageTitle = 'Blog - All Articles';
}

// --- Pagination Settings ---
$postsPerPage = 6;
$currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($currentPage < 1) { $currentPage = 1; }

// --- Data Fetching Initialization ---
$posts = [];
$totalPosts = 0;
$totalPages = 0;
$errorMessage = null;
$params = []; // Parameters for prepared statements

try {
    // --- Build SQL WHERE clause based on filter ---
    $whereClause = '';
    if ($filterCategory) {
        $whereClause = " WHERE LOWER(category) = LOWER(:category)";
        $params[':category'] = $filterCategory;
    }

    // --- Count Total Posts (with filter) ---
    $sqlCount = "SELECT COUNT(*) FROM posts" . $whereClause;
    $countStmt = $db->prepare($sqlCount);
    $countStmt->execute($params);
    $totalPosts = $countStmt->fetchColumn();

    // --- Fetch Posts for Current Page (with filter) ---
    if ($totalPosts > 0) {
        $totalPages = ceil($totalPosts / $postsPerPage);
        if ($currentPage > $totalPages) { $currentPage = $totalPages; }
        $offset = ($currentPage - 1) * $postsPerPage;

        $params[':limit'] = (int)$postsPerPage;
        $params[':offset'] = (int)$offset;

        $sqlFetch = "SELECT id, slug, title, category, excerpt, created_at, image_url
                     FROM posts" . $whereClause .
                    " ORDER BY created_at DESC
                     LIMIT :limit OFFSET :offset";

        $stmtFetch = $db->prepare($sqlFetch);

        foreach ($params as $key => &$val) {
             if ($key === ':limit' || $key === ':offset') {
                 $stmtFetch->bindValue($key, $val, PDO::PARAM_INT);
             } else {
                 $stmtFetch->bindValue($key, $val, PDO::PARAM_STR);
             }
        }
        unset($val);

        $stmtFetch->execute();
        $posts = $stmtFetch->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    error_log("error fetching posts (category: " . $filterCategory . "): " . $e->getMessage());
    $errorMessage = "sorry, couldn't load blog posts right now.";
}
?>

<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 border-b border-gray-300 pb-4 mb-8"><?php echo htmlspecialchars($pageTitle); ?></h1>

    <?php // Link to view all posts if currently filtered ?>
    <?php if ($filterCategory): ?>
        <div class="mb-6 text-sm">
            <a href="/blog" class="text-indigo-600 hover:text-indigo-800 font-medium transition duration-150 ease-in-out">&larr; View All Posts</a>
        </div>
    <?php endif; ?>


    <?php if ($errorMessage): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6" role="alert">
             <strong class="font-bold">Error!</strong> <span class="block sm:inline"><?php echo htmlspecialchars($errorMessage); ?></span>
        </div>
    <?php elseif (empty($posts)): ?>
         <div class="bg-white shadow-md rounded-lg p-8 text-center text-gray-500">
             <p>No <?php echo $filterCategory ? htmlspecialchars($filterCategory) : ''; ?> blog posts found yet.</p>
             <?php if (!$filterCategory && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                 <p class="mt-2">Maybe <a href="/admin/create" class="text-indigo-600 hover:text-indigo-800 font-semibold">create one</a>?</p>
             <?php elseif ($filterCategory): ?>
                  <p class="mt-2"><a href="/blog" class="text-indigo-600 hover:text-indigo-800 font-semibold">View all posts</a></p>
             <?php endif; ?>
         </div>
    <?php else: ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            <?php foreach ($posts as $post): ?>
                <article class="bg-white shadow-lg rounded-xl overflow-hidden transition duration-300 ease-in-out hover:shadow-xl flex flex-col group">
                     <?php if (!empty($post['image_url'])): ?>
                        <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>" class="block h-48 overflow-hidden bg-gray-100">
                            <img src="<?php echo htmlspecialchars($post['image_url']); ?>"
                                 alt="Featured image for <?php echo htmlspecialchars($post['title']); ?>"
                                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='https://placehold.co/600x400/E2E8F0/4A5568?text=Image+Error';">
                        </a>
                     <?php endif; ?>

                     <div class="p-6 flex flex-col flex-grow">
                        <?php // Category Badge Link - Use clean URL with query param ?>
                        <?php
                            $categoryLower = strtolower($post['category'] ?? '');
                            $categoryDisplay = htmlspecialchars(ucfirst($post['category'] ?? 'General'));
                            $categoryUrl = "/blog?category=" . urlencode($post['category'] ?? 'General');
                            $categoryColor = 'bg-gray-100 text-gray-800 hover:bg-gray-200'; // Default
                            if ($categoryLower === 'finance') $categoryColor = 'bg-indigo-100 text-indigo-800 hover:bg-indigo-200';
                            if ($categoryLower === 'travel') $categoryColor = 'bg-cyan-100 text-cyan-800 hover:bg-cyan-200';
                        ?>
                        <a href="<?php echo $categoryUrl; ?>" class="inline-block <?php echo $categoryColor; ?> text-xs font-semibold px-3 py-1 rounded-full mb-3 tracking-wide self-start transition duration-150 ease-in-out" title="View posts in <?php echo $categoryDisplay; ?> category">
                            <?php echo $categoryDisplay; ?>
                        </a>

                        <h2 class="text-xl font-bold text-gray-900 mb-2 leading-tight flex-grow group-hover:text-indigo-600 transition duration-200">
                            <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        </h2>
                        <div class="text-xs text-gray-500 mb-4">
                            <span><?php echo date('M j, Y', strtotime($post['created_at'])); ?></span>
                        </div>

                        <?php // Excerpt Handling ?>
                        <?php
                            $rawExcerpt = trim($post['excerpt'] ?? '');
                            if (!empty($rawExcerpt)) {
                                $sanitizedExcerpt = htmlspecialchars($rawExcerpt, ENT_QUOTES, 'UTF-8');
                                $displayExcerpt = strlen($sanitizedExcerpt) > 120 ? substr($sanitizedExcerpt, 0, 120) . '...' : $sanitizedExcerpt;
                                echo '<p class="text-sm text-gray-700 leading-relaxed mb-5 flex-grow">' . nl2br($displayExcerpt) . '</p>';
                            }
                        ?>

                        <div class="mt-auto pt-3 border-t border-gray-100">
                            <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>" class="inline-block text-indigo-600 hover:text-indigo-800 font-semibold text-sm group-hover:underline">
                                Read More &rarr;
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div> <?php // End grid ?>

        <?php // --- Pagination Links (Adjusted for clean URL base and category filter) --- ?>
        <?php if ($totalPages > 1): ?>
            <nav class="mt-12 pt-6 border-t border-gray-300 flex justify-center items-center text-sm space-x-1" aria-label="Pagination">
                 <?php $basePageUrl = "/blog";
                       $queryParams = [];
                       if ($filterCategory) $queryParams['category'] = $filterCategory;
                       $baseUrl = $basePageUrl . (!empty($queryParams) ? '?' . http_build_query($queryParams) . '&' : '?') . 'p=';
                 ?>

                 <?php if ($currentPage > 1): ?>
                     <a href="<?php echo $baseUrl . ($currentPage - 1); ?>" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-l-md text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out">&larr; Prev</a>
                 <?php else: ?>
                     <span class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-l-md text-gray-400 bg-gray-50 cursor-not-allowed" aria-disabled="true">&larr; Prev</span>
                 <?php endif; ?>
                 <div class="text-gray-600 hidden md:flex items-center space-x-1">
                     <?php $linkLimit = 2; $startPage = max(1, $currentPage - $linkLimit); $endPage = min($totalPages, $currentPage + $linkLimit); if ($startPage > 1) { echo '<a href="'.$baseUrl.'1'.'" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out -ml-px relative">1</a>'; if ($startPage > 2) echo '<span class="inline-flex items-center px-3 py-2 text-gray-500">...</span>'; } for ($i = $startPage; $i <= $endPage; $i++): $isActive = ($i == $currentPage); $linkClass = $isActive ? 'inline-flex items-center justify-center px-4 py-2 border border-indigo-600 text-sm font-medium rounded-md text-white bg-indigo-600 z-10 relative' : 'inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out -ml-px relative'; if ($isActive) { echo '<span class="'.$linkClass.'" aria-current="page">'.$i.'</span>'; } else { echo '<a href="'.$baseUrl . $i.'" class="'.$linkClass.'">'.$i.'</a>'; } endfor; if ($endPage < $totalPages) { if ($endPage < $totalPages - 1) echo '<span class="inline-flex items-center px-3 py-2 text-gray-500">...</span>'; echo '<a href="'.$baseUrl.$totalPages.'" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out -ml-px relative">'.$totalPages.'</a>'; } ?>
                 </div>
                 <div class="text-gray-600 md:hidden px-4"> Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></div>
                 <?php if ($currentPage < $totalPages): ?>
                     <a href="<?php echo $baseUrl . ($currentPage + 1); ?>" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out -ml-px">Next &rarr;</a>
                 <?php else: ?>
                     <span class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-400 bg-gray-50 cursor-not-allowed -ml-px" aria-disabled="true">Next &rarr;</span>
                 <?php endif; ?>
            </nav>
        <?php endif; // End pagination ?>

    <?php endif; // End else (display posts) ?>
</div> <?php // End container ?>
