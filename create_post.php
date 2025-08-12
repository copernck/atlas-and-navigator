<?php
// pages/create_post.php - FINAL VERSION v6 (Added DB Error Echo for Debug)

// ** Auth check removed - Handled by index.php **

// Need session for fallback username, ensure started
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$loggedInAdminUsername = $_SESSION['admin_username'] ?? 'Unknown Admin';

$pageTitle = 'Create New Post';
$db = getDbConnection();
$allowedCategories = ['Blog', 'Finance', 'Travel'];
$title = $slug = $category = $excerpt = $content = '';
$author_name = $loggedInAdminUsername; // Default author
$errorMessage = $successMessage = null;

function generateSlug($text) { /* ... (slug function as before) ... */
    $text = strtolower(trim($text)); $text = preg_replace('/[^a-z0-9\-]+/', '-', $text);
    $text = preg_replace('~-+~', '-', $text); $text = trim($text, '-');
    if (empty($text)) { return 'n-a-' . time(); } return $text;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $slug = isset($_POST['slug']) ? trim($_POST['slug']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';
    $author_name = isset($_POST['author_name']) ? trim($_POST['author_name']) : $loggedInAdminUsername;
    $excerpt = isset($_POST['excerpt']) ? trim($_POST['excerpt']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';

    // Validation
    if (empty($title)) { $errorMessage = 'post title is required.'; }
    elseif (empty($category) || !in_array($category, $allowedCategories)) { $errorMessage = 'please select a valid category.'; }
    elseif (empty($content)) { $errorMessage = 'post content cannot be empty.'; }
    elseif (empty($author_name)) { $errorMessage = 'author name cannot be empty.'; }
    else {
        if (empty($slug)) { $slug = generateSlug($title); } else { $slug = generateSlug($slug); }
        try {
            // Check slug uniqueness logic... (same as before)
            $originalSlug = $slug; $counter = 1; $slugCheckError = null; $infoMessage = null;
            while (true) {
                $sqlCheck = "SELECT id FROM posts WHERE slug = ? LIMIT 1";
                $stmtCheck = $db->prepare($sqlCheck); $stmtCheck->execute([$slug]);
                if (!$stmtCheck->fetch()) { break; }
                $slug = $originalSlug . '-' . $counter++;
                if ($counter > 100) { $slugCheckError = "could not generate unique slug."; break; }
            }
            if ($slug !== $originalSlug && !$slugCheckError) { $infoMessage = "note: used unique slug: " . htmlspecialchars($slug); }
            if ($slugCheckError) { $errorMessage = ($errorMessage ? $errorMessage . ' ' : '') . $slugCheckError; }

            if (!$slugCheckError) {
                 $sqlInsert = "INSERT INTO posts (title, slug, category, excerpt, content, author_name) VALUES (?, ?, ?, ?, ?, ?)";
                 $stmtInsert = $db->prepare($sqlInsert);
                 $success = $stmtInsert->execute([$title, $slug, $category, $excerpt, $content, $author_name]);
                 if ($success) {
                     $newPostId = $db->lastInsertId();
                     $successMessage = "post created! (ID: {$newPostId}) slug: " . htmlspecialchars($slug);
                     if ($infoMessage) { $successMessage .= " (" . $infoMessage . ")"; }
                     $title = $slug = $category = $excerpt = $content = ''; $author_name = $loggedInAdminUsername;
                 } else {
                     // Combine potential info message with the error
                     $errorMessage = ($infoMessage ? $infoMessage.' ' : '') . "database error: failed to create post.";
                     // ** This case might not be reached if execute throws PDOException **
                 }
            } elseif ($infoMessage) { $errorMessage = ($errorMessage ? $errorMessage.' ' : '') . $infoMessage; }
        } catch (PDOException $e) {
            error_log("create post PDO error: " . $e->getMessage());
            // ** TEMPORARY DEBUGGING: Show specific error on page **
            // ** REMOVE THIS LINE AFTER DEBUGGING - SECURITY RISK **
            $specificError = htmlspecialchars($e->getMessage());
            $errorMessage = "db error. <br><strong>debug info (remove after fixing!):</strong> " . $specificError;
            // ** END TEMPORARY DEBUGGING **
        } catch (Exception $e) {
             error_log("create post general error: " . $e->getMessage());
             $errorMessage = "unexpected error.";
        }
    }
} else { $author_name = $loggedInAdminUsername; }
?>

<?php // TinyMCE Script Link ?>
<script src="https://cdn.tiny.cloud/1/lumtk06f3ud335ligtz7kn7bxhrbx34u4diaffoii295ftv3/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<div class="bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
         <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($pageTitle); ?></h1>
         <a href="index.php?page=admin" class="text-sm text-blue-600 hover:text-blue-800">&larr; back to admin</a>
    </div>
    <?php if ($successMessage): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">success!</strong> <span class="block sm:inline"><?php echo htmlspecialchars($successMessage); ?></span>
            <?php if (isset($newPostId) && $newPostId > 0): ?>
                 <a href="/blog/<?php echo urlencode($slug); ?>" target="_blank" class="font-bold underline ml-2">view</a> | <a href="index.php?page=edit_post&id=<?php echo $newPostId; ?>" class="font-bold underline ml-1">edit</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php // Error message now includes debug info if PDOException occurred ?>
    <?php if ($errorMessage): ?>
         <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">error/note:</strong> <span class="block sm:inline"><?php echo $errorMessage; /* Use echo directly as message might contain <br> */ ?></span>
        </div>
    <?php endif; ?>

    <form action="index.php?page=create_post" method="post" class="space-y-4">
        <div><label for="title" class="block text-sm font-medium text-gray-700">title:</label><input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required class="mt-1 block w-full form-input"></div>
        <div><label for="slug" class="block text-sm font-medium text-gray-700">slug:</label><input type="text" id="slug" name="slug" value="<?php echo htmlspecialchars($slug); ?>" pattern="[a-z0-9\-]+" placeholder="auto-generate from title" class="mt-1 block w-full form-input"><p class="text-xs text-gray-500 mt-1">lowercase, numbers, hyphens only.</p></div>
        <div><label for="category" class="block text-sm font-medium text-gray-700">category:</label><select id="category" name="category" required class="mt-1 block w-full form-select"><option value="">-- select --</option><?php foreach ($allowedCategories as $cat): ?><option value="<?php echo htmlspecialchars($cat); ?>" <?php if ($category == $cat) echo 'selected'; ?>><?php echo htmlspecialchars($cat); ?></option><?php endforeach; ?></select></div>
        <div><label for="author_name" class="block text-sm font-medium text-gray-700">author:</label><input type="text" id="author_name" name="author_name" value="<?php echo htmlspecialchars($author_name); ?>" required class="mt-1 block w-full form-input"><p class="text-xs text-gray-500 mt-1">defaults to you, changeable.</p></div>
        <div><label for="excerpt" class="block text-sm font-medium text-gray-700">excerpt:</label><textarea id="excerpt" name="excerpt" rows="3" class="mt-1 block w-full form-textarea"><?php echo htmlspecialchars($excerpt); ?></textarea></div>
        <div><label for="tinymce-editor" class="block text-sm font-medium text-gray-700">content:</label><textarea id="tinymce-editor" name="content" rows="20" class="mt-1 block w-full form-textarea"><?php echo htmlspecialchars($content); ?></textarea></div>
        <div><button type="submit" class="btn btn-success">create post</button></div>
    </form>
</div>

<?php // TinyMCE Initialization Script ?>
<script>
  document.addEventListener('DOMContentLoaded', function() { if (typeof tinymce !== 'undefined') { tinymce.init({ selector: '#tinymce-editor', plugins: 'code table lists link image media wordcount help autoresize', toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media table | code help', height: 500, menubar: false, statusbar: false, content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }' }); } else { console.error("TinyMCE script not loaded."); } });
</script>
<style> /* Basic form styling */ .form-input, .form-textarea, .form-select { border-width: 1px; border-color: #d1d5db; border-radius: 0.375rem; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05); padding: 0.5rem 0.75rem; font-size: 0.875rem; line-height: 1.25rem; width: 100%; } .form-input:focus, .form-textarea:focus, .form-select:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgb(199 210 254 / 40%); outline: 2px solid transparent; outline-offset: 2px; } .btn { display: inline-flex; items-center; justify-content: center; padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 500; transition: background-color 0.15s ease-in-out; cursor: pointer; } .btn-success { background-color: #10B981; color: white; border: 1px solid transparent; } .btn-success:hover { background-color: #059669; } </style>
