jQuery(document).ready(function($) {
    if (!$('.analytics-dashboard').length) {
        return;
    }

    let currentPeriod = 'week';
    let currentTab = 'overview';
    let customStartDate = '';
    let customEndDate = '';
    let chartInstances = {};

    setDefaultCustomDates();
    loadAnalyticsData();

    $('.tab-button').on('click', function() {
        $('.tab-button').removeClass('active');
        $(this).addClass('active');
        currentTab = $(this).data('tab');
        loadAnalyticsData();
    });

    $(document).on('click', '.period-button', function() {
        const period = $(this).data('period');
        $('.period-button').removeClass('active');
        $(this).addClass('active');
        currentPeriod = period;

        if (period === 'custom') {
            setDefaultCustomDates();
            $('.period-filters__custom').show();
            syncCustomDateInputs();
            loadAnalyticsData();
            return;
        }

        $('.period-filters__custom').hide();
        loadAnalyticsData();
    });

    $(document).on('click', '.period-apply-custom', function() {
        customStartDate = $('#analytics-start-date').val();
        customEndDate = $('#analytics-end-date').val();

        if (!customStartDate || !customEndDate) {
            window.alert('Please select both a start and end date.');
            return;
        }

        if (customStartDate > customEndDate) {
            window.alert('Start date must be before end date.');
            return;
        }

        currentPeriod = 'custom';
        $('.period-button').removeClass('active');
        $('.period-button[data-period="custom"]').addClass('active');
        loadAnalyticsData();
    });

    function setDefaultCustomDates() {
        if (customStartDate && customEndDate) {
            return;
        }

        const end = new Date();
        const start = new Date();
        start.setDate(end.getDate() - 6);

        customEndDate = formatInputDate(end);
        customStartDate = formatInputDate(start);
    }

    function formatInputDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }

    function syncCustomDateInputs() {
        if ($('#analytics-start-date').length) {
            $('#analytics-start-date').val(customStartDate);
            $('#analytics-end-date').val(customEndDate);
        }
    }

    function loadAnalyticsData() {
        $('#analytics-content').html('<div class="loading">Loading analytics data...</div>');

        const requestData = {
            action: 'get_analytics_data',
            nonce: analytics_ajax.nonce,
            period: currentPeriod
        };

        if (currentPeriod === 'custom') {
            if (!customStartDate || !customEndDate) {
                setDefaultCustomDates();
            }
            requestData.start_date = customStartDate;
            requestData.end_date = customEndDate;
        }

        $.ajax({
            url: analytics_ajax.ajax_url,
            type: 'POST',
            data: requestData,
            success: function(response) {
                if (response.success) {
                    renderAnalyticsDashboard(response.data);
                } else {
                    const message = typeof response.data === 'string'
                        ? response.data
                        : 'Failed to load analytics data';
                    showError(message);
                    updateConnectionStatus('error');
                }
            },
            error: function() {
                showError('Failed to connect to analytics service');
                updateConnectionStatus('error');
            }
        });
    }

    function renderAnalyticsDashboard(data) {
        destroyCharts();

        if (data.date_range_start) {
            customStartDate = data.date_range_start;
        }
        if (data.date_range_end) {
            customEndDate = data.date_range_end;
        }

        updateMetrics(data);
        updateConnectionStatus(data.connection_status || 'connected');

        const content = renderTabContent(data, currentTab);
        const warnings = renderDataWarnings(data.data_warnings || []);
        $('#analytics-content').html(warnings + content);
        syncPeriodButtons();
        renderTabCharts(data, currentTab);
    }

    function renderDataWarnings(warnings) {
        if (!warnings.length) {
            return '';
        }

        return warnings.map(function(warning) {
            return '<div class="analytics-notice">' + warning + '</div>';
        }).join('');
    }

    function syncPeriodButtons() {
        $('.period-button').removeClass('active');
        $('.period-button[data-period="' + currentPeriod + '"]').addClass('active');

        if (currentPeriod === 'custom') {
            $('.period-filters__custom').show();
            syncCustomDateInputs();
        } else {
            $('.period-filters__custom').hide();
        }
    }

    function updateMetrics(data) {
        const pageViews = data.page_views || {};
        const change = pageViews.change_percent || 0;
        const changeClass = change >= 0 ? 'positive' : 'negative';
        const changePrefix = change >= 0 ? '+' : '';

        $('#page-views-value').text((pageViews.current || 0).toLocaleString());
        $('#real-time-users').text(data.real_time_users || 0);
        $('#unique-users').text((data.new_users || 0).toLocaleString());
        $('#bounce-rate').text(data.bounce_rate != null ? data.bounce_rate + '%' : '—');

        $('#page-views-change')
            .removeClass('positive negative')
            .addClass(changeClass)
            .text(changePrefix + change + '%');

        $('#unique-users-change').hide();
        $('#bounce-rate-change').hide();
    }

    function updateConnectionStatus(status) {
        const statusElement = $('#connection-status');
        statusElement.removeClass('connected sample error');

        switch (status) {
            case 'connected':
                statusElement.addClass('connected').html('<span>✅ Live GA4 Data</span>');
                break;
            case 'error':
                statusElement.addClass('error').html('<span>❌ Connection Error</span>');
                break;
            default:
                statusElement.addClass('sample').html('<span>Loading...</span>');
        }
    }

    function getInsights(data) {
        if (Array.isArray(data.trending_insights)) {
            return data.trending_insights;
        }
        if (data.trending_insights && Array.isArray(data.trending_insights.data)) {
            return data.trending_insights.data;
        }
        return [];
    }

    function getDownloads(data) {
        if (Array.isArray(data.pdf_downloads)) {
            return data.pdf_downloads;
        }
        if (data.pdf_downloads && Array.isArray(data.pdf_downloads.data)) {
            return data.pdf_downloads.data.map(function(item) {
                return {
                    file: item.event_name || item.file || 'Download',
                    downloads: item.downloads || 0,
                    last_download: item.last_download || '—'
                };
            });
        }
        return [];
    }

    function getActivity(data) {
        if (Array.isArray(data.recent_activity)) {
            return data.recent_activity;
        }
        if (data.recent_activity && Array.isArray(data.recent_activity.data)) {
            return data.recent_activity.data.map(function(item) {
                return {
                    event: item.event_name || item.event || 'Activity',
                    page: item.page_path || item.page || 'Unknown',
                    time: item.timestamp || item.time || 'Recently'
                };
            });
        }
        return [];
    }

    function formatDuration(seconds) {
        if (!seconds && seconds !== 0) {
            return '—';
        }
        const total = Math.round(Number(seconds));
        const minutes = Math.floor(total / 60);
        const secs = total % 60;
        return minutes + 'm ' + secs + 's';
    }

    function periodButtonsHtml() {
        const periods = [
            { id: 'day', label: 'Day' },
            { id: 'week', label: 'Week' },
            { id: 'month', label: 'Month' },
            { id: 'all_time', label: 'All Time' },
            { id: 'custom', label: 'Custom' }
        ];

        return periods.map(function(period) {
            const active = period.id === currentPeriod ? ' active' : '';
            return '<button type="button" class="period-button' + active + '" data-period="' + period.id + '">' + period.label + '</button>';
        }).join('');
    }

    function periodFiltersHtml(data) {
        const rangeDisplay = data && data.date_range_display ? data.date_range_display : '';
        const customVisible = currentPeriod === 'custom' ? '' : ' style="display:none"';

        return `
            <div class="period-filters">
                <div class="period-filters__buttons">${periodButtonsHtml()}</div>
                ${rangeDisplay ? '<div class="period-filters__range">' + rangeDisplay + '</div>' : ''}
                <div class="period-filters__custom"${customVisible}>
                    <label class="period-date-field">
                        <span>From</span>
                        <input type="date" id="analytics-start-date" value="${customStartDate}">
                    </label>
                    <label class="period-date-field">
                        <span>To</span>
                        <input type="date" id="analytics-end-date" value="${customEndDate}">
                    </label>
                    <button type="button" class="period-apply-custom button button-secondary">Apply</button>
                </div>
            </div>
        `;
    }

    function renderTabContent(data, tab) {
        switch (tab) {
            case 'pages':
                return renderPagesTab(data);
            case 'traffic':
                return renderTrafficTab(data);
            case 'downloads':
                return renderDownloadsTab(data);
            case 'insights':
                return renderInsightsTab(data);
            case 'activity':
                return renderActivityTab(data);
            default:
                return renderOverviewTab(data);
        }
    }

    function renderOverviewTab(data) {
        const insights = getInsights(data).slice(0, 3);

        return `
            <div class="tab-content active">
                ${periodFiltersHtml(data)}
                <div class="charts-grid">
                    <div class="chart-container">
                        <h3>Page Views Trend</h3>
                        <canvas id="pageViewsChart" class="chart-canvas"></canvas>
                    </div>
                    <div class="chart-container">
                        <h3>Traffic Sources</h3>
                        <canvas id="trafficSourcesChart" class="chart-canvas"></canvas>
                    </div>
                </div>
                <div class="insights-grid">
                    ${insights.length ? insights.map(function(insight) {
                        return `
                            <div class="insight-card ${insight.impact || 'medium'}-impact">
                                <div class="insight-title">${insight.title}</div>
                                <div class="insight-description">${insight.description}</div>
                            </div>
                        `;
                    }).join('') : '<p>No insights available for this period.</p>'}
                </div>
            </div>
        `;
    }

    function renderPagesTab(data) {
        const pages = data.top_pages || [];

        return `
            <div class="tab-content active">
                ${periodFiltersHtml(data)}
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
                        ${pages.length ? pages.map(function(page) {
                            return `
                                <tr>
                                    <td>${page.page}</td>
                                    <td>${Number(page.views).toLocaleString()}</td>
                                    <td>${page.bounce_rate != null ? page.bounce_rate + '%' : '—'}</td>
                                    <td>${formatDuration(page.avg_time)}</td>
                                </tr>
                            `;
                        }).join('') : '<tr><td colspan="4">No page data available</td></tr>'}
                    </tbody>
                </table>
            </div>
        `;
    }

    function renderTrafficTab(data) {
        const sources = (data.traffic_sources && data.traffic_sources.sources) ? data.traffic_sources.sources : [];

        return `
            <div class="tab-content active">
                ${periodFiltersHtml(data)}
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
                                ${sources.length ? sources.map(function(source) {
                                    return `
                                        <tr>
                                            <td>${source.source}</td>
                                            <td>${Number(source.sessions).toLocaleString()}</td>
                                            <td>${source.percentage}%</td>
                                        </tr>
                                    `;
                                }).join('') : '<tr><td colspan="3">No traffic source data available</td></tr>'}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
    }

    function renderDownloadsTab(data) {
        const downloads = getDownloads(data);
        const unavailable = data.pdf_downloads && data.pdf_downloads.status === 'unavailable';

        return `
            <div class="tab-content active">
                ${periodFiltersHtml(data)}
                ${unavailable ? '<div class="error-message">' + data.pdf_downloads.message + '. ' + (data.pdf_downloads.note || '') + '</div>' : ''}
                <div class="chart-container">
                    <h3>PDF Download Trends</h3>
                    <canvas id="pdfDownloadsChart" class="chart-canvas"></canvas>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Event / File</th>
                            <th>Downloads</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${downloads.length ? downloads.map(function(pdf) {
                            return `
                                <tr>
                                    <td>${pdf.file}</td>
                                    <td>${Number(pdf.downloads).toLocaleString()}</td>
                                </tr>
                            `;
                        }).join('') : '<tr><td colspan="2">No download events found for this period</td></tr>'}
                    </tbody>
                </table>
            </div>
        `;
    }

    function renderInsightsTab(data) {
        const insights = getInsights(data);

        return `
            <div class="tab-content active">
                ${periodFiltersHtml(data)}
                <h3>Trending Insights</h3>
                <div class="insights-grid">
                    ${insights.length ? insights.map(function(insight) {
                        return `
                            <div class="insight-card ${insight.impact || 'medium'}-impact">
                                <div class="insight-title">${insight.title}</div>
                                <div class="insight-description">${insight.description}</div>
                            </div>
                        `;
                    }).join('') : '<p>No insights available for this period.</p>'}
                </div>
            </div>
        `;
    }

    function renderActivityTab(data) {
        const activity = getActivity(data);
        const unavailable = data.recent_activity && data.recent_activity.status === 'unavailable';

        return `
            <div class="tab-content active">
                ${periodFiltersHtml(data)}
                <h3>Recent Activity Feed</h3>
                ${unavailable ? '<div class="error-message">' + data.recent_activity.message + '</div>' : ''}
                <div class="activity-feed">
                    ${activity.length ? activity.map(function(item) {
                        return `
                            <div class="activity-item">
                                <div class="activity-icon">📊</div>
                                <div class="activity-content">
                                    <div class="activity-event">${item.event}</div>
                                    <div class="activity-time">${item.time} • ${item.page}</div>
                                </div>
                            </div>
                        `;
                    }).join('') : '<p>No recent activity available.</p>'}
                </div>
            </div>
        `;
    }

    function chartColors() {
        return [
            'rgba(102, 126, 234, 0.8)',
            'rgba(118, 75, 162, 0.8)',
            'rgba(255, 193, 7, 0.8)',
            'rgba(40, 167, 69, 0.8)',
            'rgba(220, 53, 69, 0.8)',
            'rgba(23, 162, 184, 0.8)',
            'rgba(108, 117, 125, 0.8)'
        ];
    }

    function renderTabCharts(data, tab) {
        if (tab === 'overview') {
            renderLineChart('pageViewsChart', data.daily_trend || data.page_views, 'Page Views');
            renderDoughnutChart('trafficSourcesChart', data.traffic_sources);
        } else if (tab === 'pages') {
            renderBarChart('topPagesChart', {
                labels: (data.top_pages || []).map(function(page) { return page.page; }),
                data: (data.top_pages || []).map(function(page) { return page.views; })
            }, 'Views');
        } else if (tab === 'traffic') {
            renderDoughnutChart('trafficBreakdownChart', data.traffic_sources);
        } else if (tab === 'downloads') {
            const downloads = getDownloads(data);
            renderBarChart('pdfDownloadsChart', {
                labels: downloads.map(function(item) { return item.file; }),
                data: downloads.map(function(item) { return item.downloads; })
            }, 'Downloads');
        }
    }

    function renderLineChart(canvasId, source, label) {
        const ctx = document.getElementById(canvasId);
        if (!ctx || typeof Chart === 'undefined') {
            return;
        }

        const labels = source.labels || [];
        const values = source.data || [];

        chartInstances[canvasId] = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: values,
                    borderColor: 'rgba(102, 126, 234, 1)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    function renderBarChart(canvasId, source, label) {
        const ctx = document.getElementById(canvasId);
        if (!ctx || typeof Chart === 'undefined' || !source.labels.length) {
            return;
        }

        chartInstances[canvasId] = new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: source.labels,
                datasets: [{
                    label: label,
                    data: source.data,
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } },
                plugins: { legend: { display: false } }
            }
        });
    }

    function renderDoughnutChart(canvasId, trafficSources) {
        const ctx = document.getElementById(canvasId);
        if (!ctx || typeof Chart === 'undefined' || !trafficSources || !trafficSources.labels) {
            return;
        }

        chartInstances[canvasId] = new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: trafficSources.labels,
                datasets: [{
                    data: trafficSources.data,
                    backgroundColor: chartColors()
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    function destroyCharts() {
        Object.keys(chartInstances).forEach(function(key) {
            if (chartInstances[key]) {
                chartInstances[key].destroy();
            }
        });
        chartInstances = {};
    }

    function showError(message) {
        const settingsLink = analytics_ajax.settings_url
            ? '<p><a href="' + analytics_ajax.settings_url + '">Open Google Analytics settings</a></p>'
            : '';

        $('#analytics-content').html(`
            <div class="error-message">
                <strong>Error:</strong> ${message}
                ${settingsLink}
            </div>
        `);
    }

    setInterval(function() {
        if ($('.analytics-dashboard').length) {
            loadAnalyticsData();
        }
    }, 300000);
});
