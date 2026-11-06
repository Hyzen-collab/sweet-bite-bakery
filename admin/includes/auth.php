<?php
/**
 * Include this at the top of every admin page (except login.php).
 * It loads the helpers and sends visitors who are not logged in to the login page.
 */
require_once __DIR__ . '/../../includes/functions.php';

function admin_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_admin(): void
{
    // Log out automatically after 30 minutes without any activity
    if (admin_logged_in() && time() - ($_SESSION['last_activity'] ?? 0) > 1800) {
        session_unset();
        flash('warning', 'You were logged out because you were away for a while. Please log in again.');
    }

    if (!admin_logged_in()) {
        redirect('admin/login.php');
    }

    $_SESSION['last_activity'] = time();
}

require_admin();
