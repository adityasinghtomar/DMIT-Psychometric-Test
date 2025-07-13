<?php
/**
 * Debug URL function from auth directory
 */

require_once '../config/config.php';
require_once '../includes/functions.php';

echo "<h1>URL Debug from auth/ directory</h1>";
echo "<ul>";
echo "<li><strong>Current Script:</strong> " . $_SERVER['SCRIPT_NAME'] . "</li>";
echo "<li><strong>Current Directory:</strong> " . dirname($_SERVER['SCRIPT_NAME']) . "</li>";
echo "<li><strong>BASE_URL:</strong> " . BASE_URL . "</li>";
echo "</ul>";

echo "<h2>URL Function Tests:</h2>";
echo "<ul>";
echo "<li><strong>url('login.php'):</strong> " . url('login.php') . "</li>";
echo "<li><strong>url('auth/login.php'):</strong> " . url('auth/login.php') . "</li>";
echo "<li><strong>url('index.php'):</strong> " . url('index.php') . "</li>";
echo "</ul>";

echo "<h2>Expected Results:</h2>";
echo "<ul>";
echo "<li><strong>From auth/logout.php to auth/login.php:</strong> Should be 'login.php'</li>";
echo "<li><strong>From root to auth/login.php:</strong> Should be 'auth/login.php'</li>";
echo "</ul>";
?>
