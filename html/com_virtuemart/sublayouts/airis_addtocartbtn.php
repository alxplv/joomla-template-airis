<?php

// No direct access to this file outside of Joomla!
defined ('_JEXEC') or exit;

// Joomla! imports
// use Joomla\CMS\HTML\HTMLHelper;

// TODO: Replace static HTML with HTMLHelper::_('button', ) call if there's such thing in Joomla!'s API one day
?>

<?php if ($viewData['orderable']) : ?>

	<?php $addToCartButtonText = vmText::_('COM_VIRTUEMART_CART_ADD_TO'); ?>

	<button name="addtocart" class="addtocart-button virtuemart-product-addtocart-button" title="<?php echo $addToCartButtonText; ?>">
		<?php echo $addToCartButtonText; ?>
	</button>

<?php else : ?>

	<?php $addToCartButtonText = vmText::_('COM_VIRTUEMART_ADDTOCART_CHOOSE_VARIANT'); ?>

	<button name="addtocart" class="addtocart-button virtuemart-product-addtocart-button addtocart-button-disabled virtuemart-product-addtocart-button-disabled" title="<?php echo $addToCartButtonText; ?>" disabled>
		<?php echo $addToCartButtonText; ?>
	</button>

<?php endif;