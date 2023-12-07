<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

// Prepare item title for safe usage in HTML
$articleTitle = $this->escape(trim($this->item->title));

// Create article link for this item
$articleLink = Route::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));

// Article images are JSON-encoded
$itemImages = json_decode($this->item->images);
$itemImages->image_intro_alt = $this->escape(trim($itemImages->image_intro_alt));

?>

<?php if (!empty($itemImages->image_intro)) : ?>
	<div class="service-image text-center">
		<?php
			// Use article title as alt text in case if intro image has none
			if (!$itemImages->image_intro_alt) $itemImages->image_intro_alt = $articleTitle;

			echo HTMLHelper::image($itemImages->image_intro, $itemImages->image_intro_alt, array('class' => 'service-image-file default-shadow', 'itemprop' => 'image', 'loading' => 'lazy'));
			?>
	</div>
<?php endif; ?>

<div class="service-title">
	<div class="service-title-inner default-shadow" itemprop="name"><?php echo $articleTitle; ?></div>
</div>

<?php if (!empty(trim($this->item->introtext))) : ?>
	<div class="airis-item-content service-description" itemprop="description">
		<?php echo $this->item->introtext; ?>
	</div>
<?php endif; ?>

<?php if ($this->item->params->get('show_readmore') && $this->item->readmore) : ?>
	<div class="service-links">
		<a class="btn" href="<?php echo $articleLink; ?>" itemprop="url"><?php echo Text::sprintf('COM_CONTENT_READ_MORE_TITLE'); ?></a>
	</div>
<?php endif; ?>