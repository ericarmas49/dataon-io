<?php
/**
 * GA4 Analytics Connection Test Script
 * This script tests the fixed GA4 analytics connection
 */

// START: ga4_test_script_setup_block
// Load WordPress
require_once('../../../wp-load.php');

// Include the GA4 Analytics class
require_once(get_template_directory() . '/includes/class-ga4-analytics.php');

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
// END: ga4_test_script_setup_block

// START: ga4_test_script_main_block
echo "<h1>GA4 Analytics Connection Test</h1>\n";
echo "<p style='color: blue; font-weight: bold;'>🕒 Test Script Generated: " . date('Y-m-d H:i:s') . " UTC</p>\n";
echo "<div id='ga4_test_results'>\n";

// Check current configuration
echo "<h2>Configuration Check</h2>\n";
echo "<div id='ga4_config_check'>\n";

$ga4_enabled = get_option('ga4_enabled', false);
$ga4_property_id = get_option('ga4_property_id', '');
$ga4_service_account_email = get_option('ga4_service_account_email', '');
$ga4_private_key = get_option('ga4_private_key', '');
$ga4_project_id = get_option('ga4_project_id', '');

echo "<p><strong>GA4 Enabled:</strong> " . ($ga4_enabled ? 'Yes' : 'No') . "</p>\n";
echo "<p><strong>Property ID:</strong> " . ($ga4_property_id ? $ga4_property_id : 'Not set') . "</p>\n";
echo "<p><strong>Service Account Email:</strong> " . ($ga4_service_account_email ? $ga4_service_account_email : 'Not set') . "</p>\n";
echo "<p><strong>Private Key:</strong> " . ($ga4_private_key ? 'Set (' . strlen($ga4_private_key) . ' characters)' : 'Not set') . "</p>\n";
echo "<p><strong>Project ID:</strong> " . ($ga4_project_id ? $ga4_project_id : 'Not set') . "</p>\n";

echo "</div>\n";

// Test the connection
echo "<h2>Connection Test</h2>\n";
echo "<div id='ga4_connection_test'>\n";

if ($ga4_enabled && $ga4_property_id && $ga4_service_account_email && $ga4_private_key) {
    echo "<p>Testing GA4 connection...</p>\n";
    
    // Show private key diagnosis
    echo "<h3>Private Key Analysis</h3>\n";
    echo "<div id='ga4_private_key_analysis'>\n";
    
    $key_length = strlen($ga4_private_key);
    $has_pem_headers = strpos($ga4_private_key, '-----BEGIN') !== false;
    $has_escaped_newlines = strpos($ga4_private_key, '\\n') !== false;
    $has_actual_newlines = strpos($ga4_private_key, "\n") !== false;
    $is_base64 = base64_decode($ga4_private_key, true) !== false;
    $has_json = strpos($ga4_private_key, '"private_key"') !== false;
    
    echo "<p><strong>Key Length:</strong> {$key_length} characters</p>\n";
    echo "<p><strong>Has PEM Headers:</strong> " . ($has_pem_headers ? 'Yes' : 'No') . "</p>\n";
    echo "<p><strong>Has Escaped Newlines:</strong> " . ($has_escaped_newlines ? 'Yes' : 'No') . "</p>\n";
    echo "<p><strong>Has Actual Newlines:</strong> " . ($has_actual_newlines ? 'Yes' : 'No') . "</p>\n";
    echo "<p><strong>Is Base64:</strong> " . ($is_base64 ? 'Yes' : 'No') . "</p>\n";
    echo "<p><strong>Contains JSON:</strong> " . ($has_json ? 'Yes' : 'No') . "</p>\n";
    echo "<p><strong>Key Preview:</strong> " . htmlspecialchars(substr($ga4_private_key, 0, 100)) . "...</p>\n";
    
    echo "</div>\n";
    
    $ga4 = new GA4_Analytics();
    $test_result = $ga4->test_connection();
    
    if ($test_result['success']) {
        echo "<p style='color: green;'><strong>✅ SUCCESS:</strong> " . $test_result['message'] . "</p>\n";
        
        // Try to get some real data
        echo "<h3>Sample Data Test</h3>\n";
        echo "<div id='ga4_data_test'>\n";
        
        $analytics_data = $ga4->get_analytics_data('day');
        
        if ($analytics_data && $analytics_data['connection_status'] === 'connected') {
            echo "<p style='color: green;'>✅ Real data retrieved successfully!</p>\n";
            echo "<p><strong>Total Page Views:</strong> " . number_format($analytics_data['page_views']['current']) . "</p>\n";
            echo "<p><strong>Real-time Users:</strong> " . $analytics_data['real_time_users'] . "</p>\n";
        } else {
            echo "<p style='color: orange;'>⚠️ Connection successful but data retrieval failed</p>\n";
        }
        
        echo "</div>\n";
        
    } else {
        echo "<p style='color: red;'><strong>❌ FAILED:</strong> " . $test_result['message'] . "</p>\n";
        
        // Show troubleshooting tips
        echo "<h3>Troubleshooting Tips</h3>\n";
        echo "<div id='ga4_troubleshooting'>\n";
        echo "<ul>\n";
        echo "<li>Make sure your private key is in PEM format with proper headers and footers</li>\n";
        echo "<li>Ensure the private key has proper line breaks (not escaped \\n)</li>\n";
        echo "<li>Verify the service account has Analytics Viewer permissions</li>\n";
        echo "<li>Check that the GA4 Property ID is correct</li>\n";
        echo "<li>Ensure the service account email is added to your GA4 property</li>\n";
        echo "</ul>\n";
        echo "</div>\n";
    }
    
} else {
    echo "<p style='color: orange;'>⚠️ GA4 not fully configured. Please configure all required settings.</p>\n";
}

echo "</div>\n";

// Show recent error logs
echo "<h2>Recent Error Logs</h2>\n";
echo "<div id='ga4_error_logs'>\n";

$error_log_path = ABSPATH . '../logs/php/error.log';
if (file_exists($error_log_path)) {
    $error_logs = file_get_contents($error_log_path);
    $ga4_errors = array_filter(explode("\n", $error_logs), function($line) {
        return strpos($line, 'GA4 Analytics') !== false;
    });
    
    if (!empty($ga4_errors)) {
        echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: auto;'>\n";
        echo implode("\n", array_slice($ga4_errors, -10)); // Show last 10 GA4 errors
        echo "</pre>\n";
    } else {
        echo "<p>No recent GA4 errors found in logs.</p>\n";
    }
} else {
    echo "<p>Error log file not found.</p>\n";
}

echo "</div>\n";

echo "</div>\n";
// END: ga4_test_script_main_block

// START: ga4_test_script_styles_block
echo "<style>
#ga4_test_results {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

#ga4_config_check, #ga4_connection_test, #ga4_data_test, #ga4_error_logs {
    margin: 15px 0;
    padding: 15px;
    border: 1px solid #eee;
    border-radius: 3px;
    background: #fafafa;
}

h1, h2, h3 {
    color: #333;
}

pre {
    font-size: 12px;
    line-height: 1.4;
}
</style>\n";
// END: ga4_test_script_styles_block
?>
