<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

// Basic handles
$joomlaApplication = Factory::getApplication();
$templateParameters = $joomlaApplication->getTemplate(true)->params;
$siteName = $joomlaApplication->getCfg('sitename');

$menuBrandImageSrc = htmlspecialchars(trim($templateParameters->params->get('', '')), ENT_QUOTES, 'UTF-8');
$menuBrandText = htmlspecialchars(trim($templateParameters->params->get('', '')), ENT_QUOTES, 'UTF-8');

if ($menuBrandImageSrc !== '' || $menuBrandText !== '') {
    $menuBrandLinkAttributes = [
        'class' => '__brand-link navbar-brand',
        'title' => $siteName,
    ];

    $menuBrandLinkHref = '/';

    if ($menuBrandImageSrc !== '') {
        $menuBrandContent = HTMLHelper::link(
            $menuBrandLinkHref,
            HTMLHelper::image(
                $menuBrandImageSrc,
                $siteName,
                [
                    'class' => '__brand-image',
                ],
            ),
            $menuBrandLinkAttributes,
        );
    } elseif ($menuBrandText !== '') {
        $menuBrandContent = HTMLHelper::link(
            $menuBrandLinkHref,
            $siteName,
            $menuBrandLinkAttributes,
        );
    }
}

include join(DIRECTORY_SEPARATOR, [__DIR__, 'airis.php']);