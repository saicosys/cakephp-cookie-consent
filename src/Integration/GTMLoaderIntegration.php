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
 * GTMLoaderIntegration
 *
 * Handles the rendering of Google Tag Manager (GTM) scripts based on user consent.
 *
 * Usage example:
 *
 * ```php
 * $gtmLoader = new \Saicosys\CookieConsent\Integration\GTMLoaderIntegration($config);
 * $gtmScript = $gtmLoader->render($request);
 * // Output $gtmScript in your layout if not empty
 * ```
 */
class GTMLoaderIntegration
{
    /**
     * @var string Google Tag Manager Container ID
     */
    protected string $gtmId;

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
        $this->gtmId = $this->config['gtmId'];
    }

    /**
     * Render the GTM script HTML if enabled and consent is given.
     *
     * @param \Cake\Http\ServerRequest $request The current request instance.
     * @return string The GTM script HTML, or an empty string if not allowed
     */
    public function render(ServerRequest $request): string
    {
        // Check if GTM is enabled and a Container ID is set
        if (!$this->gtmId || empty($this->config['enableGTM'])) {
            return '';
        }

        // Use the CookieBlocker to check if the user has consented to marketing
        $blocker = new CookieBlocker($request);
        if (!$blocker->allow('marketing')) {
            return '';
        }

        // Return the GTM script HTML
        return <<<HTML
            <!-- Google Tag Manager -->
            <script>
                (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,'script','dataLayer','{$this->gtmId}');
            </script>
            <!-- End Google Tag Manager -->
        HTML;
    }
}
