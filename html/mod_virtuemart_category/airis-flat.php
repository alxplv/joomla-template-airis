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

<div class="airis-module-virtuemart-category-flat<?php echo $moduleClassSuffix; ?>">

    <?php if ($moduleHasCategories) : ?>

        <ul class="airis-module-virtuemart-category-flat__list<?php echo $menuClassSuffix; ?> unstyled">
            <?php foreach ($categories as $category) : ?>
                <li class="airis-module-virtuemart-category-flat__item">
                    <?php
                        echo HTMLHelper::link(
                            'index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id,
                            htmlspecialchars(trim(vmText::_($category->category_name)), ENT_QUOTES, 'UTF-8'),
                            ['class' => 'airis-module-virtuemart-category-flat__link'],
                        );
                    ?>
                </li>
            <?php endforeach; ?>
        </ul>

    <?php else : ?>

        <div class="airis-module-virtuemart-category-flat-empty airis-module-empty" data-nosnippet>
            <p class="airis-module-virtuemart-category-flat-empty__message airis-module-empty__message">
                <?php echo Text::_('TPL_AIRIS_MOD_VIRTUEMART_CATEGORY_NO_CATEGORIES'); ?>
            </p>
        </div>

    <?php endif; ?>

</div>