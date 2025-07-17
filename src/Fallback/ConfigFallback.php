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
namespace Saicosys\CookieConsent\Fallback;

/**
 * ConfigFallback
 *
 * Provides fallback configuration for the plugin if no config is found.
 *
 * @package Saicosys\CookieConsent\Fallback
 */
class ConfigFallback
{
    /**
     * Get the fallback configuration array.
     *
     * @return array
     */
    public static function getConfig(): array
    {
        // Default fallback config values.
        return [
            'enabled' => false,
            'banner' => [
                'position' => 'bottom',
                'style' => 'light',
                'customizable' => false,
            ],
            'compliance' => [
                'gdpr' => false,
                'cpra' => false,
                'google_cmp' => false,
            ],
            'geo_targeting' => [
                'enabled' => false,
                'regions' => [],
            ],
            'logging' => [
                'enabled' => false,
                'driver' => 'File',
            ],
            'multilingual' => [
                'enabled' => false,
                'fallback_locale' => 'en_US',
            ],
            'cookie_policy' => [
                'generator' => false,
            ],
            'fallback' => [
                'use_default' => true,
            ],
        ];
    }
}
