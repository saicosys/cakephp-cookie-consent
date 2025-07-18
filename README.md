# Saicosys Cookie Consent plugin for CakePHP 5

*A fully-featured, highly configurable, and plug-and-play CakePHP 5 plugin for cookie consent management. Supports GDPR, CPRA, Google CMP, multilingual sites, and more. Built for easy integration, customization, and compliance.*

## Features

- **Easy GDPR & CPRA compliance**: Out-of-the-box support for major privacy regulations.
- **Fully customizable cookie banner**: Change text, style, position, and behavior via config.
- **Advanced cookie scanner**: Detects cookies set by your app for easier compliance (stub, extendable).
- **Automatic cookie blocker**: Prevents non-essential cookies from being set until consent is given.
- **Geo-targeted cookie consent**: Show banner only to users in specific regions (e.g., EU, California).
- **Certified Google CMP Partner**: Ready for Google Consent Mode integration (extendable).
- **Cookie policy generator**: Generate a policy page based on your config (stub, extendable).
- **Granular cookie control**: Users can accept/reject categories (analytics, marketing, preferences, etc.).
- **Multiple consent models**: Support for opt-in, opt-out, and customizable consent flows.
- **Consent log**: All consent actions are logged for compliance audits.

## Architecture

- Middleware-based banner injection
- Service class for consent management
- Loader classes for GTM/GA4
- Logger with DB fallback
- Unit tests and CI config included

## Compliance

This plugin helps you comply with GDPR, CPRA, and Google CMP requirements.

### GDPR Compliance

- Shows consent banner to EU users (geo-targeting).
- Blocks non-essential cookies until consent is given.
- Allows granular consent for different cookie categories.
- Logs all consent actions for auditability.

### CPRA Compliance

- Shows consent banner to California users (geo-targeting).
- Supports opt-out for marketing and analytics cookies.
- Stores consent/rejection for compliance.

### Google CMP Compliance

- Ready for integration with Google Consent Mode (extendable).
- Can be configured to meet Google’s requirements for ad personalization and analytics.

## Installation

1. Install the plugin using Composer by running the following command at the root of your CakePHP 5 project:

```bash
composer require saicosys/cakephp-cookie-consent
```

2. Load the Plugin

After installation, load the plugin:

```bash
bin/cake plugin load Saicosys/CookieConsent
```

3. Publish and customize the config file:

```bash
cp vendor/Saicosys/CookieConsent/config/cookie_consent.example.php config/cookie_consent.php
```

## Integration

- AJAX endpoints for consent actions are available at `/cookie-consent/accept`, `/cookie-consent/reject`, `/cookie-consent/customize`.
- All features are fully configurable via `config/cookie_consent.php`.
- Multilingual support via CakePHP i18n and `.po` files in `resources`.
- Consent logs are stored in `logs/cookie_consent.log` for compliance.

## Quick Start

Render the banner in your layout:

```php
// Add Marketing script in the <head> tag.
<?= $this->CookieConsent->renderMarketingScript() ?>

// Add banner in the <body> tag at bottom.
<?= $this->CookieConsent->renderBanner() ?>
```

## Customization

### Customizing the Banner

- Edit `config/cookie_consent.php` to change the banner text, position (`top`, `bottom`), and style (`light`, `dark`).
- All banner content is translatable via CakePHP i18n.

### Customizing Styles

- Change the `style` option in the config for light/dark mode.
- Override the default CSS by targeting `.cookie-consent-banner` in your app’s stylesheet.

### Customizing Consent Categories

- Add, remove, or rename categories in the `categories` section of the config.
- Each category can have a label, description, and required flag.
- Only the `essential` category is required by default; others can be toggled by users. 

## Integrations: GA4, GTM, Facebook Pixel

### Google Tag Manager (GTM)

Auto-injected if `enableGTM = true`

### Google Analytics (GA4)

Auto-injected if `enableGA4 = true`

### Google Consent Mode v2

JS hooks like:

```js
gtag('consent', 'update', {
  'analytics_storage': 'granted',
  'ad_storage': 'granted'
});
```

## Maintainer

**Saicosys Technologies Private Limited**  
[https://www.saicosys.com](https://www.saicosys.com)  
Contact: [info@saicosys.com](mailto:info@saicosys.com)

## Contributions

Contributions are welcome! Please fork the repository and submit a pull request. For major changes, open an issue first to discuss what you would like to change.

## License

This project is licensed under the [MIT License](https://opensource.org/licenses/mit-license.php).

Copyright (c) 2017-2025, Saicosys Technologies Private Limited
