<?php

// Airis Template
// Copyright (C) 2016 alxplv <https://github.com/alxplv>
// Licensed under the GNU General Public License Version 3; see LICENSE

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\Factory;
use Joomla\CMS\Document\Document;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\WebAsset\WebAssetItem;

// Basic handles
$applicationMenu = Factory::getApplication()->getMenu();
$currentDocument = Factory::getApplication()->getDocument();
$currentMenuItem = $applicationMenu->getActive();
$currentUri = Uri::getInstance();
$templateMediaUriPrefix = "media/templates/site/$this->template"; // FIXME: Only using this extra variable because WebAsset mechanism doesn't pick up relative URIs for some reason
$templateUriPrefix = "templates/$this->template";
$webAssets = $currentDocument->getWebAssetManager();

// Document metadata that should be included at all times
$this->setMetaData('viewport', 'width=device-width, initial-scale=1.0');

//
// Template parameters
//

// Joomla! jQuery
if ($this->params->get('useJquery') && $webAssets->assetExists('script', 'jquery')) {
    $webAssets->useScript('jquery');

    // Joomla! jQuery Migrate
    /* Although dependent template options are hidden upon the dependee going to a disabled state
    (the "showon" attribute in templateDetails.xml) the dependent param retains its last value unless
    toggled explicitly, so we should always check the parent param value first, because we don't want to
    trigger the inclusion of it through Web Assets dependency mechanism. */
    if ($this->params->get('useJqueryMigrate') && $webAssets->assetExists('script', 'jquery-migrate')) {
        $webAssets->useScript('jquery-migrate');
    }
}

// Joomla! jQuery.noConflict()
/* Had to move this block out of the main jQuery if block to avoid duplicating script override calls */
if ((!$this->params->get('useJquery') || !$this->params->get('useJqueryNoconflict')) && $webAssets->assetExists('script', 'jquery-noconflict')) {
    // The disableScript() call doesn't prevent it from inclusion for some reason. Create an empty override instead.
    // $webAssets->disableScript('jquery-noconflict');
    $webAssets->registerScript('jquery-noconflict', '', [], [], []);
}

// Joomla! Bootstrap
if ($this->params->get('useBootstrap') && $webAssets->assetExists('style', 'bootstrap.css')) {
    $webAssets->useStyle('bootstrap.css');

    // Joomla! Bootstrap JavaScript for BS Components
    if ($this->params->get('useBootstrapJs') && $webAssets->assetExists('script', 'bootstrap.es5')) {
        $webAssets->useScript('bootstrap.es5');

        // Bootstrap Component Toasts
        if ($this->params->get('useBootstrapJsComponentToasts') && $webAssets->assetExists('script', 'bootstrap.toast')) {
            $webAssets->useScript('bootstrap.toast');
            $this->addScriptOptions('tpl_airis', ['useBootstrapJsComponentToasts' => true]);
            $bootstrapToastsEnabled = true;
        }

        /* TODO: Add options to use load BS the rest of component JS web asset individually and don't forget
        to hide options for individual component JS libraries in templateDetails.xml if 'useBootstrapJs' option
        that bundles all of them is already enabled. */

        /* For some reason, currently all individual Bootstrap JS component web assets list 'bootstrap.es5' as their
        dependency which is wrong since both the whole bundle and the individual .js file will be
        included at the same time. So pointless for us to implement individual template options for each BS JS complonent
        until Joomla! gets its media/vendor/joomla.asset.json file fixed. Another way is to override the 'bootstrap.es5' Web Asset with
        something like this $webAssets->registerScript('bootstrap.es5', '', [], [], []); so an empty item is used as a dependency. Make the override a template param
        which in turn allows (via the showon templateDetails.xml attribute) to enable the individual BS component script Web Assets when the whole bundle is disabled. */
    }
}

/* TODO: Add template options for these parameters. setLineEnd() and setTab() seem to affect <head> contents only.
Ask the Joomla! Community what's up with that. Do not active the last two options if Joomla! debug option is enabled. */
// $this->setGenerator('');
// $this->setLineEnd('');
// $this->setTab('');

// User <head> contents
if ($this->params->get('userHeadHtml', '')) {
    /* addCustomTag() contents are normally output with <jdoc:include type="scripts" />
    so it goes before </body> in our case which is not right here */
    /* TODO: See if we should validate the raw HTML here in some way using either Joomla!'s
    filtering methods here or by defining the filter attribute in templateDetails.xml */
    $userHeadHtml = $this->params->get('userHeadHtml');
}

