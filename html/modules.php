<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

function AirisGetModuleContent($module, &$params, &$attribs, $noSnippetModule = false)
{
    // Simple guard statement
    if (!isset($module->content)) {
        return;
    }

    $moduleHtml = '';
    $moduleContainerClasses = 'airis-module';
    $moduleContainerAttributes = '';
    $moduleHeadingClasses = 'airis-module__heading';
    $moduleTitle = htmlspecialchars(trim($module->title), ENT_QUOTES, 'UTF-8');

    //
    // Processing advanced options of the module
    //

    // Module class suffix
    $moduleContainerClasses .= htmlspecialchars($params->get('moduleclass_sfx'), ENT_QUOTES, 'UTF-8');

    // Container and heading HTML tag types with addittional class for easy styling
    $moduleContainerTagType = $params->get('module_tag', 'div');
    $moduleHeadingTagType = $params->get('header_tag', 'h3');
    $moduleHeadingClasses .= ' airis-module__heading_level_' . str_replace('h', '', $moduleHeadingTagType);

    // Bootstrap grid size
    $moduleBootstrapGridSize = $params->get('bootstrap_size', '0');
    if ($moduleBootstrapGridSize !== '0') {
        $moduleContainerClasses .= ' span' . $moduleBootstrapGridSize;
    }

    // Heading classes
    $moduleHeadingClassesExtra = htmlspecialchars(trim($params->get('header_class', '')), ENT_QUOTES, 'UTF-8');
    if ($moduleHeadingClassesExtra) {
        $moduleHeadingClasses .= ' ' . $moduleHeadingClassesExtra;
    }

    // Stop the contents of this module from appearing in search engine results page descriptions if required. Search engines declare that only <div>, <section> and <span> elements are a valid target for this attribute.
    if ($noSnippetModule && in_array($moduleContainerTagType, ['div', 'section'])) {
        $moduleContainerAttributes = $noSnippetModule ? ' data-nosnippet' : '';
    }

    // Populate module markup with processed options data
    $moduleHtml .= "<$moduleContainerTagType class=\"$moduleContainerClasses\"$moduleContainerAttributes>";

    if ($module->showtitle) {
        $moduleHtml .= '<div class="airis-module__header">';
        $moduleHtml .= "<$moduleHeadingTagType class=\"$moduleHeadingClasses\">$moduleTitle</$moduleHeadingTagType>";
        $moduleHtml .= '</div>';
    }

    $moduleHtml .= "<div class=\"airis-module__content\">$module->content</div>";
    $moduleHtml .= "</$moduleContainerTagType>";

    return $moduleHtml;
}

function modChrome_airis($module, &$params, &$attribs)
{
    if (!empty($module->content)) {
        echo AirisGetModuleContent($module, $params, $attribs);
    }
}

function modChrome_airis_nosnippet($module, &$params, &$attribs)
{
    if (!empty($module->content)) {
        echo AirisGetModuleContent($module, $params, $attribs, true);
    }
}