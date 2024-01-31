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
$sublayoutPrefix = pathinfo(__FILE__, PATHINFO_FILENAME) . '_';

// Respect the sitename in <title> setting of Joomla!. Also apply custom separator.
if (isset($this->product)) {
	$joomlaApplication = Factory::getApplication();
	$joomlaDocument = Factory::getDocument();
	$joomlaSitenamePagetitlesMode = (int) $joomlaApplication->getCfg('sitename_pagetitles');
	$joomlaSitename = $joomlaApplication->getCfg('sitename');

	$viewContainerClasses = 'product-container productdetails-view productdetails';

	// Acquire product data
	$productName = htmlspecialchars(trim(vmText::_($this->product->product_name)), ENT_QUOTES, 'UTF-8');
	$productDescription = trim($this->product->product_desc); // HTML is allowed for both descriptions
	$productDescriptionShort = trim($this->product->product_s_desc);
	$productSku = htmlspecialchars(trim($this->product->product_sku), ENT_QUOTES, 'UTF-8');

	// Custom Page Title value of this product. Used only in <title>.
	$productMetaCustomPageTitle = htmlspecialchars(trim(vmText::_($this->product->customtitle)), ENT_QUOTES, 'UTF-8');
	$productNameHtmlTitle = ($productMetaCustomPageTitle !== '') ? $productMetaCustomPageTitle : $productName;

	// And its category name
	$productCategoryName = '';
	if (isset($this->product->virtuemart_category_id)) {
		$productCategoryName = htmlspecialchars(trim(vmText::_($this->product->category_name)), ENT_QUOTES, 'UTF-8');
	}

	switch ($joomlaSitenamePagetitlesMode) {
		case 1:
			if ($productCategoryName) {
				$joomlaDocument->setTitle(
					Text::sprintf(
						Text::_('TPL_AIRIS_COM_VIRTUEMART_PRODUCTDETAILS_JPAGETITLE'),
						$joomlaSitename,
						$productCategoryName,
						$productNameHtmlTitle,
					),
				);
			} else {
				$joomlaDocument->setTitle(
					Text::sprintf(
						Text::_('TPL_AIRIS_COM_VIRTUEMART_JPAGETITLE'),
						$joomlaSitename,
						$productNameHtmlTitle,
					),
				);
			}
			break;
		case 2:
			if ($productCategoryName) {
				$joomlaDocument->setTitle(
					Text::sprintf(
						Text::_('TPL_AIRIS_COM_VIRTUEMART_PRODUCTDETAILS_JPAGETITLE'),
						$productNameHtmlTitle,
						$productCategoryName,
						$joomlaSitename,
					),
				);
			} else {
				$joomlaDocument->setTitle(
					Text::sprintf(
						Text::_('TPL_AIRIS_COM_VIRTUEMART_JPAGETITLE'),
						$productNameHtmlTitle,
						$joomlaSitename,
					),
				);
			}
			break;
		case 0:
			// no break (fallthrough)
		default:
			if ($productCategoryName) {
				$joomlaDocument->setTitle(
					Text::sprintf(
						Text::_('TPL_AIRIS_COM_VIRTUEMART_JPAGETITLE'),
						$productNameHtmlTitle,
						$productCategoryName,
					),
				);
			}
			break;
	}
}

?>

<?php if (isset($this->product) === false) : ?>

	<div class="virtuemart-page-empty"><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND'); ?></div>
	<div class="virtuemart-continue-shopping"><?php echo $this->continue_link_html; ?></div>

	<?php return; ?>

<?php endif; ?>

<?php echo shopFunctionsF::renderVmSubLayout('askrecomjs', ['product' => $this->product]); ?>

<?php if (vRequest::getInt('print', 0)) : ?>
	<?php
		// TODO: Add this script to the current page via Web Asssets
		// '<script>print();</script>';
	?>
<?php endif; ?>

<?php
	if (isset($this->product->prices['discountAmount'])) {
		$viewContainerClasses .= ' productdetails-product-discounted';
	}
?>