// User <body> end contents
if ($this->params->get('userBodyEndHtml', '')) {
    /* TODO: See if we should validate the raw HTML here in some way using either Joomla!'s
    filtering methods here or by defining the filter attribute in templateDetails.xml */
    /* addCustomTag() contents are output after Web Assets and we can't allow users to have their own
    <script src=""></script> tags to go after user.js inclusion. */
    // $this->addCustomTag($this->params->get('userBodyEndHtml'));
    $userBodyEndHtml = $this->params->get('userBodyEndHtml');
}

// User inline CSS
if ($this->params->get('userInlineCss', '')) {
    $webAssets->addInlineStyle(
        $this->params->get('userInlineCss'),
        [
            'name' => 'template.airis.user.inline', // FIXME: Doesn't do anything
        ],
    );
}

// User inline JS
if ($this->params->get('userInlineJs', '')) {
    $webAssets->addInlineScript(
        $this->params->get('userInlineJs'),
        [
            'name' => 'template.airis.user.inline', // FIXME: Doesn't do anything
        ],
    );
}

// Possibly disable compoment on default page
$componentEnabled = true;

if ($this->params->get('disableComponentOnDefaultPage') && $currentMenuItem === $applicationMenu->getDefault($this->language)) {
    $componentEnabled = false;
}

// Determine an additional class for component based on aside(s) presence
if ($componentEnabled) {
    $asideLeftHasModules = (bool) $this->countModules('aside-left');
    $asideRightHasModules = (bool) $this->countModules('aside-right');

    if ($asideLeftHasModules && $asideRightHasModules) {
        $componentAreaAdditionalClasses = 'airis-asides-both airis-asides-left airis-asides-right';
        $this->params->set('asides', 'both');
    } elseif ($asideLeftHasModules || $asideRightHasModules) {
        $componentAreaAdditionalClasses = 'airis-asides-single';

        if ($asideLeftHasModules) {
            $componentAreaAdditionalClasses .= ' airis-asides-left';
        } elseif ($asideRightHasModules) {
            $componentAreaAdditionalClasses .= ' airis-asides-right';
        }

        $this->params->set('asides', 'single');
    } else {
        $componentAreaAdditionalClasses = 'airis-asides-none'; // This value is also used by all module positions outside of <main> since there can be no asides
        $this->params->set('asides', 'none');
    }
}

// The Open Graph protocol basic support
if ($this->params->get('useOpenGraph')) {
    $this->setMetaData('og:url', $currentUri->toString()); /* $this->base */
    $this->setMetaData('og:type', 'website');
    $this->setMetaData('og:title', htmlspecialchars(trim($this->title), ENT_COMPAT, 'UTF-8'));
    $this->setMetaData('og:description', htmlspecialchars(trim($this->description), ENT_COMPAT, 'UTF-8'));
    $this->setMetaData('og:locale', $this->language);
    // TODO: Validate image path using Joomla utilities
    if ($this->params->get('openGraphImagePath', '')) {

        $this->setMetaData('og:image', $currentUri->toString() . htmlspecialchars($this->params->get('open_graph_image_path'), ENT_QUOTES, 'UTF-8'));
    } /* elseif ($articleImageSrc = !empty(json_decode($this->get('images')))) {
        $this->setMetaData('og:image', $currentUri->toString() . $articleImageSrc);
    } */
}

// TODO: Check if we need this in J!4
// Favicon
/* if (file_exists(join(DIRECTORY_SEPARATOR, [JPATH_THEMES, 'favicon.ico']))) {
    $this->addHeadLink(HTMLHelper::_('image', 'favicon.ico', '', [], true, 1), 'alternate icon', 'rel', ['type' => 'image/vnd.microsoft.icon']);
} */

// SVG favicon support
if (file_exists(join(DIRECTORY_SEPARATOR, [JPATH_THEMES, $this->template, 'favicon.svg']))) {
    // <jdoc:include type="scripts" /> outputs everything that's been added with addCustomTag() so we cannot use it
    // $this->addCustomTag("<link href=\"$templateUriPrefix/favicon.svg\" rel=\"icon\">");
    $svgFavicon = "<link href=\"$templateUriPrefix/favicon.svg\" rel=\"icon\">";
    // TODO: Use something similar to this in J4!+
    // $this->addHeadLink(HTMLHelper::('image', 'favicon.svg', '', [], true, 1), 'alternate icon', 'rel', ['type' => 'image/vnd.microsoft.icon']);

}

