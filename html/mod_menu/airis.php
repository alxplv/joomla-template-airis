<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
// use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;

// Basic handles
$joomlaApplication = Factory::getApplication();
$menuLevelStart = $params->get('startLevel', 1);
$menuLevelEnd = $params->get('endLevel', 0);
$menuType = htmlspecialchars(trim($params->get('menutype', '')), ENT_QUOTES, 'UTF-8'); // TODO: Probably should fallback to menu ID in case if this menu has no menutype string set for some reason
$webAssets = $joomlaApplication->getDocument()->getWebAssetManager();

// Module options
$menuClass = htmlspecialchars(trim($params->get('class_sfx', '')), ENT_QUOTES, 'UTF-8');
$menuTitle = htmlspecialchars(trim($module->title), ENT_QUOTES, 'UTF-8'); // Use module title as menu title since Factory::getApplication()->getMenu() has no means to access to the actual menu title
$menuId = htmlspecialchars(trim($params->get('tag_id', '')), ENT_QUOTES, 'UTF-8');
$targetPosition = ''; // TODO: Investigate the usage of the "Target Position" module option and use it here

$moduleClassPrefix = 'airis-module-menu';
$BemModificatorMenuType = "_menu-type_$menuType";

$menuAttributes = [
    'class' => "{$moduleClassPrefix}__list {$moduleClassPrefix}__list_level_start navbar-nav",
];

// Process module options
if ($menuClass !== '') {
    $menuAttributes['class'] .= " $menuClass";
}

if ($menuId !== '') {
    $menuAttributes['id'] = "{$moduleClassPrefix}_id_$menuId";
}

// Enable Bootstrap if it is absent
if ($webAssets->assetExists('style', 'bootstrap.css') && $webAssets->isAssetActive('style', 'bootstrap.css') === false) {
    $webAssets->useStyle('bootstrap.css');
}

if ($webAssets->assetExists('script', 'bootstrap.collapse') && $webAssets->isAssetActive('script', 'bootstrap.collapse') === false) {
    $webAssets->useScript('bootstrap.collapse');
}

// Bootstrap Navbars also optionally depend on BS Dropdown JS for displaying child items
if ($params->get('showAllChildren', 1)) {
    // TODO: There should be a better way to determine the presence of child menu items
    foreach ($list as $menuItem) {
        if ($menuItem->deeper) {
            if ($webAssets->assetExists('script', 'bootstrap.dropdown') && $webAssets->isAssetActive('script', 'bootstrap.dropdown') === false) {
                $webAssets->useScript('bootstrap.dropdown');
            }
            break;
        }
    }
}

// Unique BS collapse id
$collapseId = "{$moduleClassPrefix}__collapse_id_" . md5($module->id . hrtime(true));

// TODO: Remove this when we add support for every menu item type
$allowedMenuItemTypes = [
    'alias',
    'component',
    'url',
];

?>

