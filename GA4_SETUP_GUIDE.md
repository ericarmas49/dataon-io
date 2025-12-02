# Google Analytics 4 (GA4) Setup Guide

This guide will help you set up live Google Analytics data in your WordPress analytics dashboard.

## Prerequisites

- Google Analytics 4 property set up
- Google Cloud Console access
- WordPress admin access

## Step 1: Google Cloud Console Setup

### 1.1 Create or Select a Project
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Note your Project ID (you'll need this later)

### 1.2 Enable Required APIs
1. Go to "APIs & Services" > "Library"
2. Search for and enable these APIs:
   - **Google Analytics Data API**
   - **Google Analytics Reporting API** (for legacy support)

### 1.3 Create Service Account
1. Go to "IAM & Admin" > "Service Accounts"
2. Click "Create Service Account"
3. Fill in the details:
   - Name: `wordpress-analytics`
   - Description: `Service account for WordPress GA4 integration`
4. Click "Create and Continue"
5. Skip the "Grant access" step for now
6. Click "Done"

### 1.4 Generate Service Account Key
1. Find your newly created service account
2. Click on the email address
3. Go to "Keys" tab
4. Click "Add Key" > "Create new key"
5. Choose "JSON" format
6. Download the JSON file (keep it secure!)

### 1.5 Create API Key
1. Go to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "API Key"
3. Copy the generated API key
4. (Optional) Restrict the API key to Google Analytics APIs only

## Step 2: Google Analytics Setup

### 2.1 Get Your GA4 Property ID
1. Go to [Google Analytics](https://analytics.google.com/)
2. Select your GA4 property
3. Go to Admin > Property Settings
4. Copy the Property ID (numeric, e.g., 123456789)

### 2.2 Get Your Measurement ID
1. In the same property, go to Admin > Data Streams
2. Click on your web stream
3. Copy the Measurement ID (format: G-XXXXXXXXXX)

### 2.3 Add Service Account to GA4
1. In Google Analytics, go to Admin > Property Access Management
2. Click the "+" button
3. Add the service account email from your JSON file
4. Give it "Viewer" permissions
5. Click "Add"

## Step 3: WordPress Configuration

### 3.1 Access Settings
1. Go to your WordPress admin
2. Navigate to **Settings > Google Analytics**

### 3.2 Configure Settings
Fill in the following fields:

- **Enable GA4 Integration**: ✅ Check this box
- **API Key**: Paste your Google Cloud API key
- **Property ID**: Paste your GA4 Property ID (numeric)
- **Measurement ID**: Paste your GA4 Measurement ID (G-XXXXXXXXXX)
- **Service Account Email**: From your JSON file
- **Private Key**: Copy the entire private key from your JSON file (including the BEGIN/END lines)
- **Project ID**: Your Google Cloud Project ID

### 3.3 Test Connection
1. Click "Save Changes"
2. Click "Test GA4 Connection"
3. You should see a green success message

## Step 4: Verify Setup

### 4.1 Check Analytics Dashboard
1. Go to your Analytics dashboard in WordPress admin
2. You should see:
   - ✅ "Connected to Google Analytics - Live data" message
   - Real data instead of sample data
   - Live charts and metrics

### 4.2 Troubleshooting

If you see errors:

1. **"Failed to get access token"**
   - Check your service account email and private key
   - Ensure the service account has access to your GA4 property

2. **"API request failed"**
   - Verify your API key is correct
   - Check that the Google Analytics Data API is enabled
   - Ensure your Property ID is correct

3. **"No data returned"**
   - Check that your GA4 property has data
   - Verify the Property ID is correct
   - Ensure the service account has proper permissions

## Security Best Practices

1. **Keep credentials secure**
   - Never commit the JSON key file to version control
   - Store private keys securely
   - Regularly rotate API keys

2. **Restrict API access**
   - Restrict your API key to specific APIs
   - Use IP restrictions if possible
   - Monitor API usage

3. **Service account permissions**
   - Only give the minimum required permissions
   - Regularly audit service account access

## Data Refresh

The dashboard automatically refreshes every 5 minutes. You can also:
- Refresh the page manually
- Use the refresh button in the dashboard
- Check the connection status indicator

## Customization

### Adding Custom Metrics
To track additional metrics, modify the `GA4_Analytics` class in `/includes/class-ga4-analytics.php`.

### Custom Dimensions
Add custom dimensions by updating the API requests in the analytics class.

### Styling
Customize the dashboard appearance by modifying the CSS in your theme.

## Support

If you encounter issues:

1. Check the WordPress error logs
2. Verify all credentials are correct
3. Test the connection using the built-in test function
4. Ensure your GA4 property has recent data

## Changelog

- **v1.0**: Initial GA4 integration
- Added service account authentication
- Implemented GA4 Data API
- Added connection testing
- Created comprehensive setup guide
