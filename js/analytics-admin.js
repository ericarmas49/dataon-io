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
            console.log('Google Analytics credentials not configured. Using sample data.');
            loadSampleData();
            return;
        }
        
        // Check if Google Identity Services is loaded
        if (typeof google === 'undefined' || !google.accounts) {
            console.error('Google Identity Services not loaded');
            showError('Google Identity Services failed to load. Please refresh the page.');
            loadSampleData();
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
                        loadSampleData();
                    }
                },
                error_callback: function(error) {
                    console.error('OAuth error:', error);
                    showError(`OAuth Error: ${error.error || 'Unknown error'}`);
                    loadSampleData();
                }
            }).requestAccessToken();
            
        } catch (error) {
            console.error('Google Identity Services initialization failed:', error);
            showError(`Google Identity Services Error: ${error.message}`);
            loadSampleData();
        }
    }
    
    // Load analytics data from Google Analytics
    function loadAnalyticsData() {
        if (!GA_API_CONFIG.viewId) {
            loadSampleData();
            return;
        }
        
        const startDate = new Date();
        startDate.setDate(startDate.getDate() - 30); // Last 30 days
        
        const analytics = gapi.client.analytics;
        
        // Get page views
        analytics.data.ga.get({
            'ids': 'ga:' + GA_API_CONFIG.viewId,
            'start-date': startDate.toISOString().split('T')[0],
            'end-date': 'today',
            'metrics': 'ga:pageviews',
            'dimensions': 'ga:pagePath',
            'sort': '-ga:pageviews',
            'max-results': 10
        }).then(function(response) {
            const pageViewsData = processPageViewsData(response.result);
            
            // Get traffic sources
            analytics.data.ga.get({
                'ids': 'ga:' + GA_API_CONFIG.viewId,
                'start-date': startDate.toISOString().split('T')[0],
                'end-date': 'today',
                'metrics': 'ga:sessions',
                'dimensions': 'ga:source',
                'sort': '-ga:sessions',
                'max-results': 5
            }).then(function(response) {
                const trafficSourcesData = processTrafficSourcesData(response.result);
                
                // Get recent activity (real-time data)
                analytics.data.realtime.get({
                    'ids': 'ga:' + GA_API_CONFIG.viewId,
                    'metrics': 'rt:activeUsers'
                }).then(function(response) {
                    const recentActivityData = processRecentActivityData(response.result);
                    
                    const analyticsData = {
                        page_views: pageViewsData,
                        traffic_sources: trafficSourcesData,
                        recent_activity: recentActivityData,
                        top_pages: pageViewsData.top_pages || []
                    };
                    
                    renderAnalyticsDashboard(analyticsData);
                });
            });
        }).catch(function(error) {
            console.error('Failed to load Google Analytics data:', error);
            loadSampleData();
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
                bounce_rate: Math.floor(Math.random() * 60) + 20 // Mock bounce rate
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
    
    // Load sample data for demonstration
    function loadSampleData() {
        $.ajax({
            url: analytics_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_analytics_data',
                nonce: analytics_ajax.nonce
            },
            success: function(response) {
                console.log('AJAX Response:', response);
                
                if (response.success) {
                    renderAnalyticsDashboard(response.data);
                } else {
                    console.error('AJAX failed:', response);
                    showError('Failed to load analytics data: ' + (response.data || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {xhr, status, error});
                showError('Failed to connect to analytics service. Status: ' + status + ', Error: ' + error);
            }
        });
    }
    
    // Render the analytics dashboard
    function renderAnalyticsDashboard(data) {
        console.log('Received data:', data);
        
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
                        <div class="metric-value">${data.page_views.data.reduce((a, b) => a + b, 0).toLocaleString()}</div>
                        <div class="metric-label">Total Page Views</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value">${data.top_pages ? data.top_pages.length : 0}</div>
                        <div class="metric-label">Active Pages</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value">${Math.round((data.traffic_sources && data.traffic_sources.data && data.traffic_sources.data[0]) || 0)}%</div>
                        <div class="metric-label">Organic Traffic</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value">${data.recent_activity ? data.recent_activity.length : 0}</div>
                        <div class="metric-label">Recent Events</div>
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
                    ${(data.recent_activity || []).map(activity => `
                        <div class="activity-item">
                            <div class="activity-icon">📊</div>
                            <div class="activity-content">
                                <div class="activity-event">${activity.event}</div>
                                <div class="activity-time">${activity.time} • ${activity.page}</div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
            
            $('#analytics-content').html(content);
            
            // Initialize charts
            renderCharts(data);
        } catch (error) {
            console.error('Error rendering dashboard:', error);
            showError('Error rendering dashboard: ' + error.message);
        }
    }
    
    // Store chart instances for cleanup
    let pageViewsChart = null;
    let trafficSourcesChart = null;
    
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
            pageViewsCtx.style.height = '300px';
            pageViewsCtx.height = 300;
            
            pageViewsChart = new Chart(pageViewsCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: data.page_views.labels,
                    datasets: [{
                        label: 'Page Views',
                        data: data.page_views.data,
                        backgroundColor: 'rgba(102, 126, 234, 0.8)',
                        borderColor: 'rgba(102, 126, 234, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
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
        
        // Traffic Sources Chart
        const trafficSourcesCtx = document.getElementById('trafficSourcesChart');
        if (trafficSourcesCtx) {
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
    
    // Show error message
    function showError(message) {
        $('#analytics-content').html(`
            <div class="error-message">
                <strong>Error:</strong> ${message}
                <br><br>
                <p>To connect to Google Analytics:</p>
                <ol>
                    <li>Get your Google Analytics API credentials</li>
                    <li>Update the GA_API_CONFIG in analytics-admin.js</li>
                    <li>Ensure your Google Analytics property is properly configured</li>
                </ol>
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
    
    // For local development, use sample data
    const isLocalhost = window.location.hostname === 'localhost' || 
                       window.location.hostname.includes('local') ||
                       window.location.hostname.includes('dataon');
    
    if (isLocalhost) {
        console.log('Local development detected. Using sample data.');
        loadSampleData();
    } else if (typeof google !== 'undefined' && google.accounts) {
        initGoogleAnalytics();
    } else {
        // Fallback to sample data if Google Identity Services is not available
        loadSampleData();
    }
    
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
    });
}); 