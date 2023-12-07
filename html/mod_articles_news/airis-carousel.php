<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// Acquire template parameters
$templateParameters = Factory::getApplication()->getTemplate(true)->params;

// Module sublayout file for each Joomla! item
$moduleSublayoutFileName = ModuleHelper::getLayoutPath('mod_articles_news', pathinfo(__FILE__, PATHINFO_FILENAME) . '_item');

// Process module data and options
$moduleClassSuffix = htmlspecialchars(rtrim($params->get('moduleclass_sfx', '')), ENT_QUOTES, 'UTF-8'); // Not using trim() here to keep possible and perfectly acceptable leading whitespace
$moduleClassSuffix = preg_replace('/\s{2,}/', ' ', $moduleClassSuffix); // Also remove non-singular whitespaces just in case

// Custom options prefix
$airisModuleClassSuffixParamPrefix = ' airis-module-param-';

// Supported custom options of this view
$airisModuleClassSuffixParams = [
    'carousel-disable-caption' => false,
    'carousel-disable-caption-heading' => false,
    'carousel-disable-caption-content' => false,
    'carousel-disable-controls' => false,
    'carousel-disable-indicators' => false,
    'carousel-disable-pause-on-hover' => false, // Bootstrap default is to pause on hover
    'carousel-interval' => 5000, // Bootstrap default
    'carousel-visibility-controls' => 'hover', // Bootstrap default is always
    'carousel-visibility-indicators' => 'always',
];

if ($moduleClassSuffix && strpos($moduleClassSuffix, $airisModuleClassSuffixParamPrefix) !== false) {
    // Try to acquire new values for any found custom options
    foreach ($airisModuleClassSuffixParams as $airisModuleClassSuffixParamKey => $airisModuleClassSuffixParamValue) {
        $airisModuleClassSuffixParamPrefixed = $airisModuleClassSuffixParamPrefix . $airisModuleClassSuffixParamKey;

        // Boolean values are simply toggled / inverted as their class strings don't have any attached value in the form of a hyphen-separated suffix
        if (is_bool($airisModuleClassSuffixParamValue)) {
            if (strpos($moduleClassSuffix, $airisModuleClassSuffixParamPrefixed) !== false) {
                $airisModuleClassSuffixParams[$airisModuleClassSuffixParamKey] = !$airisModuleClassSuffixParamValue;
            }
        } else {
            $airisModuleClassSuffixParamMatches = [];

            // Prepare regular expression pattern for non-boolean values
            $airisModuleClassSuffixParamKeyPattern = preg_quote($airisModuleClassSuffixParamPrefixed);
            $airisModuleClassSuffixParamPattern = "/\b$airisModuleClassSuffixParamKeyPattern\-(\w+)\b/";

            // Only last matching custom option will be processed in case if there were duplicates for some reason
            $airisModuleClassSuffixParamMatchResult = preg_match_all($airisModuleClassSuffixParamPattern, $moduleClassSuffix, $airisModuleClassSuffixParamMatches);

            if ($airisModuleClassSuffixParamMatchResult) {
                $airisModuleClassSuffixParamValueSubstring = end($airisModuleClassSuffixParamMatches[1]);

                // Update option with extracted param value
                if ($airisModuleClassSuffixParamValueSubstring) {
                    $airisModuleClassSuffixParams[$airisModuleClassSuffixParamKey] = $airisModuleClassSuffixParamValueSubstring;
                }
            }
        }
    }
}

// Unique HTML id attribute value used for each Bootstrap carousel on current page
$carouselId = 'airis-module-articles-news-carousel-' . $module->id;

?>

