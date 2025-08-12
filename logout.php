<?php
// logout.php - destroys the admin session

// always start session to access/destroy it
session_start();

// unset all session variables
$_SESSION = array();

// if it's desired to kill the session, also delete the session cookie.
// note: this will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// finally, destroy the session.
session_destroy();

// redirect back to the login page (or homepage)
header('Location: login.php?logged_out=1'); // add param to show message maybe
exit;

?>
