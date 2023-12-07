<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

// Acqurie Joomla! SEF setting value
$joomlaApplication = Factory::getApplication();
$joomlaSefEnabled = (bool) $joomlaApplication->getCfg('sef');

$itemIdHttpGetParameter = !empty($set_Itemid) ? '&Itemid=' . $set_Itemid : '';

?>

<div class="module-virtuemart-search<?php echo $params->get('moduleclass_sfx'); ?>">
	<form class="module-virtuemart-search-form" action="<?php echo Route::_("index.php?option=com_virtuemart&view=category&virtuemart_category_id={$category_id}{$itemIdHttpGetParameter}&limitstart=0&search=true"); ?>" method="get">
		<div class="module-virtuemart-search-form-items airis-flex<?php if ($button_pos == 'top' || $button_pos == 'bottom') echo ' airis-flex-column'; ?>">

			<div class="module-virtuemart-search-form-item module-virtuemart-search-form-item-text">
				<input type="text" name="keyword" maxlength="<?php echo $maxlength; ?>" size="<?php echo $width; ?>" placeholder="<?php echo $text; ?>" class="module-virtuemart-search-form-item-text-input">
			</div>

			<?php if ($button) : ?>

				<div class="module-virtuemart-search-form-item module-virtuemart-search-form-item-submit<?php if ($button_pos == 'top' || $button_pos == 'left') echo ' ', 'airis-flex-item-order-first'; ?>">
					<button class="module-virtuemart-search-form-item-submit-button btn" title="<?php echo $button_text; ?>">
						<?php echo ($imagebutton && $imagepath) ? HTMLHelper::image($imagepath, $button_text, array('loading' => 'lazy')) : $button_text; ?>
					</button>
				</div>

			<?php endif; ?>

		</div>

		<?php if (!$joomlaSefEnabled) : ?>

			<input type="hidden" name="option" value="com_virtuemart">
			<input type="hidden" name="view" value="category">
			<input type="hidden" name="virtuemart_category_id" value="<?php echo $category_id; ?>">
			<input type="hidden" name="limitstart" value="0">

			<?php if ($set_Itemid) : ?>

				<input type="hidden" name="Itemid" value="<?php echo $set_Itemid; ?>">

			<?php endif; ?>

		<?php endif; ?>

	</form>
</div>