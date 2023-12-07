<?php

// No direct access to this file outside of Joomla!
defined ('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

// Acquire template options
$airisTemplateOptions = Factory::getApplication()->getTemplate(true)->params;

// Used in including proper sublayouts
// $sublayoutPrefix = pathinfo(__FILE__, PATHINFO_FILENAME) . '_';
$sublayoutPrefix = 'airis_'; // Make this template override compatible with airis overrides

// Respect the sitename in <title> setting of Joomla!. Also apply custom separator.
if (!empty($this->product))
{
	$joomlaApplication = Factory::getApplication();
	$joomlaDocument = Factory::getDocument();
	$joomlaSitenamePagetitlesMode = (int) $joomlaApplication->getCfg('sitename_pagetitles');
	$joomlaSitename = $joomlaApplication->getCfg('sitename');

	// Acquire product name
	$productName = htmlspecialchars(trim(vmText::_($this->product->product_name)), ENT_QUOTES, 'UTF-8');

	// Custom Page Title value of this product. Used only in <title>.
	$productMetaCustomPageTitle = htmlspecialchars(trim(vmText::_($this->product->customtitle)), ENT_QUOTES, 'UTF-8');
	$productNameHtmlTitle = !empty($productMetaCustomPageTitle) ? $productMetaCustomPageTitle : $productName;

	// And its category name
	$productCategoryName = $this->product->virtuemart_category_id ? htmlspecialchars(trim(vmText::_($this->product->category_name)), ENT_QUOTES, 'UTF-8') : '';

	switch ($joomlaSitenamePagetitlesMode)
	{
		case 1:
			if ($productCategoryName)
			{
				$joomlaDocument->setTitle(Text::sprintf(Text::_('TPL_AIRIS_COM_VIRTUEMART_PRODUCTDETAILS_JPAGETITLE'), $joomlaSitename, $productCategoryName, $productNameHtmlTitle));
			}
			else
			{
				$joomlaDocument->setTitle(Text::sprintf(Text::_('TPL_AIRIS_COM_VIRTUEMART_JPAGETITLE'), $joomlaSitename, $$productNameHtmlTitle));
			}
			break;
		case 2:
			if ($productCategoryName)
			{
				$joomlaDocument->setTitle(Text::sprintf(Text::_('TPL_AIRIS_COM_VIRTUEMART_PRODUCTDETAILS_JPAGETITLE'), $productNameHtmlTitle, $productCategoryName, $joomlaSitename));
			}
			else
			{
				$joomlaDocument->setTitle(Text::sprintf(Text::_('TPL_AIRIS_COM_VIRTUEMART_JPAGETITLE'), $productNameHtmlTitle, $joomlaSitename));
			}
			break;
		case 0:
		default:
			if ($productCategoryName)
			{
				$joomlaDocument->setTitle(Text::sprintf(Text::_('TPL_AIRIS_COM_VIRTUEMART_JPAGETITLE'), $productNameHtmlTitlee, $productCategoryName));
			}
	}
}

?>

<?php if (empty($this->product)) : ?>

	<div class="virtuemart-page-empty"><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND'); ?></div>
	<div class="virtuemart-continue-shopping"><?php echo $this->continue_link_html; ?></div>

	<?php return; ?>

<?php endif; ?>

<?php echo shopFunctionsF::renderVmSubLayout('askrecomjs', array('product' => $this->product)); ?>

<?php if (vRequest::getInt('print', 0)) : ?>
	<?php
		// TODO: There should be a better way to implement this
		// <body onload="javascript:print();">
	?>
<?php endif; ?>

<div class="product-container productdetails-view productdetails<?php if (!empty($this->product->prices['discountAmount'])) echo ' ', 'productdetails-product-discounted'; ?>">

	<?php if (VmConfig::get('product_navigation', 0)) : ?>
		<div class="productdetails-product-neighbors airis-flex" data-nosnippet>

			<?php if (!empty($this->product->neighbours['previous'][0])) : ?>
				<div class="productdetails-product-neighbors-previous">
					<?php
						$previousProductHref = Route::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours['previous'][0]['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, false);
						echo HTMLHelper::link($previousProductHref, htmlspecialchars(trim(vmText::_($this->product->neighbours['previous'][0]['product_name'])), ENT_QUOTES, 'UTF-8'), array('rel' => 'prev', 'class' => 'productdetails-product-neighbors-link productdetails-product-neighbors-link-previous btn', 'data-dynamic-update' => '1'));
					?>
				</div>
			<?php endif; ?>

			<?php
				// Back to category link
				$backToCategoryLinkHref = $this->product->virtuemart_category_id ? Route::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $this->product->virtuemart_category_id, false) : Route::_('index.php?option=com_virtuemart', false);
				$backToCategoryLinkText = $this->product->virtuemart_category_id ? $productCategoryName : vmText::_('COM_VIRTUEMART_SHOP_HOME');
			?>

			<div class="productdetails-neighbors-back-to-category">
				<?php echo HTMLHelper::link($backToCategoryLinkHref, vmText::sprintf('COM_VIRTUEMART_CATEGORY_BACK_TO', $backToCategoryLinkText), array('class' => 'productdetails-neighbors-link-back-to-category btn')); ?>
			</div>

			<?php if (!empty($this->product->neighbours['next'][0])) : ?>
				<div class="productdetails-neighbors-next">
					<?php
						$nextProductHref = Route::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours['next'][0]['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, false);
						echo HTMLHelper::link($nextProductHref, htmlspecialchars(trim(vmText::_($this->product->neighbours['next'][0]['product_name'])), ENT_QUOTES, 'UTF-8'), array('rel' => 'next', 'class' => 'productdetails-neighbors-link productdetails-neighbors-link-next btn', 'data-dynamic-update' => '1'));
					?>
				</div>
			<?php endif; ?>

		</div>
	<?php endif; ?>

	<div class="page-header virtuemart-page-header productdetails-header">
		<h2><?php echo $productName; ?></h2>
	</div>

	<?php echo $this->product->event->afterDisplayTitle; ?>

	<?php echo $this->edit_link; ?>

	<?php if (VmConfig::get('show_emailfriend') || VmConfig::get('show_printicon') || VmConfig::get('pdf_icon')) : ?>
		<div class="productdetails-icons" data-nosnippet>
			<?php
				$iconPrintAndPdfHrefBase = "index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id={$this->product->virtuemart_product_id}&tmpl=component";

				echo $this->linkIcon($iconPrintAndPdfHrefBase . '&format=pdf', 'COM_VIRTUEMART_PDF', 'pdf_button', 'pdf_icon', false);
				// echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon');
				echo $this->linkIcon($iconPrintAndPdfHrefBase . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon', false, true, false, 'class="printModal"');

				$iconEmailHref = "index.php?option=com_virtuemart&view=productdetails&task=recommend&virtuemart_product_id={$this->product->virtuemart_product_id}&virtuemart_category_id={$this->product->virtuemart_category_id}&tmpl=component";
				echo $this->linkIcon($iconEmailHref, 'COM_VIRTUEMART_EMAIL', 'emailButton', 'show_emailfriend', false, true, false, 'class="recommened-to-friend"');
			?>
		</div>
	<?php endif; ?>

	<?php if (!empty(trim($this->product->product_s_desc))) : ?>
		<div class="airis-item-content virtuemart-description virtuemart-product-description-short productdetails-description-short">
			<?php echo $this->product->product_s_desc; ?>
		</div>
	<?php endif; ?>

	<?php echo shopFunctionsF::renderVmSubLayout('son_customfields', array('product' => $this->product, 'position' => 'ontop')); ?>

	<div class="productdetails-images-and-details airis-flex" data-nosnippet>

		<div class="productdetails-images-and-details-item productdetails-images">

			<div class="productdetails-image productdetails-image-main text-center">
				<?php echo $this->loadTemplate('images'); ?>
			</div>

			<?php if (count($this->product->images) > 1) echo $this->loadTemplate('images_additional'); ?>

		</div>

		<div class="productdetails-images-and-details-item productdetails-details">

			<?php if (!empty(trim($this->product->product_sku))) : ?>
				<div class="virtuemart-product-sku productdetails-sku">

					<div class="virtuemart-product-sku-title productdetails-sku-title default-shadow">
						<?php echo Text::_('SON_COM_VIRTUEMART_PRODUCT_SKU_TITLE'); ?>
					</div>

					<div class="virtuemart-product-sku-content productdetails-sku-content default-shadow">
						<?php echo $this->product->product_sku; ?>
					</div>

				</div>
			<?php endif; ?>

			<?php // TODO: Find proper value for this param to make this block displayable ?>
			<?php if (VmConfig::get('show_rating', 0)) : ?>
				<div class="virtuemart-product-rating productdetails-rating" data-nosnippet>
					<?php echo shopFunctionsF::renderVmSubLayout('rating', array('showRating' => $this->showRating, 'product' => $this->product)); ?>
				</div>
			<?php endif; ?>

			<?php // TODO: There should be a better class name for this block. Find out what does it do even. ?>
			<div class="productdetails-display-types">
				<?php foreach ($this->productDisplayTypes as $type => $productDisplayType) : ?>
					<?php foreach ($productDisplayType as $productDisplay) : ?>
						<?php foreach ($productDisplay as $virtuemart_method_id => $productDisplayHtml) : ?>
							<div class="<?php echo substr($type, 0, -1), ' ', substr($type, 0, -1), '-', $virtuemart_method_id; ?>">
								<?php echo $productDisplayHtml; ?>
							</div>
						<?php endforeach; ?>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</div>

			<?php if (!empty($this->product->prices['salesPrice'])) : ?>
				<div class="virtuemart-product-prices productdetails-prices">
					<?php echo shopFunctionsF::renderVmSubLayout('prices', array('product' => $this->product, 'currency' => $this->currency)); ?>
				</div>
			<?php endif; ?>

			<?php $customFieldsPositionAddtocartBeforeName = 'airis-addtocart-before'; ?>
			<?php if (isset($this->product->customfieldsSorted[$customFieldsPositionAddtocartBeforeName])) : ?>
				<div class="virtuemart-product-custom-fields-position virtuemart-product-custom-fields-position-<?php echo $customFieldsPositionAddtocartBeforeName; ?> productdetails-product-custom-fields-position productdetails-product-custom-fields-position-<?php echo $customFieldsPositionAddtocartBeforeName; ?>">
					<?php echo shopFunctionsF::renderVmSubLayout('son_customfields', array('product' => $this->product, 'position' => $customFieldsPositionAddtocartBeforeName)); ?>
				</div>
			<?php endif; ?>

			<div class="virtuemart-product-controls productdetails-controls">
				<?php echo shopFunctionsF::renderVmSubLayout($sublayoutPrefix . 'addtocart', array('product' => $this->product, 'airis-template-options' => $airisTemplateOptions, 'airis-virtuemart-view' => 'productdetails')); ?>
			</div>

			<?php $customFieldsPositionAddtocartAfterName = 'airis-addtocart-after'; ?>
			<?php if (isset($this->product->customfieldsSorted[$customFieldsPositionAddtocartAfterName])) : ?>
				<div class="virtuemart-product-custom-fields-position virtuemart-product-custom-fields-position-<?php echo $customFieldsPositionAddtocartAfterName; ?> productdetails-product-custom-fields-position productdetails-product-custom-fields-position-<?php echo $customFieldsPositionAddtocartAfterName; ?>">
					<?php echo shopFunctionsF::renderVmSubLayout('son_customfields', array('product' => $this->product, 'position' => $customFieldsPositionAddtocartAfterName)); ?>
				</div>
			<?php endif; ?>

			<?php // TODO: Find a proper value for this option to make it displayable ?>
			<?php if (VmConfig::get('show_in_stock', 0)) : ?>
				<div class="virtuemart-product-stock-level productdetails-stock-level" data-nosnippet>
					<?php echo shopFunctionsF::renderVmSubLayout('stockhandle', array('product' => $this->product)); ?>
				</div>
			<?php endif; ?>

			<?php if (VmConfig::get('ask_question', 0)) : ?>
				<div class="virtuemart-product-ask-question productdetails-ask-question" data-nosnippet>
					<?php
						$askQuestionLinkHref = Route::_("index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id={$this->product->virtuemart_product_id}&virtuemart_category_id={$this->product->virtuemart_category_id}&tmpl=component", false);
						echo HTMLHelper::link($askQuestionLinkHref, vmText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL'), array('class' => 'productdetails-ask-question-link btn', 'rel' => 'nofollow'));
					?>
				</div>
			<?php endif; ?>

			<?php if (VmConfig::get('show_manufacturers', 0) && !empty($this->product->virtuemart_manufacturer_id)) : ?>
				<div class="virtuemart-product-manufacturers productdetails-manufacturers">
					<?php echo $this->loadTemplate('manufacturer'); ?>
				</div>
			<?php endif; ?>

			<?php if ($this->product->product_box) : ?>
				<div class="virtuemart-product-packaging productdetails-packaging">
					<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_UNITS_IN_BOX') . $this->product->product_box; ?>
				</div>
			<?php endif; ?>

		</div>

	</div>

	<?php echo $this->product->event->beforeDisplayContent; ?>

	<?php if (!empty($this->product->product_desc)) : ?>
		<div class="virtuemart-product-description productdetails-description">

			<div class="virtuemart-product-description-title productdetails-description-title" data-nosnippet>
				<div class="virtuemart-product-description-title-inner productdetails-description-title-inner default-shadow">
					<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_DESC_TITLE'); ?>
				</div>
			</div>

			<div class="airis-item-content virtuemart-description virtuemart-product-description-content productdetails-description-content">
				<?php echo $this->product->product_desc; ?>
			</div>

		</div>
	<?php endif; ?>

	<?php
	 	echo shopFunctionsF::renderVmSubLayout('son_customfields', array('product' => $this->product, 'position' => 'normal'));
		echo shopFunctionsF::renderVmSubLayout('son_customfields', array('product' => $this->product, 'position' => 'related_products', 'class' => 'product-related-products', 'customTitle' => true));
		echo shopFunctionsF::renderVmSubLayout('son_customfields', array('product' => $this->product, 'position' => 'related_categories', 'class' => 'product-related-categories'));
		echo shopFunctionsF::renderVmSubLayout('son_customfields', array('product' => $this->product, 'position' => 'onbot'));
	?>

	<?php echo $this->product->event->afterDisplayContent; ?>

	<?php echo $this->loadTemplate('reviews'); ?>

	<?php if ($this->cat_productdetails) echo $this->loadTemplate('showcategory'); ?>

	<?php if ($this->product->prices['salesPrice'] > 0) echo shopFunctionsF::renderVmSubLayout('snippets', array('product' => $this->product, 'currency' => $this->currency, 'showRating' => $this->showRating)); ?>

	<?php

		/* vmJsApi::addJScript('recalcReady', '
			jQuery(document).on("ready", function () {
				jQuery("form.js-recalculate").each(function () {

					var $this = jQuery(this);

					if ($this.find(".product-fields").length && !$this.find(".no-vm-bind").length) {
						var id = $this.find("input[name=\"virtuemart_product_id[]\"]").val();
						Virtuemart.setproducttype($this, id);
					}

				});
			});'
		); */

		if (VmConfig::get('jdynupdate', 0)) {

			vmJsApi::addJScript('ajaxContent', '

				Virtuemart.containerSelector = ".productdetails-view";
				// Virtuemart.recalculate = true; // Activate this line to recalculate your product after ajax

				jQuery(document).on("ready", function () {
					Virtuemart.container = jQuery(Virtuemart.containerSelector);
				});

			');

			vmJsApi::addJScript('vmPreloader', '

				jQuery(document).on("ready", function () {

					Virtuemart.stopVmLoading();
					var msg = "";

					jQuery("a[data-dynamic-update=\"1\"]")
						.off("click", Virtuemart.startVmLoading)
						.on("click", {msg:msg}, Virtuemart.startVmLoading)
					;

					jQuery("[data-dynamic-update=\"1\"]")
						.off("change", Virtuemart.startVmLoading)
						.on("change", {msg:msg}, Virtuemart.startVmLoading)
					;

				});

			');

		}

		// Disable the stock VM lightbox scripts and their CSS enqueued in \administrator\components\com_virtuemart\helpers\vmjsapi.php
		vmJsApi::removeJScript('facebox');
		vmJsApi::removeJScript('fancybox/jquery.fancybox-1.3.4.pack');
		vmJsApi::removeJScript('popups');

		unset($joomlaDocument->_styleSheets['/components/com_virtuemart/assets/css/facebox.css?vmver=' . VM_JS_VER]);
		unset($joomlaDocument->_styleSheets['/components/com_virtuemart/assets/css/jquery.fancybox-1.3.4.css?vmver=' . VM_JS_VER]);

		// This final VM JS writing call has to stay inside the product container (as defined by Virtuemart.containerSelector) for scripts above to be inlcuded during AJAX page update
		echo vmJsApi::writeJS();

	?>

</div>