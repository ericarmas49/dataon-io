<?php
/**
 * GA4 Comprehensive Data Collection Test
 * Shows all available data that can be collected from GA4 API
 */

// Load WordPress
require_once('../../../wp-load.php');

// Include the GA4 Analytics class
require_once(get_template_directory() . '/includes/class-ga4-analytics.php');

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>GA4 Comprehensive Data Collection</h1>\n";
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

// Get comprehensive data
echo "<h2>Collecting Comprehensive GA4 Data...</h2>\n";
echo "<div id='data_collection_status'>\n";
echo "<p>This may take a moment as we're collecting data from multiple GA4 API endpoints...</p>\n";
echo "</div>\n";

$comprehensive_data = $ga4->get_comprehensive_ga4_data('7daysAgo', 'today');

echo "<h2>Available GA4 Data Collection</h2>\n";
echo "<div id='comprehensive_data_output'>\n";

// Display the data structure
echo "<h3>Data Structure Overview</h3>\n";
echo "<div id='data_structure'>\n";
echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px; max-height: 400px; overflow-y: auto;'>\n";

$data_structure = [
    'connection_status' => 'Status of GA4 connection',
    'date_range' => 'Start and end dates for data collection',
    'collection_timestamp' => 'When the data was collected',
    'basic_metrics' => [
        'sessions' => 'Total number of sessions',
        'total_users' => 'Total number of users',
        'page_views' => 'Total page views',
        'bounce_rate' => 'Bounce rate percentage',
        'avg_session_duration' => 'Average session duration in seconds',
        'new_users' => 'Number of new users',
        'sessions_per_user' => 'Average sessions per user'
    ],
    'user_metrics' => [
        'age_bracket' => 'User age brackets (18-24, 25-34, etc.)',
        'gender' => 'User gender distribution',
        'total_users' => 'Users in each demographic',
        'new_users' => 'New users in each demographic',
        'active_users' => 'Active users in each demographic'
    ],
    'session_metrics' => [
        'sessions' => 'Total sessions',
        'bounce_rate' => 'Session bounce rate',
        'avg_session_duration' => 'Average session duration',
        'sessions_per_user' => 'Sessions per user',
        'page_views_per_session' => 'Page views per session',
        'conversions' => 'Total conversions'
    ],
    'page_performance' => [
        'page_path' => 'URL path of pages',
        'page_title' => 'Title of pages',
        'page_views' => 'Views per page',
        'avg_session_duration' => 'Time spent on page',
        'bounce_rate' => 'Page bounce rate',
        'exit_rate' => 'Page exit rate'
    ],
    'traffic_analysis' => [
        'channel_grouping' => 'Traffic channel (Organic, Direct, Social, etc.)',
        'source' => 'Traffic source (google, facebook, etc.)',
        'medium' => 'Traffic medium (organic, cpc, referral, etc.)',
        'sessions' => 'Sessions from each source',
        'users' => 'Users from each source',
        'page_views' => 'Page views from each source',
        'bounce_rate' => 'Bounce rate from each source'
    ],
    'geographic_data' => [
        'country' => 'User country',
        'city' => 'User city',
        'region' => 'User region/state',
        'sessions' => 'Sessions from each location',
        'users' => 'Users from each location',
        'page_views' => 'Page views from each location'
    ],
    'device_technology' => [
        'device_category' => 'Device type (desktop, mobile, tablet)',
        'operating_system' => 'OS (Windows, Mac, iOS, Android)',
        'browser' => 'Browser (Chrome, Safari, Firefox, etc.)',
        'screen_resolution' => 'Screen resolution',
        'sessions' => 'Sessions per device/browser',
        'users' => 'Users per device/browser',
        'page_views' => 'Page views per device/browser'
    ],
    'content_performance' => [
        'page_path' => 'Page URL path',
        'page_title' => 'Page title',
        'landing_page' => 'Landing page information',
        'page_views' => 'Page view count',
        'entrances' => 'Page entrance count',
        'exits' => 'Page exit count',
        'avg_session_duration' => 'Average time on page'
    ],
    'ecommerce_data' => [
        'purchase_revenue' => 'Revenue from purchases',
        'purchases' => 'Number of purchases',
        'total_revenue' => 'Total revenue',
        'avg_purchase_revenue' => 'Average purchase value',
        'conversions' => 'Total conversions'
    ],
    'realtime_data' => [
        'country' => 'Real-time user country',
        'page_path' => 'Pages currently being viewed',
        'active_users' => 'Number of active users'
    ],
    'custom_events' => [
        'event_name' => 'Name of custom events',
        'event_count' => 'Number of times event occurred',
        'total_users' => 'Users who triggered the event'
    ],
    'conversion_data' => [
        'conversion_event' => 'Name of conversion events',
        'conversions' => 'Number of conversions',
        'total_users' => 'Users who converted',
        'conversion_rate' => 'Conversion rate percentage'
    ],
    'audience_insights' => [
        'age_bracket' => 'User age brackets',
        'gender' => 'User gender',
        'country' => 'User country',
        'total_users' => 'Users in each segment',
        'new_users' => 'New users in each segment',
        'sessions' => 'Sessions from each segment'
    ]
];

