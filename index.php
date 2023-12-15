<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\Factory;
use Joomla\CMS\Document\Document;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

// Basic handles
$applicationMenu = Factory::getApplication()->getMenu();
$currentDocument = Factory::getApplication()->getDocument();
$currentMenuItem = $applicationMenu->getActive();
$currentUri = Uri::getInstance();
$templatePath = 'templates/' . $this->template;
$webAssets = $currentDocument->getWebAssetManager();

// Document metadata included at all times
$this->setMetaData('viewport', 'width=device-width, initial-scale=1.0');

//
// Template parameters
//

// Joomla! jQuery
if (!$this->params->get('loadJoomlaJquery')) {
    $webAssets->disableScript('jquery');
}

// Joomla! jQuery Migrate
if (!$this->params->get('loadJoomlaJquery') || !$this->params->get('loadJoomlaJqueryMigrate')) {
    $webAssets->disableScript('jquery-migrate');
}

// TODO: Check if we need this in J!4+
// Joomla! Bootstrap
if ($this->params->get('loadJoomlaBootstrap')) {
    HTMLHelper::_('bootstrap.loadCss');
    HTMLHelper::_('bootstrap.framework');
}

/* TODO: Add template options for these parameters. setLineEnd() and setTab() seem to affect <head> contents only.
Ask the Joomla! Community what's up with that. Do not active the last two options if Joomla! debug option is enabled. */
// $this->setGenerator(null);
// $this->setLineEnd(null);
// $this->setTab(null);

// User <head> contents
if ($this->params->get('userHeadHtml', '')) {
    $this->addCustomTag($this->params->get('userHeadHtml'));
}

// User inline CSS in <head>
if ($this->params->get('userHeadCss', '')) {
    // $this->addStyleDeclaration($this->params->get('userHeadCss'));
    $webAssets->addInlineStyle(
        $this->params->get('userHeadCss'),
        [
            'name' => 'template.airis.user.head',
        ],
    );
}

// TODO: Should probably inject $userHeadJs into <head> manually in the <head> markup below because addInlineScript() adds before the end of <body> element
// Custom inline JS in <head>
if ($this->params->get('userHeadJs', '')) {
    // $this->addScriptDeclaration($this->params->get('userHeadJs'));
    $webAssets->addInlineScript(
        $this->params->get('userHeadJs'),
        [
            'name' => 'template.airis.user.head',
        ],
    );
    // $userHeadJs = trim($this->params->get('userHeadJs'));
}

/* if ($this->params->get('userBodyEndJs', '')) {
    $webAssets->addInlineScript(
        $this->params->get('userBodyEndJs'),
        [
            'name' => 'template.airis.user.head',
        ],
    );
} */

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
/* if (file_exists($templatePath . '/favicon.ico')) {
    $this->addHeadLink(HTMLHelper::_('image', 'favicon.ico', '', [], true, 1), 'alternate icon', 'rel', ['type' => 'image/vnd.microsoft.icon']);
} */

// SVG favicon support
if (file_exists($templatePath . '/favicon.svg')) {
    $this->addCustomTag("<link href=\"$templatePath/favicon.svg\" rel=\"icon\">");
    // TODO: Use something similar to this in J4!+
    // $this->addHeadLink(HTMLHelper::('image', 'favicon.svg', '', [], true, 1), 'alternate icon', 'rel', ['type' => 'image/vnd.microsoft.icon']);
}

/* if (file_exists($templatePath . '/favicon.svg')) {
    // FIXME: Outputs an empty type attribute so keeping a custom tag instead for now
    $this->addFavicon($templatePath . '/favicon.svg', '', 'icon');
} */

// Add fancyBox
if ($this->params->get('loadJoomlaJquery') && $this->params->get('loadFancybox')) {
    $webAssets->usePreset('template.airis.fancybox');
}

// Add Flickity
if ($this->params->get('loadFlickity')) {
    $webAssets->usePreset('template.airis.flickity');
}

// Add Font Awesome
// TODO: Decide if we really need to bring our own Font Awesome
if ($this->params->get('loadFontAwesome')) {
    $webAssets->usePreset('template.airis.fontawesome');
    $this->addScriptOptions('tpl_airis', ['loadFontAwesome' => true]);

    if ($this->params->get('loadFontAwesomeBrands')) {
        $webAssets->useStyle('template.airis.fontawesome.brands');
        $this->addScriptOptions('tpl_airis', ['loadFontAwesomeBrands' => true]);
    }

    // Enable lazy loading of Joomla!'s Font Awesome
    // $webAssets->getAsset('style', 'fontawesome')->setAttribute('rel', 'lazy-stylesheet');
}

// Add GLightbox
if ($this->params->get('loadGlightbox')) {
    $webAssets->usePreset('template.airis.glightbox');
}

