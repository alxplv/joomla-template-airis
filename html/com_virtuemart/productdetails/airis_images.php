<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\Factory;
// use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Image\Image;

// Acquire template settings to possibly leverage any loaded lightbox libraries
$templateParameters = Factory::getApplication()->getTemplate(true)->params;

$productName = htmlspecialchars(trim(vmText::_($this->product->product_name)), ENT_QUOTES, 'UTF-8');
$productImage = $this->product->images[0]; // Product always has one "No image" media if there are no images defined explicitly
$productImage->file_description = htmlspecialchars(trim($productImage->file_description), ENT_QUOTES, 'UTF-8'); // Media "Displayed image subtitle" field
$productImage->file_meta = htmlspecialchars(trim($productImage->file_meta), ENT_QUOTES, 'UTF-8'); // Media "Image Alt-Text" field
$productImageClasses = 'productdetails-image-file productdetails-image-file-main airis-image';
$productImageLinkAttributes = 'class="productdetails-image-link productdetails-image-link-main" target="_blank"';

// If size limits are configured for displaying main product image, display a thumbnail of configured size instead of the source media file
$thumbnailWidthConfigurationValue = VmConfig::get('img_width_full', 0);
$thumbnailHeightConfigurationValue = VmConfig::get('img_height_full', 0);

// Use product name as alt and title attribute values
if (!isset($productImage->file_meta) || $productImage->file_meta === '') {
	$productImage->file_meta = $productName;
}

// Use a lightbox library for the main product image if possible
if ($templateParameters->get('loadFancybox')) {
	// Do not group product images into lightbox gallery if there is only one image or if the "Open additional images in the main position" option is enabled
	if (count($this->product->images) === 1 || VmConfig::get('add_img_main', 0)) {
		$productImageLinkAttributes .= ' data-fancybox';
	} else {
		$productImageLinkAttributes .= ' data-fancybox="productdetails-gallery-item"';
	}

	$productImageLightboxCaption = '';

	// Try using the alt value if there was no subtitle defined for this media
	if (isset($productImage->file_description) && $productImage->file_description !== '') {
		$productImageLightboxCaption = $productImage->file_description;
	} elseif (isset($productImage->file_meta) && $productImage->file_meta !== $productName) {
		$productImageLightboxCaption = $productImage->file_meta;
	}

	if (isset($productImageLightboxCaption) && $productImageLightboxCaption !== '') {
		$productImageLinkAttributes .= " data-caption=\"$productImageLightboxCaption\"";
	}

} /* elseif ($templateParameters->get('loadGlightbox')) {
	vmJsApi::addJScript(
		'productdetails-gallery-glightbox',
		'
			// TODO: Write code...
		',
	);
} */

if ($thumbnailWidthConfigurationValue || $thumbnailHeightConfigurationValue) {
	/* Explicitly set thumbnail width and height here since this is what we do in displayMediaThumb() below
	and otherwise getFileUrlThumb() returns a thumbnail generated at media creation time sized in accordance
	with a different set of VM settings */
	$productImageJoomlaImageClassInstance = new Image(
		$productImage->getFileUrlThumb(
			$thumbnailWidthConfigurationValue,
			$thumbnailHeightConfigurationValue,
		),
	);

	echo $productImage->displayMediaThumb(
		[
			'class' => $productImageClasses,
			'width' => $productImageJoomlaImageClassInstance->getWidth(),
			'height' => $productImageJoomlaImageClassInstance->getHeight(),
		],
		true,
		$productImageLinkAttributes,
		true,
		false,
		false,
		$thumbnailWidthConfigurationValue,
		$thumbnailHeightConfigurationValue,
	);
} else {
	$productImageJoomlaImageClassInstance = new Image($productImage->getUrl());

	echo $productImage->displayMediaFull(
		[
			'class' => $productImageClasses,
			'width' => $productImageJoomlaImageClassInstance->getWidth(),
			'height' => $productImageJoomlaImageClassInstance->getHeight(),
		],
		true,
		$productImageLinkAttributes,
		false,
	);
}