<div class="<?php echo $viewContainerClasses; ?>">

	<?php if (VmConfig::get('product_navigation', 0)) : ?>
		<div class="productdetails-product-neighbors airis-flex" data-nosnippet>

			<?php if (isset($this->product->neighbours['previous'][0])) : ?>
				<div class="productdetails-product-neighbors-previous">
					<?php
						$previousProductHref = Route::_(
							"index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id={$this->product->neighbours['previous'][0]['virtuemart_product_id']}&virtuemart_category_id={$this->product->virtuemart_category_id}",
							false,
						);

						echo HTMLHelper::link(
							$previousProductHref,
							htmlspecialchars(trim(vmText::_($this->product->neighbours['previous'][0]['product_name'])), ENT_QUOTES, 'UTF-8'),
							[
								'rel' => 'prev',
								'class' => 'productdetails-product-neighbors-link productdetails-product-neighbors-link-previous btn',
								'data-dynamic-update' => '1',
							],
						);
					?>
				</div>
			<?php endif; ?>

			<?php
				// Prepare back to category link
				$backToCategoryLinkHref = '';
				$backToCategoryLinkText = '';

				if (isset($this->product->virtuemart_category_id)) {
					$backToCategoryLinkHref = Route::_(
						"index.php?option=com_virtuemart&view=category&virtuemart_category_id={$this->product->virtuemart_category_id}",
						false,
					);
					$backToCategoryLinkText = $productCategoryName;
				} else {
					$backToCategoryLinkHref = Route::_('index.php?option=com_virtuemart', false);
					$backToCategoryLinkText = vmText::_('COM_VIRTUEMART_SHOP_HOME');
				}
			?>

			<div class="productdetails-neighbors-back-to-category">
				<?php
					echo HTMLHelper::link(
						$backToCategoryLinkHref,
						vmText::sprintf('COM_VIRTUEMART_CATEGORY_BACK_TO', $backToCategoryLinkText),
						[
							'class' => 'productdetails-neighbors-link-back-to-category btn',
						],
					);
				?>
			</div>

			<?php if (isset($this->product->neighbours['next'][0])) : ?>
				<div class="productdetails-neighbors-next">
					<?php
						$nextProductHref = Route::_(
							"index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id={$this->product->neighbours['next'][0]['virtuemart_product_id']}&virtuemart_category_id={$this->product->virtuemart_category_id}",
							false,
						);
						echo HTMLHelper::link(
							$nextProductHref,
							htmlspecialchars(trim(vmText::_($this->product->neighbours['next'][0]['product_name'])), ENT_QUOTES, 'UTF-8'),
							[
								'rel' => 'next',
								'class' => 'productdetails-neighbors-link productdetails-neighbors-link-next btn',
								'data-dynamic-update' => '1',
							],
						);
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

	<?php if (VmConfig::get('show_emailfriend', 0) || VmConfig::get('show_printicon', 0) || VmConfig::get('pdf_icon', 0)) : ?>
		<ul class="productdetails__icons list-unstyled" data-nosnippet>
			<?php $productPagePrintAndShareLinkHrefBase = "index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id={$this->product->virtuemart_product_id}&tmpl=component"; ?>

				<?php if (VmConfig::get('pdf_icon', 0)) : ?>
					<li class="productdetails__icon">
						<?php
							echo $this->linkIcon(
								"$productPagePrintAndShareLinkHrefBase&format=pdf",
								'COM_VIRTUEMART_PDF',
								'pdf_button',
								'pdf_icon',
								false,
							);
						?>
					</li>
				<?php endif; ?>

				<?php if (VmConfig::get('show_printicon', 0)) : ?>
					<li class="productdetails__icon">
						<?php
							// echo $this->linkIcon("$productPagePrintAndShareLinkHrefBase&print=1", 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon');
							echo $this->linkIcon(
								"$productPagePrintAndShareLinkHrefBase&print=1",
								'COM_VIRTUEMART_PRINT',
								'printButton',
								'show_printicon',
								false,
								true,
								false,
								'class="printModal"',
							);
						?>
					</li>
				<?php endif; ?>

				<?php if (VmConfig::get('show_emailfriend', 0)) : ?>
					<li class="productdetails__icon">
						<?php
							$iconEmailHref = "index.php?option=com_virtuemart&view=productdetails&task=recommend&virtuemart_product_id={$this->product->virtuemart_product_id}&virtuemart_category_id={$this->product->virtuemart_category_id}&tmpl=component";
							echo $this->linkIcon(
								$iconEmailHref,
								'COM_VIRTUEMART_EMAIL',
								'emailButton',
								'show_emailfriend',
								false,
								true,
								false,
								'class="recommened-to-friend"',
							);
						?>
					</li>
				<?php endif; ?>
		</ul>
	<?php endif; ?>

	<?php if ($productDescriptionShort !== '') : ?>
		<div class="airis-item-content virtuemart-description virtuemart-product-description-short productdetails-description-short">
			<?php echo $productDescriptionShort; ?>
		</div>
	<?php endif; ?>

	<?php echo shopFunctionsF::renderVmSubLayout($sublayoutPrefix . 'customfields', ['product' => $this->product, 'position' => 'ontop']); ?>

	<div class="productdetails-images-and-details airis-flex" data-nosnippet>

		<div class="productdetails-images-and-details-item productdetails-images">

			<div class="productdetails-image productdetails-image-main text-center">
				<?php echo $this->loadTemplate('images'); ?>
			</div>

			<?php
				if (count($this->product->images) > 1) {
					echo $this->loadTemplate('images_additional');
				}
			?>

		</div>

		<div class="productdetails-images-and-details-item productdetails-details">

			<?php if ($productSku !== '') : ?>
				<div class="virtuemart-product-sku productdetails-sku">

					<div class="virtuemart-product-sku-title productdetails-sku-title">
						<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_SKU'); ?>
					</div>

					<div class="virtuemart-product-sku-content productdetails-sku-content">
						<?php echo $productSku; ?>
					</div>

				</div>
			<?php endif; ?>

			<?php // TODO: Find proper value for this param to make this block displayable ?>
			<?php if (VmConfig::get('show_rating', 0)) : ?>
				<div class="virtuemart-product__rating productdetails__rating" data-nosnippet>
					<?php
						echo shopFunctionsF::renderVmSubLayout(
							'rating',
							[
								'showRating' => $this->showRating,
								'product' => $this->product,
							],
						);
					?>
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
					<?php // TODO: Replace the call to a custom prices sublayout once (and if ever) it is ready ?>
					<?php // echo shopFunctionsF::renderVmSubLayout($sublayoutPrefix . 'prices', ['product' => $this->product, 'currency' => $this->currency]); ?>
					<?php
						echo shopFunctionsF::renderVmSubLayout(
							'prices',
							[
								'product' => $this->product,
								'currency' => $this->currency,
							],
						);
					?>
				</div>
			<?php endif; ?>

			<?php $customFieldsPositionAddtocartBeforeName = 'airis-addtocart-before'; ?>
			<?php if (isset($this->product->customfieldsSorted[$customFieldsPositionAddtocartBeforeName])) : ?>
				<div class="virtuemart-product-custom-fields-position virtuemart-product-custom-fields-position-<?php echo $customFieldsPositionAddtocartBeforeName; ?> productdetails-product-custom-fields-position productdetails-product-custom-fields-position-<?php echo $customFieldsPositionAddtocartBeforeName; ?>">
					<?php
						echo shopFunctionsF::renderVmSubLayout(
							$sublayoutPrefix . 'customfields',
							[
								'product' => $this->product,
								'position' => $customFieldsPositionAddtocartBeforeName,
							],
						);
					?>
				</div>
			<?php endif; ?>

			<div class="virtuemart-product-controls productdetails-controls">
				<?php
					echo shopFunctionsF::renderVmSubLayout(
						$sublayoutPrefix . 'addtocart',
						[
							'product' => $this->product,
							'airis-template-options' => $airisTemplateOptions,
							'airis-virtuemart-view' => 'productdetails',
						],
					);
				?>
			</div>

			<?php $customFieldsPositionAddtocartAfterName = 'airis-addtocart-after'; ?>
			<?php if (isset($this->product->customfieldsSorted[$customFieldsPositionAddtocartAfterName])) : ?>
				<div class="virtuemart-product-custom-fields-position virtuemart-product-custom-fields-position-<?php echo $customFieldsPositionAddtocartAfterName; ?> productdetails-product-custom-fields-position productdetails-product-custom-fields-position-<?php echo $customFieldsPositionAddtocartAfterName; ?>">
					<?php
						echo shopFunctionsF::renderVmSubLayout(
							$sublayoutPrefix . 'customfields',
							[
								'product' => $this->product,
								'position' => $customFieldsPositionAddtocartAfterName,
							],
						);
					?>
				</div>
			<?php endif; ?>

			<?php // TODO: Find a proper value for this option to make it displayable ?>
			<?php if (VmConfig::get('show_in_stock', 0)) : ?>
				<div class="virtuemart-product-stock-level productdetails-stock-level" data-nosnippet>
					<?php
						echo shopFunctionsF::renderVmSubLayout(
							'stockhandle',
							[
								'product' => $this->product,
							],
						);
					?>
				</div>
			<?php endif; ?>

			<?php if (VmConfig::get('ask_question', 0)) : ?>
				<div class="virtuemart-product-ask-question productdetails-ask-question" data-nosnippet>
					<?php
						$askQuestionLinkHref = Route::_("index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id={$this->product->virtuemart_product_id}&virtuemart_category_id={$this->product->virtuemart_category_id}&tmpl=component", false);
						echo HTMLHelper::link(
							$askQuestionLinkHref,
							vmText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL'),
							[
								'class' => 'productdetails-ask-question-link btn',
								'rel' => 'nofollow',
							],
						);
					?>
				</div>
			<?php endif; ?>

			<?php if (VmConfig::get('show_manufacturers', 0) && isset($this->product->virtuemart_manufacturer_id) && is_countable($this->product->virtuemart_manufacturer_id) && count($this->product->virtuemart_manufacturer_id) !== 0) : ?>
				<ul class="virtuemart-product-manufacturers productdetails-manufacturers list-unstyled">
					<?php echo $this->loadTemplate('manufacturer'); ?>
				</ul>
			<?php endif; ?>

			<?php if (isset($this->product->product_box) && $this->product->product_box !== '') : ?>
				<div class="virtuemart-product-packaging productdetails-packaging">
					<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_UNITS_IN_BOX') . $this->product->product_box; ?>
				</div>
			<?php endif; ?>

		</div>

	</div>

	<?php echo $this->product->event->beforeDisplayContent; ?>

	<?php if ($productDescription !== '') : ?>
		<div class="virtuemart-product-description productdetails-description">

			<div class="virtuemart-product-description-title productdetails-description-title" data-nosnippet>
				<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_DESC_TITLE'); ?>
			</div>

			<div class="airis-item-content virtuemart-description virtuemart-product-description-content productdetails-description-content">
				<?php echo $productDescription; ?>
			</div>

		</div>
	<?php endif; ?>

	<?php
	 	echo shopFunctionsF::renderVmSubLayout(
			"{$sublayoutPrefix}customfields",
			[
				'product' => $this->product,
				'position' => 'normal',
			],
		);

		echo shopFunctionsF::renderVmSubLayout(
			"{$sublayoutPrefix}customfields",
			[
				'product' => $this->product,
				'position' => 'related_products',
				'class' => 'product-related-products',
				'customTitle' => true,
			],
		);

		echo shopFunctionsF::renderVmSubLayout(
			"{$sublayoutPrefix}customfields",
			[
				'product' => $this->product,
				'position' => 'related_categories',
				'class' => 'product-related-categories',
			],
		);

		echo shopFunctionsF::renderVmSubLayout(
			"{$sublayoutPrefix}customfields",
			[
				'product' => $this->product,
				'position' => 'onbot',
			],
		);
	?>

	<?php echo $this->product->event->afterDisplayContent; ?>

	<?php echo $this->loadTemplate('reviews'); ?>

	<?php
		if ($this->cat_productdetails) {
			echo $this->loadTemplate('showcategory');
		}

		if ($this->product->prices['salesPrice'] > 0) {
			echo shopFunctionsF::renderVmSubLayout(
				'snippets',
				[
					'product' => $this->product,
					'currency' => $this->currency,
					'showRating' => $this->showRating,
				],
			);
		}
	?>

	<?php

		/* TODO: Either rewrite this code in native JS or move it out to template-virtuemart.js and template-virtuemart-cart.js
		along with passing through the VMs config options to JS that will control the inclusion of this code below the same way
		it is done here. */

		/* vmJsApi::addJScript('recalcReady', '
			jQuery(function () {
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

				jQuery(function () {
					Virtuemart.container = jQuery(Virtuemart.containerSelector);
				});

			');

			vmJsApi::addJScript('vmPreloader', '

				jQuery(function () {

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
		// TODO: Find a better way to reliabliy disable assets with version numbers in filenames because the current way is very fragile towards VM updates that is until VM switches over to Web Assets
		vmJsApi::removeJScript('facebox');
		vmJsApi::removeJScript('fancybox/jquery.fancybox-1.3.4.2.pack');
		vmJsApi::removeJScript('popups');

		unset($joomlaDocument->_styleSheets['/components/com_virtuemart/assets/css/facebox.css?vmver=' . VM_JS_VER]);
		unset($joomlaDocument->_styleSheets['/components/com_virtuemart/assets/css/jquery.fancybox-1.3.4.css?vmver=' . VM_JS_VER]);

		// This final VM JS writing call has to stay inside the product container (as defined by Virtuemart.containerSelector) for scripts above to be inlcuded during AJAX page update
		echo vmJsApi::writeJS();

	?>

</div>