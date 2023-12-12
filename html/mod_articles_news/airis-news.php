<?php

// No direct access
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;

// Module sublayout file for each Joomla! item
$moduleSublayoutFileName = ModuleHelper::getLayoutPath('mod_articles_news', pathinfo(__FILE__, PATHINFO_FILENAME) . '_item');

?>

<div class="airis-module-news<?php echo htmlspecialchars(rtrim($moduleclass_sfx), ENT_QUOTES, 'UTF-8'); ?>">

    <?php // Replace is_array() with is_countable() once we're on PHP 7.3+ and Joomla! 5+ for good ?>
    <?php if (isset($list) && is_array($list) && count($list)) : ?>

        <ul class="airis-module-news-list unstyled">
            <?php
                foreach ($list as $item) {
                    require $moduleSublayoutFileName;
                }
            ?>
        </ul>

    <?php else : ?>

        <div class="airis-module-empty airis-module-articles-news-empty" data-nosnippet>
            <p class="airis-module-empty-message airis-module-articles-news-empty-message">
                <?php echo Text::_('TPL_AIRIS_MOD_ARTICLES_NEWS_NO_ITEMS'); ?>
            </p>
        </div>

    <?php endif; ?>

</div>