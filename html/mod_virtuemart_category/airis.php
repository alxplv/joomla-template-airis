<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// Process module data and options
$menuClassSuffix = airisPrepareHTMLClassSuffix($params->get('class_sfx', ''));
$moduleClassSuffix = airisPrepareHTMLClassSuffix($params->get('moduleclass_sfx', ''));
// TODO: Replace is_array() && instancof Countable with is_countable() once we're on PHP 7.3+ for good
$moduleHasCategories = isset($categories) && is_array($categories) && $categories instanceof Countable && count($categories);

function airisPrepareHTMLClassSuffix(string $classSuffix)
{
    // Not using trim() here to keep possible and perfectly acceptable leading whitespace
    $classSuffix = htmlspecialchars(rtrim($classSuffix), ENT_QUOTES, 'UTF-8');

    // Remove non-singular whitespaces
    $classSuffix = preg_replace('/\s{2,}/', ' ', $classSuffix);

    return $classSuffix;
}

?>

<div class="airis-module-virtuemart-category<?php echo $moduleClassSuffix; ?>">

    <?php if ($moduleHasCategories) : ?>

        <?php
            // Base category link href string for Joomla! Router
            $categoryHrefBase = 'index.php?option=com_virtuemart&view=category&virtuemart_category_id=';

            // Marker used to skip unnecessary checks
            $isActiveLevel0CategoryFound = false;
        ?>

        <ul class="airis-module-virtuemart-category__list_level_0 airis-module-virtuemart-category__list<?php echo $menuClassSuffix; ?> unstyled">
            <?php foreach ($categories as $level0Category) : ?>

                <?php
                    $level0CategoryItemClasses = 'airis-module-virtuemart-category__item_level_0 airis-module-virtuemart-category__item';

                    // Apply additional classes to active category link and its containing list item
                    if (!$isActiveLevel0CategoryFound) {
                        if ($level0Category->virtuemart_category_id === $active_category_id) {
                            $isActiveLevel0CategoryFound = true;
                            $isActiveLevel0Category = true;
                            // $level0CategoryItemClasses .= ' airis-module-virtuemart-category__item_active';
                            // Insert additonal class at the middle of existing class list than just appending it
                            $level0CategoryItemClasses = substr_replace(
                                $level0CategoryItemClasses,
                                ' airis-module-virtuemart-category__item_active',
                                strpos($level0CategoryItemClasses, '0'), // The $needle here represents menu item level here so it can be easily leveraged in case if we ever rewrite this layout to support any number of nested categories
                                0,
                            );
                        }
                    }
                ?>

                <li class="<?php echo $level0CategoryItemClasses; ?>">

                    <?php
                        if ($isActiveLevel0Category) {
                            $level0CategoryLinkClasses = 'airis-module-virtuemart-category__link_level_0 airis-module-virtuemart-category__link_active airis-module-virtuemart-category__link';
                        } else {
                            $level0CategoryLinkClasses = 'airis-module-virtuemart-category__link_level_0 airis-module-virtuemart-category__link';
                        }

                        $level0CategoryLink = HTMLHelper::link(
                            $categoryHrefBase . $level0Category->virtuemart_category_id,
                            htmlspecialchars(trim(vmText::_($level0Category->category_name)), ENT_QUOTES, 'UTF-8'),
                           ['class' => $level0CategoryLinkClasses],
                        );
                    ?>

                    <?php // TODO: Replace is_array() with is_countable once we are on PHP 7.3+ for good ?>
                    <?php if ($level >= 1 && isset($level0Category->childs) && is_array($level0Category->childs) && count($level0Category->childs)) : ?>

                        <?php // Additional container element for simplier CSS styling in presense of child categories list element ?>
                        <?php if ($isActiveLevel0Category) : ?>
                            <div class="airis-module-virtuemart-category__link-container_level_0 airis-module-virtuemart-category__link-container_active airis-module-virtuemart-category__link-container">
                        <?php else : ?>
                            <div class="airis-module-virtuemart-category__link-container_level_0 airis-module-virtuemart-category__link-container">
                        <?php endif; ?>
                            <?php echo $level0CategoryLink; ?>
                        </div>

                        <ul class="airis-module-virtuemart-category__list_level_1 airis-module-virtuemart-category__list<?php echo $menuClassSuffix; ?> unstyled">

                            <?php foreach ($level0Category->childs as $level1Category) : ?>

                                <?php
                                    $level1CategoryItemClasses = 'airis-module-virtuemart-category__item_level_1 airis-module-virtuemart-category__item';

                                    if (!$isActiveLevel0CategoryFound) {
                                        if (in_array($level1Category->virtuemart_category_id, $parentCategories)) {
                                            $isActiveLevel1Category = true;
                                            $level1CategoryItemClasses = substr_replace(
                                                $level1CategoryItemClasses,
                                                ' airis-module-virtuemart-category__item_active',
                                                strpos($level1CategoryItemClasses, '1'),
                                                0,
                                            );
                                        }
                                    }
                                ?>

                                <li class="<?php echo $level1CategoryItemClasses; ?>">

                                    <?php
                                        if ($isActiveLevel1Category) {
                                            $level1CategoryLinkClasses = 'airis-module-virtuemart-category__link_level_1 airis-module-virtuemart-category__link_active airis-module-virtuemart-category__link';
                                        } else {
                                            $level1CategoryLinkClasses = 'airis-module-virtuemart-category__link_level_1 airis-module-virtuemart-category__link';
                                        }

                                        $level1CategoryLink = HTMLHelper::link(
                                            $categoryHrefBase . $level1Category->virtuemart_category_id,
                                            htmlspecialchars(trim(vmText::_($level1Category->category_name)), ENT_QUOTES, 'UTF-8'),
                                            ['class' => $level1CategoryLinkClasses],
                                        );
                                    ?>

                                    <?php if ($level >= 2 && !empty($level1Category->childs)) : ?>

                                        <?php if ($isActiveLevel1Category) : ?>
                                            <div class="airis-module-virtuemart-category__link-container_level_1 airis-module-virtuemart-category__link-container_active airis-module-virtuemart-category__link-container">
                                        <?php else : ?>
                                            <div class="airis-module-virtuemart-category__link-container_level_1 airis-module-virtuemart-category__link-container">
                                        <?php endif; ?>
                                            <?php echo $level1CategoryLink; ?>
                                        </div>

                                        <ul class="airis-module-virtuemart-category__list_level_2 airis-module-virtuemart-category__list<?php echo $menuClassSuffix; ?> unstyled">

                                            <?php foreach ($level1Category->childs as $level2Category) : ?>

                                                <?php
                                                    // If this category is currently being browsed
                                                    if (in_array($level2Category->virtuemart_category_id, $parentCategories))
                                                    {
                                                        $level2CategoryItemClasses = 'airis-module-virtuemart-category__item_level_2 airis-module-virtuemart-category__link_active airis-module-virtuemart-category__item';
                                                        $level2CategoryLinkClasses = 'airis-module-virtuemart-category__link_level_2 airis-module-virtuemart-category__link_active airis-module-virtuemart-category__link';
                                                    } else {
                                                        $level2CategoryItemClasses = 'airis-module-virtuemart-category__item_level_2 airis-module-virtuemart-category__item';
                                                        $level2CategoryLinkClasses = 'airis-module-virtuemart-category__link_level_2 airis-module-virtuemart-category__link';
                                                    }

                                                    $level2CategoryLink = HTMLHelper::link(
                                                        $categoryHrefBase . $level2Category->virtuemart_category_id,
                                                        htmlspecialchars(trim(vmText::_($level2Category->category_name)), ENT_QUOTES, 'UTF-8'),
                                                        ['class' => $level2CategoryLinkClasses],
                                                    );
                                                ?>

                                                <li class="<?php echo $level2CategoryItemClasses; ?>">
                                                    <?php echo $level2CategoryLink; ?>
                                                </li>

                                            <?php endforeach; ?>

                                        </div>

                                    <?php else : ?>
                                        <?php echo $level1CategoryLink; ?>
                                    <?php endif; ?>

                                </li>

                            <?php endforeach; ?>

                        </ul>

                    <?php else : ?>
                        <?php echo $level0CategoryLink; ?>
                    <?php endif; ?>

                </li>

            <?php endforeach; ?>
        </ul>

    <?php else : ?>

        <div class="airis-module-virtuemart-category-empty airis-module-empty" data-nosnippet>
            <p class="airis-module-virtuemart-category-empty__message airis-module-empty__message">
                <?php echo Text::_('TPL_AIRIS_MOD_VIRTUEMART_CATEGORY_NO_CATEGORIES'); ?>
            </p>
        </div>

    <?php endif; ?>

</div>