echo json_encode($data_structure, JSON_PRETTY_PRINT);
echo "</pre>\n";
echo "</div>\n";

echo "<h3>Sample Data (Last 7 Days)</h3>\n";
echo "<div id='sample_data'>\n";

// Show sample data for each category
foreach ($comprehensive_data as $category => $data) {
    if (is_array($data)) {
        $is_unavailable = isset($data['status']) && $data['status'] === 'unavailable';
        $has_error = isset($data['error']);
        
        echo "<h4>" . ucwords(str_replace('_', ' ', $category)) . "</h4>\n";
        
        if ($is_unavailable || $has_error) {
            echo "<div class='data_category unavailable'>\n";
            echo "<div class='unavailable_header'>\n";
            echo "<span class='unavailable_icon'>⚠️</span>\n";
            echo "<strong>Data Not Available</strong>\n";
            echo "</div>\n";
            
            if ($is_unavailable) {
                echo "<p class='unavailable_message'>" . $data['message'] . "</p>\n";
                if (isset($data['note'])) {
                    echo "<p class='unavailable_note'>" . $data['note'] . "</p>\n";
                }
            } else {
                echo "<p class='unavailable_message'>" . $data['error'] . "</p>\n";
            }
            echo "</div>\n";
        } else {
            echo "<div class='data_category available'>\n";
            
            if (is_array($data) && count($data) > 0) {
                // Show first few items as sample
                $sample = array_slice($data, 0, 3);
                echo "<pre style='background: #f9f9f9; padding: 10px; border-radius: 3px; font-size: 12px;'>\n";
                echo json_encode($sample, JSON_PRETTY_PRINT);
                if (count($data) > 3) {
                    echo "\n... and " . (count($data) - 3) . " more items\n";
                }
                echo "</pre>\n";
            } else {
                echo "<p style='color: #666;'>No data available for this category</p>\n";
            }
            echo "</div>\n";
        }
    }
}

echo "</div>\n";

echo "<h3>Data Collection Summary</h3>\n";
echo "<div id='collection_summary'>\n";
echo "<ul>\n";
echo "<li><strong>Total Categories:</strong> " . count($comprehensive_data) . "</li>\n";
echo "<li><strong>Data Points Available:</strong> " . array_sum(array_map(function($item) {
    return is_array($item) ? count($item) : 0;
}, $comprehensive_data)) . "</li>\n";
echo "<li><strong>Real-time Data:</strong> " . (isset($comprehensive_data['realtime_data']) ? 'Available' : 'Not available') . "</li>\n";
echo "<li><strong>E-commerce Data:</strong> " . (isset($comprehensive_data['ecommerce_data']) ? 'Available' : 'Not available') . "</li>\n";
echo "<li><strong>Custom Events:</strong> " . (isset($comprehensive_data['custom_events']) ? 'Available' : 'Not available') . "</li>\n";
echo "</ul>\n";
echo "</div>\n";

echo "</div>\n";

echo "<style>
body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; }
#data_structure, #sample_data, #collection_summary { margin: 15px 0; }
.data_category { margin: 10px 0; padding: 10px; border: 1px solid #eee; border-radius: 3px; }
.data_category.available { border-left: 4px solid #28a745; background: #f8fff9; }
.data_category.unavailable { border: 2px solid #dc3545; background: #fff5f5; }
.unavailable_header { display: flex; align-items: center; margin-bottom: 10px; }
.unavailable_icon { font-size: 18px; margin-right: 8px; }
.unavailable_message { color: #dc3545; font-weight: bold; margin: 5px 0; }
.unavailable_note { color: #6c757d; font-style: italic; margin: 5px 0; }
pre { font-size: 12px; line-height: 1.4; }
h3 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 5px; }
h4 { color: #666; margin-top: 20px; }
</style>\n";
?>
