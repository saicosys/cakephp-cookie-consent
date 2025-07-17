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
namespace Saicosys\CookieConsent\Service;

use Cake\Core\Configure;
use Cake\Http\ServerRequest;

/**
 * CookieConsentService
 *
 * Service class for managing user cookie consent state, logging, and compliance checks.
 *
 * Usage example:
 *
 * ```php
 * $service = new \Saicosys\CookieConsent\Service\CookieConsentService();
 * // Set consent for a category
 * $service->setConsent('marketing', true);
 * // Check if consent is given
 * $hasConsent = $service->hasConsent('marketing');
 * // Get all consents for the current request
 * $consents = $service->getConsent($request);
 * ```
 */
class CookieConsentService
{
    /**
     * @var array<string, mixed> Plugin configuration
     */
    protected array $config = [];

    /**
     * Constructor.
     *
     * Loads the cookie consent configuration from CakePHP Configure.
     */
    public function __construct()
    {
        $this->config = Configure::read('cookieConsent');
    }

    /**
     * Check if user has given consent for a category.
     *
     * @param string $category Cookie category (e.g., 'analytics', 'marketing').
     * @return bool True if consent is given, false otherwise.
     */
    public function hasConsent(string $category): bool
    {
        // Use PHP session to store consent state
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $consents = $_SESSION[$this->config['cookieName']] ?? [];

        return !empty($consents[$category]);
    }

    /**
     * Store user consent for a category.
     *
     * @param string $category The cookie category.
     * @param bool $value Consent value (true for given, false for not given).
     * @return void
     */
    public function setConsent(string $category, bool $value): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION[$this->config['cookieName']][$category] = $value;
        // Log the consent action
        $this->logConsent([
            'category' => $category,
            'value' => $value,
            'timestamp' => time(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    }

    /**
     * Get all consent states for the current request.
     *
     * @param \Cake\Http\ServerRequest $request The current request instance.
     * @return array<string, bool> Consent state for each category
     */
    public function getConsent(ServerRequest $request): array
    {
        // Prefer session (just set by accept()), fallback to cookie
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $sessionConsents = $_SESSION[$this->config['cookieName']] ?? null;
        if ($sessionConsents && is_array($sessionConsents)) {
            return $sessionConsents;
        }
        // Fallback to cookie if session is not set
        return json_decode($request->getCookie($this->config['cookieName']) ?? '{}', true);
    }

    /**
     * Log user consent action to file.
     *
     * @param array<string, mixed> $data Consent data to log.
     * @return void
     */
    public function logConsent(array $data): void
    {
        $logDir = dirname(__DIR__, 2) . '/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $logFile = $logDir . '/cookie_consent.log';
        $line = json_encode($data) . PHP_EOL;
        file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
    }

    /**
     * Retrieve consent log entries (for compliance audits).
     *
     * @return array<int, array<string, mixed>> Array of consent log entries
     */
    public function getConsentLog(): array
    {
        $logFile = dirname(__DIR__, 2) . '/logs/cookie_consent.log';
        if (!file_exists($logFile)) {
            return [];
        }
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $entries = [];
        foreach ($lines as $line) {
            $entry = json_decode($line, true);
            if ($entry) {
                $entries[] = $entry;
            }
        }

        return $entries;
    }

    /**
     * Scan for cookies set by the application.
     *
     * @return array<string, array<string, mixed>> List of detected cookies with their values and categories.
     */
    public function scanCookies(): array
    {
        // Get categories from config if available
        $categories = $this->config['categories'] ?? [];
        $cookies = [];
        foreach ($_COOKIE as $name => $value) {
            // Try to find the category for this cookie
            $category = null;
            foreach ($categories as $catKey => $cat) {
                if ($name === $catKey) {
                    $category = $cat['label'] ?? $catKey;
                    break;
                }
            }
            $cookies[$name] = [
                'value' => $value,
                'category' => $category,
            ];
        }

        return $cookies;
    }

    /**
     * Check compliance for a given regulation.
     *
     * @param string $regulation Regulation key (e.g., 'gdpr', 'cpra').
     * @return bool True if compliant, false otherwise.
     */
    public function isCompliant(string $regulation): bool
    {
        $categories = $this->config['categories'] ?? [];
        // For demonstration, only check for GDPR and CPRA
        if (strtolower($regulation) === 'gdpr') {
            // GDPR: No non-essential cookies without consent
            foreach ($categories as $key => $cat) {
                if (!empty($cat['required'])) {
                    continue; // Essential cookies are allowed
                }
                if (!empty($_COOKIE[$key]) && !$this->hasConsent($key)) {
                    return false; // Non-essential cookie set without consent
                }
            }

            return true;
        }
        if (strtolower($regulation) === 'cpra') {
            // CPRA: Check for opt-out ("Do Not Sell") consent if category exists
            if (isset($categories['do_not_sell'])) {
                // If user has not opted out, and cookie is set, not compliant
                if (!empty($_COOKIE['do_not_sell']) && !$this->hasConsent('do_not_sell')) {
                    return false;
                }
            }
            // Fallback: treat like GDPR for other cookies
            foreach ($categories as $key => $cat) {
                if (!empty($cat['required'])) {
                    continue;
                }
                if (!empty($_COOKIE[$key]) && !$this->hasConsent($key)) {
                    return false;
                }
            }

            return true;
        }
        // For other regulations, return true (or implement as needed)
        return true;
    }
}
