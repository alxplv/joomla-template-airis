<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Not sure if we ever get to this point in such case but whatever
if (empty($viewData['products'])) return;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Image\Image;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

// Used in including proper sublayouts
$sublayoutPrefix = basename(__FILE__, 'products.php');

// Apply required CSS class for products per row style variant
$productsPerRow = !empty($viewData['products_per_row']) ? $viewData['products_per_row'] : VmConfig::get('products_per_row', 3);

// TODO: Rewrite this part in the same way as with mod_virtuemart_product airis template: without str_replace or in_array and so on
// Replace integers with number words
$productsPerRowAllowedDigits = range(1, 6);
$productsPerRowAllowedNumerals = array('one', 'two', 'three', 'four', 'five', 'six');

// Ignore unacceptable integers
if (!in_array($productsPerRow, $productsPerRowAllowedDigits)) $productsPerRow = 3;

$productsPerRow = str_replace($productsPerRowAllowedDigits, $productsPerRowAllowedNumerals, $productsPerRow);

// Product marker class strings
$productMarkerClassNew = 'virtuemart-product-new browse-view-product-new';
$productMarkerClassDiscount = 'virtuemart-product-discounted browse-view-product-discounted';

// Get template option for limiting string length of product short description
$productShortDescriptionStringLimit = $viewData['airis-template-options']->get('virtuemartProductDescriptionShortStringLimit', 60);

?>