/* if (file_exists(join(DIRECTORY_SEPARATOR, [JPATH_THEMES, 'favicon.svg']))) {
    // FIXME: Outputs an empty type attribute so keeping a custom tag instead for now
    $this->addFavicon("$templateUriPrefix/favicon.svg", '', 'icon');
} */

// Add fancyBox
if ($this->params->get('useJquery') && $this->params->get('useFancybox')) {
    $webAssets->usePreset('template.airis.fancybox');
}

// Add Flickity
if ($this->params->get('useFlickity')) {
    $webAssets->usePreset('template.airis.flickity');
}

// Add Font Awesome
// TODO: Decide if we really need to bring our own Font Awesome
if ($this->params->get('useFontAwesome')) {
    $webAssets->usePreset('template.airis.fontawesome');
    $this->addScriptOptions('tpl_airis', ['useFontAwesome' => true]);

    if ($this->params->get('useFontAwesomeBrands')) {
        $webAssets->useStyle('template.airis.fontawesome.brands');
        $this->addScriptOptions('tpl_airis', ['useFontAwesomeBrands' => true]);
    }

    // Enable lazy loading of Joomla!'s Font Awesome
    // $webAssets->getAsset('style', 'fontawesome')->setAttribute('rel', 'lazy-stylesheet');
}

// Add GLightbox
if ($this->params->get('useGlightbox')) {
    $webAssets->usePreset('template.airis.glightbox');
}

// Add Inputmask
if ($this->params->get('useInputmask')) {
    switch ($this->params->get('useInputmaskFlavor', 'native')) {
        case 'native':
            $webAssets->useScript('template.airis.inputmask');
            break;
        case 'jquery':
            if ($this->params->get('useJquery')) {
                $webAssets->useScript('template.airis.inputmask.jquery');

                if ($this->params->get('useInputmaskBinding')) {
                    $webAssets->useScript('template.airis.inputmask.binding');
                }
            }
            break;
    }
}

// Add ScrollReveal
if ($this->params->get('useScrollreveal')) {
    $webAssets->useScript('template.airis.scrollreveal');
}

// Add Select2
if ($this->params->get('useJquery') && $this->params->get('useSelect2')) {
    $webAssets->usePreset('template.airis.select2');
    if ($this->language === 'ru-ru') {
        $webAssets->useScript('template.airis.select2.i18n.ru');
    }
}

// Add tiny-slider
if ($this->params->get('useTiny-slider')) {
    $webAssets->usePreset('template.airis.tiny-slider');
}

// Add DoubleGis Map Widget
if ($this->params->get('useDoubleGisMapWidget')) {
    $webAssets->useScript('template.airis.doublegis.widget.firmsonmap');
}

// Template CSS and JS
$webAssets->usePreset('template.airis.template');

// Joomla! Bootstrap CSS resets
if ($this->params->get('useBootstrap') && $this->params->get('useBootstrapCssResetsFile')) {
    $webAssets->useStyle('template.airis.bootstrap.resets');
}

// VirtueMart CSS and JS
if ($this->params->get('useVirtuemartCssAndJsFiles')) {
    $webAssets->usePreset('template.airis.virtuemart');

    // TODO: This 'useVirtuemartCssAndJsFiles' option could also disable VirtueMart's own assets instead of relying on administrator disabling them in VM's configuration manually
    // $webAssets->disableStyle('');
    // $webAssets->disableScript('');
    // $webAssets->disablePreset('');

    // TODO: Not sure if we still need the ability to load cart files independently and not just load a preset
/*     if ($this->params->get('useVirtuemartCartCssAndJsFiles')) {
        $webAssets->usePreset('template.airis.virtuemart.cart');
    } */

    // Optional CSS and JS for non-catalog only VirtueMart installations
    if ($this->params->get('useVirtuemartCartCssFile')) {
        $webAssets->useStyle('template.airis.virtuemart.cart');
    }

    if ($this->params->get('useVirtuemartCartJsFile')) {
        $webAssets->useScript('template.airis.virtuemart.cart');

        // Route to cart view used by this script file
        $this->addScriptOptions(
            'tpl_airis',
            [
                'vmCartUri' => Route::_('index.php?option=com_virtuemart&view=cart'),
            ],
        );

        // Language constants used by this script file
        Text::script('TPL_AIRIS_COM_VIRTUEMART_ALERT_PRODUCT_ADD_ERROR');
        Text::script('TPL_AIRIS_COM_VIRTUEMART_CONFIRM_SHOW_CART');

        // Use Bootstrap Toasts components as cart notifications
        if (
            $webAssets->isAssetActive('script', 'bootstrap.es5')
            && $webAssets->isAssetActive('script', 'bootstrap.toast')
            && $this->params->get('useBootstrapToastsAsVirtuemartCartNotifications')
        ) {
            $this->addScriptOptions(
                'tpl_airis',
                [
                    'useBootstrapToastsAsVirtuemartCartNotifications' => true,
                ],
            );

            // Additional language constants
            Text::script('TPL_AIRIS_RELOAD_PAGE');
            Text::script('TPL_AIRIS_COM_VIRTUEMART_CART_PRODUCT_ADDED_SHORT');
            Text::script('TPL_AIRIS_COM_VIRTUEMART_CART_PRODUCT_ADDED_CART_LINK_TITLE');
        }
    }
}

