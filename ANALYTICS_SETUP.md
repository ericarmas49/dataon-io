# Google Analytics Dashboard Setup

This document provides step-by-step instructions to set up the Google Analytics dashboard in your WordPress admin.

## Overview

The analytics dashboard provides:
- Real-time page view analytics
- Traffic source breakdown
- Recent activity feed
- Interactive charts using Chart.js
- Google Analytics API integration

## Setup Instructions

### 1. Google Cloud Console Setup

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the Google Analytics API:
   - Go to "APIs & Services" > "Library"
   - Search for "Google Analytics API"
   - Click on it and press "Enable"

### 2. Create API Credentials

#### API Key
1. Go to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "API Key"
3. Copy the generated API key
4. (Optional) Restrict the API key to Google Analytics API only

#### OAuth 2.0 Client ID
1. In the same Credentials page, click "Create Credentials" > "OAuth 2.0 Client IDs"
2. Set application type to "Web application"
3. Add your domain to "Authorized JavaScript origins":
   - `http://localhost` (for local development)
   - `https://yourdomain.com` (for production)
4. Add authorized redirect URIs if needed
5. Copy the generated Client ID

### 3. Get Google Analytics View ID

1. Go to [Google Analytics](https://analytics.google.com/)
2. Select your property
3. Go to Admin > View Settings
4. Copy the View ID (format: 123456789)

### 4. Configure the Dashboard

1. Edit the file: `wp-content/themes/blankslate-dataon/js/analytics-config.js`
2. Update the configuration with your credentials:

```javascript
window.GA_CONFIG = {
    apiKey: 'YOUR_API_KEY_HERE',
    clientId: 'YOUR_CLIENT_ID_HERE',
    viewId: 'YOUR_VIEW_ID_HERE',
    scopes: ['https://www.googleapis.com/auth/analytics.readonly'],
    propertyId: 'G-XXXXXXXXXX', // Your GA4 property ID
    measurementProtocolSecret: '', // Optional for GA4
    // ... rest of config
};
```

### 5. Test the Dashboard

1. Go to your WordPress admin
2. Navigate to "Analytics" in the admin menu
3. The dashboard should load with sample data initially
4. Once configured with real credentials, it will show live Google Analytics data

## Features

### Metrics Cards
- **Total Page Views**: Sum of all page views in the selected period
- **Active Pages**: Number of pages with traffic
- **Organic Traffic**: Percentage of traffic from organic search
- **Recent Events**: Number of recent user interactions

### Charts
- **Page Views Chart**: Bar chart showing page views by page
- **Traffic Sources Chart**: Doughnut chart showing traffic breakdown by source

### Activity Feed
- Real-time user activity
- Page views, form submissions, downloads, etc.
- Timestamp and page information

## Troubleshooting

### Common Issues

1. **"Failed to connect to analytics service"**
   - Check that your API credentials are correct
   - Ensure Google Analytics API is enabled in Cloud Console
   - Verify your domain is in authorized origins

2. **"Google Analytics API initialization failed"**
   - Check browser console for specific error messages
   - Ensure all required scripts are loading
   - Verify OAuth client ID is correct

3. **Charts not displaying**
   - Check that Chart.js is loading properly
   - Verify data is being returned from the API
   - Check browser console for JavaScript errors

### Debug Mode

To enable debug mode, add this to your browser console:
```javascript
localStorage.setItem('analytics_debug', 'true');
```

### Fallback Mode

If Google Analytics API fails to load, the dashboard will automatically fall back to sample data for demonstration purposes.

## Security Notes

- Keep your API credentials secure
- Restrict API keys to specific domains
- Use environment variables for production deployments
- Regularly rotate API keys

## Customization

### Adding Custom Metrics

To track custom metrics, update the `customMetrics` object in `analytics-config.js`:

```javascript
customMetrics: {
    'metric1': 'scroll_depth',
    'metric2': 'time_on_page',
    'metric3': 'engagement_score'
}
```

### Adding Custom Dimensions

To track custom dimensions, update the `customDimensions` object:

```javascript
customDimensions: {
    'dimension1': 'user_type',
    'dimension2': 'content_category',
    'dimension3': 'page_type'
}
```

### Styling Customization

The dashboard uses CSS classes that can be customized:
- `.analytics-dashboard`: Main container
- `.metric-card`: Individual metric cards
- `.chart-container`: Chart containers
- `.activity-feed`: Activity feed section

## Support

For issues or questions:
1. Check the browser console for error messages
2. Verify all setup steps were completed
3. Test with sample data first
4. Ensure proper WordPress permissions

## Changelog

- **v1.0**: Initial release with Google Analytics integration
- Added Chart.js for data visualization
- Implemented AJAX data loading
- Added fallback to sample data
- Created comprehensive setup documentation 