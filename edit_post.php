<?php
// pages/edit_post.php - FINAL VERSION v6 (Fix execute() param count)

// ** Auth check removed - Handled by index.php **

// Need session for fallback username, ensure started
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$loggedInAdminUsername = $_SESSION['admin_username'] ?? 'Unknown Admin';

$db = getDbConnection();
$allowedCategories = ['Blog', 'Finance', 'Travel'];
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0; // Get ID from GET param (set by index.php router)
$title = $slug = $category = $excerpt = $content = $author_name = '';
$errorMessage = $successMessage = null;
$postExists = false; $postData = null;

// --- Fetch existing post data ---
if ($postId > 0) {
    try {
        $sqlFetch = "SELECT * FROM posts WHERE id = :post_id LIMIT 1"; // Use named placeholder
        $stmtFetch = $db->prepare($sqlFetch);
        // Bind the parameter for fetching
        $stmtFetch->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $stmtFetch->execute();
        $postData = $stmtFetch->fetch();

        if ($postData) {
            $postExists = true;
            // Pre-fill form on initial GET or if validation failed on POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !empty($errorMessage)) {
                $title = $postData['title']; $slug = $postData['slug']; $category = $postData['category'];
                $excerpt = $postData['excerpt']; $content = $postData['content'];
                $author_name = $postData['author_name'] ?? $loggedInAdminUsername;
            } else {
                // Keep submitted values if POST is in progress and no error yet
                // These are set below in the POST handling block
            }
        } else { $errorMessage = "post not found (id: " . $postId . ")."; $postExists = false; }
    } catch (PDOException $e) { error_log("edit post fetch error: " . $e->getMessage()); $errorMessage = "db error fetching."; $postExists = false; }
} else { $errorMessage = "invalid post id."; $postExists = false; }

// --- Slug generation helper ---
function generateSlug($text) {
    $text = strtolower(trim($text)); $text = preg_replace('/[^a-z0-9\-]+/', '-', $text);
    $text = preg_replace('~-+~', '-', $text); $text = trim($text, '-');
    if (empty($text)) { return 'n-a-' . time(); } return $text;
}

