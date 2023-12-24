<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

// Simply use the default Joomla! error.php file because we don't want to remove our own
require join(DIRECTORY_SEPARATOR, [JPATH_THEMES, 'system', 'error.php']);
return;

$currentUri = Uri::getInstance();

// 301 redirect to the main page
// TODO: As of fall 2023 Yandex recommends against using the old 301 redirect method for non-existent URLs. Replace with a 404 redirect to the main page.
// Factory::getApplication()->redirect($currentUri->root(), 301);
Factory::getApplication()->redirect($currentUri->root(), Text::_('JERROR_PAGE_NOT_FOUND'), 'error', true); // Better variant as we can send our error text for system message
// Factory::getApplication()->redirect($currentUri->root(), 404); // Doesn't work. Redirects with 303 status.

// Factory::getApplication()->setHeader('Status', 404, true); // Outputs a non-HTML page with JERROR_PAGE_NOT_FOUND value text
// See https://docs.joomla.org/Custom_error_pages && /templates/system/error.php for more info.
// echo '<pre>', var_dump(Factory::getApplication()), '</pre>';