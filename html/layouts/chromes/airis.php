<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\Utilities\ArrayHelper;

// Skip empty modules
if ($displayData['module']->content === '') {
    return;
}

// Basic handles
$module = $displayData['module'];
$moduleParams = $displayData['params'];

// Prepare module options
$moduleBootstrapGridSize = $moduleParams->get('bootstrap_size', '0');
$moduleClassSuffix = htmlspecialchars(rtrim($moduleParams->get('moduleclass_sfx', '')), ENT_QUOTES, 'UTF-8'); // Leftside whitespace is perfectly acceptable
$moduleClassSuffixFullTrim = ltrim($moduleClassSuffix);
$moduleContainerElement = $moduleParams->get('module_tag', 'div');
$moduleHeadingClass = htmlspecialchars(trim($moduleParams->get('header_class', '')), ENT_QUOTES, 'UTF-8');
$moduleHeadingElement = $moduleParams->get('header_tag', 'h3');
$moduleHeadingElementCharacters = str_split($moduleHeadingElement);
$moduleTitle = htmlspecialchars(trim($module->title), ENT_QUOTES, 'UTF-8');

// Prepare HTML class and other attribute strings for our chrome
$moduleContainerAttributes = [];
$moduleContainerClasses = "airis-module airis-module_container-type_$moduleContainerElement";
$moduleHeadingAttributes = [];
$moduleHeadingClasses = 'airis-module__heading';

// This flag is set when this chrome is invoked by the airis-nosnippet proxy chrome
if (isset($displayData['airisModuleChromeUseDataNoSnippetAttribute']) && $displayData['airisModuleChromeUseDataNoSnippetAttribute'] === true) {
    $moduleContainerAttributes['data-nosnippet'] = true;
}

// "Module Class Suffix" option. If the suffix value has leading whitespace then output it as a separate class instead of a BEM modifier-name_modifier-value pair
if ($moduleClassSuffix !== '') {
    if (strlen($moduleClassSuffix) !== strlen($moduleClassSuffixFullTrim)) {
        $moduleContainerClasses .= " $moduleClassSuffixFullTrim";
    } else {
        $moduleContainerClasses .= " airis-module_type_$moduleClassSuffixFullTrim";
    }
}

// "Bootstrap Size" option
if ($moduleBootstrapGridSize !== '0') {
    $moduleContainerClasses .= " col-md-$moduleBootstrapGridSize";
}

// "Module Tag" option. The <div> elements without certain aria-roles are not allowed to use the aria-labelledby attribute and we don't use these here
if ($moduleContainerElement !== 'div') {
    $moduleIdUniqueSuffix = md5($module->id . hrtime(true)); // The module ID alone wouldn't suffice here as there can be multiple instances of the same module with the same ID per page rendered which would break both the HTML and the ARIA specs
    $moduleIdHtmlAttribute = "airis-module__heading_unique-id_$moduleIdUniqueSuffix";
    $moduleContainerAttributes['aria-labelledby'] = $moduleIdHtmlAttribute;
    $moduleHeadingAttributes['id'] = $moduleIdHtmlAttribute;
}

// "Header Tag" option. Apply different class sets based on the selected type of the element
if (count($moduleHeadingElementCharacters) === 2 && $moduleHeadingElementCharacters[0] === 'h' && is_numeric($moduleHeadingElementCharacters[1])) {
    $moduleHeadingClasses .= " airis-module__heading_type_h airis-module__heading_level_$moduleHeadingElementCharacters[1]";
} else {
    $moduleHeadingClasses .= " airis-module__heading_type_$moduleHeadingElement";
}

// "Header Class" option
if ($moduleHeadingClass !== '') {
    $moduleHeadingClasses .= " $moduleHeadingClass";
}

// Optional whitespace for container and heading HTML attributes output to avoid using static markup whitespace which will lead to unnecessary whitespace in tags without additional attributes set
$moduleContainerAttributesPadding = (count($moduleContainerAttributes)) ? ' ' : '';
$moduleHeadingAttributesPadding = (isset($moduleContainerAttributes['aria-labelledby'])) ? ' ' : '';

?>

<<?php echo $moduleContainerElement; ?> class="<?php echo $moduleContainerClasses; ?>"<?php echo $moduleContainerAttributesPadding, ArrayHelper::toString($moduleContainerAttributes); ?>>

    <?php if ($module->showtitle) : ?>

        <div class="airis-module__header">
            <<?php echo $moduleHeadingElement; ?> class="<?php echo $moduleHeadingClasses; ?>"<?php echo $moduleHeadingAttributesPadding, ArrayHelper::toString($moduleHeadingAttributes); ?>>
                <?php echo $moduleTitle; ?>
            </<?php echo $moduleHeadingElement; ?>>
        </div>

    <?php endif; ?>

    <div class="airis-module__content">
        <?php echo $module->content; ?>
    </div>

</<?php echo $moduleContainerElement; ?>>