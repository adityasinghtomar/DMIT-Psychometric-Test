<?php
/**
 * Debug Redirect - DMIT Psychometric Test System
 * Debug script to test redirect functionality
 */

require_once 'config/config.php';
require_once 'includes/functions.php';

echo "<h1>Redirect Debug Information</h1>";

echo "<h2>Configuration:</h2>";
echo "<ul>";
echo "<li><strong>BASE_URL:</strong> " . BASE_URL . "</li>";
echo "<li><strong>Current Script:</strong> " . $_SERVER['SCRIPT_NAME'] . "</li>";
echo "<li><strong>Current Directory:</strong> " . dirname($_SERVER['SCRIPT_NAME']) . "</li>";
echo "<li><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</li>";
echo "</ul>";

echo "<h2>URL Function Tests:</h2>";
echo "<ul>";
echo "<li><strong>url('index.php'):</strong> " . url('index.php') . "</li>";
echo "<li><strong>url('auth/login.php'):</strong> " . url('auth/login.php') . "</li>";
echo "<li><strong>url('auth/logout.php'):</strong> " . url('auth/logout.php') . "</li>";
echo "</ul>";

echo "<h2>Test Links:</h2>";
echo "<ul>";
echo "<li><a href='" . url('index.php') . "'>Go to Dashboard (using url function)</a></li>";
echo "<li><a href='index.php'>Go to Dashboard (direct link)</a></li>";
echo "<li><a href='" . BASE_URL . "'>Go to Dashboard (using BASE_URL)</a></li>";
echo "</ul>";

echo "<h2>Redirect Test:</h2>";
if (isset($_GET['test_redirect'])) {
    echo "<p>Testing redirect to index.php...</p>";
    redirect('index.php', 'Redirect test successful!', 'success');
} else {
    echo "<p><a href='?test_redirect=1' class='btn btn-primary'>Test Redirect to Dashboard</a></p>";
}

echo "<h2>Server Information:</h2>";
echo "<ul>";
echo "<li><strong>HTTP_HOST:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'Not set') . "</li>";
echo "<li><strong>SERVER_NAME:</strong> " . ($_SERVER['SERVER_NAME'] ?? 'Not set') . "</li>";
echo "<li><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "</li>";
echo "<li><strong>PHP_SELF:</strong> " . ($_SERVER['PHP_SELF'] ?? 'Not set') . "</li>";
echo "</ul>";

// Check if there are any redirect headers being sent
if (headers_sent()) {
    echo "<div style='color: red;'><strong>Warning:</strong> Headers already sent!</div>";
} else {
    echo "<div style='color: green;'><strong>Good:</strong> Headers not sent yet.</div>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h2 { color: #333; }
ul { background: #f5f5f5; padding: 15px; border-radius: 5px; }
.btn { background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 3px; }
</style>
