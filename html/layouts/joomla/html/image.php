<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Image\Image;
use Joomla\CMS\Log\Log;
use Joomla\Utilities\ArrayHelper;

// Skip empty images
if (isset($displayData['src']) === false) {
	return;
}

$imageAttributes = '';
$pictureHtmlElementSources = [];

// TODO: Include additonal sources from found modern image format files of the same name as the passed one
// TODO: Test compatibility of used JS carousel libraries with <picture> tag

// Remove extra substring Joomla's media manager which can be harmful to SEO
$displayData['src'] = $this->escape(
	HTMLHelper::cleanImageURL($displayData['src'])->url,
);

// Class
if (isset($displayData['class']) && $displayData['class'] !== '') {
	$displayData['class'] .= ' img-fluid';
} else {
	$displayData['class'] = 'img-fluid';
}

// Alt
if (isset($displayData['alt'])) {
	if ($displayData['alt'] === false || $displayData['alt'] === '') {
		unset($displayData['alt']);
	} else {
		$displayData['alt'] = $this->escape($displayData['alt']);
	}
}

// Title
if (isset($displayData['title']) && $displayData['title'] !== '') {
	$displayData['title'] = $this->escape($displayData['title']);
}

// Width & Height
if (
	(isset($displayData['width']) === false || $displayData['width'] === '')
	|| (isset($displayData['height']) === false || $displayData['height'] === '')
) {
	try {
		// NOTE: Have to use an absolute path in layouts unlike when working with views
		$imagePathAbsolute = join(
			DIRECTORY_SEPARATOR,
			[
				JPATH_BASE,
				$displayData['src'],
			],
		);

		// Throws various exceptions if it is unable to create an image instance
		$joomlaImageClassInstance = new Image($imagePathAbsolute);

		// getWidth() and getHeight() too throw exceptions for nonexistent images
		if ($joomlaImageClassInstance->isLoaded()) {
			if (isset($displayData['width']) === false) {
				$displayData['width'] = $joomlaImageClassInstance->getWidth();
			}

			if (isset($displayData['height']) === false) {
				$displayData['height'] = $joomlaImageClassInstance->getHeight();
			}
		}
	} catch (Exception $joomlaImageClassInstanceCreationException) {
		if (JDEBUG) {
			Log::add(
				"Unable to acquire image dimensions of file \"$imagePathAbsolute\". " . $joomlaImageClassInstanceCreationException->getMessage(),
				Log::DEBUG,
				'templates-airis-html-layouts-joomla-html-image',
			);
		}
	}
}

// Loading
if (isset($displayData['loading']) === false) {
	$displayData['loading'] = 'lazy';
}

/* $supportedPictureElementSourceTypeExtensions = [
	'avif',
	'heic',
	'heif',
	'webp', // As of Joomla! 5.0, only this format is supported by the Image class
]; */

// Should probably just try to pass each file variant path to Image class contrutor or find a Joomla!'s proper equivalent of file_exists();
/* foreach ($supportedPictureElementSourceTypeExtensions as $supportedPictureElementSourceTypeExtension) {
	pathinfo($item->imageSrc, PATHINFO_)

	if (file_exists()) {}
} */

?>

<picture>
	<?php if (count($pictureHtmlElementSources) !== 0) : ?>

	<?php endif; ?>

	<img <?= ArrayHelper::toString($displayData); ?>>
</picture>