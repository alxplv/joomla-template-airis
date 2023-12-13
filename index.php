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

// Use Joomla!'s default CSS and JS versioning mode
$autoVersion = ['version' => 'auto'];

// Used for a non-blocking loading mode of 3rd-party JavaScript libraries
$deferScript = ['defer' => true];

//
// Template parameters
//

// Joomla! jQuery
if (!$this->params->get('loadJoomlaJquery')) {
    unset($currentDocument->_scripts['/media/jui/js/jquery.min.js']);
}

// Joomla! jQuery.noConflict()
if (!$this->params->get('loadJoomlaJquery') || !$this->params->get('loadJoomlaJqueryNoconflict')) {
    unset($currentDocument->_scripts['/media/jui/js/jquery-noconflict.js']);
}

// Joomla! jQuery Migrate
if (!$this->params->get('loadJoomlaJquery') || !$this->params->get('loadJoomlaJqueryMigrate')) {
    unset($currentDocument->_scripts['/media/jui/js/jquery-migrate.min.js']);
}

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

// Joomla! SqueezeBox (also loads MooTools)
if ($this->params->get('loadJoomlaSqueezebox')) {
    HTMLHelper::_('behavior.modal');
}

// Custom <head> contents
if (!empty(trim($this->params->get('customHeadHtml')))) {
    $this->addCustomTag($this->params->get('customHeadHtml'));
}

// Custom inline CSS in <head>
if (!empty(trim($this->params->get('customHeadCss')))) {
    $this->addStyleDeclaration($this->params->get('customHeadCss'));
}

// Custom inline JS in <head>
if (!empty(trim($this->params->get('customHeadJs')))) {
    $this->addScriptDeclaration($this->params->get('customHeadJs'));
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
    // TODO: Replace with a simple EOD declaration
    $openGraphMetaHtml = '<meta property="og:url" content="' . $currentUri->toString() /* $this->base */ . '" />' . PHP_EOL . "\t";
    $openGraphMetaHtml .= '<meta property="og:type" content="website" />' . PHP_EOL . "\t";
    $openGraphMetaHtml .= '<meta property="og:title" content="' . htmlspecialchars(trim($this->title), ENT_COMPAT, 'UTF-8') . '" />' . PHP_EOL . "\t";
    $openGraphMetaHtml .= '<meta property="og:description" content="' . htmlspecialchars(trim($this->description), ENT_COMPAT, 'UTF-8') . '" />' . PHP_EOL . "\t";

    if (!empty($this->params->get('openGraphImagePath'))) {
        $openGraphMetaHtml .= '<meta property="og:image" content="' . $currentUri->toString() . htmlspecialchars($this->params->get('open_graph_image_path'), ENT_COMPAT, 'UTF-8') . '" />' . PHP_EOL . "\t";
    } /* elseif ($articleImageSrc = !empty(json_decode($this->get('images')))) {
        $openGraphMetaHtml .= '<meta property="og:image" content="' . $currentUri->toString() . $articleImageSrc . '" />' . PHP_EOL . "\t";
    } */

    $openGraphMetaHtml .= '<meta property="og:locale" content="' . $this->language . '" />';

    $this->addCustomTag($openGraphMetaHtml);
}

// SVG favicon support
if (file_exists($templatePath . '/favicon.svg')) {
    $this->addCustomTag("<link href=\"$templatePath/favicon.svg\" rel=\"icon\">");
}

/* if (file_exists($templatePath . '/favicon.svg')) {
    // FIXME: Outputs an empty type attribute so keeping a custom tag instead for now
    $this->addFavicon($templatePath . '/favicon.svg', '', 'icon');
} */

// Add fancyBox
if ($this->params->get('loadJoomlaJquery') && $this->params->get('loadFancybox')) {
    $this->addStyleSheet($templatePath . '/vendor/fancybox/jquery.fancybox.min.css', $autoVersion);
    $this->addScript($templatePath . '/vendor/fancybox/jquery.fancybox.min.js', $autoVersion, $deferScript);
}

// Add Flickity
if ($this->params->get('loadJoomlaJquery') && $this->params->get('loadFlickity')) {
    $this->addStyleSheet($templatePath . '/vendor/flickity/flickity.min.css', $autoVersion);
    $this->addScript($templatePath . '/vendor/flickity/flickity.pkgd.min.js', $autoVersion, $deferScript);
}

// Add Font Awesome
if ($this->params->get('loadFontAwesome')) {
    $this->addStyleSheet($templatePath . '/vendor/fontawesome/css/fontawesome.min.css', $autoVersion);
    $this->addStyleSheet($templatePath . '/vendor/fontawesome/css/solid.min.css', $autoVersion);
    $this->addStyleSheet($templatePath . '/vendor/fontawesome/css/regular.min.css', $autoVersion);
    $this->addScriptOptions('tpl_airis', ['loadFontAwesome' => true]);

    if ($this->params->get('loadFontAwesomeBrands')) {
        $this->addStyleSheet($templatePath . '/vendor/fontawesome/css/brands.min.css', $autoVersion);
        $this->addScriptOptions('tpl_airis', ['loadFontAwesomeBrands' => true]);
    }
}