<div class="browse-view-products airis-flex-item-rows">
	<?php foreach ($viewData['products'] as $products) : ?>

		<?php
			// Sort products by in stock value if required
			// TODO: First check if out-of-stock products are displayable in VM settings

			// Push out-of-stock products to the very end of the list
			$productsCount = count($products);

			for ($i = 0; $i < $productsCount; $i++)
			{
				if ($products[$i]->product_in_stock === 0)
				{
					$tempProduct = $products[$i];
					unset($products[$i]);
					$products[] = $tempProduct;
				}
			}

			// Just in case
			unset($tempProduct);

			// Also reindex products array just in case
			$products = array_values($products);
		?>

		<?php foreach ($products as $product) : ?>

			<?php
				// Prepare the name string for safe usage in HTML
				$productName = htmlspecialchars(trim(vmText::_($product->product_name)), ENT_QUOTES, 'UTF-8');

				// TODO: Rewrite this whole section based on mod_virtuemart_product airis view code.
				// TODO: Add support for a list of badges that can be displayed by each product instead of a single badge
				// Apply any additional product marker classes used by select products
				$productMarkerClasses = '';

				$productIsDiscounted = false;
				$productIsNew = false;

				// Check if this product is discounted
				if (!empty($product->prices['discountAmount']))
				{
					$productMarkerClasses .= " $productMarkerClassDiscount";
					$productIsDiscounted = true;
				}

				// Check if this product is marked as new. Discount marker takes preference if a product is marked both as discounted and new at the same time.
				if (!$productIsDiscounted && isset($product->customfieldsSorted['airis-product-markers']))
				{
					foreach ($product->customfieldsSorted['airis-product-markers'] as $marker)
					{
						if ($marker->custom_desc == 'airis-product-marker-new')
						{
							$productMarkerClasses .= " $productMarkerClassNew";
							$productIsNew = true;
							break;
						}
					}
				}

				// Use canonical link for this product if available
				$productHref = empty($product->link) ? $product->canonical : $product->link;

				// Joomla! Itemid GET parameter used for proper module placement when SEF URIs are not in use
				if (!empty($lastVisitedItemId)) $productHref .= '&Itemid=' . shopFunctionsF::getLastVisitedItemId();
			?>

			<div class="product-container virtuemart-product browse-view-product airis-flex-item-per-row-<?php echo $productsPerRow; ?> browse-view-product-per-row-<?php echo $productsPerRow; ?> airis-flex airis-flex-column<?php echo $productMarkerClasses; ?>" data-vm="product-container">

				<div class="virtuemart-product-image browse-view-product-image text-center">
					<?php
						$productImageAttributes = array(
							'class' => 'virtuemart-product-image-file browse-view-product-image-file',
							'alt' => $productName
						);
						
						// TODO: While getFileUrlThumb() provides correct path to a possible "NO IMAGE" image for this item, the Image class instance above cannot be successfully contructed for such images for some reason, so we can only fallback to configured thumbnail dimensions in such cases because getUrl() returns no valid image path to extract full image dimensions from there since proper aspect ratio is what browsers care about anyway
						if (!empty($product->images[0]->virtuemart_media_id))
						{
							// Construct an Image instance from product thumbnail URI first since VM's VmMediaHandler class currently doesn't provide any properties or methods to get thumbnail width and height
							$productImage = new Image($product->images[0]->getFileUrlThumb());

							$productImageAttributes['width'] = $productImage->getWidth();
							$productImageAttributes['height'] = $productImage->getHeight();
						}
						else
						{
							$productImageAttributes['width'] = VmConfig::get('img_width', 0); 
							$productImageAttributes['height'] = VmConfig::get('img_height', 0);
						}

						echo HTMLHelper::link($productHref, $product->images[0]->displayMediaThumb($productImageAttributes, false), array('title' => $productName));
					?>
				</div>

				<div class="virtuemart-product-title browse-view-product-title">
					<?php echo HTMLHelper::link($productHref, $productName, array('class' => 'virtuemart-product-title-link browse-view-product-title-link')); ?>
				</div>

				<?php // TODO: Add conditional display of this SKU block with airis template param ?>
				<?php if (!empty(trim($product->product_sku))) : ?>
					<div class="virtuemart-product-sku browse-view-product-sku">

						<div class="virtuemart-product-sku-title browse-view-product-sku-title">
							<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_SKU'); ?>
						</div>

						<div class="virtuemart-product-sku-content browse-view-product-sku-content">
							<?php echo $product->product_sku; ?>
						</div>

					</div>
				<?php endif; ?>

				<?php $customFieldsPositionAddtocartBeforeName = 'airis-addtocart-before'; ?>
				<?php if (isset($product->customfieldsSorted[$customFieldsPositionAddtocartBeforeName])) : ?>
					<div class="virtuemart-product-custom-fields-position virtuemart-product-custom-fields-position-<?php echo $customFieldsPositionAddtocartBeforeName; ?> browse-view-product-custom-fields-position browse-view-product-custom-fields-position-<?php echo $customFieldsPositionAddtocartBeforeName; ?>">
						<?php echo shopFunctionsF::renderVmSubLayout($sublayoutPrefix . 'customfields', array('product' => $product, 'position' => $customFieldsPositionAddtocartBeforeName)); ?>
					</div>
				<?php endif; ?>

				<?php if (!empty($product->product_s_desc)) : ?>
					<div class="virtuemart-description virtuemart-product-description-short browse-view-product-description-short airis-item-content">
						<?php echo shopFunctionsF::limitStringByWord($product->product_s_desc, $productShortDescriptionStringLimit, '…'); ?>
					</div>
				<?php endif; ?>

				<?php if (!empty($product->prices['salesPrice'])) : ?>
					<div class="virtuemart-product-prices browse-view-product-prices">
						<?php echo shopFunctionsF::renderVmSubLayout('prices', array('product' => $product, 'currency' => $viewData['currency'])); ?>
					</div>
				<?php endif; ?>

				<?php // This sublayout has to be included at all times because it is used by addtocart position for VM custom fields ?>
				<div class="virtuemart-product-controls browse-view-product-controls">
					<?php echo shopFunctionsF::renderVmSubLayout($sublayoutPrefix . 'addtocart', array('product' => $product, 'position' => 'addtocart', 'airis-template-options' => $viewData['airis-template-options'])); ?>
				</div>

				<?php $customFieldsPositionAddtocartAfterName = 'airis-addtocart-after'; ?>
				<?php if (isset($product->customfieldsSorted[$customFieldsPositionAddtocartAfterName])) : ?>
					<div class="virtuemart-product-custom-fields-position virtuemart-product-custom-fields-position-<?php echo $customFieldsPositionAddtocartAfterName; ?> browse-view-product-custom-fields-position browse-view-product-custom-fields-position-<?php echo $customFieldsPositionAddtocartAfterName; ?>">
						<?php echo shopFunctionsF::renderVmSubLayout($sublayoutPrefix . 'customfields', array('product' => $product, 'position' => $customFieldsPositionAddtocartAfterName)); ?>
					</div>
				<?php endif; ?>

				<div class="virtuemart-product-links browse-view-product-links">
					<?php echo HTMLHelper::link($productHref, vmText::_('COM_VIRTUEMART_PRODUCT_DETAILS'), array('class' => 'virtuemart-product-link virtuemart-product-link-details browse-view-product-link browse-view-product-link-details btn')); ?>
				</div>

				<?php // This JS has to be included inside product container otherwise AJAX for product content is broken after the very first iteration ?>
				<?php if (vRequest::getInt('dynamic', 0) && vRequest::getInt('virtuemart_product_id', 0)) echo vmJsApi::writeJS(); ?>

				<?php // TODO: Rewrite this part in the same way it's done in airis view of mod_virtuemart_product ?>
				<?php if ($productIsDiscounted) : ?>
					<div class="virtuemart-product-badge virtuemart-product-badge-discount browse-view-product-badge-discount"><?php echo Text::_('TPL_AIRIS_COM_VIRTUEMART_PRODUCT_BADGE_DISCOUNT'); ?></div>
				<?php elseif ($productIsNew) : ?>
					<div class="virtuemart-product-badge virtuemart-product-badge-new browse-view-product-badge-new"><?php echo Text::_('TPL_AIRIS_COM_VIRTUEMART_PRODUCT_BADGE_NEW'); ?></div>
				<?php endif; ?>

			</div>
		<?php endforeach; ?>
	<?php endforeach; ?>
</div>