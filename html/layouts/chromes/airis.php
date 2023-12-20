<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Skip empty modules
if ($displayData['module']->content === '') {
    return;
}

// Basic handles
$module = $displayData['module'];
$moduleParams = $displayData['params'];

// Prepare module options
$moduleBootstrapGridSize = $moduleParams->get('bootstrap_size', '0');
$moduleClass = htmlspecialchars(rtrim($moduleParams->get('moduleclass_sfx', '')), ENT_QUOTES, 'UTF-8'); // Leftside whitespace is perfectly acceptable
$moduleClassFullTrim = ltrim($moduleClass);
$moduleContainerElement = $moduleParams->get('module_tag', 'div');

// Prepare HTML class and other attribute strings for our chrome
$moduleContainerAttributes = ''; // Could use Joomla\Utilities\ArrayHelper::toString() here instead of plain strings but it doesn't support value-less attributes properly
$moduleContainerClasses = "airis-module airis-module_container-type_$moduleContainerElement";

// "Module Class" option. If the suffix value has leading whitespace then output it as a separate class instead of a BEM modifier-name_modifier token pair
if ($moduleClass !== '') {
    if (strlen($moduleClass) !== strlen($moduleClassFullTrim)) {
        $moduleContainerClasses .= " $moduleClassFullTrim";
    } else {
        $moduleContainerClasses .= " airis-module_suffix_$moduleClassFullTrim";
    }
}

// "Bootstrap Size" option
if ($moduleBootstrapGridSize !== '0') {
    $moduleContainerClasses .= " col-md-$moduleBootstrapGridSize";
}

// "Show Tile" option
if ($module->showtitle) {
    // Prepare module options
    $moduleHeadingClass = htmlspecialchars(trim($moduleParams->get('header_class', '')), ENT_QUOTES, 'UTF-8');
    $moduleHeadingElement = $moduleParams->get('header_tag', 'h3');
    $moduleHeadingElementCharacters = str_split($moduleHeadingElement);
    $moduleTitle = htmlspecialchars(trim($module->title), ENT_QUOTES, 'UTF-8');

    // Prepare HTML class and other attribute strings
    $moduleHeadingAttributes = '';
    $moduleHeadingClasses = 'airis-module__heading';

    // Additional "Module Tag" option processing. The <div> elements without certain aria-roles are not allowed to use the aria-labelledby attribute and we don't use these here
    if ($moduleContainerElement !== 'div') {
        $moduleIdHash = md5($module->id . hrtime(true)); // The module ID alone wouldn't suffice here as there can be multiple instances of the same module with the same ID per page rendered which would break both the HTML and the ARIA specs
        $moduleIdHtmlAttribute = "airis-module__heading_unique-id_$moduleIdHash";
        $moduleContainerAttributes .= " aria-labelledby=\"$moduleIdHtmlAttribute\"";
        $moduleHeadingAttributes .= "id=\"$moduleIdHtmlAttribute\"";
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
}

// This option is only set when this chrome is invoked by the airis-nosnippet proxy chrome
if ($displayData['params']->get('airisChromeUseNoSnippetAttribute', false)) {
    $moduleContainerAttributes .= ' data-nosnippet';
}

?>

<<?php echo $moduleContainerElement; ?> class="<?php echo $moduleContainerClasses; ?>"<?php echo $moduleContainerAttributes; ?>>

    <?php if ($module->showtitle) : ?>
        <div class="airis-module__header">
            <<?php echo $moduleHeadingElement; ?> class="<?php echo $moduleHeadingClasses; ?>"<?php echo $moduleHeadingAttributes; ?>>
                <?php echo $moduleTitle; ?>
            </<?php echo $moduleHeadingElement; ?>>
        </div>
    <?php endif; ?>

    <div class="airis-module__content">
        <?php echo $module->content; ?>
    </div>

</<?php echo $moduleContainerElement; ?>>