// Add Inputmask
if ($this->params->get('loadInputmask')) {
    switch ($this->params->get('loadInputmaskFlavor')) {
        case 'native':
            $webAssets->useScript('template.airis.inputmask');
            break;
        case 'jquery':
            if ($this->params->get('loadJoomlaJquery')) {
                $webAssets->useScript('template.airis.inputmask.jquery');
                if ($this->params->get('loadInputmaskBinding')) {
                    $webAssets->useScript('template.airis.inputmask.binding');
                }
            }
            break;
    }
}

// Add ScrollReveal
if ($this->params->get('loadScrollreveal')) {
    $webAssets->useScript('template.airis.scrollreveal');
}

// Add Select2
if ($this->params->get('loadJoomlaJquery') && $this->params->get('loadSelect2')) {
    $webAssets->usePreset('template.airis.select2');
    if ($this->language == 'ru-ru') {
        $webAssets->useScript('template.airis.select2.i18n.ru');
    }
}

// Add tiny-slider
if ($this->params->get('loadTiny-slider')) {
    $webAssets->usePreset('template.airis.tiny-slider');
}

// Add DoubleGis Map Widget
if ($this->params->get('loadDoubleGisMapWidget')) {
    $webAssets->useScript('template.airis.doublegis.widget.firmsonmap');
}

// Template CSS and JS
$webAssets->usePreset('template.airis.template');

// Joomla! Bootstrap CSS resets
if ($this->params->get('loadJoomlaBootstrap') && $this->params->get('loadJoomlaBootstrapCssResetsFile')) {
    $webAssets->useStyle('template.airis.boostrap.resets');
}

// VirtueMart CSS and JS
if ($this->params->get('loadVirtuemartCssAndJsFiles')) {
    $webAssets->usePreset('template.airis.virtuemart');

    // TODO: This 'loadVirtuemartCssAndJsFiles' option could also disable VirtueMart's own assets instead of relying on administrator disabling them in VM's configuration manually
    // $webAssets->disableStyle('');
    // $webAssets->disableScript('');
    // $webAssets->disablePreset('');

    // TODO: Not sure if we still need the ability to load cart files independently and not just load a preset
/*     if ($this->params->get('loadVirtuemartCartCssAndJsFiles')) {
        $webAssets->usePreset('template.airis.virtuemart.cart');
    } */

    // Optional CSS and JS for non-catalog only VirtueMart installations
    if ($this->params->get('loadVirtuemartCartCssFile')) {
        $webAssets->useStyle('template.airis.virtuemart.cart');
    }

    if ($this->params->get('loadVirtuemartCartJsFile')) {
        $webAssets->useScript('template.airis.virtuemart.cart');

        // Additional language strings used by this script file
        Text::script('TPL_AIRIS_COM_VIRTUEMART_ALERT_PRODUCT_ADD_ERROR');
        Text::script('TPL_AIRIS_COM_VIRTUEMART_CONFIRM_SHOW_CART');
    }
}

// TODO: Find out if we actually need explicit setting of auto version here along with the whole switch
// user.css file support
if ($this->params->get('loadUserCssFile')) {
    $userCssFileAttributes = [];
    $userCssFileName = 'user.css';
    $userCssFilePath = "$templatePath/css/$userCssFileName";

    if (file_exists($userCssFilePath)) {
        switch ($this->params->get('userCssFileVersioningMode', 'default')) {
            case 'datetime':
                $userCssFileAttributes['version'] = md5(filemtime($userCssFilePath));
                break;
            case 'default':
                $userCssFileAttributes['version'] = 'auto';
                break;
            case 'none':
                // Do nothing
                break;
        }

        $webAssets->registerAndUseStyle(
            'template.airis.user',
            $userCssFileName,
            [],
            $userCssFileAttributes,
            [],
        );
    }
}

// user.js file support
if ($this->params->get('loadUserJsFile')) {
    $userJsFileAttributes = [
        'defer' => true
    ];
    $userJsFileName = 'user.js';
    $userJsFilePath = "$templatePath/js/$userJsFileName";

    if (file_exists($userJsFilePath)) {
        switch ($this->params->get('userJsFileVersioningMode', 'default')) {
            case 'datetime':
                $userJsFileAttributes['version'] = md5(filemtime($userJsFilePath));
                break;
            case 'default':
                $userJsFileAttributes['version'] = 'auto';
                break;
            case 'none';
                // Do nothing
                break;
        }

        $webAssets->registerAndUseScript(
            'template.airis.user',
            $userJsFileName,
            [],
            $userJsFileAttributes,
            [],
        );
    }
}

// Language strings for template.js
Text::script('TPL_AIRIS_MAIN_MENU_CHILD_MENU_TOGGLE_BTN_TITLE');

