<?php

// https://getbootstrap.com/2.3.2/javascript.html#collapse

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// Get the current Joomla! template along with its options
$joomlaCurrentTemplate = Factory::getApplication()->getTemplate(true);

// Get template options for Font Awesome
$fontAwesomeLoaded = $joomlaCurrentTemplate->params->get('loadFontAwesome');

// Use Font Awesome for accordion toggle links if available
$accordionToggleLinkContent = $fontAwesomeLoaded ? '<span class="fas fa-chevron-down virtuemart-module-category-accordion-heading-toggle-link-icon" aria-hidden="true"></span>' : HTMLHelper::image("templates/$joomlaCurrentTemplate/images/icons/airis-chevron-down.svg", array('class' => 'airis-svg airis-svg-chevron-down virtuemart-module-category-accordion-heading-toggle-link-icon'));

// Making sure to have a unique Bootstrap accordion id for each module
$accordionId = 'virtuemart-module-category-accordion-' . $this->module_id;

// Base category link href string for Joomla! router
$categoryHrefBase = 'index.php?option=com_virtuemart&view=category&virtuemart_category_id=';

?>

<?php // Using the full Bootstrap accordion markup to ensure correct operation when multiple mod_virtuemart_category modules with this template exist on a page ?>
<div class="accordion virtuemart-module-category-accordion<?php echo $class_sfx; ?>" id="<?php echo $accordionId; ?>">
    <?php foreach ($categories as $category) : ?>
        <div class="accordion-group virtuemart-module-category-accordion-group">

            <?php $categoryLink = HTMLHelper::link($categoryHrefBase . $category->virtuemart_category_id, htmlspecialchars(trim(vmText::_($category->category_name))), array('class' => 'virtuemart-module-category-accordion-heading-link')); ?>

            <?php if ($level >= 1 && !empty($category->childs)) : ?>

                <div class="accordion-heading virtuemart-module-category-accordion-heading airis-flex">

                    <?php $accordionBodyId = "$accordionId-body-$category->virtuemart_category_id"; ?>

                    <div class="virtuemart-module-category-accordion-heading-container">
                        <?php echo $categoryLink; ?>
                    </div>

                    <div class="virtuemart-module-category-accordion-heading-toggle">
                        <?php echo HTMLHelper::link($accordionBodyId, $accordionToggleLinkContent, array('class' => 'accordion-toggle virtuemart-module-category-accordion-heading-toggle-link', 'title' => Text::_('TPL_AIRIS_MOD_VIRTUEMART_CATEGORY_ACCORDION_TOGGLE_BTN_TITLE'), 'rel' => 'nofollow', 'data-toggle' => 'collapse', 'data-parent' => $accordionId)); ?>
                    </div>

                </div>

                <?php // TODO: Probably should output each category ID as data attribute for their respective links and output $parentCategories as JS variables via Joomla API. This way we will be able to collapse in proper accordion bodies during Category Ajax Navigation. Alternatively we can write a script which will collapse in a required accordion body based on location of child category link marked as active. This way we can skip pre-collapsing parent body server side and do this entierly in JS at all times both for normal page load and AJAX update. (More on that here: https://docs.joomla.org/J3.x:Adding_JavaScript_and_CSS_to_the_page) ?>
                <div class="accordion-body virtuemart-module-category-accordion-body collapse<?php if (in_array($category->virtuemart_category_id, $parentCategories)) echo ' ', ' in'; ?>" id="<?php echo $accordionBodyId; ?>">
                    <div class="accordion-inner virtuemart-module-category-accordion-inner">
                        <?php foreach ($category->childs as $childCategory) : ?>
                            <div class="virtuemart-module-category-accordion-inner-item">
                                <?php
                                    // Mark active child category link with an additional class
                                    $childCategoryLinkAttributes = array('class' => 'virtuemart-module-category-accordion-inner-item-link');
                                    if ($category->virtuemart_category_id == $active_category_id) $childCategoryLinkAttributes['class'] .= ' virtuemart-module-category-accordion-inner-item-link-active';

                                    echo HTMLHelper::link($categoryHrefBase . $childCategory->virtuemart_category_id, htmlspecialchars(trim(vmText::_($childCategory->category_name))), $childCategoryLinkAttributes);
                                ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            <?php else : ?>
                <div class="accordion-heading virtuemart-module-category-accordion-heading">
                    <?php echo $categoryLink; ?>
                </div>
            <?php endif; ?>

        </div>
    <?php endforeach; ?>
</div>