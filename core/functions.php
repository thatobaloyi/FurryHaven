<?php
// app/core/functions.php

/**
 * Redirects the user to a specified URL.
 * @param string $path The path to redirect to (e.g., '/dashboard').
 */
function redirectTo($path) {
    header("Location: $path");
    exit();
}

/**
 * Checks if a user is currently logged in based on session data.
 * @return bool True if logged in, false otherwise.
 */
function isLoggedIn() {
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
}

/**
 * Sanitizes input string to prevent XSS.
 * @param string $input The string to sanitize.
 * @return string The sanitized string.
 */
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}