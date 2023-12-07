<?php

// No direct access to this file outside of Joomla!
defined ('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;

$customFields = $viewData['searchcustomvalues'];

?>

<?php foreach ($customFields as $customField) : ?>

	<?php
		// Only these custom field types are searchable
		if ($customField->field_type != 'S' && $customField->field_type != 'P') continue;
	?>

	<div class="virtuemart-search-form-custom-fields-search-value browse-view-search-form-custom-fields-search-value airis-flex-item-per-row-four">

		<?php $customFieldFormElemementsTitle = htmlspecialchars(trim(vmText::_($customField->custom_title)), ENT_QUOTES, 'UTF-8'); ?>

		<div class="virtuemart-search-form-custom-fields-search-value-label-container browse-view-search-form-custom-fields-search-value-label-container">
			<?php // TODO: echo HTMLHelper::_('label', ?>
			<label for="<?php echo 'customfields' . $customField->virtuemart_custom_id ?>" class="virtuemart-search-form-custom-fields-search-value-label browse-view-search-form-custom-fields-search-value-label">
				<?php echo $customFieldFormElemementsTitle; ?>
			</label>
		</div>

		<div class="virtuemart-search-form-custom-fields-search-control-container browse-view-search-form-custom-fields-search-control-container">

			<?php if ($customField->field_type == 'S') : ?>

				<?php
					echo HTMLHelper::_(
						'select.genericlist',
						$customField->value_options,
						"customfields[$customField->virtuemart_custom_id]",
						array(
							'class' => 'changeSendForm virtuemart-search-form-custom-fields-search-control-element virtuemart-search-form-custom-fields-search-control-element-select browse-view-search-form-custom-fields-search-control-element browse-view-search-form-custom-fields-search-control-element-select',
							'title' => $customFieldFormElemementsTitle
						),
						'virtuemart_custom_id',
						'custom_title',
						$customField->v
					);
				?>

			<?php elseif ($customField->field_type == 'P') : ?>

				<?php // TODO: echo HTMLHelper::_('text', ?>
				<?php // TODO: Check how to enable JS Submit for this field or whatever else VM offers ?>
				<input type="text" name="<?php echo "customfields[$customField->virtuemart_custom_id]"; ?>" value="<?php echo vRequest::getString("customfields[$customField->virtuemart_custom_id]"); ?>" class="virtuemart-search-form-custom-fields-search-control-element virtuemart-search-form-custom-fields-search-control-element-input-text browse-view-search-form-custom-fields-search-control-element browse-view-search-form-custom-fields-search-control-element-input-text" id="<?php echo 'customfields', $customField->virtuemart_custom_id; ?>">

			<?php endif; ?>

		</div>

	</div>
<?php endforeach;