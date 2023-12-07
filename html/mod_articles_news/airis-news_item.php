<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;

// Make item title HTML-safe
$itemTitle = htmlspecialchars(trim($item->title), ENT_QUOTES, 'UTF-8');

// Prepare item image
if ($params->get('img_intro_full') !== 'none' && !empty($item->imageSrc))
{
	// Create image alt from item title if required
	if (!empty(trim($item->imageAlt))) $item->imageAlt = $itemTitle;

	// Make image caption HTML-safe
	$item->imageCaption = htmlspecialchars(trim($item->imageCaption), ENT_QUOTES, 'UTF-8');

	// TODO: Add image width and height via creating Image instance
	$itemImage = HTMLHelper::image($item->imageSrc, $item->imageAlt, array('class' => 'airis-module-news-list-item-image-file', 'loading' => 'lazy'));
	$itemLinkWithImage = HTMLHelper::link($item->link, $itemImage, array('class' => 'airis-module-news-list-item-image-link', 'title' => $itemTitle));
}

?>

<li class="airis-module-news-list-item">

	<?php if (!empty($itemLinkWithImage)) : ?>
		<figure class="airis-module-news-list-item-image">

			<?php echo $itemLinkWithImage; ?>

			<?php if (!empty($item->imageCaption)) : ?>
				<figcaption class="airis-module-news-list-item-image-caption">
					<?php echo $item->imageCaption; ?>
				</figcaption>
			<?php endif; ?>

		</figure>
	<?php endif; ?>

	<?php if ($params->get('item_title')) : ?>
		<div class="airis-module-news-list-item-title">
			<<?php echo $params->get('item_heading', 'h4'); ?> class="airis-module-news-list-item-title-heading">

				<?php if (!empty($item->link) && $params->get('link_titles')) : ?>
					<?php echo HTMLHelper::link($item->link, $itemTitle, array('class' => 'airis-module-news-list-item-title-heading-link')); ?>
				<?php else : ?>
					<?php echo $itemTitle; ?>
				<?php endif; ?>

			</<?php echo $params->get('item_heading', 'h4'); ?>>
		</div>
	<?php endif; ?>

</li>