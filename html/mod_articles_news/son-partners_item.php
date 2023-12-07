<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Image\Image;

$itemImage = '';
$itemImageAlt = '';
$itemImageCaption = '';
$itemImages = json_decode($item->images);

// Check if this item has any images with intro image being the preferred one due to (usually) smaller size
if ($itemImages->image_intro)
{
	$itemImage = $itemImages->image_intro;
	$itemImageAlt = $itemImages->image_intro_alt;
	$itemImageCaption = $itemImages->image_intro_caption;
}
elseif ($itemImages->image_fulltext)
{
	$itemImage = $itemImages->image_fulltext;
	$itemImageAlt = $itemImages->image_fulltext_alt;
	$itemImageCaption = $itemImages->image_fulltext_caption;
}
else
{
	// Skip this item entierly if it has no intro or full article image
	return;
}

// Make item strings HTML-safe
$itemTitle = htmlspecialchars(trim($item->title), ENT_QUOTES, 'UTF-8');
$itemImageAlt = htmlspecialchars(trim($itemImageAlt), ENT_QUOTES, 'UTF-8');
$itemImageCaption = htmlspecialchars(trim($itemImageCaption), ENT_QUOTES, 'UTF-8');

// Use item title as item image alt if there was no alt value set for it
if (!$itemImageAlt) $itemImageAlt = $itemTitle;

// Prepare custom fields for direct access by field name below
$itemCustomFields = array();

foreach ($item->jcfields as $jcfield)
{
	$itemCustomFields[$jcfield->name] = $jcfield;
}

// Prepare item image
$itemImageAttributes = [
	'class' => 'partner-image',
	'loading' => 'lazy'
];

// Creating the Image class instance to acquire image dimensions
$itemImageJoomlaImageClassInstance = new Image($itemImage);

// In case if $item->imageSrc points to a non-existing file
if ($itemImageJoomlaImageClassInstance->isLoaded())
{
	$itemImageAttributes['width'] = $itemImageJoomlaImageClassInstance->getWidth();
	$itemImageAttributes['height'] = $itemImageJoomlaImageClassInstance->getHeight();
}

$itemImageHtml = HTMLHelper::image($itemImage, $itemImageAlt, $itemImageAttributes);

// Item Custom Fields
$itemCustomFieldPartnerUrl = htmlspecialchars(trim($itemCustomFields['airis-partner-url']->rawvalue), ENT_QUOTES, 'UTF-8');

// Finally prepare item link containing the image
$itemLinkWithImage = HTMLHelper::link($itemCustomFieldPartnerUrl, $itemImageHtml, array('title' => $itemTitle, 'target' => '_blank'));

?>

<li class="airis-module-partners__item">

	<?php if ($itemImageCaption) : ?>

		<figure class="partners__figure">

			<?php echo $itemLinkWithImage; ?>

			<figcaption class="partners__figure-caption"><?php echo $itemImageCaption; ?></figcaption>

		</figure>

	<?php else : ?>

		<?php echo $itemLinkWithImage; ?>

	<?php endif; ?>

</li>