// Module position rendering settings used for rendering occupied module positions with a single function call
$modulePositionGroups = [
    'header' => [
        'hasModules' => false,
        'positionNamePrefix' => 'header',
        'positionsTotal' => 6, // totalPositions is always based on a number of declared positions in templateDetails.xml
    ],
    'footer' => [
        'hasModules' => false,
        'positionNamePrefix' => 'footer',
        'positionsTotal' => 6,
    ],
    'before' => [
        'hasModules' => false,
        'positionNamePrefix' => 'before',
        'positionsTotal' => 10,
    ],
    'after' => [
        'hasModules' => false,
        'positionNamePrefix' => 'after',
        'positionsTotal' => 10,
    ],
    'off-screen' => [
        'hasModules' => false,
        'positionNamePrefix' => 'off-screen',
        'positionsTotal' => 3,
    ],
];

// Mark non-empty module positon groups for rendering
foreach ($modulePositionGroups as &$modulePositionGroup) {
    for ($i = 1; $i <= $modulePositionGroup['positionsTotal']; $i++) {
        if ($this->countModules("{$modulePositionGroup['positionNamePrefix']}-$i")) {
            $modulePositionGroup['hasModules'] = true;
            break;
        }
    }
}

function renderModulePositionGroup(array $groupSettings, Document $currentDocument)
{
    // Contains HTML for every non-empty module position of its group
    $groupHtml = '';

    for ($i = 1; $i <= $groupSettings['positionsTotal']; $i++) {
        $modulePositionName = $groupSettings['positionNamePrefix'];
        $modulePositionNameWithNumber = "$modulePositionName-$i";

        if ($currentDocument->countModules($modulePositionNameWithNumber)) {
            // TODO: Replace with NOWDOC declaration once we've moved to PHP 7.3+ or Joomla! 4 for good.
            // The 'airis-asides-none' class is used by all module positions outside of <main></main> since there can be no asides and many template.css styles rely on these classes
            $groupHtml .= "<div class=\"airis-module-position-$modulePositionNameWithNumber airis-module-position-$modulePositionName airis-module-position\"><div class=\"airis-module-container airis-container container airis-asides-none\"><jdoc:include type=\"modules\" name=\"$modulePositionNameWithNumber\" style=\"airis\" /></div></div>";
        }
    }

    return $groupHtml;
}

?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
    <head>
        <jdoc:include type="metas" />
        <jdoc:include type="styles" />
        <?php if (isset($userHeadJs) && $userHeadJs !== '') : ?>
            <?php echo $userHeadJs; ?>
        <?php endif; ?>
    </head>

    <?php if ($currentMenuItem) : ?>
        <body class="airis-page-template-index airis-page-type-menu-item airis-page-type-<?php echo $currentMenuItem->menutype, '-item-', $currentMenuItem->id; ?>">
    <?php else : ?>
        <body class="airis-page-template-index">
    <?php endif; ?>

        <?php if ($modulePositionGroups['header']['hasModules']) : ?>
            <header>
                <?php echo renderModulePositionGroup($modulePositionGroups['header'], $currentDocument); ?>
            </header>
        <?php endif; ?>

        <?php if ($this->countModules('nav')) : ?>
            <nav class="navbar container">
                <div class="navbar-inner">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse" title="<?php echo Text::_('TPL_AIRIS_MAIN_MENU_BTN_TITLE'); ?>">
                        <span class="icon-bar" aria-hidden="true"></span>
                        <span class="icon-bar" aria-hidden="true"></span>
                        <span class="icon-bar" aria-hidden="true"></span>
                      </a>
                    <?php if ($this->params->get('bootsrap_brand')) : ?>
                        <a class="brand" href="/"><?php echo $this->params->get('bootsrap_brand'); ?></a>
                    <?php endif; ?>
                    <div class="nav-collapse collapse">
                        <jdoc:include type="modules" name="nav" />
                    </div>
                </div>
            </nav>
        <?php endif; ?>

        <div class="airis-area-message" data-nosnippet>
            <div class="airis-area-container airis-container container">
                <jdoc:include type="message" />
            </div>
        </div>

        <?php echo renderModulePositionGroup($modulePositionGroups['before'], $currentDocument); ?>

        <?php if ($componentEnabled) : ?>
            <div class="airis-area-component">
                <div class="airis-container container <?php echo $componentAreaAdditionalClasses; ?>">

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

        <?php echo renderModulePositionGroup($modulePositionGroups['after'], $currentDocument); ?>

        <?php if ($modulePositionGroups['footer']['hasModules']) : ?>
            <footer>
                <?php echo renderModulePositionGroup($modulePositionGroups['footer'], $currentDocument); ?>
            </footer>
        <?php endif; ?>

        <?php if ($modulePositionGroups['off-screen']['hasModules']) : ?>
            <?php echo renderModulePositionGroup($modulePositionGroups['off-screen']), $currentDocument; ?>
        <?php endif; ?>

        <?php if ($this->countModules('debug')) : ?>
            <div class="airis-module-position-debug">
                <div class="airis-module-container airis-container container airis-asides-none">
                    <jdoc:include type="modules" name="debug" />
                </div>
            </div>
        <?php endif; ?>

        <jdoc:include type="scripts" />

    </body>
</html>