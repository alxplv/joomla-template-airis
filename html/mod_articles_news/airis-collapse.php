<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;

// Module sublayout file for each Joomla! item
$moduleSublayoutFileName = ModuleHelper::getLayoutPath('mod_articles_news', pathinfo(__FILE__, PATHINFO_FILENAME) . '_item');

// Process module data and options
$moduleClassSuffix = htmlspecialchars(rtrim($params->get('moduleclass_sfx', '')), ENT_QUOTES, 'UTF-8'); // Not using trim() here to keep possible and perfectly acceptable leading whitespace
$moduleClassSuffix = preg_replace('/\s{2,}/', ' ', $moduleClassSuffix); // Also remove non-singular whitespaces just in case

// Custom options prefix
$airisModuleClassSuffixParamPrefix = ' airis-module-param-';

// Supported custom options of this view
$airisModuleClassSuffixParams = [
    'accordion-enabled' => false,
    'accordion-collapse-in-first-item' => false,
];

if ($moduleClassSuffix && strpos($moduleClassSuffix, $airisModuleClassSuffixParamPrefix) !== false) {
    // Try to acquire new values for any found custom options
    foreach ($airisModuleClassSuffixParams as $airisModuleClassSuffixParamKey => $airisModuleClassSuffixParamValue) {
        $airisModuleClassSuffixParamPrefixed = $airisModuleClassSuffixParamPrefix . $airisModuleClassSuffixParamKey;

        // Boolean values are simply toggled / inverted as their class strings don't have any attached value in the form of a hyphen-separated suffix
        if (is_bool($airisModuleClassSuffixParamValue)) {
            if (strpos($moduleClassSuffix, $airisModuleClassSuffixParamPrefixed) !== false) {
                $airisModuleClassSuffixParams[$airisModuleClassSuffixParamKey] = !$airisModuleClassSuffixParamValue;
            }
        }
    }
}

// https://getbootstrap.com/2.3.2/javascript.html#collapse