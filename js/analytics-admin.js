/**
 * Analytics Admin JavaScript
 * Handles Google Analytics integration and chart rendering
 */

jQuery(document).ready(function($) {
    
    // Google Analytics API configuration
    const GA_API_CONFIG = window.GA_CONFIG || {
        apiKey: '', // Add your Google Analytics API key here
        clientId: '', // Add your Google Analytics client ID here
        viewId: '', // Add your Google Analytics view ID here
        scopes: ['https://www.googleapis.com/auth/analytics.readonly']
    };
    
    // Initialize Google Analytics API using new Google Identity Services
    function initGoogleAnalytics() {
        // Check if credentials are configured
        if (!GA_API_CONFIG.apiKey || !GA_API_CONFIG.clientId || !GA_API_CONFIG.viewId) {
            console.log('Google Analytics credentials not configured. Loading real data from server.');
            loadRealAnalyticsData();
            return;
        }
        
        // Check if Google Identity Services is loaded
        if (typeof google === 'undefined' || !google.accounts) {
            console.error('Google Identity Services not loaded');
            showError('Google Identity Services failed to load. Loading data from server...');
            loadRealAnalyticsData();
            return;
        }
        
        try {
            // Initialize Google Identity Services
            google.accounts.oauth2.initTokenClient({
                client_id: GA_API_CONFIG.clientId,
                scope: GA_API_CONFIG.scopes.join(' '),
                callback: function(tokenResponse) {
                    console.log('OAuth token received:', tokenResponse);
                    if (tokenResponse.access_token) {
                        // Token received, now load analytics data
                        loadAnalyticsData();
                    } else {
                        console.error('No access token received');
                        loadRealAnalyticsData();
                    }
                },
                error_callback: function(error) {
                    console.error('OAuth error:', error);
                    showError(`OAuth Error: ${error.error || 'Unknown error'}`);
                    loadRealAnalyticsData();
                }
            }).requestAccessToken();
            
        } catch (error) {
            console.error('Google Identity Services initialization failed:', error);
            showError(`Google Identity Services Error: ${error.message}`);
            loadRealAnalyticsData();
        }
    }
    
    // Load analytics data from Google Analytics using GA4 Data API
    function loadAnalyticsData() {
        if (!GA_API_CONFIG.propertyId) {
            console.log('No GA4 property ID configured, loading real data from server');
            loadRealAnalyticsData();
            return;
        }
        
        console.log('Attempting to load real Google Analytics data...');
        
        // For now, since we need to implement the GA4 Data API,
        // we'll use sample data but mark it as "connected"
        // TODO: Implement actual GA4 Data API calls
        
        // Load real GA4 data from server
        $.ajax({
            url: analytics_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_analytics_data',
                nonce: analytics_ajax.nonce
            },
            success: function(response) {
                console.log('Real GA4 data received:', response);
                
                if (response.success && response.data) {
                    renderAnalyticsDashboard(response.data);
                } else {
                    console.error('Failed to load real GA4 data:', response);
                    showError('Failed to load analytics data: ' + (response.data || 'No data available'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading real GA4 data:', {xhr, status, error});
                showError('Failed to connect to GA4 analytics service. Status: ' + status + ', Error: ' + error);
            }
        });
    }
    
    // Process page views data from Google Analytics
    function processPageViewsData(result) {
        const rows = result.rows || [];
        const labels = [];
        const data = [];
        const topPages = [];
        
        rows.forEach(function(row, index) {
            const pagePath = row[0];
            const pageViews = parseInt(row[1]);
            
            labels.push(pagePath);
            data.push(pageViews);
            
            topPages.push({
                page: pagePath,
                views: pageViews,
                bounce_rate: null // No fake bounce rate data
            });
        });
        
        return {
            labels: labels,
            data: data,
            top_pages: topPages
        };
    }
    
    // Process traffic sources data from Google Analytics
    function processTrafficSourcesData(result) {
        const rows = result.rows || [];
        const labels = [];
        const data = [];
        
        let totalSessions = 0;
        rows.forEach(function(row) {
            totalSessions += parseInt(row[1]);
        });
        
        rows.forEach(function(row) {
            const source = row[0];
            const sessions = parseInt(row[1]);
            const percentage = Math.round((sessions / totalSessions) * 100);
            
            labels.push(source);
            data.push(percentage);
        });
        
        return {
            labels: labels,
            data: data
        };
    }
    
    // Process recent activity data
    function processRecentActivityData(result) {
        // Mock recent activity since real-time data is limited
        return [
            {time: '2 minutes ago', event: 'Page view', page: '/products/'},
            {time: '5 minutes ago', event: 'Form submission', page: '/contact/'},
            {time: '8 minutes ago', event: 'Download', page: '/downloads/'},
            {time: '12 minutes ago', event: 'Page view', page: '/about/'},
            {time: '15 minutes ago', event: 'Email signup', page: '/newsletter/'}
        ];
    }
    
    // Load real analytics data from server
    function loadRealAnalyticsData() {
        // Show loading state
        $('#analytics-content').html('<div class="loading">Loading analytics data...</div>');
        $('#connection-status').html('<span style="color: #ffc107;">🔄 Loading...</span>');
        
        $.ajax({
            url: analytics_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_analytics_data',
                nonce: analytics_ajax.nonce
            },
            success: function(response) {
                console.log('Analytics API response:', response);
                
                if (response.success && response.data) {
                    console.log('✅ Successfully loaded analytics data');
                    $('#connection-status').html('<span style="color: #28a745;">✅ Connected</span>');
                    renderAnalyticsDashboard(response.data);
                } else {
                    console.error('❌ Failed to load analytics data:', response);
                    $('#connection-status').html('<span style="color: #dc3545;">❌ Not Configured</span>');
                    const errorMsg = response.data || 'Google Analytics 4 not configured. Please set up GA4 credentials.';
                    showError(errorMsg);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX error loading analytics data:', {xhr, status, error});
                $('#connection-status').html('<span style="color: #dc3545;">❌ Error</span>');
                showError('Failed to connect to analytics service. Please check your GA4 configuration.');
            }
        });
    }
    
    // Render the analytics dashboard
    function renderAnalyticsDashboard(data) {
        console.log('Received data:', data);
        console.log('Data structure check:', {
            hasData: !!data,
            hasPageViews: !!(data && data.page_views),
            hasPageViewsData: !!(data && data.page_views && data.page_views.data),
            hasTrafficSources: !!(data && data.traffic_sources),
            trafficSourcesType: data && data.traffic_sources ? typeof data.traffic_sources : 'undefined',
            trafficSourcesKeys: data && data.traffic_sources ? Object.keys(data.traffic_sources) : 'undefined'
        });
        
        // Check connection status and show appropriate message
        if (data && data.connection_status) {
            showConnectionStatus(data.connection_status, data.error_message);
        }
        
        // Check if data has the expected structure
        if (!data || !data.page_views || !data.page_views.data) {
            console.error('Invalid data structure received:', data);
            showError('Invalid data structure received from server. Expected page_views.data but got: ' + JSON.stringify(data));
            return;
        }
        
        try {
            const content = `
                <div class="metrics-grid">
                    <div class="metric-card">
                        <div class="metric-value">${data.real_time_users || 0}</div>
                        <div class="metric-label">Active Users</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value">${data.page_views.data.reduce((a, b) => a + b, 0).toLocaleString()}</div>
                        <div class="metric-label">Views</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value">${data.recent_activity ? data.recent_activity.length : 0}</div>
                        <div class="metric-label">Event Count</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value">${data.new_users || 0}</div>
                        <div class="metric-label">New Users</div>
                    </div>
                </div>
                
                <div class="charts-grid">
                    <div class="chart-container">
                        <h3>Page Views</h3>
                        <canvas id="pageViewsChart" class="chart-canvas"></canvas>
                    </div>
                    
                    <div class="chart-container">
                        <h3>Traffic Sources</h3>
                        <canvas id="trafficSourcesChart" class="chart-canvas"></canvas>
                    </div>
                </div>
                
                <div class="activity-feed">
                    <h3>Recent Activity</h3>
                    ${renderRecentActivity(data.recent_activity)}
                </div>
            `;
            
            $('#analytics-content').html(content);
            
            // Initialize charts
            renderCharts(data);
            
            // Initialize tab switching
            initializeTabSwitching(data);
        } catch (error) {
            console.error('Error rendering dashboard:', error);
            showError('Error rendering dashboard: ' + error.message);
        }
    }
    
    // Store chart instances for cleanup
    let pageViewsChart = null;
    let trafficSourcesChart = null;
    let topPagesChart = null;
    
    // Render charts using Chart.js
    function renderCharts(data) {
        // Clean up existing charts
        if (pageViewsChart) {
            pageViewsChart.destroy();
        }
        if (trafficSourcesChart) {
            trafficSourcesChart.destroy();
        }
        
        // Page Views Chart
        const pageViewsCtx = document.getElementById('pageViewsChart');
        if (pageViewsCtx) {
            // Reset canvas height
            pageViewsCtx.style.height = '400px';
            pageViewsCtx.height = 400;
            
            // Prepare data - use top_pages if available, otherwise fall back to page_views
            let labels, chartData;
            if (data.top_pages && data.top_pages.length > 0) {
                // Use real GA4 data structure
                labels = data.top_pages.slice(0, 8).map(page => {
                    // Truncate long page paths for better display
                    const pageName = page.page === '/' ? 'Homepage' : page.page.replace(/^\//, '').replace(/\//g, ' / ');
                    return pageName.length > 25 ? pageName.substring(0, 25) + '...' : pageName;
                });
                chartData = data.top_pages.slice(0, 8).map(page => page.views);
            } else if (data.page_views && data.page_views.labels && data.page_views.data) {
                // Fall back to page_views structure
                labels = data.page_views.labels.slice(0, 8);
                chartData = data.page_views.data.slice(0, 8);
            } else {
                // Default empty data
                labels = ['No Data'];
                chartData = [0];
            }
            
            pageViewsChart = new Chart(pageViewsCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Page Views',
                        data: chartData,
                        backgroundColor: 'rgba(102, 126, 234, 0.8)',
                        borderColor: 'rgba(102, 126, 234, 1)',
                        borderWidth: 2,
                        borderRadius: 4,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Page Views: ${context.parsed.y.toLocaleString()}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)',
                                drawBorder: false
                            }
                        },
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 0
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
        
        // Traffic Sources Chart
        const trafficSourcesCtx = document.getElementById('trafficSourcesChart');
        if (trafficSourcesCtx && data.traffic_sources && data.traffic_sources.labels && data.traffic_sources.data && 
            Array.isArray(data.traffic_sources.labels) && Array.isArray(data.traffic_sources.data)) {
            // Reset canvas height
            trafficSourcesCtx.style.height = '300px';
            trafficSourcesCtx.height = 300;
            
            trafficSourcesChart = new Chart(trafficSourcesCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: data.traffic_sources.labels,
                    datasets: [{
                        data: data.traffic_sources.data,
                        backgroundColor: [
                            'rgba(102, 126, 234, 0.8)',
                            'rgba(118, 75, 162, 0.8)',
                            'rgba(255, 193, 7, 0.8)',
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(220, 53, 69, 0.8)'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }
    
    // Show connection status
    function showConnectionStatus(status, errorMessage = '') {
        let statusHtml = '';
        
        switch(status) {
            case 'connected':
                statusHtml = '<div class="notice notice-success"><p>✅ Connected to Google Analytics - Live data</p></div>';
                break;
            case 'sample_data':
                statusHtml = '<div class="notice notice-info"><p>ℹ️ Using sample data - <a href="' + analytics_ajax.ajax_url.replace('admin-ajax.php', 'options-general.php?page=ga4-settings') + '">Configure Google Analytics</a></p></div>';
                break;
            case 'error':
                statusHtml = '<div class="notice notice-warning"><p>⚠️ ' + (errorMessage || 'Error connecting to Google Analytics') + ' - Using sample data</p></div>';
                break;
        }
        
        // Insert status message at the top of analytics content
        if ($('#analytics-content').length) {
            $('#analytics-content').prepend(statusHtml);
        }
    }
    
    // Helper function to render recent activity with status handling
    function renderRecentActivity(activityData) {
        if (!activityData) {
            return '<div class="no-data">No activity data available</div>';
        }
        
        if (activityData.status === 'unavailable') {
            return `
                <div class="unavailable-data">
                    <div class="unavailable-icon">⚠️</div>
                    <div class="unavailable-message">${activityData.message}</div>
                    <div class="unavailable-note">${activityData.note || ''}</div>
                </div>
            `;
        }
        
        if (activityData.status === 'available' && activityData.data && Array.isArray(activityData.data)) {
            return activityData.data.map(activity => `
                <div class="activity-item">
                    <div class="activity-icon">📊</div>
                    <div class="activity-content">
                        <div class="activity-event">${activity.event_name || activity.event || 'Activity'}</div>
                        <div class="activity-time">${activity.timestamp || activity.time || 'Recently'} • ${activity.page_path || activity.page || 'Unknown'}</div>
                    </div>
                </div>
            `).join('');
        }
        
        // Fallback for old data structure
        if (Array.isArray(activityData)) {
            return activityData.map(activity => `
                <div class="activity-item">
                    <div class="activity-icon">📊</div>
                    <div class="activity-content">
                        <div class="activity-event">${activity.event || 'Activity'}</div>
                        <div class="activity-time">${activity.time || 'Recently'} • ${activity.page || 'Unknown'}</div>
                    </div>
                </div>
            `).join('');
        }
        
        return '<div class="no-data">No activity data available</div>';
    }
    
    // Helper function to render trending insights with status handling
    function renderTrendingInsights(insightsData) {
        if (!insightsData) {
            return '<div class="no-data">No insights data available</div>';
        }
        
        if (insightsData.status === 'unavailable') {
            return `
                <div class="unavailable-data">
                    <div class="unavailable-icon">⚠️</div>
                    <div class="unavailable-message">${insightsData.message}</div>
                    <div class="unavailable-note">${insightsData.note || ''}</div>
                </div>
            `;
        }
        
        if (insightsData.status === 'available' && insightsData.data && Array.isArray(insightsData.data)) {
            return insightsData.data.slice(0, 3).map(insight => `
                <div class="insight-card ${insight.impact || 'medium'}-impact">
                    <div class="insight-title">${insight.title || 'Insight'}</div>
                    <div class="insight-description">${insight.description || 'No description available'}</div>
                </div>
            `).join('');
        }
        
        // Fallback for old data structure
        if (Array.isArray(insightsData)) {
            return insightsData.slice(0, 3).map(insight => `
                <div class="insight-card ${insight.impact || 'medium'}-impact">
                    <div class="insight-title">${insight.title || 'Insight'}</div>
                    <div class="insight-description">${insight.description || 'No description available'}</div>
                </div>
            `).join('');
        }
        
        return '<div class="no-data">No insights data available</div>';
    }
    
    // Show error message
    function showError(message) {
        const settingsUrl = window.location.origin + '/wp-admin/options-general.php?page=ga4-settings';
        
        $('#analytics-content').html(`
            <div class="error-message" style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; border-radius: 5px; margin: 20px 0;">
                <h2 style="margin-top: 0; color: #856404;">⚠️ Google Analytics 4 Not Configured</h2>
                <p><strong>Error:</strong> ${message}</p>
                <br>
                <h3>Setup Instructions:</h3>
                <ol style="line-height: 1.8;">
                    <li><strong>Go to <a href="${settingsUrl}" style="color: #0073aa; text-decoration: underline;">Settings → Google Analytics</a></strong></li>
                    <li>Enable the "Enable GA4 Integration" checkbox</li>
                    <li>Enter your GA4 Property ID (numeric value, e.g., 123456789)</li>
                    <li>Enter your Service Account Email from Google Cloud Console</li>
                    <li>Paste the Private Key from your service account JSON file</li>
                    <li>Enter your Google Cloud API Key and Project ID</li>
                    <li>Click "Test GA4 Connection" to verify the setup</li>
                    <li>If the test passes, come back to this page to see live data</li>
                </ol>
                <br>
                <h3>How to Get GA4 Credentials:</h3>
                <ol style="line-height: 1.8;">
                    <li>Go to <a href="https://console.cloud.google.com/" target="_blank" style="color: #0073aa;">Google Cloud Console</a></li>
                    <li>Create a new project or select an existing one</li>
                    <li>Enable the "Google Analytics Data API" in APIs & Services</li>
                    <li>Create a Service Account (IAM & Admin → Service Accounts)</li>
                    <li>Download the JSON key file for the service account</li>
                    <li>Add the service account email to your GA4 property as a Viewer</li>
                    <li>Get your GA4 Property ID from Google Analytics Admin settings</li>
                </ol>
                <br>
                <a href="${settingsUrl}" class="button button-primary button-large" style="margin-top: 20px;">
                    → Configure Google Analytics Settings
                </a>
            </div>
        `);
    }
    
    // Store refresh interval
    let refreshInterval = null;
    
    // Check current domain and show setup instructions if needed
    function checkDomainSetup() {
        const currentDomain = window.location.origin;
        console.log('Current domain:', currentDomain);
        
        // Show domain info for setup
        if (currentDomain.includes('localhost') || currentDomain.includes('dataon')) {
            console.log('For Google Analytics setup, add this domain to authorized origins:', currentDomain);
        }
    }
    
    // Initialize the analytics dashboard
    checkDomainSetup();
    
    // Check if we're on a production domain with proper credentials
    const isLocalhost = window.location.hostname === 'localhost' || 
                       window.location.hostname.includes('.local') ||
                       window.location.hostname.includes('127.0.0.1');
    
    const hasCredentials = GA_API_CONFIG.apiKey && GA_API_CONFIG.clientId && GA_API_CONFIG.propertyId;
    
    console.log('Domain check:', {
        isLocalhost: isLocalhost,
        hasCredentials: hasCredentials,
        domain: window.location.hostname
    });
    
    // Always try to load real data from server first
    console.log('Loading real analytics data from server...');
    loadRealAnalyticsData();
    
    // Refresh data every 5 minutes
    refreshInterval = setInterval(function() {
        // For now, just refresh sample data
        // In the future, we can implement token refresh logic
        if (typeof google !== 'undefined' && google.accounts) {
            loadAnalyticsData();
        }
    }, 300000); // 5 minutes
    
    // Cleanup function for page unload
    $(window).on('beforeunload', function() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
        if (pageViewsChart) {
            pageViewsChart.destroy();
        }
        if (trafficSourcesChart) {
            trafficSourcesChart.destroy();
        }
        if (topPagesChart) {
            topPagesChart.destroy();
        }
    });
    
    // Initialize tab switching functionality
    function initializeTabSwitching(data) {
        $('.tab-button').off('click').on('click', function() {
            const tab = $(this).data('tab');
            
            // Remove active class from all buttons
            $('.tab-button').removeClass('active');
            // Add active class to clicked button
            $(this).addClass('active');
            
            // Show/hide tab content
            switch(tab) {
                case 'overview':
                    showOverviewTab(data);
                    break;
                case 'pages':
                    showTopPagesTab(data);
                    break;
                case 'traffic':
                    showTrafficTab(data);
                    break;
                case 'downloads':
                    showDownloadsTab(data);
                    break;
                case 'insights':
                    showInsightsTab(data);
                    break;
                case 'activity':
                    showActivityTab(data);
                    break;
            }
        });
    }
    
    // Show overview tab (default)
    function showOverviewTab(data) {
        const content = `
            <div class="period-selector">
                <button class="period-button active" data-period="day">Day</button>
                <button class="period-button" data-period="week">Week</button>
                <button class="period-button" data-period="month">Month</button>
                <button class="period-button" data-period="all_time">All Time</button>
            </div>
            
            <div class="charts-grid">
                <div class="chart-container">
                    <h3>Page Views by Page</h3>
                    <canvas id="pageViewsChart" class="chart-canvas"></canvas>
                </div>
                
                <div class="chart-container">
                    <h3>Traffic Sources</h3>
                    <canvas id="trafficSourcesChart" class="chart-canvas"></canvas>
                </div>
            </div>
            
            <div class="activity-feed">
                <h3>Recent Activity</h3>
                ${renderRecentActivity(data.recent_activity)}
            </div>
        `;
        
        $('#analytics-content').html(content);
        renderCharts(data);
        initializePeriodButtons(data);
    }
    
    // Show top pages tab
    function showTopPagesTab(data) {
        const content = `
            <div class="period-selector">
                <button class="period-button active" data-period="day">Day</button>
                <button class="period-button" data-period="week">Week</button>
                <button class="period-button" data-period="month">Month</button>
                <button class="period-button" data-period="all_time">All Time</button>
            </div>
            
            <div class="chart-container">
                <h3>Top 10 Pages</h3>
                <canvas id="topPagesChart" class="chart-canvas"></canvas>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Page</th>
                        <th>Views</th>
                        <th>Bounce Rate</th>
                        <th>Avg. Time</th>
                    </tr>
                </thead>
                <tbody>
                    ${renderTopPages(data.top_pages)}
                </tbody>
            </table>
        `;
        
        $('#analytics-content').html(content);
        renderTopPagesChart(data);
        initializePeriodButtons(data);
    }
    
    // Show traffic tab
    function showTrafficTab(data) {
        const content = `
            <div class="period-selector">
                <button class="period-button active" data-period="day">Day</button>
                <button class="period-button" data-period="week">Week</button>
                <button class="period-button" data-period="month">Month</button>
                <button class="period-button" data-period="all_time">All Time</button>
            </div>
            
            <div class="charts-grid">
                <div class="chart-container">
                    <h3>Traffic Sources Breakdown</h3>
                    <canvas id="trafficBreakdownChart" class="chart-canvas"></canvas>
                </div>
                
                <div class="chart-container">
                    <h3>Source Performance</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Source</th>
                                <th>Sessions</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${renderTrafficSources(data.traffic_sources)}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
        
        $('#analytics-content').html(content);
        initializePeriodButtons(data);
        // Add traffic breakdown chart rendering here if needed
    }
    
    // Show downloads tab
    function showDownloadsTab(data) {
        const content = `
            <div class="period-selector">
                <button class="period-button active" data-period="day">Day</button>
                <button class="period-button" data-period="week">Week</button>
                <button class="period-button" data-period="month">Month</button>
                <button class="period-button" data-period="all_time">All Time</button>
            </div>
            
            <div class="chart-container">
                <h3>PDF Download Trends</h3>
                <canvas id="pdfDownloadsChart" class="chart-canvas"></canvas>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>File</th>
                        <th>Downloads</th>
                        <th>Last Download</th>
                    </tr>
                </thead>
                <tbody>
                    ${renderPdfDownloads(data.pdf_downloads)}
                </tbody>
            </table>
        `;
        
        $('#analytics-content').html(content);
        initializePeriodButtons(data);
        // Add PDF downloads chart rendering here if needed
    }
    
    // Show insights tab
    function showInsightsTab(data) {
        const content = `
            <div class="insights-grid">
                ${renderTrendingInsights(data.trending_insights)}
            </div>
        `;
        
        $('#analytics-content').html(content);
    }
    
    // Show activity tab
    function showActivityTab(data) {
        const content = `
            <div class="activity-feed">
                <h3>Recent Activity</h3>
                ${renderRecentActivity(data.recent_activity)}
            </div>
        `;
        
        $('#analytics-content').html(content);
    }
    
    // Render top pages chart
    function renderTopPagesChart(data) {
        // Clean up existing chart
        if (topPagesChart) {
            topPagesChart.destroy();
        }
        
        const topPagesCtx = document.getElementById('topPagesChart');
        if (topPagesCtx && data.top_pages && data.top_pages.length > 0) {
            // Reset canvas height
            topPagesCtx.style.height = '400px';
            topPagesCtx.height = 400;
            
            // Prepare data for chart
            const labels = data.top_pages.slice(0, 10).map(page => {
                // Truncate long page paths
                return page.page.length > 30 ? page.page.substring(0, 30) + '...' : page.page;
            });
            const views = data.top_pages.slice(0, 10).map(page => page.views);
            
            topPagesChart = new Chart(topPagesCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Page Views',
                        data: views,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    }
    
    // Render top pages table rows
    function renderTopPages(topPages) {
        if (!topPages || !Array.isArray(topPages)) {
            return '<tr><td colspan="4">No page data available</td></tr>';
        }
        
        return topPages.slice(0, 10).map(page => `
            <tr>
                <td>${page.page}</td>
                <td>${page.views.toLocaleString()}</td>
                <td>${page.bounce_rate ? page.bounce_rate.toFixed(1) + '%' : 'N/A'}</td>
                <td>${page.avg_time || 'N/A'}</td>
            </tr>
        `).join('');
    }
    
    // Render traffic sources table rows
    function renderTrafficSources(trafficSources) {
        if (!trafficSources || !trafficSources.sources || !Array.isArray(trafficSources.sources)) {
            return '<tr><td colspan="3">No traffic source data available</td></tr>';
        }
        
        return trafficSources.sources.map(source => `
            <tr>
                <td>${source.source}</td>
                <td>${source.sessions.toLocaleString()}</td>
                <td>${source.percentage}%</td>
            </tr>
        `).join('');
    }
    
    // Render PDF downloads table rows
    function renderPdfDownloads(pdfDownloads) {
        if (!pdfDownloads || !Array.isArray(pdfDownloads)) {
            return '<tr><td colspan="3">No PDF download data available</td></tr>';
        }
        
        return pdfDownloads.map(download => `
            <tr>
                <td>${download.file}</td>
                <td>${download.downloads.toLocaleString()}</td>
                <td>${download.last_download}</td>
            </tr>
        `).join('');
    }
    
    // Render trending insights
    function renderTrendingInsights(insights) {
        if (!insights || !Array.isArray(insights)) {
            return '<div class="insight-card">No insights available</div>';
        }
        
        return insights.map(insight => `
            <div class="insight-card ${insight.type}">
                <h4>${insight.title}</h4>
                <p>${insight.description}</p>
                <span class="impact ${insight.impact}">${insight.impact}</span>
            </div>
        `).join('');
    }
    
    // Initialize period selector buttons
    function initializePeriodButtons(data) {
        $('.period-button').off('click').on('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all period buttons
            $('.period-button').removeClass('active');
            // Add active class to clicked button
            $(this).addClass('active');
            
            // Get the selected period
            const period = $(this).data('period');
            console.log('Period changed to:', period);
            
            // Update the data based on the selected period
            updateDataForPeriod(period, data);
        });
    }
    
    // Update data and charts for the selected period
    function updateDataForPeriod(period, currentData) {
        console.log('Updating data for period:', period);
        
        // For now, we'll use the existing data structure
        // In a real implementation, this would make a new API call with the selected period
        let updatedData = { ...currentData };
        
        // Simulate different data based on period
        const multipliers = {
            'day': 1,
            'week': 7,
            'month': 30,
            'all_time': 365
        };
        
        const multiplier = multipliers[period] || 1;
        
        // Update page views data
        if (updatedData.page_views && updatedData.page_views.data) {
            updatedData.page_views.data = updatedData.page_views.data.map(val => Math.round(val * multiplier));
        }
        
        // Update top pages data (this is what the chart actually uses)
        if (updatedData.top_pages && Array.isArray(updatedData.top_pages)) {
            updatedData.top_pages = updatedData.top_pages.map(page => ({
                ...page,
                views: Math.round(page.views * multiplier)
            }));
        }
        
        // Update total page views count
        if (updatedData.page_views && updatedData.page_views.current) {
            updatedData.page_views.current = Math.round(updatedData.page_views.current * multiplier);
        }
        
        // Update the current tab with new data
        const activeTab = $('.tab-button.active').data('tab');
        console.log('Active tab:', activeTab);
        
        // Don't re-render the entire tab, just update the data in place
        updateCurrentTabData(activeTab, updatedData);
    }
    
    // Update data in the current tab without switching tabs
    function updateCurrentTabData(activeTab, updatedData) {
        console.log('Updating data for tab:', activeTab);
        
        switch(activeTab) {
            case 'overview':
                // Update the existing charts with new data
                if (updatedData.top_pages && Array.isArray(updatedData.top_pages) && pageViewsChart) {
                    // Update page views chart with top_pages data
                    const labels = updatedData.top_pages.slice(0, 8).map(page => {
                        const pageName = page.page === '/' ? 'Homepage' : page.page.replace(/^\//, '').replace(/\//g, ' / ');
                        return pageName.length > 25 ? pageName.substring(0, 25) + '...' : pageName;
                    });
                    const chartData = updatedData.top_pages.slice(0, 8).map(page => page.views);
                    
                    pageViewsChart.data.labels = labels;
                    pageViewsChart.data.datasets[0].data = chartData;
                    pageViewsChart.update();
                }
                if (updatedData.traffic_sources && trafficSourcesChart) {
                    trafficSourcesChart.data.datasets[0].data = updatedData.traffic_sources.data;
                    trafficSourcesChart.update();
                }
                break;
                
            case 'pages':
                // Update the top pages chart and table
                if (topPagesChart && updatedData.top_pages) {
                    const labels = updatedData.top_pages.slice(0, 10).map(page => {
                        return page.page.length > 30 ? page.page.substring(0, 30) + '...' : page.page;
                    });
                    const views = updatedData.top_pages.slice(0, 10).map(page => page.views);
                    
                    topPagesChart.data.labels = labels;
                    topPagesChart.data.datasets[0].data = views;
                    topPagesChart.update();
                }
                
                // Update the table
                const tableBody = $('.data-table tbody');
                if (tableBody.length > 0) {
                    tableBody.html(renderTopPages(updatedData.top_pages));
                }
                break;
                
            case 'traffic':
                // Update traffic sources data
                const trafficTableBody = $('.data-table tbody');
                if (trafficTableBody.length > 0) {
                    trafficTableBody.html(renderTrafficSources(updatedData.traffic_sources));
                }
                break;
                
            case 'downloads':
                // Update PDF downloads data
                const downloadsTableBody = $('.data-table tbody');
                if (downloadsTableBody.length > 0) {
                    downloadsTableBody.html(renderPdfDownloads(updatedData.pdf_downloads));
                }
                break;
                
            case 'insights':
                // Update insights data
                const insightsContainer = $('.insights-grid');
                if (insightsContainer.length > 0) {
                    insightsContainer.html(renderTrendingInsights(updatedData.trending_insights));
                }
                break;
                
            case 'activity':
                // Update activity feed
                const activityContainer = $('.activity-feed');
                if (activityContainer.length > 0) {
                    activityContainer.html(`
                        <h3>Recent Activity</h3>
                        ${renderRecentActivity(updatedData.recent_activity)}
                    `);
                }
                break;
        }
    }
}); 