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

// The Open Graph protocol basic support
if ($this->params->get('useOpenGraph')) {
    // TODO: Replace with a simple EOD declaration
    $openGraphMetaHtml = '<meta property="og:url" content="' . $currentUri->toString() /* $this->base */ . '" />' . PHP_EOL . "\t";
    $openGraphMetaHtml .= '<meta property="og:type" content="website" />' . PHP_EOL . "\t";
    $openGraphMetaHtml .= '<meta property="og:title" content="' . htmlspecialchars(trim($this->title)) . '" />' . PHP_EOL . "\t";
    $openGraphMetaHtml .= '<meta property="og:description" content="' . htmlspecialchars(trim($this->description)) . '" />' . PHP_EOL . "\t";

    if (!empty($this->params->get('openGraphImagePath'))) {
        $openGraphMetaHtml .= '<meta property="og:image" content="' . $currentUri->toString() . htmlspecialchars($this->params->get('open_graph_image_path'), ENT_QUOTES) . '" />' . PHP_EOL . "\t";
    } /* elseif ($articleImageSrc = !empty(json_decode($this->get('images')))) {
        $openGraphMetaHtml .= '<meta property="og:image" content="' . $currentUri->toString() . $articleImageSrc . '" />' . PHP_EOL . "\t";
    } */

    $openGraphMetaHtml .= '<meta property="og:locale" content="' . $this->language . '" />';

    $this->addCustomTag($openGraphMetaHtml);
}

// SVG favicon support
if (file_exists($templatePath . '/favicon.svg')) {
    $this->addCustomTag("<link rel=\"icon\" href=\"$templatePath/favicon.svg\">");
}

// Add Font Awesome
if ($this->params->get('loadFontAwesome')) {
    $this->addStyleSheet($templatePath . '/libraries/fontawesome/css/fontawesome.min.css', $autoVersion);
    $this->addStyleSheet($templatePath . '/libraries/fontawesome/css/solid.min.css', $autoVersion);
    $this->addStyleSheet($templatePath . '/libraries/fontawesome/css/regular.min.css', $autoVersion);
    $this->addScriptOptions('tpl_airis', ['loadFontAwesome' => true]);

    if ($this->params->get('loadFontAwesomeBrands')) {
        $this->addStyleSheet($templatePath . '/libraries/fontawesome/css/brands.min.css', $autoVersion);
        $this->addScriptOptions('tpl_airis', ['loadFontAwesomeBrands' => true]);
    }
}

// Add Inputmask
if ($this->params->get('loadInputmask')) {
    switch ($this->params->get('loadInputmaskFlavor')) {
        case 'native':
            $this->addScript($templatePath . '/libraries/inputmask/inputmask.min.js', $autoVersion, $deferScript);
            break;
        case 'jquery':
            if (($this->params->get('useJoomlaJquery') || $this->params->get('loadJquery'))) {
                $this->addScript($templatePath . '/libraries/inputmask/jquery.inputmask.min.js', $autoVersion, $deferScript);
                if ($this->params->get('loadInputmaskBinding')) {
                    $this->addScript($templatePath . '/libraries/inputmask/inputmask.binding.js', $autoVersion, $deferScript);
                }
            }
            break;
    }
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
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>">
    <head>
        <jdoc:include type="head" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body class="airis-page-template-component airis-asides-none">
        <div class="airis-area-message">
            <div class="airis-area-container airis-container container">
                <jdoc:include type="message" />
            </div>
        </div>
        <main class="airis-main">
            <jdoc:include type="component" />
        </main>
    </body>
</html>