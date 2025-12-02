<?php
/**
 * Test Real Data vs Sample Data
 * Verifies all data is real and shows unavailable sections with red borders
 */

// Load WordPress
require_once('../../../wp-load.php');

// Include the GA4 Analytics class
require_once(get_template_directory() . '/includes/class-ga4-analytics.php');

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>GA4 Real Data Verification</h1>\n";
echo "<p style='color: blue; font-weight: bold;'>🕒 Generated: " . date('Y-m-d H:i:s') . " UTC</p>\n";

// Initialize GA4 Analytics
$ga4 = new GA4_Analytics();

// Test connection first
$test_result = $ga4->test_connection();
if (!$test_result['success']) {
    echo "<p style='color: red;'>❌ Connection failed: " . $test_result['message'] . "</p>\n";
    exit;
}

echo "<p style='color: green;'>✅ Connected to GA4 successfully!</p>\n";

// Test each data category
$data_categories = [
    'Basic Metrics' => function() use ($ga4) { return $ga4->get_basic_metrics('7daysAgo', 'today'); },
    'User Metrics' => function() use ($ga4) { return $ga4->get_user_metrics('7daysAgo', 'today'); },
    'Session Metrics' => function() use ($ga4) { return $ga4->get_session_metrics('7daysAgo', 'today'); },
    'Page Performance' => function() use ($ga4) { return $ga4->get_page_performance('7daysAgo', 'today'); },
    'Traffic Analysis' => function() use ($ga4) { return $ga4->get_traffic_analysis('7daysAgo', 'today'); },
    'Geographic Data' => function() use ($ga4) { return $ga4->get_geographic_data('7daysAgo', 'today'); },
    'Device Technology' => function() use ($ga4) { return $ga4->get_device_technology('7daysAgo', 'today'); },
    'Content Performance' => function() use ($ga4) { return $ga4->get_content_performance('7daysAgo', 'today'); },
    'E-commerce Data' => function() use ($ga4) { return $ga4->get_ecommerce_data('7daysAgo', 'today'); },
    'Real-time Data' => function() use ($ga4) { return $ga4->get_realtime_data(); },
    'Custom Events' => function() use ($ga4) { return $ga4->get_custom_events('7daysAgo', 'today'); },
    'Conversion Data' => function() use ($ga4) { return $ga4->get_conversion_data('7daysAgo', 'today'); },
    'Audience Insights' => function() use ($ga4) { return $ga4->get_audience_insights('7daysAgo', 'today'); },
    'PDF Downloads' => function() use ($ga4) { return $ga4->get_pdf_downloads('7daysAgo', 'today'); },
    'Trending Insights' => function() use ($ga4) { return $ga4->get_trending_insights('7daysAgo', 'today'); },
    'Recent Activity' => function() use ($ga4) { return $ga4->get_recent_activity(); }
];

echo "<h2>Data Category Status</h2>\n";
echo "<div id='data_status_grid'>\n";

$available_count = 0;
$unavailable_count = 0;

foreach ($data_categories as $category_name => $test_function) {
    try {
        $data = $test_function();
        
        $is_unavailable = isset($data['status']) && $data['status'] === 'unavailable';
        $has_error = isset($data['error']);
        $is_empty = is_array($data) && empty($data);
        
        if ($is_unavailable || $has_error || $is_empty) {
            $unavailable_count++;
            echo "<div class='status_card unavailable'>\n";
            echo "<div class='status_header'>\n";
            echo "<span class='status_icon'>⚠️</span>\n";
            echo "<h3>" . $category_name . "</h3>\n";
            echo "</div>\n";
            echo "<div class='status_content'>\n";
            
            if ($is_unavailable) {
                echo "<p class='status_message'>" . $data['message'] . "</p>\n";
                if (isset($data['note'])) {
                    echo "<p class='status_note'>" . $data['note'] . "</p>\n";
                }
            } elseif ($has_error) {
                echo "<p class='status_message'>" . $data['error'] . "</p>\n";
            } else {
                echo "<p class='status_message'>No data available</p>\n";
            }
            
            echo "</div>\n";
            echo "</div>\n";
        } else {
            $available_count++;
            echo "<div class='status_card available'>\n";
            echo "<div class='status_header'>\n";
            echo "<span class='status_icon'>✅</span>\n";
            echo "<h3>" . $category_name . "</h3>\n";
            echo "</div>\n";
            echo "<div class='status_content'>\n";
            
            if (is_array($data)) {
                $data_count = count($data);
                echo "<p class='status_message'>" . $data_count . " data points available</p>\n";
                
                // Show sample data
                if ($data_count > 0) {
                    $sample = array_slice($data, 0, 2);
                    echo "<div class='sample_data'>\n";
                    echo "<pre style='font-size: 10px; background: #f0f0f0; padding: 5px; border-radius: 3px;'>\n";
                    echo json_encode($sample, JSON_PRETTY_PRINT);
                    echo "</pre>\n";
                    echo "</div>\n";
                }
            } else {
                echo "<p class='status_message'>Data available (non-array format)</p>\n";
            }
            
            echo "</div>\n";
            echo "</div>\n";
        }
    } catch (Exception $e) {
        $unavailable_count++;
        echo "<div class='status_card unavailable'>\n";
        echo "<div class='status_header'>\n";
        echo "<span class='status_icon'>❌</span>\n";
        echo "<h3>" . $category_name . "</h3>\n";
        echo "</div>\n";
        echo "<div class='status_content'>\n";
        echo "<p class='status_message'>Error: " . $e->getMessage() . "</p>\n";
        echo "</div>\n";
        echo "</div>\n";
    }
}

echo "</div>\n";

echo "<h2>Summary</h2>\n";
echo "<div id='summary'>\n";
echo "<div class='summary_stats'>\n";
echo "<div class='stat available'>\n";
echo "<h3>" . $available_count . "</h3>\n";
echo "<p>Available</p>\n";
echo "</div>\n";
echo "<div class='stat unavailable'>\n";
echo "<h3>" . $unavailable_count . "</h3>\n";
echo "<p>Unavailable</p>\n";
echo "</div>\n";
echo "<div class='stat total'>\n";
echo "<h3>" . count($data_categories) . "</h3>\n";
echo "<p>Total</p>\n";
echo "</div>\n";
echo "</div>\n";
echo "</div>\n";

echo "<style>
body { font-family: Arial, sans-serif; max-width: 1400px; margin: 20px auto; padding: 20px; }
#data_status_grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0; }
.status_card { border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.status_card.available { border-left: 4px solid #28a745; background: #f8fff9; }
.status_card.unavailable { border: 2px solid #dc3545; background: #fff5f5; }
.status_header { display: flex; align-items: center; margin-bottom: 10px; }
.status_icon { font-size: 20px; margin-right: 10px; }
.status_header h3 { margin: 0; color: #333; }
.status_message { color: #dc3545; font-weight: bold; margin: 5px 0; }
.status_note { color: #6c757d; font-style: italic; margin: 5px 0; font-size: 12px; }
.sample_data { margin-top: 10px; }
#summary { margin: 30px 0; }
.summary_stats { display: flex; justify-content: center; gap: 30px; }
.stat { text-align: center; padding: 20px; border-radius: 8px; min-width: 100px; }
.stat.available { background: #d4edda; border: 2px solid #28a745; }
.stat.unavailable { background: #f8d7da; border: 2px solid #dc3545; }
.stat.total { background: #e2e3e5; border: 2px solid #6c757d; }
.stat h3 { margin: 0; font-size: 2em; color: #333; }
.stat p { margin: 5px 0 0 0; font-weight: bold; }
</style>\n";
?>