// user.css file support
if ($this->params->get('useUserCssFile')) {
    $userCssFileName = 'user.css';
    $userCssFileUri = "$templateMediaUriPrefix/css/$userCssFileName";
    $userCssFilePath = join(
        DIRECTORY_SEPARATOR,
        [
            JPATH_ROOT,
            'media',
            'templates',
            'site',
            $this->template,
            'css',
            $userCssFileName,
        ],
    );

    if (file_exists($userCssFilePath)) {
        // Respect chosen versioning mode
        if ($this->params->get('userCssFileVersioningMode', 'default') === 'datetime') {
            // Joomla's own generateMediaVersion() of Joomla\CMS\Version class only takes the first six characters of generated checksum so keeping it uniform here
            $userCssFileWebAssetItemVersion = substr(md5(filemtime($userCssFilePath)), 0, 6);
        } else {
            $userCssFileWebAssetItemVersion = 'auto';
        }

        $userCssFileWebAssetItem = new WebAssetItem(
            'template.airis.user',
            $userCssFileUri,
            [
                'type' => 'style',
                'version' => $userCssFileWebAssetItemVersion,
                'weight' => PHP_INT_MAX, // TODO: Find a proper way (calculate the highest weight of active asset and use it + 10 points) to ensure the last position among included styles
            ]
        );

        /* For some reason (probably planned a setVersion() method that is complimentary
        to the current getVersion(), the setOption() method doesn't update the version property
        for existing WebAssetItem instances, so we cannot create one before the versioning
        mode is determined. */
        /* if ($this->params->get('userCssFileVersioningMode', 'default') === 'datetime') {
            $userCssFileWebAssetItem->setOption(
                'version',
                substr(md5(filemtime($userCssFileUri)), 0, 6),
            );
        } */

        $webAssets->registerAndUseStyle($userCssFileWebAssetItem);
    }
}

// user.js file support
if ($this->params->get('useUserJsFile')) {
    $userJsFileName = 'user.js';
    $userJsFileUri = "$templateMediaUriPrefix/js/$userJsFileName";
    $userJsFilePath = join(
        DIRECTORY_SEPARATOR,
        [
            JPATH_ROOT,
            'media',
            'templates',
            'site',
            $this->template,
            'js',
            $userJsFileName,
        ],
    );

    if (file_exists($userJsFilePath)) {
        if ($this->params->get('userJsFileVersioningMode', 'default') === 'datetime') {
            $userJsFileWebAssetItemVersion = substr(md5(filemtime($userJsFilePath)), 0, 6);
        } else {
            $userJsFileWebAssetItemVersion = 'auto';
        }

        $userJsFileWebAssetItem = new WebAssetItem(
            'template.airis.user',
            $userJsFileUri,
            [
                'type' => 'script',
                'version' => $userJsFileWebAssetItemVersion,
                'weight' => PHP_INT_MAX,
            ],
            [
                'defer' => true,
            ]
        );

        $webAssets->registerAndUseScript($userJsFileWebAssetItem);
    }
}

// TODO: Should probably remove it along with the corresponding template.js code and language constant since it won't be used in our new mod_menu airis layout
// Language strings for template's JS files.
Text::script('TPL_AIRIS_MAIN_MENU_CHILD_MENU_TOGGLE_BTN_TITLE');

