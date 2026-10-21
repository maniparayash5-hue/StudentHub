<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Prevent browser caching of protected pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$timeoutSeconds = 15 * 60;

// Check whether the user is logged in
if (
    empty($_SESSION["student_id"]) ||
    empty($_SESSION["role"])
) {
    header("Location: login.php");
    exit;
}

// Check session timeout
if (
    !isset($_SESSION["last_activity"]) ||
    time() - $_SESSION["last_activity"] >= $timeoutSeconds
) {
    // Clear session data
    $_SESSION = [];

    // Remove session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();

        setcookie(session_name(), "", [
            "expires" => time() - 42000,
            "path" => $params["path"],
            "domain" => $params["domain"],
            "secure" => $params["secure"],
            "httponly" => $params["httponly"],
            "samesite" => $params["samesite"] ?? "Lax"
        ]);
    }

    session_destroy();

    header("Location: login.php?timeout=1");
    exit;
}

// Update last activity time
$_SESSION["last_activity"] = time();

?>