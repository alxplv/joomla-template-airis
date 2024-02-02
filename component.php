<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

// Basic handles
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

// Joomla! Bootstrap
if ($this->params->get('loadJoomlaBootstrap')) {
    HTMLHelper::_('bootstrap.loadCss');
    HTMLHelper::_('bootstrap.framework');
}

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

// SVG favicon support
if (file_exists($templatePath . '/favicon.svg')) {
    $this->addCustomTag("<link rel=\"icon\" href=\"$templatePath/favicon.svg\">");
}

// Add Font Awesome
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

        // Route to cart view used by this script file
        $this->addScriptOptions(
            'tpl_airis',
            [
                'vmCartUri' => Route::_('index.php?option=com_virtuemart&view=cart'),
            ],
        );

        // Additional language strings used by this script file
        Text::script('TPL_AIRIS_COM_VIRTUEMART_ALERT_PRODUCT_ADD_ERROR');
        Text::script('TPL_AIRIS_COM_VIRTUEMART_CONFIRM_SHOW_CART');
    }
}

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
    <body class="airis-page airis-page_template_component airis-asides-none">
        <div class="airis-area-message">
            <div class="airis-area-container airis-container container">
                <jdoc:include type="message" />
            </div>
        </div>
        <main class="airis-main">
            <jdoc:include type="component" />
        </main>
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