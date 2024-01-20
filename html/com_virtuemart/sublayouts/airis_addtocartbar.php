<?php

// No direct access to this file
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$sublayoutPrefix = basename(__FILE__, 'addtocartbar.php');

// Get template options
$fontAwesomeLoaded = $viewData['airis-template-options']->get('loadFontAwesome');

$product = $viewData['product'];

// Defaults for addtocart controls of this product
$init = 1;
$step = 1;
$maxOrder = '';
$addtoCartButton = '';
$minOrderLevel = 1;

// Possibly override certain defaults using product settings
if (isset($viewData['init'])) $init = $viewData['init'];
if (!empty($product->min_order_level) && $init < $product->min_order_level) $init = $product->min_order_level;

if (!empty($product->step_order_level))
{
	$step = $product->step_order_level;

	if (!empty($init)) $init = ($init < $step) ? $init = $step : ceil($init / $step) * $step;
	if (empty($product->min_order_level) && !isset($viewData['init'])) $init = $step;
}

if (!empty($product->max_order_level)) $maxOrder = "max=\"$product->max_order_level\"";

if (!VmConfig::get('use_as_catalog', 0))
{
	if (!$product->addToCartButton && $product->addToCartButton !== '')
	{
		$addtoCartButton = self::renderVmSubLayout($sublayoutPrefix . 'addtocartbtn', array('orderable' => $product->orderable));
	}
	else
	{
		$addtoCartButton = $product->addToCartButton;
	}
}

if ($product->min_order_level > 0) $minOrderLevel = $product->min_order_level;

if (!VmConfig::get('use_as_catalog', 0)) : ?>
	<div class="addtocart-bar virtuemart-product-addtocartbar" data-nosnippet>

		<?php $stockhandle = (VmConfig::get('stockhandle_products', false) && $product->product_stockhandle) ? $product->product_stockhandle : VmConfig::get('stockhandle', 'none'); ?>

		<?php if ($product->show_notify) : ?>

			<?php

				// The default notify page link of VM can be replaced with a simple Contact Us link in template options. The replacement link is output in addtocart.php override.
				$contactUsLinkEnabled = $viewData['airis-template-options']->get('showVirtuemartProductLinkContactUs');
				$replaceNotifyLinkWithContactUs = $viewData['airis-template-options']->get('replaceVirtuemartProductLinkNotifyWithContactUs');

				// Skip replacing if Contact Us link is disabled
				if (!$contactUsLinkEnabled) $replaceNotifyLinkWithContactUs = false;

				if (!$replaceNotifyLinkWithContactUs)
				{
					echo HTMLHelper::link('index.php?option=com_virtuemart&view=productdetails&layout=notify&virtuemart_product_id=' . $product->virtuemart_product_id, vmText::_('COM_VIRTUEMART_CART_NOTIFY'), array('class' => 'virtuemart-product-link virtuemart-product-link-notify btn', 'rel' => 'nofollow'));
				}

			?>

		<?php else : ?>

			<div class="virtuemart-product-quantity-controls">

				<div class="quantity-controls virtuemart-product-quantity-controls-item js-recalculate">
					<button type="button" class="quantity-controls quantity-minus virtuemart-product-quantity-controls-button virtuemart-product-quantity-controls-minus">
						<?php if ($fontAwesomeLoaded) : ?>
							<span class="fas fa-minus virtuemart-product-quantity-controls-button-icon virtuemart-product-quantity-controls-button-icon-minus"></span>
						<?php else : ?>
							&minus;
						<?php endif; ?>
					</button>
				</div>

				<div class="quantity-box virtuemart-product-quantity-controls-item virtuemart-product-quantity">
					<input type="text" class="quantity-input virtuemart-product-quantity-input js-recalculate" name="quantity[]" data-errStr="<?php echo vmText::_('COM_VIRTUEMART_WRONG_AMOUNT_ADDED'); ?>" value="<?php echo $init; ?>" data-init="<?php echo $init; ?>" data-step="<?php echo $step; ?>" <?php echo $maxOrder; ?>>
				</div>

				<div class="quantity-controls virtuemart-product-quantity-controls-item js-recalculate">
					<button type="button" class="quantity-controls quantity-plus virtuemart-product-quantity-controls-button virtuemart-product-quantity-controls-plus">
						<?php if ($fontAwesomeLoaded) : ?>
							<span class="fas fa-plus virtuemart-product-quantity-controls-button-icon virtuemart-product-quantity-controls-button-icon-plus"></span>
						<?php else : ?>
							&plus;
						<?php endif; ?>
					</button>
				</div>

			</div>

			<div class="addtocart-button virtuemart-product-addtocartbtn">
				<?php echo $addtoCartButton; ?>
			</div>

			<input type="hidden" name="virtuemart_product_id[]" value="<?php echo $product->virtuemart_product_id; ?>">

			<noscript><input type="hidden" name="task" value="add"></noscript>

		<?php endif; ?>
	</div>

<?php endif;