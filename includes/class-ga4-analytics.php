<?php
/**
 * Google Analytics 4 Data API Integration
 * Handles authentication and data fetching from GA4
 */

class GA4_Analytics {
    
    private $api_key;
    private $property_id;
    private $service_account_email;
    private $private_key;
    private $project_id;
    private $access_token;
    
    public function __construct() {
        $this->api_key = get_option('ga4_api_key', '');
        $this->property_id = get_option('ga4_property_id', '');
        $this->service_account_email = get_option('ga4_service_account_email', '');
        $this->private_key = get_option('ga4_private_key', '');
        $this->project_id = get_option('ga4_project_id', '');
        
        // Log class initialization with timestamp
        error_log('GA4 Analytics: Class initialized at ' . date('Y-m-d H:i:s') . ' UTC');
    }
    
    /**
     * Get access token using service account
     * Fixed private key handling with proper validation and formatting
     */
    private function get_access_token() {
        // START: ga4_access_token_validation_block
        if (empty($this->service_account_email) || empty($this->private_key)) {
            error_log('GA4 Analytics: Missing service account email or private key');
            return false;
        }
        // END: ga4_access_token_validation_block
        
        // START: ga4_jwt_payload_creation_block
        $jwt_header = json_encode(['typ' => 'JWT', 'alg' => 'RS256']);
        $now = time();
        $jwt_payload = json_encode([
            'iss' => $this->service_account_email,
            'scope' => 'https://www.googleapis.com/auth/analytics.readonly',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ]);
        
        $jwt_header_encoded = $this->base64url_encode($jwt_header);
        $jwt_payload_encoded = $this->base64url_encode($jwt_payload);
        // END: ga4_jwt_payload_creation_block
        
        // START: ga4_private_key_processing_block
        $private_key_formatted = $this->format_private_key($this->private_key);
        if (!$private_key_formatted) {
            error_log('GA4 Analytics: Failed to format private key');
            return false;
        }
        
        $private_key_resource = openssl_pkey_get_private($private_key_formatted);
        if (!$private_key_resource) {
            $openssl_error = openssl_error_string();
            error_log('GA4 Analytics: Failed to load private key: ' . $openssl_error);
            return false;
        }
        // END: ga4_private_key_processing_block
        
        // START: ga4_jwt_signature_creation_block
        $signature = '';
        $signing_data = $jwt_header_encoded . '.' . $jwt_payload_encoded;
        
        if (!openssl_sign($signing_data, $signature, $private_key_resource, 'SHA256')) {
            $openssl_error = openssl_error_string();
            error_log('GA4 Analytics: Failed to sign JWT: ' . $openssl_error);
            openssl_pkey_free($private_key_resource);
            return false;
        }
        
        $jwt_signature_encoded = $this->base64url_encode($signature);
        $jwt = $jwt_header_encoded . '.' . $jwt_payload_encoded . '.' . $jwt_signature_encoded;
        
        // Clean up the private key resource
        openssl_pkey_free($private_key_resource);
        // END: ga4_jwt_signature_creation_block
        
        // START: ga4_token_request_block
        $response = wp_remote_post('https://oauth2.googleapis.com/token', [
            'body' => [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ],
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        ]);
        
        if (is_wp_error($response)) {
            error_log('GA4 Analytics: Token request failed: ' . $response->get_error_message());
            return false;
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            error_log('GA4 Analytics: Token request returned status: ' . $response_code);
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['error'])) {
            error_log('GA4 Analytics: Token request error: ' . $data['error_description']);
            return false;
        }
        
        if (isset($data['access_token'])) {
            $this->access_token = $data['access_token'];
            return $this->access_token;
        }
        