final class AirisTemplate {
    // Module template constants
    public const ITEMS_PER_ROW_DEFAULT = 3;
    public const ITEMS_PER_ROW_MIN = 1;
    public const ITEMS_PER_ROW_MAX = 6;

    // The total amount of module positions per group is always based on a number of declared positions in templateDetails.xml
    private static array $modulePositionPerGroupCounts = [
        // TODO: Hopefully, PHP introduces static constructors or something similar so we can use these lines with having to create unnecessary field initialization methods and similar workarounds
        // AirisTemplateModulePositionGroups::Header->value => 6,
        // AirisTemplateModulePositionGroups::Footer->value => 6,
        // AirisTemplateModulePositionGroups::Before->value => 10,
        // AirisTemplateModulePositionGroups::After->value => 10,
        // AirisTemplateModulePositionGroups::OffScreen => 3,
        'header' => 6,
        'footer' => 6,
        'before' => 10,
        'after' => 10,
        'off-screen' => 3,
    ];

    // Alternative to moduleGroupHasModules()
    public static function countModulesInGroup(
        AirisTemplateModulePositionGroups $modulePositionGroup,
        Document $documentInstance,
    ): int {
        $activeModulesInGroupCount = 0;

        for (
            $currentInGroupPosition = 1;
            $currentInGroupPosition <= self::$modulePositionInGroupCounts[$modulePositionGroup->value];
            $currentInGroupPosition++
        ) {
            if ($documentInstance->countModules("{$modulePositionGroup->value}-$currentInGroupPosition")) {
                $activeModulesInGroupCount++;
            }
        }

        return $activeModulesInGroupCount;
    }

    public static function moduleGroupHasModules(
        AirisTemplateModulePositionGroups $modulePositionGroup,
        Document $documentInstance,
    ): bool {
        $activeModulesPerGroupCount = false;

        for (
            $currentInGroupPosition = 1;
            $currentInGroupPosition <= self::$modulePositionPerGroupCounts[$modulePositionGroup->value];
            $currentInGroupPosition++
        ) {
            if ($documentInstance->countModules("{$modulePositionGroup->value}-$currentInGroupPosition")) {
                $activeModulesPerGroupCount = true;
                break;
            }
        }

        return $activeModulesPerGroupCount;
    }

    public static function getModulePositionGroupHtml(
        AirisTemplateModulePositionGroups $modulePositionGroup,
        Document $documentInstance,
        ): string {
        $moduleGroupHtml = '';

        for (
            $currentInGroupPosition = 1;
            $currentInGroupPosition <= self::$modulePositionPerGroupCounts[$modulePositionGroup->value];
            $currentInGroupPosition++
        ) {
            $modulePositionNameFull = "{$modulePositionGroup->value}-$currentInGroupPosition";

            if ($documentInstance->countModules($modulePositionNameFull)) {
                // The 'airis-asides-none' class is used by all module positions outside of <main></main> since there can be no asides and many template.css styles rely on these classes
                $moduleGroupHtml .= join(
                    [
                        "<div class=\"airis-module-position-$modulePositionNameFull airis-module-position-{$modulePositionGroup->value} airis-module-position\">",
                        '<div class="airis-module-container airis-container container airis-asides-none">',
                        "<jdoc:include type=\"modules\" name=\"$modulePositionNameFull\" style=\"airis\" />",
                        '</div>',
                        '</div>',
                    ],
                );
            }
        }

        return $moduleGroupHtml;
    }
}

enum AirisTemplateModulePositionGroups: string {
    case Header = 'header';
    case Footer = 'footer';
    case Before = 'before';
    case After = 'after';
    case OffScreen = 'off-screen';
}

