<?php
// pages/search.php
// handles searching posts and displaying results - UI POLISH

$pageTitle = 'Search Results'; 
$db = getDbConnection(); 
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
$postsPerPage = 6; // Match blog list
$currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($currentPage < 1) { $currentPage = 1; }
$posts = []; 
$totalPosts = 0;
$totalPages = 0;
$errorMessage = null; 
$searchQueryDisplay = htmlspecialchars($searchTerm); 

if (empty($searchTerm)) {
    $errorMessage = "please enter a search term.";
} else {
    try {
        $searchPattern = '%' . $searchTerm . '%'; 
        // Count
        $sqlCount = "SELECT COUNT(*) FROM posts WHERE title LIKE :term OR content LIKE :term";
        $countStmt = $db->prepare($sqlCount);
        $countStmt->execute([':term' => $searchPattern]); 
        $totalPosts = $countStmt->fetchColumn(); 

        if ($totalPosts > 0) {
            // Fetch
            $totalPages = ceil($totalPosts / $postsPerPage);
            if ($currentPage > $totalPages) { $currentPage = $totalPages; }
            $offset = ($currentPage - 1) * $postsPerPage;
            $sqlFetch = "SELECT id, slug, title, category, excerpt, created_at, image_url 
                         FROM posts WHERE title LIKE :term OR content LIKE :term ORDER BY created_at DESC LIMIT :limit OFFSET :offset"; 
            $stmtFetch = $db->prepare($sqlFetch);
            $params = [':term' => $searchPattern, ':limit' => (int)$postsPerPage, ':offset' => (int)$offset];
            $stmtFetch->execute($params); 
            $posts = $stmtFetch->fetchAll(); 
        }
    } catch (PDOException $e) {
        error_log("error searching posts (term: " . $searchTerm . "): " . $e->getMessage());
        $errorMessage = "sorry, couldn't perform search right now.";
    }
}

// Update page title 
if (!empty($searchTerm) && !$errorMessage) { $pageTitle = 'Search Results for "' . $searchQueryDisplay . '"'; } 
elseif (empty($searchTerm)) { $pageTitle = 'Search'; } 

?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"> {/* Consistent padding */}
    <h1 class="text-4xl font-bold text-gray-900 border-b border-gray-300 pb-4 mb-8"><?php echo $pageTitle; ?></h1>

    <?php if ($errorMessage): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6" role="alert">... error message ...</div>
         <p><a href="index.php?page=home" class="text-indigo-600 hover:text-indigo-800">&larr; back home</a></p>
    <?php elseif (!empty($searchTerm) && empty($posts)): ?>
         <div class="bg-white shadow-md rounded-lg p-8 text-center text-gray-500">
             <p>No results found for "<?php echo $searchQueryDisplay; ?>". Try a different search term?</p>
         </div>
    <?php elseif (!empty($searchTerm) && !empty($posts)): ?>
        <p class="text-gray-600 mb-8">Found <?php echo $totalPosts; ?> result(s) for "<?php echo $searchQueryDisplay; ?>".</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"> {/* Grid layout */}
            <?php foreach ($posts as $post): ?>
                 <article class="bg-white shadow-lg rounded-xl overflow-hidden transition duration-300 ease-in-out hover:shadow-xl flex flex-col group"> {/* Card styling */}
                     <div class="p-6 flex flex-col flex-grow"> 
                        <?php 
                            $categoryColor = 'bg-gray-100 text-gray-800'; // Default
                            if (strtolower($post['category']) === 'finance') $categoryColor = 'bg-indigo-100 text-indigo-800';
                            if (strtolower($post['category']) === 'travel') $categoryColor = 'bg-cyan-100 text-cyan-800';
                        ?>
                        <span class="inline-block <?php echo $categoryColor; ?> text-xs font-semibold px-3 py-1 rounded-full mb-3 tracking-wide self-start"><?php echo htmlspecialchars(ucfirst($post['category'])); ?></span>
                        <h2 class="text-xl font-bold text-gray-900 mb-2 leading-tight flex-grow group-hover:text-indigo-600 transition duration-200">
                            <a href="index.php?page=single_post&slug=<?php echo htmlspecialchars($post['slug']); ?>">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        </h2>
                        <div class="text-xs text-gray-500 mb-4">
                            <span><?php echo date('M j, Y', strtotime($post['created_at'])); ?></span> 
                        </div>
                        <p class="text-sm text-gray-700 leading-relaxed mb-5 flex-grow"> 
                            <?php 
                                $excerpt = trim($post['excerpt'] ?? ''); 
                                $displayExcerpt = !empty($excerpt) ? (strlen($excerpt) > 120 ? substr($excerpt, 0, 120) . '...' : $excerpt) : 'No summary available.'; 
                                echo nl2br(htmlspecialchars($displayExcerpt)); 
                            ?>
                        </p>
                        <div class="mt-auto pt-3 border-t border-gray-100"> 
                            <a href="index.php?page=single_post&slug=<?php echo htmlspecialchars($post['slug']); ?>" class="inline-block text-indigo-600 hover:text-indigo-800 font-semibold text-sm group-hover:underline">
                                Read More &rarr;
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <?php // --- pagination links --- ?>
        <?php if ($totalPages > 1): ?>
            <div class="mt-12 pt-6 border-t border-gray-300 flex justify-center items-center text-sm space-x-1"> 
                <?php $baseUrlSearch = "index.php?page=search&q=" . urlencode($searchTerm) . "&p="; ?>
                <?php if ($currentPage > 1): ?><a href="<?php echo $baseUrlSearch . ($currentPage - 1); ?>" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-l-md text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out">&larr; Prev</a><?php else: ?><span class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-l-md text-gray-400 bg-gray-50 cursor-not-allowed">&larr; Prev</span><?php endif; ?>
                <div class="text-gray-600 hidden md:flex items-center space-x-1"> <?php $startPage = max(1, $currentPage - 2); $endPage = min($totalPages, $currentPage + 2); if ($startPage > 1) echo '<span class="inline-flex items-center px-3 py-2 text-gray-500">...</span>'; for ($i = $startPage; $i <= $endPage; $i++): $isActive = ($i == $currentPage); $linkClass = $isActive ? 'inline-flex items-center justify-center px-4 py-2 border border-indigo-600 text-sm font-medium rounded-md text-white bg-indigo-600 z-10 relative' : 'inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out -ml-px relative'; if ($isActive) { echo '<span class="'.$linkClass.'">'.$i.'</span>'; } else { echo '<a href="'.$baseUrlSearch . $i.'" class="'.$linkClass.'">'.$i.'</a>'; } endfor; if ($endPage < $totalPages) echo '<span class="inline-flex items-center px-3 py-2 text-gray-500">...</span>'; ?></div>
                <div class="text-gray-600 md:hidden px-4"> Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></div>
                <?php if ($currentPage < $totalPages): ?><a href="<?php echo $baseUrlSearch . ($currentPage + 1); ?>" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out -ml-px">Next &rarr;</a><?php else: ?><span class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-400 bg-gray-50 cursor-not-allowed -ml-px">Next &rarr;</span><?php endif; ?>
            </div>
        <?php endif; ?>
        <?php // --- end pagination links --- ?>
    <?php endif; ?>
    <?php if (empty($searchTerm) && !$errorMessage): ?>
         <div class="bg-white shadow rounded-lg p-6 text-center text-gray-500"><p>please enter a term in the search box above.</p></div>
    <?php endif; ?>
</div> 