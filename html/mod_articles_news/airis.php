<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;

$itemsTotal = 0;

// Check if this module has any items suitable for display
if (isset($list) === true && is_countable($list) === true) {
    $itemsTotal = count($list);
}

if ($itemsTotal !== 0) {
    // Module sublayout file for each Joomla! item
    $moduleSublayoutFileName = ModuleHelper::getLayoutPath('mod_articles_news', pathinfo(__FILE__, PATHINFO_FILENAME) . '_item');

    // Apply required CSS class to each list item for a proper single-row flex display
    $itemsPerRow = $params->get('count', AirisTemplate::ITEMS_PER_ROW_DEFAULT);

    $itemsContainerDisplayModeClass = 'airis-block-items';
    $itemDisplayModeClass = 'airis-block-item';

    // Replace integers with numeral words with a 2-based array for additional code simplicity
    // $itemsPerRowAllowedDigits = range(1, 6);
    // $itemsPerRowAllowedNumerals = ['one', 'two', 'three', 'four', 'five', 'six'];
    $itemsPerRowAllowedNumerals = [2 => 'two', 'three', 'four', 'five', 'six'];

    // Ignore unacceptable integers
/*     if (in_array($itemsPerRow, $itemsPerRowAllowedDigits, true) === false) {
        $itemsPerRow = AirisTemplate::ITEMS_PER_ROW_DEFAULT;
    } */
    if ($itemsPerRow < AirisTemplate::ITEMS_PER_ROW_MIN || $itemsPerRow > AirisTemplate::ITEMS_PER_ROW_MAX) {
        $itemsPerRow = AirisTemplate::ITEMS_PER_ROW_DEFAULT;
    }

    // Reduce the number of items per row to avoid having empty slots
    if ($itemsTotal < $itemsPerRow) {
        $itemsPerRow = $itemsTotal;
    }

    // Finally select classes for container and its items
    if ($itemsPerRow !== AirisTemplate::ITEMS_PER_ROW_MIN) {
        $itemsContainerDisplayModeClass = 'airis-flex-item-rows';
        $itemDisplayModeClass = "airis-flex-item-per-row-$itemsPerRowAllowedNumerals[$itemsPerRow]";
    }

    // $itemDisplayModeClass = str_replace($itemsPerRowAllowedDigits, $itemsPerRowAllowedNumerals, $itemsPerRow);
}

?>

<div class="airis-module-articles-news">

    <?php if ($itemsTotal !== 0) : ?>

        <ul class="airis-module-articles-news__list <?= $itemsContainerDisplayModeClass; ?> list-unstyled">
            <?php
                foreach ($list as $item) {
                    require $moduleSublayoutFileName;
                }
            ?>
        </ul>

    <?php else : ?>

        <div class="airis-module-empty airis-module-articles-news-empty" data-nosnippet>
            <p class="airis-module-empty__message airis-module-articles-news-empty__message">
                <?= Text::_('TPL_AIRIS_MOD_ARTICLES_NEWS_NO_ITEMS'); ?>
            </p>
        </div>

    <?php endif; ?>

</div>