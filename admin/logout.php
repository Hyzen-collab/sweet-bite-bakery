<?php
require_once __DIR__ . '/../includes/functions.php';

$_SESSION = [];
session_destroy();

header('Location: ' . url('admin/login.php'));
exit;
