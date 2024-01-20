<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Image\Image;
use Joomla\CMS\Language\Text;

// Prepare custom fields for direct access by field name below
$itemCustomFields = [];

if (isset($item->jcfields)) {
    $itemCustomFields = [];

    // Prepare custom fields for direct access by field name below
    foreach ($item->jcfields as $jcfield) {
        $itemCustomFields[$jcfield->name] = $jcfield;
    }
}

// Process item data and make it HTML-safe
$companyName = htmlspecialchars(trim($item->title), ENT_QUOTES, 'UTF-8');
$personName = htmlspecialchars(trim($itemCustomFields['airis-testimonial-person-name']->value), ENT_QUOTES, 'UTF-8');
$personPosition = htmlspecialchars(trim($itemCustomFields['airis-testimonial-person-position']->value), ENT_QUOTES, 'UTF-8');
$personLocation = htmlspecialchars(trim($itemCustomFields['airis-testimonial-person-location']->value), ENT_QUOTES, 'UTF-8');

// Also remove possibly unnecessary whitespace if certain custom fields are empty
$personPositionWithCompanyName = trim("$personPosition $companyName");

?>

<li class="airis-testimonial">
	<figure>

		<?php // TODO: Acquire item image in the same way it's done in airis-partners ?>
		<?php if ($params->get('img_intro_full') !== 'none' && $item->imageSrc) : ?>
			<div class="airis-testimonial-image">
				<?php

					// Use custom image alt if this item has none defined
					$imageAlt = htmlspecialchars(trim($item->imageAlt), ENT_QUOTES, 'UTF-8');

					// Get rid of useless leading comma and whitespace if certain custom fields are empty
					$personPositionLowerCase = mb_strtolower($personPosition);
					if (empty($imageAlt)) $imageAlt = trim("$personName, $personPositionLowerCase $companyName", ', ');

					$itemImageAttributes = [
						'class' => 'airis-testimonial-image-file',
						'loading' => 'lazy'
					];

					$itemImageJoomlaImageClassInstance = new Image($item->imageSrc);

					// In case if $item->imageSrc points to a non-existing file
					if ($itemImageJoomlaImageClassInstance->isLoaded())
					{
						$itemImageAttributes['width'] = $itemImageJoomlaImageClassInstance->getWidth();
						$itemImageAttributes['height'] = $itemImageJoomlaImageClassInstance->getHeight();
					}

					echo HTMLHelper::image($item->imageSrc, $imageAlt, $itemImageAttributes);

				?>
			</div>
		<?php endif; ?>

		<div class="airis-testimonial-text">

			<?php if ($personName) : ?>
				<figcaption class="airis-testimonial-text-name">
					<cite class="airis-testimonial-text-name-cite">
						<?php echo $personName; ?>
					</cite>
				</figcaption>
			<?php endif; ?>

			<?php if ($personPositionWithCompanyName) : ?>
				<div class="airis-testimonial-text-position-and-company-name">
					<?php echo $personPositionWithCompanyName; ?>
				</div>
			<?php endif; ?>

			<?php if ($personLocation) : ?>
				<div class="airis-testimonial-text-location">
					<?php echo $personLocation; ?>
				</div>
			<?php endif; ?>

			<?php // TODO: Only include Font Awesome icons if it's enabled in Joomla or template ?>
			<?php // if ($webAssets->assetExists('style', 'fontawesome') && $webAssets->isAssetActive('style', 'fontawesome')) ?>
			<blockquote class="airis-testimonial-text-quote">
				<span class="fas fa-angle-double-left airis-testimonial-text-quote-icon airis-testimonial-text-quote-icon-before" aria-hidden="true"></span>
					<?php echo $item->introtext; ?>
				<span class="fas fa-angle-double-right airis-testimonial-text-quote-icon airis-testimonial-text-quote-icon-after" aria-hidden="true"></span>
			</blockquote>

			<?php if (isset($item->link) && $item->readmore && $params->get('readmore')) : ?>
				<div class="airis-testimonial-text-readmore">
					<?php echo HTMLHelper::link($item->link, Text::_('TPL_AIRIS_MOD_ARTICLES_NEWS_TESTIMONIALS_READ_MORE_TITLE'), array('class' => 'airis-testimonial-text-readmore-link')); ?>
				</div>
			<?php endif; ?>

		</div>

	</figrure>
</li>