// Add GLightbox
if ($this->params->get('loadGlightbox')) {
    $this->addStyleSheet($templatePath . '/vendor/glightbox/glightbox.min.css', $autoVersion);
    $this->addScript($templatePath . '/vendor/glightbox/glightbox.min.js', $autoVersion, $deferScript);
}

// Add Inputmask
if ($this->params->get('loadInputmask')) {
    switch ($this->params->get('loadInputmaskFlavor')) {
        case 'native':
            $this->addScript($templatePath . '/vendor/inputmask/inputmask.min.js', $autoVersion, $deferScript);
            break;
        case 'jquery':
            if ($this->params->get('loadJoomlaJquery')) {
                $this->addScript($templatePath . '/vendor/inputmask/jquery.inputmask.min.js', $autoVersion, $deferScript);
                if ($this->params->get('loadInputmaskBinding')) {
                    $this->addScript($templatePath . '/vendor/inputmask/inputmask.binding.js', $autoVersion, $deferScript);
                }
            }
            break;
    }
}

// Add ScrollReveal
if ($this->params->get('loadScrollreveal')) {
    $this->addScript($templatePath . '/vendor/scrollreveal/scrollreveal.min.js', $autoVersion, $deferScript);
}

// Add Select2
if ($this->params->get('loadJoomlaJquery') && $this->params->get('loadSelect2')) {
    $this->addStyleSheet($templatePath . '/vendor/select2/select2.min.css', $autoVersion);
    $this->addScript($templatePath . '/vendor/select2/select2.min.js', $autoVersion, $deferScript);
    if ($this->language == 'ru-ru') {
        $this->addScript($templatePath . '/vendor/select2/i18n/ru.js', $autoVersion, $deferScript);
    }
}

// Add tiny-slider
if ($this->params->get('loadTiny-slider')) {
    $this->addStyleSheet($templatePath . '/vendor/tiny-slider/tiny-slider.css', $autoVersion);
    $this->addScript($templatePath . '/vendor/tiny-slider/tiny-slider.js', $autoVersion, $deferScript);
}

// Add DoubleGis Map Widget
if ($this->params->get('loadDoubleGisMapWidget')) {
    $this->addScript('https://widgets.2gis.com/js/DGWidgetLoader.js', null, $deferScript);
}

// Template CSS and JS
$this->addStyleSheet($templatePath . '/css/template.min.css', $autoVersion);
$this->addScript($templatePath . '/js/template.min.js', $autoVersion);

// Joomla! Bootstrap CSS resets
if ($this->params->get('loadJoomlaBootstrap') && $this->params->get('loadJoomlaBootstrapCssResetsFile')) {
    $this->addStyleSheet($templatePath . '/css/template-joomla-bootstrap-resets.min.css', $autoVersion);
}

// VirtueMart CSS and JS
if ($this->params->get('loadVirtuemartCssAndJsFiles')) {
    $this->addStyleSheet($templatePath . '/css/template-virtuemart.min.css', $autoVersion);
    $this->addScript($templatePath . '/js/template-virtuemart.min.js', $autoVersion);

    // Optional CSS and JS for non-catalog only VirtueMart installations
    if ($this->params->get('loadVirtuemartCartCssFile')) {
        $this->addStyleSheet($templatePath . '/css/template-virtuemart-cart.min.css', $autoVersion);
    }

    if ($this->params->get('loadVirtuemartCartJsFile')) {
        $this->addScript($templatePath . '/js/template-virtuemart-cart.min.js', $autoVersion);

        // Additional language strings for this file
        Text::script('TPL_AIRIS_COM_VIRTUEMART_ALERT_PRODUCT_ADD_ERROR');
        Text::script('TPL_AIRIS_COM_VIRTUEMART_CONFIRM_SHOW_CART');
    }
}

// custom.css file support
if ($this->params->get('loadCustomCssFile')) {
    $customCssFilePath = $templatePath . '/css/custom.css';

    // No versioning scheme used in case if option is set to none
    $versioningScheme = null;

    switch ($this->params->get('customCssFileVersioningMode')) {
        case 'datetime':
            $versioningScheme = ['version' => md5(filemtime($customCssFilePath))];
            break;
        case 'default':
            $versioningScheme = $autoVersion;
            break;
    }

    $this->addStyleSheet($customCssFilePath, $versioningScheme);
}

// custom.js file support
if ($this->params->get('loadCustomJsFile')) {
    $customJsFilePath = $templatePath . '/js/custom.js';

    // No versioning scheme used in case if option is set to none
    $versioningScheme = null;

    switch ($this->params->get('customJsFileVersioningMode')) {
        case 'datetime':
            $versioningScheme = ['version' => md5(filemtime($customJsFilePath))];
            break;
        case 'default':
            $versioningScheme = $autoVersion;
            break;
    }

    $this->addScript($customJsFilePath, $versioningScheme);
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
<html lang="<?php echo $this->language; ?>">
    <head>
        <jdoc:include type="head" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    </body>
</html>