// --- Handle form submission for UPDATE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $postExists) {
    $submitted_post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $slug = isset($_POST['slug']) ? trim($_POST['slug']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';
    $author_name = isset($_POST['author_name']) ? trim($_POST['author_name']) : $loggedInAdminUsername;
    $excerpt = isset($_POST['excerpt']) ? trim($_POST['excerpt']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $currentErrorMessage = null;

    // Validation
    if ($submitted_post_id !== $postId) { $currentErrorMessage = "id mismatch."; }
    elseif (empty($title)) { $currentErrorMessage = 'title required.'; }
    elseif (empty($category) || !in_array($category, $allowedCategories)) { $currentErrorMessage = 'valid category required.';}
    elseif (empty($content)) { $currentErrorMessage = 'content required.';}
    elseif (empty($author_name)) { $currentErrorMessage = 'author required.'; }
    else {
        // Generate/clean slug
        $submittedSlug = $slug; if (empty($slug)) { $slug = generateSlug($title); } else { $slug = generateSlug($slug); }
        try {
            // Check slug uniqueness logic... (same as before)
            $originalSlug = $slug; $counter = 1; $slugCheckError = null; $infoMessage = null;
            while (true) {
                $sqlCheck = "SELECT id FROM posts WHERE slug = ? AND id != ? LIMIT 1";
                $stmtCheck = $db->prepare($sqlCheck); $stmtCheck->execute([$slug, $postId]);
                if (!$stmtCheck->fetch()) { break; }
                $slug = $originalSlug . '-' . $counter++;
                if ($counter > 100) { $slugCheckError = "could not generate unique slug."; break; }
            }
            if ($slug !== $originalSlug && !$slugCheckError) { $infoMessage = "note: used unique slug: " . htmlspecialchars($slug); }
            if ($slugCheckError) { $currentErrorMessage = ($currentErrorMessage ? $currentErrorMessage . ' ' : '') . $slugCheckError; }

            // Proceed with update if no fatal errors so far
            if (empty($currentErrorMessage)) {
                 // SQL has 7 placeholders: 6 for SET, 1 for WHERE
                 $sqlUpdate = "UPDATE posts SET title=?, slug=?, category=?, excerpt=?, content=?, author_name=? WHERE id=?";
                 $stmtUpdate = $db->prepare($sqlUpdate);
                 // ** FIXED: Pass exactly 7 values in the correct order to execute() **
                 $success = $stmtUpdate->execute([
                     $title,         // corresponds to title=?
                     $slug,          // corresponds to slug=?
                     $category,      // corresponds to category=?
                     $excerpt,       // corresponds to excerpt=?
                     $content,       // corresponds to content=?
                     $author_name,   // corresponds to author_name=?
                     $postId         // corresponds to id=?
                 ]);

                 if ($success) {
                     $successMessage = "post updated! (ID: " . $postId . ")";
                     if ($infoMessage) { $successMessage .= " " . $infoMessage; }
                     // Re-fetch data to show updated values in form
                     // Need to re-prepare or re-use the fetch statement
                     $stmtFetchAfterUpdate = $db->prepare("SELECT * FROM posts WHERE id = :post_id LIMIT 1");
                     $stmtFetchAfterUpdate->bindParam(':post_id', $postId, PDO::PARAM_INT);
                     $stmtFetchAfterUpdate->execute();
                     $postData = $stmtFetchAfterUpdate->fetch();
                     if ($postData) {
                          // Update local variables to reflect the newly saved state
                          $title=$postData['title']; $slug=$postData['slug']; $category=$postData['category'];
                          $excerpt=$postData['excerpt']; $content=$postData['content']; $author_name=$postData['author_name']??'';
                     } else { $currentErrorMessage = "could not re-fetch after update."; $postExists = false; }
                 } else {
                     // If execute() returns false but doesn't throw exception
                     $currentErrorMessage = ($infoMessage ? $infoMessage.' ' : '') . "database error: failed to update post (execute returned false).";
                 }
            } elseif ($infoMessage) {
                 // Append slug info message to existing validation error
                 $currentErrorMessage = ($currentErrorMessage ? $currentErrorMessage.' ' : '') . $infoMessage;
            }
        } catch (PDOException $e) {
            error_log("update post PDO error: " . $e->getMessage());
            // Show specific error temporarily for debugging if needed
            // $specificError = htmlspecialchars($e->getMessage());
            // $currentErrorMessage = "db error during update. Debug: " . $specificError;
            $currentErrorMessage = "db error during update."; // Keep generic for user
        } catch (Exception $e) {
             error_log("update post general error: " . $e->getMessage());
             $currentErrorMessage = "unexpected error during update.";
        }
    }
    // Assign any validation/processing errors back to the main error variable for display
    if ($currentErrorMessage) { $errorMessage = $currentErrorMessage; }
} // end POST handling

// --- Set Page Title ---
$displayTitle = $title ?: ($postData['title'] ?? 'Post');
if ($postExists) { $pageTitle = 'Edit Post: ' . htmlspecialchars($displayTitle); } else { $pageTitle = 'Edit Post Error'; }
?>

<?php // TinyMCE Script Link ?>
<script src="https://cdn.tiny.cloud/1/lumtk06f3ud335ligtz7kn7bxhrbx34u4diaffoii295ftv3/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<div class="bg-white shadow rounded-lg p-6 lg:p-8">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
         <h1 class="text-3xl font-bold text-gray-800"><?php echo $pageTitle; ?></h1>
         <a href="index.php?page=manage_posts" class="text-sm text-blue-600 hover:text-blue-800">&larr; back to manage posts</a>
    </div>
    <?php if ($successMessage): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">success!</strong> <span class="block sm:inline"><?php echo htmlspecialchars($successMessage); ?></span>
             <a href="/blog/<?php echo urlencode($slug); ?>" target="_blank" class="font-bold underline ml-2">view post</a>
        </div>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
         <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">error/note:</strong> <span class="block sm:inline"><?php echo htmlspecialchars($errorMessage); ?></span>
        </div>
    <?php endif; ?>

    <?php if ($postExists): ?>
        <form action="index.php?page=edit_post&id=<?php echo $postId; ?>" method="post" class="space-y-4">
            <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
            <div><label for="title" class="block text-sm font-medium text-gray-700">title:</label><input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required class="mt-1 block w-full form-input"></div>
            <div><label for="slug" class="block text-sm font-medium text-gray-700">slug:</label><input type="text" id="slug" name="slug" value="<?php echo htmlspecialchars($slug); ?>" pattern="[a-z0-9\-]+" placeholder="auto-generate from title" class="mt-1 block w-full form-input"><p class="text-xs text-gray-500 mt-1">lowercase, numbers, hyphens only.</p></div>
            <div><label for="category" class="block text-sm font-medium text-gray-700">category:</label><select id="category" name="category" required class="mt-1 block w-full form-select"><option value="">-- select --</option><?php foreach ($allowedCategories as $cat): ?><option value="<?php echo htmlspecialchars($cat); ?>" <?php if ($category == $cat) echo 'selected'; ?>><?php echo htmlspecialchars($cat); ?></option><?php endforeach; ?></select></div>
            <div><label for="author_name" class="block text-sm font-medium text-gray-700">author:</label><input type="text" id="author_name" name="author_name" value="<?php echo htmlspecialchars($author_name); ?>" required class="mt-1 block w-full form-input"><p class="text-xs text-gray-500 mt-1">name to display as author.</p></div>
            <div><label for="excerpt" class="block text-sm font-medium text-gray-700">excerpt:</label><textarea id="excerpt" name="excerpt" rows="3" class="mt-1 block w-full form-textarea"><?php echo htmlspecialchars($excerpt); ?></textarea></div>
            <div><label for="tinymce-editor" class="block text-sm font-medium text-gray-700">content:</label><textarea id="tinymce-editor" name="content" rows="20" class="mt-1 block w-full form-textarea"><?php echo htmlspecialchars($content); ?></textarea></div>
            <div><button type="submit" class="btn btn-primary">update post</button></div>
        </form>
    <?php elseif (!$errorMessage): ?>
         <p class="text-red-500">invalid post id specified.</p>
         <p><a href="index.php?page=manage_posts" class="text-sm text-blue-600 hover:text-blue-800">&larr; back to manage posts</a></p>
    <?php endif; ?>
</div>

<?php // TinyMCE Initialization Script ?>
<script>
  document.addEventListener('DOMContentLoaded', function() { if (typeof tinymce !== 'undefined') { tinymce.init({ selector: '#tinymce-editor', plugins: 'code table lists link image media wordcount help autoresize', toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media table | code help', height: 500, menubar: false, statusbar: false, content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }', setup: function (editor) { editor.on('change', function () { tinymce.triggerSave(); }); } }); } else { console.error("TinyMCE script not loaded."); } });
</script>
<?php // Basic form styling ?>
<style> .form-input, .form-textarea, .form-select { border-width: 1px; border-color: #d1d5db; border-radius: 0.375rem; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05); padding: 0.5rem 0.75rem; font-size: 0.875rem; line-height: 1.25rem; width: 100%; } .form-input:focus, .form-textarea:focus, .form-select:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgb(199 210 254 / 40%); outline: 2px solid transparent; outline-offset: 2px; } .btn { display: inline-flex; items-center; justify-content: center; padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 500; transition: background-color 0.15s ease-in-out; cursor: pointer; } .btn-primary { background-color: #4f46e5; color: white; border: 1px solid transparent; } .btn-primary:hover { background-color: #4338ca; } </style>
