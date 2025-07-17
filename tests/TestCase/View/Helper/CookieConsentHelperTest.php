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
namespace Saicosys\CookieConsent\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use Saicosys\CookieConsent\View\Helper\CookieConsentHelper;

/**
 * Saicosys\CookieConsent\View\Helper\CookieConsentHelper Test Case
 */
class CookieConsentHelperTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Saicosys\CookieConsent\View\Helper\CookieConsentHelper
     */
    protected $CookieConsent;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $view = new View();
        $this->CookieConsent = new CookieConsentHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->CookieConsent);

        parent::tearDown();
    }
}
