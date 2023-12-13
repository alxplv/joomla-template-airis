<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;
// use Joomla\CMS\Filter\FilterOutput;
use Joomla\Filter\OutputFilter;

$moduleClassPrefix = 'airis-module-menu-flat';

// Output only items with these menu item types
$allowedItemTypes = [
    'alias',
    'component',
    'url',
];

// Selection of Joomla!'s menu item options to be used as HTML link tag attributes
$usableLinkAttributes = [
    'class' => 'anchor_css',
    'title' => 'anchor_title',
    'rel' => 'anchor_rel',
];

?>

<div class="<?php echo $moduleClassPrefix, $class_sfx; ?>" id="<?php echo $params->get('tag_id', "$moduleClassPrefix-$module->id"); ?>">

    <?php // TODO: Replace is_array() && instanceof Countable with is_countable() once we're on PHP 7.3+ for good ?>
    <?php if (isset($list) && is_array($list) && $list instanceof Countable) : ?>
        <?php
            $menuItemsTotal = count($list);

            // Remove menu items types not suitable for a flat link list output
            if ($menuItemsTotal) {
                for ($i = 0; $i < $menuItemsTotal; $i++) {
                    if (!in_array($list[$i]->type, $allowedItemTypes)) {
                        unset($list[$i]);
                    }
                }

                // Just in case
                $list = array_values($list);
                $menuItemsTotal = count($list);
            }

            $menuHasItems = (bool) $menuItemsTotal;
        ?>

        <?php if ($menuHasItems) : ?>
            <ul class="<?php echo $moduleClassPrefix; ?>__list unstyled">

                <?php foreach ($list as $menuItem) : ?>
                    <?php
                        $itemContainerClasses = "{$moduleClassPrefix}__item module-menu-item-$menuItem->id item-$menuItem->id";
                        $itemAnchorCSS = htmlspecialchars(trim($menuItem->anchor_css));

                        // Prefix all defined menu item classes for their use by item container
                        if ($itemAnchorCSS !== '') {
                            $itemAnchorCSSChunks = explode(' ', $itemAnchorCSS);

                            foreach ($itemAnchorCSSChunks as $itemAnchorCSSChunk) {
                                $itemContainerClasses .= " $itemAnchorCSSChunk";
                            }
                        }
                    ?>

                    <li class="<?php echo $itemContainerClasses; ?>">
                        <?php
                            $menuItemLinkAttributes = [
                                'class' => "{$moduleClassPrefix}__link module-menu-link-$menuItem->id",
                            ];

                            // Process menu item options into array of applicable link attributes
                            foreach ($usableLinkAttributes as $linkAttributeName => $linkAttributeValueRaw) {
                                // Joomla! doesn't care about whitespace or special characters but we do
                                $linkAttributeValue = htmlspecialchars(trim($menuItem->$linkAttributeValueRaw));

                                if ($linkAttributeValue !== '') {
                                    $menuItemLinkAttributes[$linkAttributeName] .= $linkAttributeValue;
                                }
                            }

                            $menuItemLinkAttributes['class'] = trim($menuItemLinkAttributes['class']);

                            // Express both positive Target Window option values as target="_blank" attribute
                            if ($menuItem->browserNav) {
                                $menuItemLinkAttributes['target'] = '_blank';
                            }

                            // TODO: Not sure if we need ampReplace filter at all
                            echo HTMLHelper::link(
                                // FilterOutput::ampReplace($menuItem->flink),
                                OutputFilter::ampReplace($menuItem->flink),
                                htmlspecialchars(trim($menuItem->title)),
                                $menuItemLinkAttributes,
                            );
                        ?>
                    </li>

                <?php endforeach; ?>

            </ul>
        <?php endif; ?>

    <?php endif; ?>

</div>