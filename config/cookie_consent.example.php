<?php
/**
 * Cookie Consent Plugin Configuration Example
 *
 * This file provides a sample configuration for the Saicosys Cookie Consent plugin.
 * Copy this file to your config directory as cookie_consent.php and adjust as needed.
 *
 * Usage:
 * - Configure categories, banner text, compliance, and integrations here.
 * - Refer to the inline comments for guidance on each option.
 */

return [
    'cookieConsent' => [
        // Enable or disable the cookie consent plugin
        'enabled' => true,
        // Cookie expiration time in seconds
        'expiration' => 3600,
        // Path for the consent cookie
        'path' => '/',
        // Set to true to use secure cookies (HTTPS only)
        'secure' => true,
        // SameSite policy for the consent cookie
        'samesite' => 'Lax',
        // Set to true to use HttpOnly cookies
        'httpOnly' => false,
        // Name of the consent cookie
        'cookieName' => 'cookie_consent',
        // Website name (for display or logging)
        'website' => 'example',
        // Domain for the consent cookie
        'domain' => 'www.example.com',
        // Encoding type for the cookie value
        'encodingType' => 'raw',
        // Consent flags for Google Consent Mode and other integrations
        'consentFlags' => [
            'functionalStorageRequired' => true,
            'functionalStorageOptional' => true,
            'analyticsStorage' => true,
            'marketingStorage' => true,
            'personalizationStorage' => true,
            'securityStorage' => true,
            'marketingUserDataStorage' => true,
            'marketingPersonalization' => true,
        ],
        // Optional callback function for consent changes
        'callbackFunction' => null,
        // Banner configuration
        'banner' => [
            // Link for more information in the modal
            'modalMainTextMoreLink' => null,
            // Timeout for the banner (ms)
            'barTimeout' => 1000,
            // Position of the banner (top or bottom)
            'position' => 'bottom',
            // Banner style classes
            'style' => [
                'barColor' => 'bg-white text-dark',
                'acceptButton' => 'btn btn-light',
                'rejectButton' => 'btn btn-light',
                'customizeButton' => 'btn btn-light',
                'savecustomizeButton' => 'btn btn-success',
                'cancelcustomizeButton' => 'btn btn-light',
            ],
            // Allow users to customize preferences
            'customizable' => true,
            // Banner title
            'title' => __('This site uses cookies'),
            // Banner message
            'message' => __('We use cookies to personalise content and ads, to provide social media features and to analyse our traffic. We also share information about your use of our site with our social media, advertising and analytics partners who may combine it with other information that you’ve provided to them or that they’ve collected from your use of their services.'),
            // Button texts
            'acceptText' => __('Accept All'),
            'rejectText' => __('Reject All'),
            'customizeText' => __('Customize'),
            // Modal configuration for customization
            'modal' => [
                'title' => __('Customize Cookie Preferences'),
                'message' => __('Cookies are small pieces of data sent from a website and stored on the user computer by the user web browser while the user is browsing. Your browser stores each message in a small file, called cookie. When you request another page from the server, your browser sends the cookie back to the server. Cookies were designed to be a reliable mechanism for websites to remember information or to record the user browsing activity.'),
                'saveButtonText' => __('Save Preferences'),
                'cancelButtonText' => __('Cancel'),
            ],
            // Link to your cookie policy page
            'cookiePolicyLink' => '/cookies-policy',
        ],
        // Compliance settings for GDPR, CPRA, Google CMP
        'compliance' => [
            'gdpr' => true,
            'cpra' => true,
            'google_cmp' => true,
        ],
        // Geo-targeting settings
        'geoTargeting' => [
            'enabled' => false, // Set to true to enable geo-targeting
            'regions' => ['EU', 'US-CA'], // Regions to show the banner
        ],
        // Logging settings
        'logging' => [
            'enabled' => true,
            'driver' => 'File',
        ],
        // Multilingual support
        'multilingual' => [
            'enabled' => true,
            'fallbackLocale' => 'en_US',
            'supportedLangs' => ['en', 'fr', 'de'],
        ],
        // Cookie policy generator settings
        'cookiePolicy' => [
            'generator' => true,
        ],
        // Fallback settings
        'fallback' => [
            'useDefault' => true,
        ],
        // Cookie categories and their configuration
        'categories' => [
            // Essential cookies (cannot be disabled)
            'essential' => [
                'label' => 'Essential',
                'description' => 'Required for basic site functionality and cannot be disabled.',
                'required' => true,
                'cookies' => [
                    'PHPSESSID',
                    'CAKEPHP', // CakePHP session cookie
                    'csrfToken', // CSRF protection
                    // Add other essential cookies here
                ],
                'services' => [
                    'CakePHP Session',
                    'CSRF Protection',
                    'Authentication',
                    // Add other essential services here
                ],
            ],
            // Preference cookies
            'preferences' => [
                'label' => 'Preferences',
                'description' => 'Remembers your preferences and settings.',
                'required' => false,
                'cookies' => [
                    // Add preference cookies here
                ],
                'services' => [
                    // Add preference-related services here
                ],
            ],
            // Statistics/analytics cookies
            'statistics' => [
                'label' => 'Statistics',
                'description' => 'Helps us understand how visitors interact with the website by collecting and reporting information anonymously.',
                'required' => false,
                'cookies' => [
                    // Google Analytics (Universal Analytics)
                    '_ga', '_gid', '_gat',
                    // Matomo
                    '_pk_id', '_pk_ses',
                    // Add more statistics/analytics cookies here
                ],
                'services' => [
                    'Google Analytics',
                    'Matomo',
                    // Add more statistics/analytics services here
                ],
            ],
            // Marketing cookies
            'marketing' => [
                'label' => 'Marketing & Analytics',
                'description' => 'Used to track visitors across websites to display relevant ads and measure the effectiveness of marketing campaigns.',
                'required' => false,
                'cookies' => [
                    // Google Tag Manager
                    '_gtm', '_dc_gtm_',
                    // Facebook Pixel
                    '_fbp',
                    // Add more 3rd party marketing cookies here
                ],
                'services' => [
                    'Google Tag Manager',
                    'Facebook Pixel',
                    // Add more marketing services here
                ],
            ],
        ],
        // Google integration settings
        'google' => [
            'enableConsentMode' => true, // Enable Google Consent Mode
            'enableGTM' => true,         // Enable Google Tag Manager
            'gtmId' => 'GT-XXXXXXXX',     // GTM Container ID
            'enableGA4' => true,         // Enable Google Analytics 4
            'ga4Id' => 'G-XXXXXXXXX',   // GA4 Measurement ID
        ],
    ],
];