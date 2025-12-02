<?php
/**
 * Debug GA4 Private Key Format
 * This script will show detailed debugging of the private key formatting
 */

// Load WordPress
require_once('../../../wp-load.php');

// Include the GA4 Analytics class
require_once(get_template_directory() . '/includes/class-ga4-analytics.php');

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>GA4 Private Key Debug</h1>\n";
echo "<p style='color: blue; font-weight: bold;'>🕒 Debug Script Generated: " . date('Y-m-d H:i:s') . " UTC</p>\n";

// Get the private key from options
$private_key = get_option('ga4_private_key', '');

if (empty($private_key)) {
    echo "<p style='color: red;'>No private key found in options</p>\n";
    exit;
}

echo "<h2>Original Private Key Analysis</h2>\n";
echo "<div id='original_key_analysis'>\n";

$key_length = strlen($private_key);
$has_pem_headers = strpos($private_key, '-----BEGIN') !== false;
$has_escaped_newlines = strpos($private_key, '\\n') !== false;
$has_actual_newlines = strpos($private_key, "\n") !== false;
$is_base64 = base64_decode($private_key, true) !== false;
$has_json = strpos($private_key, '"private_key"') !== false;

echo "<p><strong>Key Length:</strong> {$key_length} characters</p>\n";
echo "<p><strong>Has PEM Headers:</strong> " . ($has_pem_headers ? 'Yes' : 'No') . "</p>\n";
echo "<p><strong>Has Escaped Newlines:</strong> " . ($has_escaped_newlines ? 'Yes' : 'No') . "</p>\n";
echo "<p><strong>Has Actual Newlines:</strong> " . ($has_actual_newlines ? 'Yes' : 'No') . "</p>\n";
echo "<p><strong>Is Base64:</strong> " . ($is_base64 ? 'Yes' : 'No') . "</p>\n";
echo "<p><strong>Contains JSON:</strong> " . ($has_json ? 'Yes' : 'No') . "</p>\n";

echo "</div>\n";

echo "<h2>Key Preview (First 200 characters)</h2>\n";
echo "<div id='key_preview'>\n";
echo "<pre style='background: #f5f5f5; padding: 10px; word-wrap: break-word;'>\n";
echo htmlspecialchars(substr($private_key, 0, 200)) . "...\n";
echo "</pre>\n";
echo "</div>\n";

echo "<h2>Testing GA4 Connection with Debugging</h2>\n";
echo "<div id='connection_test'>\n";

$ga4 = new GA4_Analytics();
$test_result = $ga4->test_connection();

if ($test_result['success']) {
    echo "<p style='color: green;'><strong>✅ SUCCESS:</strong> " . $test_result['message'] . "</p>\n";
} else {
    echo "<p style='color: red;'><strong>❌ FAILED:</strong> " . $test_result['message'] . "</p>\n";
}

echo "</div>\n";

echo "<h2>Recent Error Logs</h2>\n";
echo "<div id='error_logs'>\n";

$error_log_path = ABSPATH . '../logs/php/error.log';
if (file_exists($error_log_path)) {
    $error_logs = file_get_contents($error_log_path);
    $ga4_errors = array_filter(explode("\n", $error_logs), function($line) {
        return strpos($line, 'GA4 Analytics') !== false;
    });
    
    if (!empty($ga4_errors)) {
        echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 400px; overflow-y: auto;'>\n";
        echo implode("\n", array_slice($ga4_errors, -20)); // Show last 20 GA4 errors
        echo "</pre>\n";
    } else {
        echo "<p>No recent GA4 errors found in logs.</p>\n";
    }
} else {
    echo "<p>Error log file not found.</p>\n";
}

echo "</div>\n";

echo "<style>
body { font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px; }
#original_key_analysis, #key_preview, #connection_test, #error_logs {
    margin: 15px 0;
    padding: 15px;
    border: 1px solid #eee;
    border-radius: 3px;
    background: #fafafa;
}
pre { font-size: 12px; line-height: 1.4; }
</style>\n";
?>