<div class="airis-module-articles-news-carousel<?php echo $moduleClassSuffix; ?>">

    <?php // TODO: Replace is_array() && instanceof Countable with is_countable() once were on PHP 7.3+ and Joomla! 5+ for good ?>
    <?php if (isset($list) && is_array($list) && count($list)) : ?>

        <?php $slidesTotal = count($list); ?>

        <div class="airis-module-articles-news-carousel__container carousel slide" id="<?php echo $carouselId; ?>">

            <?php if (!$airisModuleClassSuffixParams['carousel-disable-indicators']) : ?>

                <ol class="airis-module-articles-news-carousel__indicators carousel-indicators unstyled">

                    <?php for($currentSlide = 0; $currentSlide < $slidesTotal; $currentSlide++) : ?>

                        <?php
                            $indicatorClasses = 'airis-module-articles-news-carousel__indicator';

                            // Bootstrap default would be 'always' which doesn't require any additonal classses
                            if ($airisModuleClassSuffixParams['carousel-visibility-indicators'] === 'hover') {
                                $indicatorClasses = 'airis-module-articles-news-carousel__indicator_hover-only ' . $indicatorClasses;
                            }

                            // The first indicator is always marked as active
                            if ($currentSlide === 0) {
                                $indicatorClasses = "airis-module-articles-news-carousel__indicator_active $indicatorClasses active";
                            }
                        ?>

                        <li class="<?php echo $indicatorClasses; ?>" data-target="<?php echo $carouselId; ?>" data-slide-to="<?php echo $currentSlide; ?>"></li>

                    <?php endfor; ?>

                </ol>

            <?php endif; ?>

            <ul class="airis-module-articles-news-carousel__items carousel-inner">
                <?php
                    foreach ($list as $item) {
                        require $moduleSublayoutFileName;
                    }
                ?>
            </ul>

            <?php if (!$airisModuleClassSuffixParams['carousel-disable-controls']) : ?>

                <?php
                    $controlClassesBase = 'airis-module-articles-news-carousel__control';
                    $controlIconClassesBase = 'airis-module-articles-news-carousel__control-icon';
                    $controlPreviousContent = "<span class=\"$controlIconClassesBase-previous $controlIconClassesBase\" aria-hidden=\"true\">&lsaquo;</span>";
                    $controlNextContent = "<span class=\"$controlIconClassesBase-next $controlIconClassesBase\" aria-hidden=\"true\">&rsaquo;</span>";

                    // Use Font Awesome icons if the font is loaded
                    if ($templateParameters->get('loadFontAwesome')) {
                        $controlPreviousContent = "<span class=\"$controlIconClassesBase-previous $controlIconClassesBase fas fa-chevron-left\" aria-hidden=\"true\"></span>";
                        $controlNextContent = "<span class=\"$controlIconClassesBase-next $controlIconClassesBase fas fa-chevron-right\" aria-hidden=\"true\"></span>";
                    }

                    echo HTMLHelper::link(
                        '#' . $carouselId,
                        $controlPreviousContent,
                        [
                            'class' => "{$controlClassesBase}_direction_left $controlClassesBase carousel-control left",
                            'title' => Text::_('TPL_AIRIS_MOD_ARTICLES_NEWS_CAROUSEL_PREVIOUS_ITEM_BTN_TITLE'),
                            'data-slide' => 'prev',
                        ],
                    );

                    echo HTMLHelper::link(
                        '#' . $carouselId,
                        $controlNextContent,
                        [
                            'class' => "{$controlClassesBase}_direction_right $controlClassesBase carousel-control right",
                            'title' => Text::_('TPL_AIRIS_MOD_ARTICLES_NEWS_CAROUSEL_NEXT_ITEM_BTN_TITLE'),
                            'data-side' => 'next',
                        ],
                    );
                ?>

            <?php endif; ?>

        </div>

    <?php else : ?>

        <div class="airis-module-empty airis-module-articles-news-carousel-empty" data-nosnippet>
            <p class="airis-module-empty__message airis-module-articles-news-carousel-empty__message">
                <?php echo Text::_('TPL_AIRIS_MOD_ARTICLES_NEWS_CAROUSEL_NO_ITEMS'); ?>
            </p>
        </div>

    <?php endif; ?>

</div>