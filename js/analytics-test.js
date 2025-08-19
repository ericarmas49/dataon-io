/**
 * Google Analytics Connection Test Script
 * Use this to troubleshoot connection issues
 */

jQuery(document).ready(function($) {
    
    // Test configuration
    function testConfiguration() {
        console.log('=== GOOGLE ANALYTICS CONFIGURATION TEST ===');
        
        if (typeof window.GA_CONFIG === 'undefined') {
            console.error('❌ GA_CONFIG not found - check if analytics-config.js is loading');
            return false;
        }
        
        console.log('✅ GA_CONFIG found');
        console.log('API Key:', window.GA_CONFIG.apiKey ? '✅ Set' : '❌ Missing');
        console.log('Client ID:', window.GA_CONFIG.clientId ? '✅ Set' : '❌ Missing');
        console.log('View ID:', window.GA_CONFIG.viewId ? '✅ Set' : '❌ Missing');
        
        if (!window.GA_CONFIG.apiKey || !window.GA_CONFIG.clientId || !window.GA_CONFIG.viewId) {
            console.error('❌ Missing required credentials');
            return false;
        }
        
        console.log('✅ All credentials present');
        return true;
    }
    
    // Test Google Identity Services loading
    function testGoogleAPI() {
        console.log('\n=== GOOGLE IDENTITY SERVICES TEST ===');
        
        if (typeof google === 'undefined' || !google.accounts) {
            console.error('❌ Google Identity Services not loaded');
            console.log('Check if https://accounts.google.com/gsi/client is loading');
            return false;
        }
        
        console.log('✅ Google Identity Services loaded');
        return true;
    }
    
    // Test domain authorization
    function testDomainAuthorization() {
        console.log('\n=== DOMAIN AUTHORIZATION TEST ===');
        
        const currentDomain = window.location.origin;
        console.log('Current domain:', currentDomain);
        
        if (currentDomain.includes('localhost')) {
            console.warn('⚠️  Running on localhost - will use sample data');
            return false;
        }
        
        if (!currentDomain.startsWith('https://')) {
            console.error('❌ Domain must use HTTPS for production');
            return false;
        }
        
        console.log('✅ Domain format is correct');
        return true;
    }
    
    // Test API initialization
    function testAPIInitialization() {
        console.log('\n=== GOOGLE IDENTITY SERVICES INITIALIZATION TEST ===');
        
        if (typeof google === 'undefined' || !google.accounts) {
            console.error('❌ Google Identity Services not available');
            return false;
        }
        
        try {
            // Test OAuth 2.0 initialization
            google.accounts.oauth2.initTokenClient({
                client_id: window.GA_CONFIG.clientId,
                scope: window.GA_CONFIG.scopes.join(' '),
                callback: function(tokenResponse) {
                    console.log('✅ OAuth 2.0 initialization successful');
                    console.log('✅ Ready to connect to Google Analytics');
                    console.log('✅ Access token received:', tokenResponse.access_token ? 'Yes' : 'No');
                },
                error_callback: function(error) {
                    console.error('❌ OAuth 2.0 initialization failed:', error);
                    
                    if (error.error === 'access_denied') {
                        console.error('❌ ACCESS DENIED');
                        console.error('Check your client ID and authorized origins');
                    }
                    
                    if (error.error === 'popup_closed_by_user') {
                        console.error('❌ POPUP CLOSED');
                        console.error('User closed the OAuth popup');
                    }
                }
            });
            
            console.log('✅ Google Identity Services initialized successfully');
            return true;
            
        } catch (error) {
            console.error('❌ Google Identity Services initialization failed:', error);
            return false;
        }
    }
    
    // Run all tests
    function runAllTests() {
        console.log('🚀 Starting Google Analytics Connection Tests...\n');
        
        const configOk = testConfiguration();
        const apiOk = testGoogleAPI();
        const domainOk = testDomainAuthorization();
        
        if (configOk && apiOk && domainOk) {
            console.log('\n✅ All basic tests passed - testing API initialization...');
            testAPIInitialization();
        } else {
            console.log('\n❌ Basic tests failed - fix issues before testing API');
        }
    }
    
    // Add test button to analytics page
    if ($('#analytics-content').length > 0) {
        const testButton = $('<button>')
            .text('🔍 Test Connection')
            .css({
                'position': 'fixed',
                'top': '20px',
                'right': '20px',
                'z-index': '9999',
                'padding': '10px 20px',
                'background': '#667eea',
                'color': 'white',
                'border': 'none',
                'border-radius': '5px',
                'cursor': 'pointer'
            })
            .click(runAllTests);
        
        $('body').append(testButton);
        
        console.log('🔍 Connection test button added to top-right corner');
        console.log('Click it to run diagnostic tests');
    }
    
    // Auto-run tests if there are errors
    setTimeout(function() {
        if ($('.error-message').length > 0) {
            console.log('🔍 Auto-running connection tests due to errors...');
            runAllTests();
        }
    }, 2000);
});
