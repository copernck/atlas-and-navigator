<?php
// login.php - Secure login form - Redirects to index.php?page=admin

require_once __DIR__ . '/config/config.php'; // Defines DB constants, SITE_NAME, starts session
require_once __DIR__ . '/includes/database.php'; // Provides getDbConnection()

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$errorMessage = null;
$username = '';

// --- Redirect if already logged in ---
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    // ** Redirect to the OLD URL format for the admin page **
    header('Location: index.php?page=admin'); // Use relative path with query string
    exit;
}

// --- Handle form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($username) || empty($password)) {
        $errorMessage = 'username and password are required.';
    } else {
        try {
            $db = getDbConnection();
            $sql = "SELECT id, username, password_hash FROM admins WHERE username = :username LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $adminUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($adminUser && password_verify($password, $adminUser['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['is_admin'] = true;
                $_SESSION['admin_username'] = $adminUser['username'];
                $_SESSION['admin_id'] = $adminUser['id'];

                // ** Redirect to the OLD URL format for the admin area **
                header('Location: index.php?page=admin'); // Use relative path with query string
                exit;
            } else {
                $errorMessage = 'invalid username or password.';
            }
        } catch (PDOException $e) {
            error_log("admin login PDO error: " . $e->getMessage());
            $errorMessage = "login failed due to a server error.";
        } catch (Exception $e) {
             error_log("admin login general error: " . $e->getMessage());
             $errorMessage = "an unexpected error occurred during login.";
        }
    }
}

// --- Display Login Form ---
$siteNameForTitle = SITE_NAME . ' - Admin Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($siteNameForTitle); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { background-color: #f3f4f6; } </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-xs">
        <form action="login.php" method="post" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h1 class="text-2xl font-bold text-center text-gray-700 mb-6">Admin Login</h1>
            <?php if ($errorMessage): ?>
                <p class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded text-xs mb-4"><?php echo htmlspecialchars($errorMessage); ?></p>
            <?php endif; ?>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Username</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" name="username" type="text" placeholder="Username" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                <input class="shadow appearance-none border <?php if($errorMessage && stripos($errorMessage, 'password') !== false) echo 'border-red-500'; ?> rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="password" name="password" type="password" placeholder="******************" required>
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">Sign In</button>
                <?php // Link back to home page using clean URL ?>
                <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="/">Back to Site</a>
            </div>
        </form>
        <p class="text-center text-gray-500 text-xs">&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?></p>
    </div>
</body>
</html>
