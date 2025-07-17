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
namespace Saicosys\CookieConsent\Blocker;

use Cake\Http\ServerRequest;

/**
 * CookieBlocker
 *
 * Utility class to check if a cookie category is allowed based on user consent.
 *
 * Usage example:
 *
 * ```php
 * // In your integration or middleware:
 * $blocker = new \Saicosys\CookieConsent\Blocker\CookieBlocker($request);
 * if ($blocker->allow('marketing')) {
 *     // Inject marketing scripts
 * }
 * ```
 */
class CookieBlocker
{
    /**
     * @var array<string, bool> Consent state for each category
     */
    protected array $consent;

    /**
     * Constructor.
     *
     * @param \Cake\Http\ServerRequest $request The current request instance.
     */
    public function __construct(ServerRequest $request)
    {
        // Parse the consent cookie from the request
        $this->consent = json_decode(
            $request->getCookie('cookie_consent') ?? '{}',
            true,
        );
    }

    /**
     * Check if a category is allowed based on user consent.
     *
     * @param string $category The cookie category (e.g., 'marketing', 'statistics').
     * @return bool True if allowed, false otherwise.
     */
    public function allow(string $category): bool
    {
        // If the category is not set in consent, treat as not allowed
        if (!isset($this->consent[$category])) {
            return false;
        }
        // Return true if the user has consented to this category
        return $this->consent[$category] === true;
    }

    /**
     * Check if a category should be blocked (not allowed).
     *
     * @param string $category The cookie category.
     * @return bool True if should be blocked, false otherwise.
     */
    public function shouldBlock(string $category): bool
    {
        // For demonstration, block all except 'necessary' (could be extended)
        return !in_array($category, ['necessary'], true);
    }
}
