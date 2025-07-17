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
namespace Saicosys\CookieConsent\Integration;

use Cake\Http\ServerRequest;
use Saicosys\CookieConsent\Blocker\CookieBlocker;

/**
 * GA4LoaderIntegration
 *
 * Handles the rendering of Google Analytics 4 (GA4) scripts based on user consent.
 *
 * Usage example:
 *
 * ```php
 * $ga4Loader = new \Saicosys\CookieConsent\Integration\GA4LoaderIntegration($config);
 * $ga4Script = $ga4Loader->render($request);
 * // Output $ga4Script in your layout if not empty
 * ```
 */
class GA4LoaderIntegration
{
    /**
     * @var string Google Analytics 4 Measurement ID
     */
    protected string $ga4Id;

    /**
     * @var array<string, mixed> Configuration array
     */
    protected array $config = [];

    /**
     * Constructor.
     *
     * @param array<string, mixed> $config Optional configuration array from middleware.
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->ga4Id = $this->config['ga4Id'];
    }

    /**
     * Render the GA4 script HTML if enabled and consent is given.
     *
     * @param \Cake\Http\ServerRequest $request The current request instance.
     * @return string The GA4 script HTML, or an empty string if not allowed
     */
    public function render(ServerRequest $request): string
    {
        // Check if GA4 is enabled and a Measurement ID is set
        if (!$this->ga4Id || empty($this->config['enableGA4'])) {
            return '';
        }

        // Use the CookieBlocker to check if the user has consented to statistics/analytics
        $blocker = new CookieBlocker($request);
        if (!$blocker->allow('statistics')) {
            return '';
        }

        // Return the GA4 script HTML
        return <<<HTML
            <!-- Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id={$this->ga4Id}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '{$this->ga4Id}', { 'anonymize_ip': true });
            </script>
            <!-- End Google Analytics -->
        HTML;
    }
}
