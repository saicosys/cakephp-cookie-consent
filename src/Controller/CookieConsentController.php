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
namespace Saicosys\CookieConsent\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\Response;
use Saicosys\CookieConsent\Service\CookieConsentService;

/**
 * CookieConsentController
 *
 * Controller for handling cookie consent actions such as accept, reject, and customize.
 * Provides endpoints for managing user cookie preferences.
 *
 * Usage example (AJAX from frontend):
 *
 * ```js
 * // Accept all categories
 * fetch('/cookie-consent/accept', { method: 'POST', headers: { 'Content-Type': 'application/json' } });
 *
 * // Customize categories
 * fetch('/cookie-consent/customize', {
 *   method: 'POST',
 *   headers: { 'Content-Type': 'application/json' },
 *   body: JSON.stringify({ categories: { marketing: true, statistics: false } })
 * });
 * ```
 */
class CookieConsentController extends AppController
{
    /**
     * @var \Saicosys\CookieConsent\Service\CookieConsentService Service for managing cookie consent state
     */
    protected CookieConsentService $service;

    /**
     * Controller initialization hook.
     *
     * Loads necessary components and services for the CookieConsentController.
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->service = new CookieConsentService();
    }

    /**
     * beforeFilter callback.
     *
     * Allows unauthenticated access to the 'accept', 'reject', and 'customize' actions if the Authentication component is loaded.
     *
     * @param \Cake\Event\EventInterface $event The beforeFilter event.
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        // Allow unauthenticated access to consent actions if Authentication is loaded
        if ($this->components()->has('Authentication')) {
            $this->Authentication->allowUnauthenticated(['accept', 'reject', 'customize']);
        }
    }

    /**
     * Accept consent for all categories (AJAX).
     *
     * @return \Cake\Http\Response|null JSON response with all categories set to true
     */
    public function accept(): ?Response
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException('POST required');
        }

        // Get all categories from config using Configure::read
        $categories = array_keys(Configure::read('cookieConsent.categories') ?? []);

        // Set consent for all categories to true
        foreach ($categories as $category) {
            $this->service->setConsent($category, true);
        }

        // Get the updated consent state
        $consents = $this->service->getConsent($this->request);

        $this->set([
            'success' => true,
            'categories' => $consents,
        ]);

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'categories' => $consents,
            ]));
    }

    /**
     * Reject consent for a category (AJAX).
     *
     * @return \Cake\Http\Response|null JSON response with the rejected category
     */
    public function reject(): ?Response
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException('POST required');
        }

        // Get the category to reject (default to 'essential')
        $category = $this->request->getData('category', 'essential');
        $this->service->setConsent($category, false);

        $consents = $this->service->getConsent($this->request);

        $this->set([
            'success' => true,
            'category' => $category,
            'categories' => $consents,
        ]);

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'category' => $category,
                'categories' => $consents,
            ]));
    }

    /**
     * Customize consent (AJAX, accepts array of categories).
     *
     * @return \Cake\Http\Response|null JSON response with updated categories
     */
    public function customize(): ?Response
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException('POST required');
        }

        // Get the categories and their values from the request
        $categories = $this->request->getData('categories', []);
        if (!is_array($categories)) {
            throw new BadRequestException('Categories must be an array');
        }

        // Set consent for each provided category
        foreach ($categories as $category => $value) {
            $this->service->setConsent($category, (bool)$value);
        }

        // Get the updated consent state
        $consents = $this->service->getConsent($this->request);
        $this->set([
            'success' => true,
            'categories' => $consents,
        ]);

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode([
                'success' => true,
                'categories' => $consents,
            ]));
    }
}
