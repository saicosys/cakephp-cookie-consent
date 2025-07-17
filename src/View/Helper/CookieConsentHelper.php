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
namespace Saicosys\CookieConsent\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

/**
 * CookieConsent helper
 *
 * Provides methods to render the cookie consent banner and inject consented scripts.
 *
 * Usage example (in your layout):
 *
 * ```php
 * // Render the cookie consent banner. Always add it in the bottom of the <body> tag.
 * <?= $this->CookieConsent->renderBanner() ?>
 *
 * // Render consented scripts (GTM, GA4, etc.). Always add it in the <head> tag.
 * <?= $this->CookieConsent->renderMarketingScripts() ?>
 * ```
 */
class CookieConsentHelper extends Helper
{
    /**
     * @var array<string, mixed> Plugin configuration
     */
    protected array $config;

    /**
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [];

    /**
     * @var array<string> CakePHP helpers used by this helper
     */
    protected array $helpers = ['Html'];

    /**
     * Constructor.
     *
     * @param \Cake\View\View $view The view instance.
     * @param array<string, mixed> $config Configuration array.
     */
    public function __construct(View $view, array $config = [])
    {
        parent::__construct($view, $config);
        $this->config = $config['config'];
    }

    /**
     * Render the cookie consent banner if the user is in a target region and has not yet given consent.
     *
     * @return string HTML for the cookie consent banner, or an empty string if not shown
     */
    public function renderBanner(): string
    {
        // Get consent attributes from the request
        $consent = $this->_View->getRequest()->getAttribute('cookieConsent', []);
        $inTargetRegion = $consent['inTargetRegion'] ?? true;
        $consentGiven = $consent['consentGiven'] ?? false;

        // If not in target region or consent already given, do not show the banner
        if (!$inTargetRegion || $consentGiven) {
            return '';
        }

        // Render the cookie consent banner element
        $element = 'Saicosys/CookieConsent.cookie_banner';
        $html = $this->_View->element($element, ['config' => $this->config]);
        // Add the consent CSS
        $html .= $this->Html->css('Saicosys/CookieConsent.cookie-consent');
        // Add the consent JS
        $html .= $this->Html->script('Saicosys/CookieConsent.cookie-consent');

        return $html;
    }

    /**
     * Render the cookie consent marketing scripts (GTM, GA4, etc.) based on user consent.
     *
     * This should be called in your layout <head> tag
     * e.g. <?= $this->CookieConsent->renderMarketingScripts() ?>
     *
     * @return string HTML script tags or raw HTML for consented scripts
     */
    public function renderMarketingScripts(): string
    {
        // Get the list of scripts to inject from the request attribute
        $scripts = $this->_View->getRequest()->getAttribute('cookieConsentScripts', []);
        $output = '';
        foreach ($scripts as $script) {
            // If the script is a URL, render as <script src="...">
            if (
                is_string($script) &&
                (str_starts_with($script, 'http') ||
                    str_starts_with($script, '/'))
            ) {
                $output .= $this->Html->script($script);
            } else {
                // Otherwise, assume it's raw HTML (e.g., GTM/GA4 inline)
                $output .= $script;
            }
        }

        return $output;
    }
}
