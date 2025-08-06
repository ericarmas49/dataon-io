/**
 * Google Analytics Configuration
 * Update these values with your Google Analytics API credentials
 */

// Google Analytics API Configuration
window.GA_CONFIG = {
    // Your Google Analytics API Key
    apiKey: 'AIzaSyDpUmCNxaAmdQ4pi6FBJpibPqynVAa_I6g',
    
    // Your Google Analytics Client ID (from Google Cloud Console)
    clientId: '80073531163-sb62ld6sqfu7uvum4fj32cfrfkjo2hd3.apps.googleusercontent.com',
    
    // Your Google Analytics View ID (found in GA Admin > View Settings)
    viewId: '444630398',
    
    // API Scopes
    scopes: ['https://www.googleapis.com/auth/analytics.readonly'],
    
    // Analytics property ID (GA4: G-XXXXXXXXXX, Universal Analytics: UA-XXXXXXXX-X)
    propertyId: 'G-444630398',
    
    // Measurement Protocol API Secret (for GA4)
    measurementProtocolSecret: '',
    
    // Custom dimensions (optional)
    customDimensions: {
        // Add any custom dimensions you want to track
        // 'dimension1': 'user_type',
        // 'dimension2': 'content_category'
    },
    
    // Custom metrics (optional)
    customMetrics: {
        // Add any custom metrics you want to track
        // 'metric1': 'scroll_depth',
        // 'metric2': 'time_on_page'
    }
};

// Instructions for setup:
/*
1. Go to Google Cloud Console (https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the Google Analytics API
4. Create credentials:
   - Go to APIs & Services > Credentials
   - Click "Create Credentials" > "API Key"
   - Copy the API key to apiKey above
   - Click "Create Credentials" > "OAuth 2.0 Client IDs"
   - Set application type to "Web application"
   - Add your domain to authorized origins
   - Copy the Client ID to clientId above
5. Get your View ID:
   - Go to Google Analytics
   - Admin > View Settings
   - Copy the View ID to viewId above
6. For GA4 properties, also get your Measurement Protocol Secret:
   - Go to GA4 Admin > Data Streams
   - Select your web stream
   - Measurement Protocol API secrets
   - Create a new secret and copy to measurementProtocolSecret above

IMPORTANT: Add this domain to authorized origins in Google Cloud Console:
' + window.location.origin + '

NOTE: For local development, the dashboard will automatically use sample data.
For production, add your actual domain (like https://yourdomain.com) to the
authorized origins in Google Cloud Console.
*/ 