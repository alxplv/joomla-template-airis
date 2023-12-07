<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;

$product = $viewData['product'];
$position = $viewData['position'];
$customTitle = isset($viewData['customTitle']) ? $viewData['customTitle'] : false;
$class = isset($viewData['class']) ? $viewData['class'] : $class = 'product-fields virtuemart-product-custom-fields';

?>

<?php if (!empty($product->customfieldsSorted[$position])) : ?>

	<div class="<?php echo $class; ?>">

		<?php if ($customTitle && isset($product->customfieldsSorted[$position][0])) : ?>

			<?php $field = $product->customfieldsSorted[$position][0]; ?>

			<div class="product-field-title virtuemart-product-custom-field-title">
				<?php echo vmText::_($field->custom_title); ?>
				<?php if ($field->custom_tip) : ?>
					<?php echo HTMLHelper::tooltip(vmText::_($field->custom_tip), vmText::_($field->custom_title), 'tooltip.png'); ?>
				<?php endif; ?>
			</div>

		<?php endif; ?>

		<?php $customTitle = null; ?>

		<?php foreach ($product->customfieldsSorted[$position] as $field) : ?>

			<?php if ($field->is_hidden || empty($field->display)) continue; ?>

			<?php
				// TODO: Replace strpos with str_starts_with once we've moved to PHP 8 or newer for good
				// Airis-prefixed fields are processed separately
				$airisCustomFieldPrefix = 'airis-';
				$airisCustomFieldDescription = htmlspecialchars(trim(vmText::_($field->custom_desc)), ENT_QUOTES, 'UTF-8');
			?>

			<?php if (strpos($airisCustomFieldDescription, $airisCustomFieldPrefix) === 0) : ?>
				<?php // TODO: Do not display custom field description for certain (or for all) airis-prefixed custom fields since most of these descriptions are for processing only ?>
				<?php $airisCustomFieldDescription = str_replace($airisCustomFieldPrefix, '', $airisCustomFieldDescription); ?>

				<div class="virtuemart-product-custom-field virtuemart-product-airis-prefixed-custom-field virtuemart-product-custom-field-<?php echo $airisCustomFieldDescription; ?>">

					<div class="virtuemart-product-custom-field-title">
						<?php echo $field->custom_title; ?>
					</div>

					<div class="virtuemart-product-custom-field-description">
						<?php echo $airisCustomFieldDescription; ?>
					</div>

					<div class="airis-item-content virtuemart-product-custom-field-content">

						<?php if ($field->field_type == 'X') : ?>

							<?php $customFieldContentLines = explode(PHP_EOL, $field->display); ?>

							<?php foreach ($customFieldContentLines as $customFieldContentLine) : ?>

								<div class="virtuemart-product-custom-field-content-item">
									<?php echo $customFieldContentLine; ?>
								</div>

							<?php endforeach; ?>

						<?php else : ?>

							<?php echo $field->display; ?>

						<?php endif; ?>

					</div>

				</div>

			<?php else : ?>

				<div class="product-field virtuemart-product-custom-field virtuemart-product-custom-field-<?php echo vmText::_($field->custom_desc); ?> product-field-type-<?php echo $field->field_type; ?> virtuemart-product-custom-field-type-<?php echo $field->field_type; ?>">

					<?php if (!$customTitle && $field->custom_title != $customTitle && $field->show_title) : ?>
						<div class="product-field-title virtuemart-product-custom-field-title">

							<?php echo vmText::_($field->custom_title); ?>

							<?php if ($field->custom_tip) : ?>
								<?php echo HTMLHelper::tooltip(vmText::_($field->custom_tip), vmText::_($field->custom_title), 'tooltip.png'); ?>
							<?php endif; ?>

						</div>
					<?php endif; ?>

					<?php if (!empty($field->display)) : ?>
						<div class="airis-item-content product-field-display virtuemart-product-custom-field-content"><?php echo $field->display; ?></div>
					<?php endif; ?>

					<?php if (!empty($field->custom_desc)) : ?>
						<div class="product-field-description virtuemart-product-custom-field-description"><?php echo vmText::_($field->custom_desc); ?></div>
					<?php endif; ?>

				</div>

				<?php $customTitle = $field->custom_title; ?>

			<?php endif; ?>

		<?php endforeach; ?>

	</div>

<?php endif;