        error_log('GA4 Analytics: No access token in response');
        return false;
        // END: ga4_token_request_block
    }
    
    /**
     * Make API request to GA4 Data API
     */
    private function make_api_request($endpoint, $params = []) {
        if (!$this->access_token) {
            $this->get_access_token();
        }
        
        if (!$this->access_token) {
            return false;
        }
        
        $url = 'https://analyticsdata.googleapis.com/v1beta/' . $endpoint;
        $url .= '?key=' . $this->api_key;
        
        $headers = [
            'Authorization' => 'Bearer ' . $this->access_token,
            'Content-Type' => 'application/json'
        ];
        
        $response = wp_remote_post($url, [
            'headers' => $headers,
            'body' => json_encode($params),
            'timeout' => 30
        ]);
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        return json_decode($body, true);
    }
    
    /**
     * Get page views data
     */
    public function get_page_views($start_date = '7daysAgo', $end_date = 'today') {
        $request_body = [
            'dateRanges' => [
                ['startDate' => $start_date, 'endDate' => $end_date]
            ],
            'dimensions' => [
                ['name' => 'pagePath']
            ],
            'metrics' => [
                ['name' => 'screenPageViews']
            ],
            'orderBys' => [
                ['metric' => ['metricName' => 'screenPageViews'], 'desc' => true]
            ],
            'limit' => 10
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runReport', $request_body);
        
        if (!$response || isset($response['error'])) {
            return false;
        }
        
        $page_views = [];
        $labels = [];
        $data = [];
        
        if (isset($response['rows'])) {
            foreach ($response['rows'] as $row) {
                $page_path = $row['dimensionValues'][0]['value'];
                $views = intval($row['metricValues'][0]['value']);
                
                $page_views[] = [
                    'page' => $page_path,
                    'views' => $views,
                    'bounce_rate' => null // GA4 doesn't provide bounce rate in page views report
                ];
                
                $labels[] = $page_path;
                $data[] = $views;
            }
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'top_pages' => $page_views
        ];
    }
    
    /**
     * Get traffic sources data
     */
    public function get_traffic_sources($start_date = '7daysAgo', $end_date = 'today') {
        $request_body = [
            'dateRanges' => [
                ['startDate' => $start_date, 'endDate' => $end_date]
            ],
            'dimensions' => [
                ['name' => 'sessionDefaultChannelGrouping']
            ],
            'metrics' => [
                ['name' => 'sessions']
            ],
            'orderBys' => [
                ['metric' => ['metricName' => 'sessions'], 'desc' => true]
            ]
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runReport', $request_body);
        
        if (!$response || isset($response['error'])) {
            return false;
        }
        
        $sources = [];
        $labels = [];
        $data = [];
        $total_sessions = 0;
        
        if (isset($response['rows'])) {
            // Calculate total sessions first
            foreach ($response['rows'] as $row) {
                $total_sessions += intval($row['metricValues'][0]['value']);
            }
            
            // Process each source
            foreach ($response['rows'] as $row) {
                $source = $row['dimensionValues'][0]['value'];
                $sessions = intval($row['metricValues'][0]['value']);
                $percentage = $total_sessions > 0 ? round(($sessions / $total_sessions) * 100) : 0;
                
                $sources[] = [
                    'source' => $source,
                    'sessions' => $sessions,
                    'percentage' => $percentage
                ];
                
                $labels[] = $source;
                $data[] = $percentage;
            }
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'sources' => $sources
        ];
    }
    
    /**
     * Get real-time users
     */
    public function get_realtime_users() {
        $request_body = [
            'dimensions' => [
                ['name' => 'country']
            ],
            'metrics' => [
                ['name' => 'activeUsers']
            ]
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runRealtimeReport', $request_body);
        
        if (!$response || isset($response['error'])) {
            return 0;
        }
        
        $total_users = 0;
        if (isset($response['rows'])) {
            foreach ($response['rows'] as $row) {
                $total_users += intval($row['metricValues'][0]['value']);
            }
        }
        
        return $total_users;
    }
    
    /**
     * Get new users for a period
     */
    public function get_new_users($start_date = '7daysAgo', $end_date = 'today') {
        $request_body = [
            'dateRanges' => [
                ['startDate' => $start_date, 'endDate' => $end_date]
            ],
            'metrics' => [
                ['name' => 'newUsers']
            ]
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runReport', $request_body);
        
        if (!$response || isset($response['error'])) {
            return 0;
        }
        
        if (isset($response['rows'][0]['metricValues'][0]['value'])) {
            return intval($response['rows'][0]['metricValues'][0]['value']);
        }
        
        return 0;
    }
    
    /**
     * Get total page views for a period
     */
    public function get_total_page_views($start_date = '7daysAgo', $end_date = 'today') {
        $request_body = [
            'dateRanges' => [
                ['startDate' => $start_date, 'endDate' => $end_date]
            ],
            'metrics' => [
                ['name' => 'screenPageViews']
            ]
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runReport', $request_body);
        
        if (!$response || isset($response['error'])) {
            return 0;
        }
        
        if (isset($response['rows'][0]['metricValues'][0]['value'])) {
            return intval($response['rows'][0]['metricValues'][0]['value']);
        }
        
        return 0;
    }
    
    /**
     * Get comprehensive analytics data
     */
    public function get_analytics_data($period = 'day') {
        $date_ranges = [
            'day' => ['7daysAgo', 'today'],
            'week' => ['30daysAgo', 'today'],
            'month' => ['90daysAgo', 'today'],
            'all_time' => ['365daysAgo', 'today']
        ];
        
        $range = $date_ranges[$period] ?? $date_ranges['day'];
        $start_date = $range[0];
        $end_date = $range[1];
        
        // Get all data
        $page_views_data = $this->get_page_views($start_date, $end_date);
        $traffic_sources_data = $this->get_traffic_sources($start_date, $end_date);
        $realtime_users = $this->get_realtime_users();
        $total_page_views = $this->get_total_page_views($start_date, $end_date);
        $new_users = $this->get_new_users($start_date, $end_date);
        
        // Calculate previous period for comparison
        $prev_start = $this->get_previous_period_start($start_date, $period);
        $prev_total_views = $this->get_total_page_views($prev_start, $start_date);
        
        $change_percent = 0;
        if ($prev_total_views > 0) {
            $change_percent = round((($total_page_views - $prev_total_views) / $prev_total_views) * 100, 1);
        }
        
        return [
            'connection_status' => 'connected',
            'period' => $period,
            'page_views' => [
                'current' => $total_page_views,
                'previous' => $prev_total_views,
                'change_percent' => $change_percent,
                'trend' => $change_percent >= 0 ? 'up' : 'down',
                'labels' => $page_views_data['labels'] ?? [],
                'data' => $page_views_data['data'] ?? []
            ],
            'real_time_users' => $realtime_users,
            'new_users' => $new_users,
            'top_pages' => $page_views_data['top_pages'] ?? [],
            'traffic_sources' => $traffic_sources_data,
            'pdf_downloads' => $this->get_pdf_downloads($start_date, $end_date),
            'trending_insights' => $this->get_trending_insights($start_date, $end_date),
            'recent_activity' => $this->get_recent_activity()
        ];
    }
    
    /**
     * Get comprehensive GA4 data collection - all available metrics and dimensions
     */
    public function get_comprehensive_ga4_data($start_date = '7daysAgo', $end_date = 'today') {
        // START: ga4_comprehensive_data_collection_block
        $comprehensive_data = [
            'connection_status' => 'connected',
            'date_range' => ['start' => $start_date, 'end' => $end_date],
            'collection_timestamp' => date('Y-m-d H:i:s'),
            
            // Basic Metrics
            'basic_metrics' => $this->get_basic_metrics($start_date, $end_date),
            
            // User Metrics
            'user_metrics' => $this->get_user_metrics($start_date, $end_date),
            
            // Session Metrics
            'session_metrics' => $this->get_session_metrics($start_date, $end_date),
            
            // Page Performance
            'page_performance' => $this->get_page_performance($start_date, $end_date),
            
            // Traffic Sources
            'traffic_analysis' => $this->get_traffic_analysis($start_date, $end_date),
            
            // Geographic Data
            'geographic_data' => $this->get_geographic_data($start_date, $end_date),
            
            // Device & Technology
            'device_technology' => $this->get_device_technology($start_date, $end_date),
            
            // Content Performance
            'content_performance' => $this->get_content_performance($start_date, $end_date),
            
            // E-commerce (if applicable)
            'ecommerce_data' => $this->get_ecommerce_data($start_date, $end_date),
            
            // Real-time Data
            'realtime_data' => $this->get_realtime_data(),
            
            // Custom Events
            'custom_events' => $this->get_custom_events($start_date, $end_date),
            
            // Conversion Data
            'conversion_data' => $this->get_conversion_data($start_date, $end_date),
            
            // Audience Insights
            'audience_insights' => $this->get_audience_insights($start_date, $end_date)
        ];
        
        return $comprehensive_data;
        // END: ga4_comprehensive_data_collection_block
    }
    
    /**
     * Get basic metrics (sessions, users, page views, etc.)
     */
    private function get_basic_metrics($start_date, $end_date) {
        // START: ga4_basic_metrics_block
        $request_body = [
            'dateRanges' => [['startDate' => $start_date, 'endDate' => $end_date]],
            'metrics' => [
                ['name' => 'sessions'],
                ['name' => 'totalUsers'],
                ['name' => 'screenPageViews'],
                ['name' => 'bounceRate'],
                ['name' => 'averageSessionDuration'],
                ['name' => 'newUsers'],
                ['name' => 'sessionsPerUser']
            ]
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runReport', $request_body);
        
        if (!$response || isset($response['error'])) {
            return ['error' => 'Failed to fetch basic metrics'];
        }
        
        $metrics = [];
        if (isset($response['rows'][0]['metricValues'])) {
            $values = $response['rows'][0]['metricValues'];
            $metrics = [
                'sessions' => intval($values[0]['value']),
                'total_users' => intval($values[1]['value']),
                'page_views' => intval($values[2]['value']),
                'bounce_rate' => floatval($values[3]['value']),
                'avg_session_duration' => floatval($values[4]['value']),
                'new_users' => intval($values[5]['value']),
                'sessions_per_user' => floatval($values[6]['value'])
            ];
        }
        
        return $metrics;
        // END: ga4_basic_metrics_block
    }
    
    /**
     * Get user metrics and demographics
     */
    private function get_user_metrics($start_date, $end_date) {
        // START: ga4_user_metrics_block
        $request_body = [
            'dateRanges' => [['startDate' => $start_date, 'endDate' => $end_date]],
            'dimensions' => [
                ['name' => 'userAgeBracket'],
                ['name' => 'userGender']
            ],
            'metrics' => [
                ['name' => 'totalUsers'],
                ['name' => 'newUsers'],
                ['name' => 'activeUsers']
            ]
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runReport', $request_body);
        
        if (!$response || isset($response['error'])) {
            return ['error' => 'Failed to fetch user metrics'];
        }
        
        $user_data = [];
        if (isset($response['rows'])) {
            foreach ($response['rows'] as $row) {
                $age = $row['dimensionValues'][0]['value'];
                $gender = $row['dimensionValues'][1]['value'];
                $total_users = intval($row['metricValues'][0]['value']);
                $new_users = intval($row['metricValues'][1]['value']);
                $active_users = intval($row['metricValues'][2]['value']);
                
                $user_data[] = [
                    'age_bracket' => $age,
                    'gender' => $gender,
                    'total_users' => $total_users,
                    'new_users' => $new_users,
                    'active_users' => $active_users
                ];
            }
        }
        
        return $user_data;
        // END: ga4_user_metrics_block
    }
    
    /**
     * Get session metrics
     */
    private function get_session_metrics($start_date, $end_date) {
        // START: ga4_session_metrics_block
        $request_body = [
            'dateRanges' => [['startDate' => $start_date, 'endDate' => $end_date]],
            'metrics' => [
                ['name' => 'sessions'],
                ['name' => 'bounceRate'],
                ['name' => 'averageSessionDuration'],
                ['name' => 'sessionsPerUser'],
                ['name' => 'screenPageViewsPerSession'],
                ['name' => 'conversions']
            ]
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runReport', $request_body);
        
        if (!$response || isset($response['error'])) {
            return ['error' => 'Failed to fetch session metrics'];
        }
        
        $session_metrics = [];
        if (isset($response['rows'][0]['metricValues'])) {
            $values = $response['rows'][0]['metricValues'];
            $session_metrics = [
                'sessions' => intval($values[0]['value']),
                'bounce_rate' => floatval($values[1]['value']),
                'avg_session_duration' => floatval($values[2]['value']),
                'sessions_per_user' => floatval($values[3]['value']),
                'page_views_per_session' => floatval($values[4]['value']),
                'conversions' => intval($values[5]['value'])
            ];
        }
        
        return $session_metrics;
        // END: ga4_session_metrics_block
    }
    
    /**
     * Get page performance data
     */
    private function get_page_performance($start_date, $end_date) {
        // START: ga4_page_performance_block
        $request_body = [
            'dateRanges' => [['startDate' => $start_date, 'endDate' => $end_date]],
            'dimensions' => [
                ['name' => 'pagePath'],
                ['name' => 'pageTitle']
            ],
            'metrics' => [
                ['name' => 'screenPageViews'],
                ['name' => 'averageSessionDuration'],
                ['name' => 'bounceRate'],
                ['name' => 'exitRate']
            ],
            'orderBys' => [
                ['metric' => ['metricName' => 'screenPageViews'], 'desc' => true]
            ],
            'limit' => 20
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runReport', $request_body);
        
        if (!$response || isset($response['error'])) {
            return ['error' => 'Failed to fetch page performance'];
        }
        
        $page_data = [];
        if (isset($response['rows'])) {
            foreach ($response['rows'] as $row) {
                $page_data[] = [
                    'page_path' => $row['dimensionValues'][0]['value'],
                    'page_title' => $row['dimensionValues'][1]['value'],
                    'page_views' => intval($row['metricValues'][0]['value']),
                    'avg_session_duration' => floatval($row['metricValues'][1]['value']),
                    'bounce_rate' => floatval($row['metricValues'][2]['value']),
                    'exit_rate' => floatval($row['metricValues'][3]['value'])
                ];
            }
        }
        
        return $page_data;
        // END: ga4_page_performance_block
    }
    
    /**
     * Get traffic analysis data
     */
    private function get_traffic_analysis($start_date, $end_date) {
        // START: ga4_traffic_analysis_block
        $request_body = [
            'dateRanges' => [['startDate' => $start_date, 'endDate' => $end_date]],
            'dimensions' => [
                ['name' => 'sessionDefaultChannelGrouping'],
                ['name' => 'source'],
                ['name' => 'medium']
            ],
            'metrics' => [
                ['name' => 'sessions'],
                ['name' => 'totalUsers'],
                ['name' => 'screenPageViews'],
                ['name' => 'bounceRate']
            ],
            'orderBys' => [
                ['metric' => ['metricName' => 'sessions'], 'desc' => true]
            ],
            'limit' => 50
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runReport', $request_body);
        
        if (!$response || isset($response['error'])) {
            return ['error' => 'Failed to fetch traffic analysis'];
        }
        
        $traffic_data = [];
        if (isset($response['rows'])) {
            foreach ($response['rows'] as $row) {
                $traffic_data[] = [
                    'channel_grouping' => $row['dimensionValues'][0]['value'],
                    'source' => $row['dimensionValues'][1]['value'],
                    'medium' => $row['dimensionValues'][2]['value'],
                    'sessions' => intval($row['metricValues'][0]['value']),
                    'users' => intval($row['metricValues'][1]['value']),
                    'page_views' => intval($row['metricValues'][2]['value']),
                    'bounce_rate' => floatval($row['metricValues'][3]['value'])
                ];
            }
        }
        
        return $traffic_data;
        // END: ga4_traffic_analysis_block
    }
    
    /**
     * Get geographic data
     */
    private function get_geographic_data($start_date, $end_date) {
        // START: ga4_geographic_data_block
        $request_body = [
            'dateRanges' => [['startDate' => $start_date, 'endDate' => $end_date]],
            'dimensions' => [
                ['name' => 'country'],
                ['name' => 'city'],
                ['name' => 'region']
            ],
            'metrics' => [
                ['name' => 'sessions'],
                ['name' => 'totalUsers'],
                ['name' => 'screenPageViews']
            ],
            'orderBys' => [
                ['metric' => ['metricName' => 'sessions'], 'desc' => true]
            ],
            'limit' => 30
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runReport', $request_body);
        
        if (!$response || isset($response['error'])) {
            return ['error' => 'Failed to fetch geographic data'];
        }
        
        $geo_data = [];
        if (isset($response['rows'])) {
            foreach ($response['rows'] as $row) {
                $geo_data[] = [
                    'country' => $row['dimensionValues'][0]['value'],
                    'city' => $row['dimensionValues'][1]['value'],
                    'region' => $row['dimensionValues'][2]['value'],
                    'sessions' => intval($row['metricValues'][0]['value']),
                    'users' => intval($row['metricValues'][1]['value']),
                    'page_views' => intval($row['metricValues'][2]['value'])
                ];
            }
        }
        
        return $geo_data;
        // END: ga4_geographic_data_block
    }
    
    /**
     * Get device and technology data
     */
    private function get_device_technology($start_date, $end_date) {
        // START: ga4_device_technology_block
        $request_body = [
            'dateRanges' => [['startDate' => $start_date, 'endDate' => $end_date]],
            'dimensions' => [
                ['name' => 'deviceCategory'],
                ['name' => 'operatingSystem'],
                ['name' => 'browser'],
                ['name' => 'screenResolution']
            ],
            'metrics' => [
                ['name' => 'sessions'],
                ['name' => 'totalUsers'],
                ['name' => 'screenPageViews']
            ],
            'orderBys' => [
                ['metric' => ['metricName' => 'sessions'], 'desc' => true]
            ],
            'limit' => 25
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runReport', $request_body);
        
        if (!$response || isset($response['error'])) {
            return ['error' => 'Failed to fetch device technology data'];
        }
        
        $device_data = [];
        if (isset($response['rows'])) {
            foreach ($response['rows'] as $row) {
                $device_data[] = [
                    'device_category' => $row['dimensionValues'][0]['value'],
                    'operating_system' => $row['dimensionValues'][1]['value'],
                    'browser' => $row['dimensionValues'][2]['value'],
                    'screen_resolution' => $row['dimensionValues'][3]['value'],
                    'sessions' => intval($row['metricValues'][0]['value']),
                    'users' => intval($row['metricValues'][1]['value']),
                    'page_views' => intval($row['metricValues'][2]['value'])
                ];
            }
        }
        
        return $device_data;
        // END: ga4_device_technology_block
    }
    
    /**
     * Get content performance data
     */
    private function get_content_performance($start_date, $end_date) {
        // START: ga4_content_performance_block
        $request_body = [
            'dateRanges' => [['startDate' => $start_date, 'endDate' => $end_date]],
            'dimensions' => [
                ['name' => 'pagePath'],
                ['name' => 'pageTitle'],
                ['name' => 'landingPage']
            ],
            'metrics' => [
                ['name' => 'screenPageViews'],
                ['name' => 'entrances'],
                ['name' => 'exits'],
                ['name' => 'averageSessionDuration']
            ],
            'orderBys' => [
                ['metric' => ['metricName' => 'screenPageViews'], 'desc' => true]
            ],
            'limit' => 20
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runReport', $request_body);
        
        if (!$response || isset($response['error'])) {
            return ['error' => 'Failed to fetch content performance'];
        }
        
        $content_data = [];
        if (isset($response['rows'])) {
            foreach ($response['rows'] as $row) {
                $content_data[] = [
                    'page_path' => $row['dimensionValues'][0]['value'],
                    'page_title' => $row['dimensionValues'][1]['value'],
                    'landing_page' => $row['dimensionValues'][2]['value'],
                    'page_views' => intval($row['metricValues'][0]['value']),
                    'entrances' => intval($row['metricValues'][1]['value']),
                    'exits' => intval($row['metricValues'][2]['value']),
                    'avg_session_duration' => floatval($row['metricValues'][3]['value'])
                ];
            }
        }
        
        return $content_data;
        // END: ga4_content_performance_block
    }
    
    /**
     * Get e-commerce data (if applicable)
     */
    private function get_ecommerce_data($start_date, $end_date) {
        // START: ga4_ecommerce_data_block
        $request_body = [
            'dateRanges' => [['startDate' => $start_date, 'endDate' => $end_date]],
            'metrics' => [
                ['name' => 'purchaseRevenue'],
                ['name' => 'purchases'],
                ['name' => 'totalRevenue'],
                ['name' => 'averagePurchaseRevenue'],
                ['name' => 'conversions']
            ]
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runReport', $request_body);
        
        if (!$response || isset($response['error'])) {
            return ['error' => 'Failed to fetch e-commerce data'];
        }
        
        $ecommerce_data = [];
        if (isset($response['rows'][0]['metricValues'])) {
            $values = $response['rows'][0]['metricValues'];
            $ecommerce_data = [
                'purchase_revenue' => floatval($values[0]['value']),
                'purchases' => intval($values[1]['value']),
                'total_revenue' => floatval($values[2]['value']),
                'avg_purchase_revenue' => floatval($values[3]['value']),
                'conversions' => intval($values[4]['value'])
            ];
        }
        
        return $ecommerce_data;
        // END: ga4_ecommerce_data_block
    }
    
    /**
     * Get real-time data
     */
    private function get_realtime_data() {
        // START: ga4_realtime_data_block
        $request_body = [
            'dimensions' => [
                ['name' => 'country'],
                ['name' => 'pagePath']
            ],
            'metrics' => [
                ['name' => 'activeUsers']
            ],
            'limit' => 20
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runRealtimeReport', $request_body);
        
        if (!$response || isset($response['error'])) {
            return ['error' => 'Failed to fetch real-time data'];
        }
        
        $realtime_data = [];
        if (isset($response['rows'])) {
            foreach ($response['rows'] as $row) {
                $realtime_data[] = [
                    'country' => $row['dimensionValues'][0]['value'],
                    'page_path' => $row['dimensionValues'][1]['value'],
                    'active_users' => intval($row['metricValues'][0]['value'])
                ];
            }
        }
        
        return $realtime_data;
        // END: ga4_realtime_data_block
    }
    
    /**
     * Get custom events
     */
    private function get_custom_events($start_date, $end_date) {
        // START: ga4_custom_events_block
        $request_body = [
            'dateRanges' => [['startDate' => $start_date, 'endDate' => $end_date]],
            'dimensions' => [
                ['name' => 'eventName']
            ],
            'metrics' => [
                ['name' => 'eventCount'],
                ['name' => 'totalUsers']
            ],
            'orderBys' => [
                ['metric' => ['metricName' => 'eventCount'], 'desc' => true]
            ],
            'limit' => 20
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runReport', $request_body);
        
        if (!$response || isset($response['error'])) {
            return ['error' => 'Failed to fetch custom events'];
        }
        
        $events_data = [];
        if (isset($response['rows'])) {
            foreach ($response['rows'] as $row) {
                $events_data[] = [
                    'event_name' => $row['dimensionValues'][0]['value'],
                    'event_count' => intval($row['metricValues'][0]['value']),
                    'total_users' => intval($row['metricValues'][1]['value'])
                ];
            }
        }
        
        return $events_data;
        // END: ga4_custom_events_block
    }
    
    /**
     * Get conversion data
     */
    private function get_conversion_data($start_date, $end_date) {
        // START: ga4_conversion_data_block
        $request_body = [
            'dateRanges' => [['startDate' => $start_date, 'endDate' => $end_date]],
            'dimensions' => [
                ['name' => 'conversionEventName']
            ],
            'metrics' => [
                ['name' => 'conversions'],
                ['name' => 'totalUsers'],
                ['name' => 'conversionRate']
            ],
            'orderBys' => [
                ['metric' => ['metricName' => 'conversions'], 'desc' => true]
            ],
            'limit' => 15
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runReport', $request_body);
        
        if (!$response || isset($response['error'])) {
            return ['error' => 'Failed to fetch conversion data'];
        }
        
        $conversion_data = [];
        if (isset($response['rows'])) {
            foreach ($response['rows'] as $row) {
                $conversion_data[] = [
                    'conversion_event' => $row['dimensionValues'][0]['value'],
                    'conversions' => intval($row['metricValues'][0]['value']),
                    'total_users' => intval($row['metricValues'][1]['value']),
                    'conversion_rate' => floatval($row['metricValues'][2]['value'])
                ];
            }
        }
        
        return $conversion_data;
        // END: ga4_conversion_data_block
    }
    
    /**
     * Get audience insights
     */
    private function get_audience_insights($start_date, $end_date) {
        // START: ga4_audience_insights_block
        $request_body = [
            'dateRanges' => [['startDate' => $start_date, 'endDate' => $end_date]],
            'dimensions' => [
                ['name' => 'userAgeBracket'],
                ['name' => 'userGender'],
                ['name' => 'country']
            ],
            'metrics' => [
                ['name' => 'totalUsers'],
                ['name' => 'newUsers'],
                ['name' => 'sessions']
            ],
            'orderBys' => [
                ['metric' => ['metricName' => 'totalUsers'], 'desc' => true]
            ],
            'limit' => 30
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runReport', $request_body);
        
        if (!$response || isset($response['error'])) {
            return ['error' => 'Failed to fetch audience insights'];
        }
        
        $audience_data = [];
        if (isset($response['rows'])) {
            foreach ($response['rows'] as $row) {
                $audience_data[] = [
                    'age_bracket' => $row['dimensionValues'][0]['value'],
                    'gender' => $row['dimensionValues'][1]['value'],
                    'country' => $row['dimensionValues'][2]['value'],
                    'total_users' => intval($row['metricValues'][0]['value']),
                    'new_users' => intval($row['metricValues'][1]['value']),
                    'sessions' => intval($row['metricValues'][2]['value'])
                ];
            }
        }
        
        return $audience_data;
        // END: ga4_audience_insights_block
    }
    
    /**
     * Get PDF downloads (custom event tracking)
     * Note: This requires custom event tracking setup in GA4
     */
    private function get_pdf_downloads($start_date, $end_date) {
        // START: ga4_pdf_downloads_block
        // Try to get real PDF download events from GA4
        $request_body = [
            'dateRanges' => [['startDate' => $start_date, 'endDate' => $end_date]],
            'dimensions' => [
                ['name' => 'eventName'],
                ['name' => 'fileExtension']
            ],
            'metrics' => [
                ['name' => 'eventCount'],
                ['name' => 'totalUsers']
            ],
            'dimensionFilter' => [
                'filter' => [
                    'fieldName' => 'eventName',
                    'stringFilter' => [
                        'matchType' => 'CONTAINS',
                        'value' => 'download'
                    ]
                ]
            ],
            'orderBys' => [
                ['metric' => ['metricName' => 'eventCount'], 'desc' => true]
            ],
            'limit' => 10
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runReport', $request_body);
        
        if (!$response || isset($response['error'])) {
            return [
                'status' => 'unavailable',
                'message' => 'PDF download tracking not configured in GA4',
                'note' => 'Requires custom event tracking setup',
                'data' => []
            ];
        }
        
        $download_data = [];
        if (isset($response['rows'])) {
            foreach ($response['rows'] as $row) {
                $download_data[] = [
                    'event_name' => $row['dimensionValues'][0]['value'],
                    'file_extension' => $row['dimensionValues'][1]['value'],
                    'downloads' => intval($row['metricValues'][0]['value']),
                    'users' => intval($row['metricValues'][1]['value'])
                ];
            }
        }
        
        if (empty($download_data)) {
            return [
                'status' => 'unavailable',
                'message' => 'No PDF download events found',
                'note' => 'Configure file download tracking in GA4',
                'data' => []
            ];
        }
        
        return [
            'status' => 'available',
            'data' => $download_data
        ];
        // END: ga4_pdf_downloads_block
    }
    
    /**
     * Get trending insights
     * Note: This requires custom analysis of GA4 data trends
     */
    private function get_trending_insights($start_date, $end_date) {
        // START: ga4_trending_insights_block
        // Trending insights analysis not implemented - return empty data
        return [
            'status' => 'unavailable',
            'message' => 'Trending insights analysis requires custom implementation',
            'data' => []
        ];
        // END: ga4_trending_insights_block
    }
    
    /**
     * Get recent activity
     * Note: GA4 real-time API has limited activity data
     */
    private function get_recent_activity() {
        // START: ga4_recent_activity_block
        // Check if we have valid credentials first
        if (empty($this->property_id) || empty($this->service_account_email)) {
            error_log('GA4 Analytics: Missing credentials for real-time data');
            return [
                'status' => 'unavailable',
                'message' => 'Real-time activity data not available',
                'note' => 'GA4 credentials not configured. Please check your API settings.',
                'data' => []
            ];
        }
        
        // Get real-time data from GA4
        $request_body = [
            'dimensions' => [
                ['name' => 'pagePath'],
                ['name' => 'eventName']
            ],
            'metrics' => [
                ['name' => 'activeUsers']
            ],
            'limit' => 10
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runRealtimeReport', $request_body);
        
        if (!$response || isset($response['error'])) {
            // Log the specific error for debugging
            if (isset($response['error'])) {
                error_log('GA4 Analytics: Real-time API error: ' . json_encode($response['error']));
            }
            
            // Try to get fallback activity data when real-time fails
            $fallback_activity = $this->get_fallback_activity();
            if (!empty($fallback_activity)) {
                return [
                    'status' => 'fallback',
                    'message' => 'Using recent page activity data',
                    'note' => 'Real-time data temporarily unavailable, showing today\'s page activity instead',
                    'data' => $fallback_activity
                ];
            }
            
            return [
                'status' => 'unavailable',
                'message' => 'Real-time activity data not available',
                'note' => 'GA4 real-time API has limited activity details. This is normal during low-traffic periods or when no users are currently active.',
                'data' => []
            ];
        }
        
        $activity_data = [];
        if (isset($response['rows'])) {
            foreach ($response['rows'] as $row) {
                $activity_data[] = [
                    'page_path' => $row['dimensionValues'][0]['value'],
                    'event_name' => $row['dimensionValues'][1]['value'],
                    'active_users' => intval($row['metricValues'][0]['value']),
                    'timestamp' => 'Currently active'
                ];
            }
        }
        
        if (empty($activity_data)) {
            // Try to get some fallback activity data from recent page views
            $fallback_activity = $this->get_fallback_activity();
            if (!empty($fallback_activity)) {
                return [
                    'status' => 'fallback',
                    'message' => 'Using recent page activity data',
                    'note' => 'Real-time data unavailable, showing recent page views instead',
                    'data' => $fallback_activity
                ];
            }
            
            return [
                'status' => 'unavailable',
                'message' => 'No recent activity detected',
                'note' => 'No active users or events in real-time. This is normal during low-traffic periods.',
                'data' => []
            ];
        }
        
        return [
            'status' => 'available',
            'data' => $activity_data
        ];
        // END: ga4_recent_activity_block
    }
    
    /**
     * Calculate previous period start date
     */
    private function get_previous_period_start($start_date, $period) {
        $days = [
            'day' => 7,
            'week' => 30,
            'month' => 90,
            'all_time' => 365
        ];
        
        $days_back = $days[$period] ?? 7;
        return $days_back . 'daysAgo';
    }
    
    /**
     * Format private key for OpenSSL
     * Handles various private key formats and ensures proper PEM formatting
     */
    private function format_private_key($private_key) {
        // START: ga4_private_key_formatting_block
        if (empty($private_key)) {
            error_log('GA4 Analytics: Private key is empty');
            return false;
        }
        
        // Remove any existing whitespace and newlines
        $private_key = trim($private_key);
        
        // Log the first 50 characters for debugging (without exposing the full key)
        error_log('GA4 Analytics: Private key starts with: ' . substr($private_key, 0, 50) . '...');
        
        // If it's already in PEM format, validate and return
        if (strpos($private_key, '-----BEGIN PRIVATE KEY-----') !== false) {
            // Handle multiple types of escaped newlines
            $formatted_key = $private_key;
            
            // Replace various forms of escaped newlines with actual newlines
            $formatted_key = str_replace('\\\\n', "\n", $formatted_key);  // Double-escaped (most common issue)
            $formatted_key = str_replace('\\n', "\n", $formatted_key);    // Single-escaped
            $formatted_key = str_replace('\n', "\n", $formatted_key);     // Single quotes
            $formatted_key = str_replace('\\r', "\r", $formatted_key);    // Carriage returns
            $formatted_key = str_replace('\r', "\r", $formatted_key);     // Single quote carriage returns
            
            // Remove any remaining backslashes that might be escaping characters
            $formatted_key = str_replace('\\', '', $formatted_key);
            
            // Ensure proper line breaks and remove any extra whitespace
            $formatted_key = preg_replace('/\r\n|\r|\n/', "\n", $formatted_key);
            $formatted_key = preg_replace('/\n+/', "\n", $formatted_key); // Remove multiple newlines
            
            error_log('GA4 Analytics: Formatted PEM key with proper newlines');
            
            // Test if this key can be loaded
            $test_resource = openssl_pkey_get_private($formatted_key);
            if ($test_resource) {
                openssl_pkey_free($test_resource);
                error_log('GA4 Analytics: PEM key validation successful');
                return $formatted_key;
            } else {
                $openssl_error = openssl_error_string();
                error_log('GA4 Analytics: PEM key validation failed: ' . $openssl_error);
                
                // Try one more approach - ensure the key is properly formatted
                $lines = explode("\n", $formatted_key);
                $clean_lines = [];
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!empty($line)) {
                        $clean_lines[] = $line;
                    }
                }
                $formatted_key = implode("\n", $clean_lines);
                
                $test_resource = openssl_pkey_get_private($formatted_key);
                if ($test_resource) {
                    openssl_pkey_free($test_resource);
                    error_log('GA4 Analytics: Cleaned PEM key validation successful');
                    return $formatted_key;
                } else {
                    error_log('GA4 Analytics: Cleaned PEM key validation failed: ' . openssl_error_string());
                    
                    // Final attempt - try to reconstruct the key properly
                    $formatted_key = $this->reconstruct_pem_key($private_key);
                    if ($formatted_key) {
                        $test_resource = openssl_pkey_get_private($formatted_key);
                        if ($test_resource) {
                            openssl_pkey_free($test_resource);
                            error_log('GA4 Analytics: Reconstructed PEM key validation successful');
                            return $formatted_key;
                        } else {
                            error_log('GA4 Analytics: Reconstructed key validation failed: ' . openssl_error_string());
                            
                            // Try one more approach - convert using OpenSSL
                            $converted_key = $this->convert_key_with_openssl($formatted_key);
                            if ($converted_key) {
                                $test_resource = openssl_pkey_get_private($converted_key);
                                if ($test_resource) {
                                    openssl_pkey_free($test_resource);
                                    error_log('GA4 Analytics: OpenSSL converted key validation successful');
                                    return $converted_key;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        // If it's a raw private key (from JSON), format it properly
        if (strpos($private_key, '-----') === false) {
            // Remove any escape characters
            $clean_key = str_replace(['\\n', '\n', '\\r', '\r'], '', $private_key);
            $clean_key = str_replace('\\', '', $clean_key); // Remove any remaining backslashes
            $clean_key = preg_replace('/\s+/', '', $clean_key);
            
            // Add proper PEM headers and footers
            $formatted_key = "-----BEGIN PRIVATE KEY-----\n";
            $formatted_key .= chunk_split($clean_key, 64, "\n");
            $formatted_key .= "-----END PRIVATE KEY-----\n";
            
            // Test if this formatted key can be loaded
            $test_resource = openssl_pkey_get_private($formatted_key);
            if ($test_resource) {
                openssl_pkey_free($test_resource);
                return $formatted_key;
            } else {
                error_log('GA4 Analytics: Formatted raw key validation failed: ' . openssl_error_string());
            }
        }
        
        // If it looks like it might be a JSON key, try to extract the private key
        if (strpos($private_key, '"private_key"') !== false) {
            $key_data = json_decode($private_key, true);
            if (isset($key_data['private_key'])) {
                error_log('GA4 Analytics: Extracting private key from JSON');
                return $this->format_private_key($key_data['private_key']);
            }
        }
        
        // Try to handle base64 encoded keys
        if (base64_decode($private_key, true) !== false) {
            error_log('GA4 Analytics: Attempting to format base64 encoded key');
            $decoded_key = base64_decode($private_key);
            $formatted_key = "-----BEGIN PRIVATE KEY-----\n";
            $formatted_key .= chunk_split(base64_encode($decoded_key), 64, "\n");
            $formatted_key .= "-----END PRIVATE KEY-----\n";
            
            $test_resource = openssl_pkey_get_private($formatted_key);
            if ($test_resource) {
                openssl_pkey_free($test_resource);
                return $formatted_key;
            } else {
                error_log('GA4 Analytics: Base64 decoded key validation failed: ' . openssl_error_string());
            }
        }
        
        // Try to handle PKCS#8 format
        if (strpos($private_key, '-----BEGIN') === false && strlen($private_key) > 100) {
            error_log('GA4 Analytics: Attempting PKCS#8 format');
            $formatted_key = "-----BEGIN PRIVATE KEY-----\n";
            $formatted_key .= chunk_split($private_key, 64, "\n");
            $formatted_key .= "-----END PRIVATE KEY-----\n";
            
            $test_resource = openssl_pkey_get_private($formatted_key);
            if ($test_resource) {
                openssl_pkey_free($test_resource);
                return $formatted_key;
            } else {
                error_log('GA4 Analytics: PKCS#8 format validation failed: ' . openssl_error_string());
            }
        }
        
        error_log('GA4 Analytics: All private key formatting attempts failed');
        return false;
        // END: ga4_private_key_formatting_block
    }
    
    /**
     * Base64 URL encode
     */
    private function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Test connection with detailed error reporting
     */
    public function test_connection() {
        // START: ga4_connection_test_block
        // Check if required credentials are present
        if (empty($this->service_account_email)) {
            return ['success' => false, 'message' => 'Service account email not configured'];
        }
        
        if (empty($this->private_key)) {
            return ['success' => false, 'message' => 'Private key not configured'];
        }
        
        if (empty($this->property_id)) {
            return ['success' => false, 'message' => 'GA4 Property ID not configured'];
        }
        
        // Diagnose private key format
        $key_diagnosis = $this->diagnose_private_key($this->private_key);
        error_log('GA4 Analytics: Private key diagnosis: ' . $key_diagnosis);
        
        // Test private key formatting
        $formatted_key = $this->format_private_key($this->private_key);
        if (!$formatted_key) {
            return ['success' => false, 'message' => 'Private key format is invalid. Diagnosis: ' . $key_diagnosis];
        }
        
        // Test private key loading
        $private_key_resource = openssl_pkey_get_private($formatted_key);
        if (!$private_key_resource) {
            $openssl_error = openssl_error_string();
            return ['success' => false, 'message' => 'Private key cannot be loaded: ' . $openssl_error . '. Diagnosis: ' . $key_diagnosis];
        }
        openssl_pkey_free($private_key_resource);
        
        // Test access token generation
        $token = $this->get_access_token();
        if (!$token) {
            return ['success' => false, 'message' => 'Failed to get access token - check service account permissions'];
        }
        
        // Test API request
        $response = $this->make_api_request('properties/' . $this->property_id . ':runReport', [
            'dateRanges' => [['startDate' => '7daysAgo', 'endDate' => 'today']],
            'metrics' => [['name' => 'sessions']]
        ]);
        
        if ($response && !isset($response['error'])) {
            return ['success' => true, 'message' => 'Connection successful - GA4 API responding'];
        }
        
        $error_message = 'API request failed';
        if (isset($response['error'])) {
            $error_message .= ': ' . ($response['error']['message'] ?? 'Unknown API error');
        }
        
        return ['success' => false, 'message' => $error_message];
        // END: ga4_connection_test_block
    }
    
    /**
     * Reconstruct PEM key from malformed input
     */
    private function reconstruct_pem_key($private_key) {
        // START: ga4_pem_reconstruction_block
        if (empty($private_key)) {
            return false;
        }
        
        // Extract the base64 content between headers
        $start_marker = '-----BEGIN PRIVATE KEY-----';
        $end_marker = '-----END PRIVATE KEY-----';
        
        $start_pos = strpos($private_key, $start_marker);
        $end_pos = strpos($private_key, $end_marker);
        
        if ($start_pos === false || $end_pos === false) {
            error_log('GA4 Analytics: Could not find PEM markers in key');
            return false;
        }
        
        // Extract the content between markers
        $content = substr($private_key, $start_pos + strlen($start_marker), $end_pos - $start_pos - strlen($start_marker));
        
        // Log the raw content for debugging (first 100 chars only)
        error_log('GA4 Analytics: Raw content preview: ' . substr($content, 0, 100) . '...');
        
        // Clean up the content - remove all escape sequences and whitespace
        $content = str_replace(['\\\\n', '\\n', '\n', '\\r', '\r', "\r", "\n"], '', $content);
        
        // Remove any remaining backslashes that might be escaping characters
        $content = str_replace('\\', '', $content);
        
        // Remove any remaining whitespace
        $content = preg_replace('/\s+/', '', $content);
        
        // Log the cleaned content for debugging
        error_log('GA4 Analytics: Cleaned content preview: ' . substr($content, 0, 100) . '...');
        error_log('GA4 Analytics: Cleaned content length: ' . strlen($content));
        
        // Validate that the content looks like base64
        if (!preg_match('/^[A-Za-z0-9+\/=]+$/', $content)) {
            error_log('GA4 Analytics: Content does not appear to be valid base64');
            return false;
        }
        
        // Reconstruct the key with proper formatting
        $reconstructed = $start_marker . "\n";
        $reconstructed .= chunk_split($content, 64, "\n");
        $reconstructed .= $end_marker . "\n";
        
        // Log the reconstructed key preview for debugging
        error_log('GA4 Analytics: Reconstructed key preview: ' . substr($reconstructed, 0, 100) . '...');
        
        error_log('GA4 Analytics: Reconstructed PEM key from malformed input');
        return $reconstructed;
        // END: ga4_pem_reconstruction_block
    }
    
    /**
     * Convert key using OpenSSL command line
     */
    private function convert_key_with_openssl($private_key) {
        // START: ga4_openssl_conversion_block
        if (empty($private_key)) {
            return false;
        }
        
        // Create temporary files
        $temp_input = tempnam(sys_get_temp_dir(), 'ga4_key_input_');
        $temp_output = tempnam(sys_get_temp_dir(), 'ga4_key_output_');
        
        if (!$temp_input || !$temp_output) {
            error_log('GA4 Analytics: Could not create temporary files for OpenSSL conversion');
            return false;
        }
        
        // Write the key to input file
        if (file_put_contents($temp_input, $private_key) === false) {
            error_log('GA4 Analytics: Could not write key to temporary input file');
            unlink($temp_input);
            unlink($temp_output);
            return false;
        }
        
        // Try to convert using OpenSSL
        $command = "openssl rsa -in {$temp_input} -outform PEM 2>/dev/null";
        $output = shell_exec($command);
        
        if ($output && file_exists($temp_output)) {
            $converted_key = file_get_contents($temp_output);
            if ($converted_key) {
                error_log('GA4 Analytics: Successfully converted key using OpenSSL');
                unlink($temp_input);
                unlink($temp_output);
                return $converted_key;
            }
        }
        
        // Try alternative conversion
        $command = "openssl pkcs8 -in {$temp_input} -outform PEM 2>/dev/null";
        $output = shell_exec($command);
        
        if ($output && file_exists($temp_output)) {
            $converted_key = file_get_contents($temp_output);
            if ($converted_key) {
                error_log('GA4 Analytics: Successfully converted key using OpenSSL PKCS8');
                unlink($temp_input);
                unlink($temp_output);
                return $converted_key;
            }
        }
        
        // Clean up
        unlink($temp_input);
        unlink($temp_output);
        
        error_log('GA4 Analytics: OpenSSL conversion failed');
        return false;
        // END: ga4_openssl_conversion_block
    }
    
    /**
     * Diagnose private key format and provide helpful information
     */
    private function diagnose_private_key($private_key) {
        // START: ga4_private_key_diagnosis_block
        if (empty($private_key)) {
            return 'Empty private key';
        }
        
        $key_length = strlen($private_key);
        $has_pem_headers = strpos($private_key, '-----BEGIN') !== false;
        $has_escaped_newlines = strpos($private_key, '\\n') !== false;
        $has_actual_newlines = strpos($private_key, "\n") !== false;
        $is_base64 = base64_decode($private_key, true) !== false;
        $has_json = strpos($private_key, '"private_key"') !== false;
        
        $diagnosis = "Length: {$key_length}, ";
        $diagnosis .= "PEM Headers: " . ($has_pem_headers ? 'Yes' : 'No') . ", ";
        $diagnosis .= "Escaped Newlines: " . ($has_escaped_newlines ? 'Yes' : 'No') . ", ";
        $diagnosis .= "Actual Newlines: " . ($has_actual_newlines ? 'Yes' : 'No') . ", ";
        $diagnosis .= "Base64: " . ($is_base64 ? 'Yes' : 'No') . ", ";
        $diagnosis .= "JSON: " . ($has_json ? 'Yes' : 'No');
        
        return $diagnosis;
        // END: ga4_private_key_diagnosis_block
    }
    
    /**
     * Test real-time API connection specifically
     */
    public function test_realtime_connection() {
        // START: ga4_realtime_connection_test_block
        if (empty($this->property_id) || empty($this->service_account_email)) {
            return [
                'success' => false,
                'message' => 'GA4 credentials not configured',
                'details' => 'Please configure your GA4 Property ID and Service Account credentials'
            ];
        }
        
        // Test basic connection first
        $basic_test = $this->test_connection();
        if (!$basic_test['success']) {
            return [
                'success' => false,
                'message' => 'Basic GA4 connection failed',
                'details' => $basic_test['message']
            ];
        }
        
        // Test real-time API specifically
        $request_body = [
            'dimensions' => [
                ['name' => 'country']
            ],
            'metrics' => [
                ['name' => 'activeUsers']
            ],
            'limit' => 1
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runRealtimeReport', $request_body);
        
        if (!$response) {
            return [
                'success' => false,
                'message' => 'Real-time API request failed',
                'details' => 'Unable to connect to GA4 real-time API'
            ];
        }
        
        if (isset($response['error'])) {
            return [
                'success' => false,
                'message' => 'Real-time API error',
                'details' => $response['error']['message'] ?? 'Unknown real-time API error'
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Real-time API connection successful',
            'details' => 'GA4 real-time API is responding correctly'
        ];
        // END: ga4_realtime_connection_test_block
    }
    
    /**
     * Get fallback activity data from recent page views when real-time data is unavailable
     */
    private function get_fallback_activity() {
        // START: ga4_fallback_activity_block
        $request_body = [
            'dateRanges' => [['startDate' => 'today', 'endDate' => 'today']],
            'dimensions' => [
                ['name' => 'pagePath'],
                ['name' => 'pageTitle']
            ],
            'metrics' => [
                ['name' => 'screenPageViews'],
                ['name' => 'totalUsers']
            ],
            'orderBys' => [
                ['metric' => ['metricName' => 'screenPageViews'], 'desc' => true]
            ],
            'limit' => 5
        ];
        
        $response = $this->make_api_request('properties/' . $this->property_id . ':runReport', $request_body);
        
        if (!$response || isset($response['error'])) {
            return [];
        }
        
        $fallback_data = [];
        if (isset($response['rows'])) {
            foreach ($response['rows'] as $row) {
                $fallback_data[] = [
                    'page_path' => $row['dimensionValues'][0]['value'],
                    'page_title' => $row['dimensionValues'][1]['value'],
                    'page_views' => intval($row['metricValues'][0]['value']),
                    'users' => intval($row['metricValues'][1]['value']),
                    'timestamp' => 'Today'
                ];
            }
        }
        
        return $fallback_data;
        // END: ga4_fallback_activity_block
    }
}
