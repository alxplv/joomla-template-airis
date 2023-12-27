<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Image\Image;

// Acquire template settings to possibly leverage any loaded lightbox libraries
$templateParameters = Factory::getApplication()->getTemplate(true)->params;

$productName = htmlspecialchars(trim(vmText::_($this->product->product_name)), ENT_QUOTES, 'UTF-8');
$productImagesTotal = count($this->product->images);
$productImageClasses = 'productdetails-image-file productdetails-image-file-additional';
$productImageLinkAttributes = 'class="productdetails-image-link productdetails-image-link-additional" target="_blank"';

// Display additional images inside the main product image container first instead of opening up a lightbox
$useMainImagePosition = VmConfig::get('add_img_main', false);

// Invert the boolean value from config to directly use it as a starting index
$productImagesStartingIndex = $useMainImagePosition ? 0 : 1;

// Use media "Displayed image subtitle" value (best suitable for figcaption and lightbox libraries) in place of alt and title attributes
$useMediaSubtitleAsImageAlt = VmConfig::get('add_thumb_use_descr', false);

// Our custom JS for additional images in the main position
if ($useMainImagePosition)
{
	// Skip target="_blank" and lightbox actions and replace the main product image via the clicked additional one
	vmJsApi::addJScript('productdetails-display-additional-images-in-main-image', '
		jQuery(document).on("ready", function () {
			jQuery(".productdetails-view").on("click", ".productdetails-image-link-additional", function (event) {

				// event.preventDefault();

				var $clickedAdditionalImageLink = jQuery(this);
				var $clickedAdditionalImage = $clickedAdditionalImageLink.children();

				// jQuery(".productdetails-image-link-main").replaceWith($clickedAdditionalImageLink).toggleClass("productdetails-image-link-main productdetails-image-link-additional").children(".productdetails-image-file").toggleClass("productdetails-image-file-main productdetails-image-file-additional");
			});
		});
	');
}

?>

<ul class="productdetails-images-additional airis-flex airis-flex-wrap list-unstyled">
	<?php for ($i = $productImagesStartingIndex; $i < $productImagesTotal; $i++) : ?>
		<li class="productdetails-image productdetails-image-additional">
			<?php
				$productImageAdditional = $this->product->images[$i];
				$productImageAdditional->file_description = htmlspecialchars(trim($productImageAdditional->file_description), ENT_QUOTES, 'UTF-8'); // Media "Displayed image subtitle" field
				$productImageAdditional->file_meta = htmlspecialchars(trim($productImageAdditional->file_meta), ENT_QUOTES, 'UTF-8'); // Media "Image Alt-Text" field

				// Media "Image Alt-Text" value can be replaced by "Displayed image subtitle" value if required by VirtueMart settings
				if ($useMediaSubtitleAsImageAlt && !empty($productImageAdditional->file_description)) $productImageAdditional->file_meta = $productImageAdditional->file_description;

				// Use product name as alt and title attribute values in case if "Image Alt-Text" is still empty
				if (empty($productImageAdditional->file_meta)) $productImageAdditional->file_meta = $productName;

				// Use a lightbox library for additional product images if possible
				if ($templateParameters->get('loadFancybox'))
				{
					// Do not group product images into lightbox gallery if "Open additional images in the main position" option is enabled
					$productImagePerImageLinkAttributes = $useMainImagePosition ? ' data-fancybox' : ' data-fancybox="productdetails-gallery-item"';
					$productImageLightboxCaption = '';

					// Try using the alt value if there was no subtitle defined for this media
					if (!empty($productImageAdditional->file_description))
					{
						$productImageLightboxCaption = $productImageAdditional->file_description;
					}
					elseif ($productImageAdditional->file_meta !== $productName)
					{
						$productImageLightboxCaption = $productImageAdditional->file_meta;
					}

					if (!empty($productImageLightboxCaption)) $productImagePerImageLinkAttributes .= " data-caption=\"$productImageLightboxCaption\"";
				}
				else if ($templateParameters->get('loadGlightbox'))
				{

				}
				// TODO: Find out how to override /layouts/jooma/html/image.php template which is used everywhere like HTMLHelper::image(), by into and full images in com_content and by Virtuemart too.
				
				// This works and does include our airis template override
				// echo Joomla\CMS\Layout\LayoutHelper::render('joomla.html.image', array('src' => $productImageAdditional->getFileUrlThumb(), 'alt' => $productName));
				
				// And this one doesn't work and keeps including the stock layout
				// echo HTMLHelper::image($productImageAdditional->getFileUrlThumb(), '', '');
				// echo HTMLHelper::_('image', $productImageAdditional->getFileUrlThumb(), '', '');
				$productImageAdditionalJoomlaImageClassInstance = new Image($productImageAdditional->getFileUrlThumb());
				echo $productImageAdditional->displayMediaThumb(array('class' => $productImageClasses, 'width' => $productImageAdditionalJoomlaImageClassInstance->getWidth(), 'height' => $productImageAdditionalJoomlaImageClassInstance->getHeight()), true, $productImageLinkAttributes . $productImagePerImageLinkAttributes);
			?>
		</li>
	<?php endfor; ?>
</ul>