<?php // TODO: Add an airis-module-param- to switch the .navbar-expand{-sm|-md|-lg|-xl|-xxl} HTML class to change BS breakpoints for toggler display ?>
<nav class="<?php echo "$moduleClassPrefix {$moduleClassPrefix}{$BemModificatorMenuType}"; ?> navbar navbar-expand-lg">
    <div class="<?php echo "{$moduleClassPrefix}__container {$moduleClassPrefix}__container$BemModificatorMenuType"; ?> container-fluid">

        <?php // Bootstrap menu brand support enabled via proxy layout ?>
        <?php if (isset($menuBrandContent) && $menuBrandContent !== '') : ?>
            <?php echo $menuBrandContent; ?>
        <?php endif; ?>

        <button class="<?php echo "{$moduleClassPrefix}__toggler {$moduleClassPrefix}__toggler$BemModificatorMenuType"; ?> navbar-toggler" type="button" title="<?php echo Text::_('TPL_AIRIS_MAIN_MENU_BTN_TITLE'); ?>" data-bs-toggle="collapse" data-bs-target="#<?php echo $collapseId; ?>" aria-controls="<?php echo $collapseId; ?>" aria-expanded="false" aria-label="<?php echo $menuTitle; ?>">
            <?php
                $togglerIconAttributes = [
                    'class' => "{$moduleClassPrefix}__toggler-icon {$moduleClassPrefix}__toggler-icon$BemModificatorMenuType",
                ];

                // Use Font Awesome for dropdown toggler button icon if possible
                if ($webAssets->assetExists('style', 'fontawesome') && $webAssets->isAssetActive('style', 'fontawesome')) {
                    $togglerIconAttributes['class'] .= ' fas fa-bars';
                    $togglerIconAttributes['aira-hidden'] = 'true';
                } else {
                    $togglerIconAttributes['class'] .= ' navbar-toggler-icon';
                }
            ?>

            <span <?php echo ArrayHelper::toString($togglerIconAttributes); ?>></span>
        </button>

        <div class="<?php echo "{$moduleClassPrefix}__collapse {$moduleClassPrefix}__collapse$BemModificatorMenuType"; ?> collapse navbar-collapse" id="<?php echo $collapseId; ?>">
            <ul <?php echo ArrayHelper::toString($menuAttributes); ?>>

                <?php foreach ($list as $menuItem) : ?>

                    <?php
                        // TODO: Add support of all the rest menu item types
                        if (!in_array($menuItem->type, $allowedMenuItemTypes)) {
                            continue;
                        }

                        $menuItemHtml = '';
                        $menuItemIconClass = htmlspecialchars(trim($menuItem->menu_icon), ENT_QUOTES, 'UTF-8');
                        $menuItemImageSrc = htmlspecialchars(trim($menuItem->menu_image), ENT_QUOTES, 'UTF-8');
                        $menuItemLevelValue = ($menuItem->level === $menuLevelStart) ? 'start' : (string) $menuItem->level;
                        $menuItemParams = $menuItem->getParams();
                        $menuItemText = htmlspecialchars(trim($menuItem->title), ENT_QUOTES, 'UTF-8');
                        $menuItemTitle = $menuItemText;
                        $menuListItemClasses = "nav-item {$moduleClassPrefix}__item {$moduleClassPrefix}__item_type_$menuItem->type {$moduleClassPrefix}__item_id_$menuItem->id {$moduleClassPrefix}__item_level_$menuItemLevelValue {$moduleClassPrefix}__item$BemModificatorMenuType";

                        $menuItemAttributes = [
                            'class' => "{$moduleClassPrefix}__link {$moduleClassPrefix}__link_type_$menuItem->type {$moduleClassPrefix}__link_id_$menuItem->id {$moduleClassPrefix}__link_level_$menuItemLevelValue {$moduleClassPrefix}__link$BemModificatorMenuType",
                        ];

                        // Hide menu item text if required
                        if ($menuItemParams->get('menu_text', 1) === 0) {
                            $menuItemText = "<span class=\"{$moduleClassPrefix}__link-text_visually-hidden visually-hidden\">$menuItemText</span>";
                            $menuListItemClasses .= " {$moduleClassPrefix}__item_text-hidden";
                        }

                        // Icon takes priorty over image as hinted in the Joomla!'s com_menus item edit layout
                        if ($menuItemIconClass) {
                            $menuItemIconClasses = "{$moduleClassPrefix}__icon {$moduleClassPrefix}__icon_level_$menuItemLevelValue {$moduleClassPrefix}__icon$BemModificatorMenuType";

                            if ($menuItem->level > $menuLevelStart) {
                                $menuItemIconClasses .= " {$moduleClassPrefix}__icon_dropdown";
                            }

                            if ($menuItemParams->get('menu_text', 1) === 0) {
                                $menuItemIconClasses .= " {$moduleClassPrefix}__icon_text-hidden";
                            }

                            $menuItemText = "<span class=\"$menuItemIconClasses $menuItemIconClass\" aria-hidden=\"true\"></span>$menuItemText";
                        } elseif ($menuItemImageSrc) {
                            $menuItemImageAttributes = [
                                'class' => "{$moduleClassPrefix}__image {$moduleClassPrefix}__image_level_$menuItemLevelValue {$moduleClassPrefix}__image$BemModificatorMenuType",
                            ];

                            if ($menuItem->level > $menuLevelStart) {
                                $menuItemImageAttributes['class'] .= " {$moduleClassPrefix}__image_dropdown";
                            }

                            if ($menuItemParams->get('menu_text', 1) === 0) {
                                $menuItemImageAttributes['class'] .= " {$moduleClassPrefix}__image_text-hidden";
                            }

                            $menuItemImageClass = htmlspecialchars(trim($menuItem->menu_image_css), ENT_QUOTES, 'UTF-8');
                            if ($menuItemImageClass !== '') {
                                $menuItemImageAttributes['class'] .= " $menuItemImageClass";
                            }

                            $menuItemImageHtml = HTMLHelper::image(
                                $menuItemImageSrc,
                                $menuItemTitle,
                                $menuItemImageAttributes,
                            );

                            $menuItemText = $menuItemImageHtml . $menuItemText;
                        }

                        // Additional classes for use with CSS
                        if ($menuItem->level > $menuLevelStart) {
                            $menuListItemClasses .= " {$moduleClassPrefix}__item_dropdown";
                        }

                        // TODO: Replace is_array() && instanceof Countable with is_countable() once we're on PHP 7.3+ for good
                        if (isset($path) && is_array($path) && $path instanceof Countable) {
                            $menuItemParentItemsCount = count($path);

                            if ($menuItemParentItemsCount !== 0 && in_array($menuItem->id, $path)) {
                                $menuItemAttributes['class'] .= " {$moduleClassPrefix}__link_active";
                                $menuListItemClasses .= " {$moduleClassPrefix}__item_active";
                            } elseif ($menuItem->type === 'alias') {
                                $aliasMenuItemTargetId = $menuItemParams->get('aliasoptions');

                                // TODO: Not sure if we need this at all (directly based the default mod_menu layout)
                                if ($menuItemParentItemsCount !== 0 && $aliasMenuItemTargetId === $path[$menuItemParentItemsCount - 1]) {
                                    $menuItemAttributes['class'] .= " {$moduleClassPrefix}__link_active";
                                    $menuListItemClasses .= " {$moduleClassPrefix}__item_active";
                                } elseif (in_array($aliasMenuItemTargetId, $path)) {
                                    $menuListItemClasses .= " {$moduleClassPrefix}__item_type_{$menuItem->type}_has-active-parent";
                                }
                            }
                        }

                        if ($menuItem->id === $active_id || ($menuItem->type === 'alias' && $menuItemParams->get('aliasoptions') === $active_id)) {
                            $menuItemAttributes['aria-current'] = 'page';
                            $menuItemAttributes['class'] .= " {$moduleClassPrefix}__link_current";
                            $menuListItemClasses .= " {$moduleClassPrefix}__item_current";
                        }

                        if ($menuItem->id === $default_id) {
                            $menuItemAttributes['class'] .= " {$moduleClassPrefix}__link_default";
                            $menuListItemClasses .= " {$moduleClassPrefix}__item_default";
                        }

                        if ($menuItem->deeper) {
                            $menuItemAttributes['aria-expanded'] = 'false';
                            $menuItemAttributes['class'] .= ' dropdown-toggle';
                            $menuItemAttributes['data-bs-toggle'] = 'dropdown';
                            $menuItemAttributes['role'] = 'button';
                            $menuListItemClasses .= " {$moduleClassPrefix}__item_deeper dropdown";
                        }

                        if ($menuItem->parent) {
                            $menuListItemClasses .= " {$moduleClassPrefix}__item_parent";
                        }

                        switch ($menuItem->type) {
                            case 'separator':
                                $menuItemHtml = "<span class=\"{$moduleClassPrefix}__$menuItem->type dropdown-item-text\">$menuItemText</span>";
                                break;
                            // Bootstrap also supports <hr>-based menu dividers but there is no suitable menu item type in the Joomla! menu system
                            // case '':
                                // $menuItemHtml = "<hr class=\"dropdown-divider\">";
                                // break;
                            case 'heading':
                                $menuItemHtml = "<h6 class=\"{$moduleClassPrefix}__$menuItem->type dropdown-header\">$menuItemText</h6>";
                                break;
                            case 'url':
                                // all the rest item types are a link
                                // no break
                            default:
                                // All links below the start menu level should have an extra HTML class for proper BS Dropdown styling
                                if ($menuItem->level > $menuLevelStart) {
                                    $menuItemAttributes['class'] .= ' nav-link dropdown-item';
                                } else {
                                    $menuItemAttributes['class'] .= ' nav-link';
                                }

                                $menuItemAttributeClassCustom = htmlspecialchars(trim($menuItem->anchor_css), ENT_QUOTES, 'UTF-8');
                                $menuItemAttributeTitle = htmlspecialchars(trim($menuItem->anchor_title), ENT_QUOTES, 'UTF-8');
                                $menuItemAttributeRel = htmlspecialchars(trim($menuItem->anchor_rel), ENT_QUOTES, 'UTF-8');

                                if ($menuItemAttributeClassCustom !== '') {
                                    $menuItemAttributes['class'] .= " $menuItemAttributeClassCustom";
                                }

                                if ($menuItemAttributeTitle !== '') {
                                    $menuItemAttributes['title'] = $menuItemAttributeTitle;
                                }

                                if ($menuItemAttributeRel !== '') {
                                    if (isset($menuItemAttributes['rel']) && $menuItemAttributes['rel'] !== '') {
                                        $menuItemAttributes['rel'] .= " $menuItemAttributeRel";
                                    } else {
                                        $menuItemAttributes['rel'] = $menuItemAttributeRel;
                                    }
                                }

                                switch ($menuItem->browserNav) {
                                    case 1:
                                        $menuItemAttributes['target'] = '_blank';

                                        if (isset($menuItemAttributes['rel']) && $menuItemAttributes['rel'] !== '') {
                                            if (strpos($menuItemAttributes['rel'], 'noopener') === false) {
                                                $menuItemAttributes['rel'] .= ' noopener';
                                            }

                                            if (strpos($menuItemAttributes['rel'], 'noreferrer') === false) {
                                                $menuItemAttributes['rel'] .= ' noreferrer';
                                            }
                                        } else {
                                            $menuItemAttributes['rel'] = 'noopener noreferrer';
                                        }

                                        if (isset($menuItemAttributes['rel']) && $menuItemAttributes['rel'] !== '' && strpos($menuItemAttributes['rel'], 'nofollow') === false) {
                                            $menuItemAttributes['rel'] .= ' nofollow';
                                        } else {
                                            $menuItemAttributes['rel'] = 'nofollow';
                                        }

                                        break;
                                    case 2:
                                        // TODO: Replace the onlick inline code via a proper JS function added to the current document with addInlineScript()
                                        $menuItemAttributes['onclick'] = 'window.open(this.href, &quot;targetWindow&quot;, &quot;toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes&quot;); return false;';
                                        break;
                                    default:
                                        // do nothing
                                        break;
                                }

                                $menuItemHtml = HTMLHelper::link(
                                    // TODO: Not sure if we need this filter at all but that's what the stock layout of mod_menu does
                                    OutputFilter::ampReplace(htmlspecialchars(
                                        $menuItem->flink,
                                        ENT_COMPAT,
                                        'UTF-8',
                                        false,
                                    )),
                                    $menuItemText,
                                    $menuItemAttributes,
                                );
                                break;
                        }
                    ?>

                    <li class="<?php echo $menuListItemClasses; ?>">

                        <?php echo $menuItemHtml; ?>

                        <?php if ($menuItem->deeper) : ?>
                            <ul class="dropdown-menu <?php echo "{$moduleClassPrefix}__list {$moduleClassPrefix}__list_dropdown {$moduleClassPrefix}__list_level_$menuItemLevelValue"; ?>">
                        <?php elseif ($menuItem->shallower) : ?>
                            </li>
                            <?php // echo str_repeat('</ul></li>', $menuItem->level_diff); ?>
                            <?php for ($i = 1; $i < $menuItem->level_diff; $i++) : ?>
                                    </ul>
                                </li>
                            <?php endfor; ?>
                        <?php else : ?>
                            </li>
                        <?php endif; ?>

                    <?php
                        // Disabled item (not used by the Joomla! menu system)
                        /*
                            <li class="nav-item">
                                <a class="nav-link disabled" aria-disabled="true">Disabled</a>
                            </li>
                        */

                        // TODO: Implement split buttons for parent menu items which are not separators or headings and have an actual link:
                        // https://getbootstrap.com/docs/5.3/components/dropdowns/#split-button
                        /*
                            <a href="#" class="">Action</a>
                            <!-- Add this split section for non-separator and non-heading type menu items ->
                            <a href="#"class="dropdown-toggle dropdown-toggle-split" title="<?php echo Text::_('TPL_AIRIS_MAIN_MENU_BTN_TITLE'); ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="visually-hidden"><?php echo Text::_('TPL_AIRIS_MAIN_MENU_BTN_TITLE'); ?></span>
                            </a>
                            <ul class="dropdown-menu"></ul>
                        */
                    ?>

                <?php endforeach; ?>

            </ul>

            <?php
                // TODO: Send navbar form submits to Joomla!'s Smart Search or to VM's search. Control this section with airis-module-option CSS class prefixes.
                /*
                    <form class="d-flex" role="search">
                        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                */
            ?>

        </div>

    </div>
</nav>