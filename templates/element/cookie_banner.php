<?php
/**
 * @var \App\View\AppView $this
 */

// Banner content (should be loaded from config or i18n)
$bannerText = __d('cookie_consent', $config['banner']['message']);
$acceptText = __d('cookie_consent', $config['banner']['acceptText']);
$rejectText = __d('cookie_consent', $config['banner']['rejectText']);
$customizeText = __d('cookie_consent', $config['banner']['customizeText']);

// Banner style from config
$position = $config['banner']['position'] ?? 'bottom';
$style = $config['banner']['style'];

// Get categories from config
$categories = $config['categories'] ?? [];
$customizeHtml = __d('cookie_consent', 'Read more');

// Modal HTML for customization
$modalHtml = '<div id="cookie-consent-modal">
    <div class="consent-model-customize">
        <h3>' . __d('cookie_consent', $config['banner']['modal']['title']) . '</h3>
        <p>' . __d('cookie_consent', $config['banner']['modal']['message']) . '</p>
        <form id="cookie-consent-modal-form">
';

foreach ($categories as $key => $cat) {
    if (!empty($cat['required'])) continue; // Skip essential
    $modalHtml .= sprintf(
        '<label><input type="checkbox" class="cookie-category-modal" name="%s" value="1"> %s</label>',
        htmlspecialchars($key),
        htmlspecialchars($cat['label'] ?? ucfirst($key))
    );
}
$modalHtml .= '<button type="button" class="cookie-consent-save-modal ' . $style['savecustomizeButton'] . '">' . __d('cookie_consent', $config['banner']['modal']['saveButtonText']) . '</button>
            <button type="button" class="cookie-consent-cancel-modal  ' . $style['cancelcustomizeButton'] . '">' . __d('cookie_consent', $config['banner']['modal']['cancelButtonText']) . '</button>
        </form>
    </div>
</div>';
?>
<?= $modalHtml ?>
<div id="cookie-consent-banner" class="cookie-consent-banner <?= h($position) ?> <?= $style['barColor'] ?>">
    <div id="cookie-introduction">
        <div><?= h($bannerText) ?></div>
        <?php if (isset($config['banner']['cookiePolicyLink']) && $config['banner']['cookiePolicyLink']): ?>
            <div><?= $this->Html->link(__('Check cookie policy'), $config['banner']['cookiePolicyLink']) ?></div>
        <?php endif; ?>
    </div>
    <form id="cookie-consent-form" onsubmit="window.cookieConsentCustomize(); return false;">
        <button type="button" class="cookie-consent-accept <?= $style['acceptButton'] ?>"><?= h($acceptText) ?></button>
        <button type="button" class="cookie-consent-reject <?= $style['rejectButton'] ?>"><?= h($rejectText) ?></button>
        <button type="button" class="cookie-consent-customize <?= $style['customizeButton'] ?>"><?= h($customizeText) ?></button>
    </form>
</div>