?>
<!DOCTYPE html>
<html lang="<?= $this->language; ?>" dir="<?= $this->direction; ?>">
    <head>

        <?php // Native implementation of jQuery(function () { }); ?>
        <script>
            function onDOMContentLoaded(handler) {
                if (typeof handler !== "function") {
                    console.log("The provided handler is not a function.");
                    return;
                }

                if (document.readyState === "loading") {
                    document.addEventListener("DOMContentLoaded", handler);
                } else {
                    handler();
                }
            }
        </script>

        <?php if ($this->params->get('moveScriptsToBodyEnd')) : ?>
            <jdoc:include type="metas" />
            <jdoc:include type="styles" />
        <?php else : ?>
            <jdoc:include type="head" />
        <?php endif; ?>

        <?php if (isset($svgFavicon) && $svgFavicon !== '') : ?>
            <?= $svgFavicon; ?>
        <?php endif; ?>

        <?php if (isset($userHeadHtml) && $userHeadHtml !== '') : ?>
            <?= $userHeadHtml; ?>
        <?php endif; ?>

        <?php if (isset($userHeadJs) && $userHeadJs !== '') : ?>
            <?= $userHeadJs; ?>
        <?php endif; ?>
    </head>

    <?php if ($currentMenuItem) : ?>
        <body class="airis-page airis-page_template_index airis-page_type_menu-item airis-page_menu-type_<?= $currentMenuItem->menutype; ?> airis-page_menu-item-id_<?= $currentMenuItem->id; ?>">
    <?php else : ?>
        <body class="airis-page airis-page_template_index">
    <?php endif; ?>

        <?php if (AirisTemplate::moduleGroupHasModules(AirisTemplateModulePositionGroups::Header, $currentDocument)) : ?>
            <header>
                <?= AirisTemplate::getModulePositionGroupHtml(AirisTemplateModulePositionGroups::Header, $currentDocument); ?>
            </header>
        <?php endif; ?>

        <div class="airis-area-message" data-nosnippet>
            <div class="airis-area-container airis-container container">
                <jdoc:include type="message" />
            </div>
        </div>

        <?= AirisTemplate::getModulePositionGroupHtml(AirisTemplateModulePositionGroups::Before, $currentDocument); ?>

        <?php if ($componentEnabled) : ?>
            <div class="airis-area-component">
                <div class="airis-container container <?= $componentAreaAdditionalClasses; ?>">

                    <?php if ($asideLeftHasModules) : ?>
                        <aside class="airis-module-position-aside-left airis-module-position-aside airis-module-position airis-aside-left airis-aside">
                            <jdoc:include type="modules" name="aside-left" style="airis" />
                        </aside>
                    <?php endif; ?>

                    <main class="airis-main">

                        <?php if ($this->countModules('inside-top')) : ?>
                            <div class="airis-module-position-inside-top airis-module-position-inside airis-module-position">
                                <jdoc:include type="modules" name="inside-top" style="airis" />
                            </div>
                        <?php endif; ?>

                        <jdoc:include type="component" />

                        <?php if ($this->countModules('inside-bottom')) : ?>
                            <div class="airis-module-position-inside-bottom airis-module-position-inside airis-module-position">
                                <jdoc:include type="modules" name="inside-bottom" style="airis" />
                            </div>
                        <?php endif; ?>

                    </main>

                    <?php if ($asideRightHasModules) : ?>
                        <aside class="airis-module-position-aside-right airis-module-position-aside airis-module-position airis-aside-right airis-aside">
                            <jdoc:include type="modules" name="aside-right" style="airis" />
                        </aside>
                    <?php endif; ?>

                </div>
            </div>
        <?php endif; ?>

        <?= AirisTemplate::getModulePositionGroupHtml(AirisTemplateModulePositionGroups::After, $currentDocument); ?>

        <?php if (AirisTemplate::moduleGroupHasModules(AirisTemplateModulePositionGroups::Footer, $currentDocument)) : ?>
            <footer>
                <?= AirisTemplate::getModulePositionGroupHtml(AirisTemplateModulePositionGroups::Footer, $currentDocument); ?>
            </footer>
        <?php endif; ?>

        <?php if (isset($bootstrapToastsEnabled) && $bootstrapToastsEnabled === true) : ?>
            <div class="airis-area-toasts toast-container fixed-bottom ms-auto" aria-live="assertive"></div>
        <?php endif; ?>

        <?php if (AirisTemplate::moduleGroupHasModules(AirisTemplateModulePositionGroups::OffScreen, $currentDocument)) : ?>
            <?= AirisTemplate::getModulePositionGroupHtml(AirisTemplateModulePositionGroups::OffScreen, $currentDocument); ?>
        <?php endif; ?>

        <?php if ($this->countModules('debug')) : ?>
            <div class="airis-module-position-debug">
                <div class="airis-module-container airis-container container airis-asides-none">
                    <jdoc:include type="modules" name="debug" />
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($userBodyEndHtml) && $userBodyEndHtml !== '') : ?>
            <?= $userBodyEndHtml; ?>
        <?php endif; ?>

        <?php if ($this->params->get('moveScriptsToBodyEnd')) : ?>
            <jdoc:include type="scripts" />
        <?php endif; ?>

    </body>
</html>