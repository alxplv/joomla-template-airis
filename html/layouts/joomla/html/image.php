<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Image\Image;
use Joomla\Utilities\ArrayHelper;

// TODO: Include additonal sources from found modern image format files of the same name as the passed one
// TODO: Test compatibility of used JS carousel libraries with <picture> tag

// Src
if (isset($displayData['src'])) {
	// Remove extra substring Joomla's media manager which can be harmful to SEO
	$itemImageDataCleaned = HTMLHelper::cleanImageURL($displayData['src']);
	$displayData['src'] = $this->escape($itemImageDataCleaned->url);
}

// Class
if (isset($displayData['class'])) {
	$displayData['class'] = 'airis-image ' . $displayData['class'];
} else {
	$displayData['class'] = 'airis-image';
}

// Alt
if (isset($displayData['alt'])) {
	if ($displayData['alt'] === false) {
		unset($displayData['alt']);
	} else {
		$displayData['alt'] = $this->escape($displayData['alt']);
	}
}

// Title
if (!empty($displayData['title'])) {
	$displayData['title'] = $this->escape($displayData['title']);
}

// Width & Height
if (empty($displayData['width']) || empty($displayData['height'])) {
	$joomlaImageClassInstance = new Image($displayData['src']);

	// In case if path string points to a non-existing file
	if ($joomlaImageClassInstance->isLoaded()) {
		if (empty($displayData['width'])) {
			$displayData['width'] = $joomlaImageClassInstance->getWidth();
		}

		if (empty($displayData['height'])) {
			$displayData['height'] = $joomlaImageClassInstance->getHeight();
		}
	}
}

// Loading
if (empty($displayData['loading'])) {
	$displayData['loading'] = 'lazy';
}

/* $supportedPictureElementSourceTypeExtensions = [
	'avif',
	'heic',
	'heif',
	'webp',
];

// Should probably just try to pass each file variant path to Image class contrutor or find a Joomla!'s proper equivalent of file_exists();
foreach ($supportedPictureElementSourceTypeExtensions as $supportedPictureElementSourceTypeExtension) {
	pathinfo($item->imageSrc, PATHINFO_)

	if (file_exists()) {}
} */

$pictureSources = '';

// Implode image attributes into a final string
$imageAttributes = ArrayHelper::toString($displayData);

echo "<picture>$pictureSources<img $imageAttributes></picture>";