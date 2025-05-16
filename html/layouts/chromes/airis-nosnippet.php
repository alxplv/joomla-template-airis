<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
// use Joomla\CMS\Factory;
// use Joomla\CMS\Layout\LayoutHelper;

// Simple guard statement
if (isset($displayData['params']) === false) {
    return;
}

// Prepare module options
$moduleContainerElement = $displayData['params']->get('module_tag', 'div');

// Search engines declare that only <div>, <section> and <span> elements are a valid target for data-nosnippet
$noSnippetAllowedTags = [
    'div',
    'section',
    /* 'span', // Not used by Joomla! as a module container tag option */
];

if (in_array($moduleContainerElement, $noSnippetAllowedTags, true) === true) {
    $displayData['params']->set('airisChromeUseNoSnippetAttribute', true);
}

// FIXME: This wouldn't work so we had to revert to a simple include
// LayoutHelper::render('chromes.airis', $displayData, JPATH_THEMES . DIRECTORY_SEPARATOR . Factory::getApplication()->getTemplate() . '/html/layouts');
include join(DIRECTORY_SEPARATOR, [__DIR__, 'airis.php']);