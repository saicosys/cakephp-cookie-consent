<?php
declare(strict_types=1);

/**
 * Saicosys Technologies Private Limited
 * Copyright (c) 2017-2025, Saicosys Technologies
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.md
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    Saicosys <info@saicosys.com>
 * @copyright Copyright (c) 2017-2025, Saicosys Technologies
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 * @link      https://www.saicosys.com
 * @since     1.0.0
 */
namespace Saicosys\CookieConsent\Middleware;

use Cake\Event\EventManager;
use Cake\Utility\Hash;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Saicosys\CookieConsent\Integration\GA4LoaderIntegration;
use Saicosys\CookieConsent\Integration\GTMLoaderIntegration;
use Saicosys\CookieConsent\Service\CookieConsentService;

/**
 * CookieConsentMiddleware
 *
 * Middleware to handle cookie consent logic, geo-targeting, and script injection based on user consent.
 *
 * Usage example (in your Application.php):
 *
 * ```php
 * $middlewareQueue->add(new \Saicosys\CookieConsent\Middleware\CookieConsentMiddleware(Configure::read('cookieConsent')));
 * ```
 */
class CookieConsentMiddleware implements MiddlewareInterface
{
    /**
     * @var \Saicosys\CookieConsent\Service\CookieConsentService Service for managing cookie consent state
     */
    protected CookieConsentService $service;

    /**
     * @var array<string, mixed> Plugin configuration
     */
    protected array $config;

    /**
     * Constructor.
     *
     * @param array<string, mixed> $config Optional configuration array.
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->service = new CookieConsentService();
    }

    /**
     * Process the incoming request and handle cookie consent logic.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Server\RequestHandlerInterface $handler The request handler.
     * @return \Psr\Http\Message\ResponseInterface A response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 1. Geo-targeting: Only show banner/apply logic if user is in a target region
        $region = $this->_detectRegion($request);
        $geoRegions = Hash::get($this->config, 'geoTargeting.regions', ['EU', 'US-CA']);
        $geoEnabled = Hash::get($this->config, 'geoTargeting.enabled', true);
        $inTargetRegion = !$geoEnabled || in_array($region, $geoRegions, true);

        // 2. Check consent status from cookie (per category)
        $consentCookie = $_COOKIE['cookie_consent'] ?? null;
        $consent = [];
        if ($consentCookie) {
            // Parse the consent cookie as an associative array
            $consent = json_decode($consentCookie, associative: true) ?: [];
        } else {
            // Fallback: check session or service for consent state
            $consent = [
                'essential' => $this->service->hasConsent('essential'),
                'statistics' => $this->service->hasConsent('statistics'),
                'marketing' => $this->service->hasConsent('preferences'),
                'preferences' => $this->service->hasConsent('preferences'),
            ];
        }
        $consentGiven = !empty($consent['essential']);

        // 3. Block non-essential cookies if consent not given
        if ($inTargetRegion && !$consentGiven) {
            $this->_blockNonEssentialCookies();
        }

        // 4. Build the list of scripts to inject based on consent
        $scripts = [];
        // Only inject GTM if marketing consent is given
        if (!empty($consent['marketing'])) {
            $gtm = (new GTMLoaderIntegration($this->config['google']))->render($request);
            if ($gtm) {
                $scripts[] = $gtm;
            }
        }
        // Only inject GA4 if statistics consent is given
        if (!empty($consent['statistics'])) {
            $ga4 = (new GA4LoaderIntegration($this->config['google']))->render($request);
            if ($ga4) {
                $scripts[] = $ga4;
            }
        }

        // Pass consent status and scripts to the request for use in views/helpers
        $request = $request->withAttribute('cookieConsent', [
            'region' => $region,
            'inTargetRegion' => $inTargetRegion,
            'consentGiven' => $consentGiven,
            'consent' => $consent,
        ]);
        $request = $request->withAttribute('cookieConsentScripts', $scripts);

        // 5. Add the CookieConsent helper to the view for rendering the banner and scripts
        EventManager::instance()->on('Controller.beforeRender', function ($event): void {
            $controller = $event->getSubject();
            $builder = $controller->viewBuilder();
            $builder->addHelper('Saicosys/CookieConsent.CookieConsent', ['config' => $this->config]);
        });

        // 6. Continue to next middleware/handler
        return $handler->handle($request);
    }

    /**
     * Detect the user's region (stub for geo-targeting).
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return string Region code (e.g., 'EU', 'US-CA', 'OTHER')
     */
    private function _detectRegion(ServerRequestInterface $request): string
    {
        // Get client IP from server params
        $ip = null;
        if (method_exists($request, 'getServerParams')) {
            $params = $request->getServerParams();
            $ip = $params['REMOTE_ADDR'] ?? null;
        }
        if (!$ip || $ip === '127.0.0.1' || $ip === '::1') {
            // Localhost fallback for dev
            return 'EU';
        }

        // Use ipapi.co to get country code
        $countryCode = null;
        try {
            $response = @file_get_contents("https://ipapi.co/{$ip}/country/");
            if ($response !== false) {
                $countryCode = trim($response);
            }
        } catch (Exception $e) {
            // Ignore errors, fallback below
        }

        // Map country code to region
        $euCountries = [
            'AT','BE','BG','HR','CY','CZ','DK','EE','FI','FR','DE','GR','HU','IE','IT','LV','LT','LU','MT','NL','PL','PT','RO','SK','SI','ES','SE','IS','LI','NO','CH', // EEA/EFTA
        ];
        if ($countryCode) {
            if (in_array($countryCode, $euCountries, true)) {
                return 'EU';
            }
            if ($countryCode === 'US') {
                // For demo, treat California as 'US-CA' if header present (real check would need more data)
                // You could use https://ipapi.co/{ip}/region/ for state
                $region = @file_get_contents("https://ipapi.co/{$ip}/region_code/");
                if (trim($region) === 'CA') {
                    return 'US-CA';
                }

                return 'US';
            }
        }

        return 'OTHER';
    }

    /**
     * Block non-essential cookies if consent is not given.
     *
     * This removes non-essential cookies from $_COOKIE and sets expired headers.
     *
     * @return void
     */
    private function _blockNonEssentialCookies(): void
    {
        $categories = $this->config['categories'] ?? [];

        foreach ($categories as $cat) {
            // Skip essential cookies
            if (!empty($cat['required'])) {
                continue;
            }
            // Remove all cookies listed in this category
            if (!empty($cat['cookies']) && is_array($cat['cookies'])) {
                foreach ($cat['cookies'] as $cookieName) {
                    if (!empty($_COOKIE[$cookieName])) {
                        unset($_COOKIE[$cookieName]);
                        setcookie(
                            $cookieName,
                            '',
                            time() - 3600,
                            $this->config['path'] ?? '/',
                            $this->config['domain'] ?? '',
                            $this->config['secure'] ?? false,
                            $this->config['httpOnly'] ?? false,
                        );
                    }
                }
            }
        }
    }
}
