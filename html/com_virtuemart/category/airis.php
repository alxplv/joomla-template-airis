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

// Controls the output of disclaimers container
$haveDisclaimers = false;

// Controls the display of not-a-public-offer disclaimer
$displayNotAPublicOfferDisclaimer = false;
$notAPublicOfferDisclaimerDisplayMode = $airisTemplateOptions->get('virtuemartDisplayModeDisclaimerNotAPublicOffer', 'none');

if ($notAPublicOfferDisclaimerDisplayMode === 'always' || ($notAPublicOfferDisclaimerDisplayMode === 'catalog' && VmConfig::get('use_as_catalog', 0)))
{
	$haveDisclaimers = true;
	$displayNotAPublicOfferDisclaimer = true;
}

// Controls the display of optional custom category view disclaimers defined using language overrides
$maximumCustomDisclaimers = 5;
$customDisclaimerLanguageConstantBase = 'TPL_AIRIS_COM_VIRTUEMART_CATEGORY_DISCLAIMER_CUSTOM_';

// See if we have any custom disclaimers defined
if (!$haveDisclaimers) {
	for ($currentCustomDisclaimerIndex = 1; $currentCustomDisclaimerIndex <= $maximumCustomDisclaimers; $currentCustomDisclaimerIndex++)
	{
		$currentCustomDisclaimerLanguageConstant = $customDisclaimerLanguageConstantBase . $currentCustomDisclaimerIndex;
	
		if (Text::_($currentCustomDisclaimerLanguageConstant) !== $currentCustomDisclaimerLanguageConstant)
		{
			$haveDisclaimers = true;
			break;
		}
	}
}

// Used in including proper sublayouts
$sublayoutPrefix = pathinfo(__FILE__, PATHINFO_FILENAME) . '_';

$joomlaApplication = Factory::getApplication();
$joomlaSefEnabled = (bool) $joomlaApplication->getCfg('sef');

// Respect the sitename in <title> setting of Joomla!. Also apply custom separator.
$joomlaDocument = Factory::getDocument();
$joomlaSitenamePagetitlesMode = (int) $joomlaApplication->getCfg('sitename_pagetitles');

if ($joomlaSitenamePagetitlesMode)
{
	$joomlaSitename = $joomlaApplication->getCfg('sitename');
	$virtuemartCategoryPagetitle = $this->category->virtuemart_category_id ? vmText::_($this->category->category_name) : $joomlaDocument->title;
	$virtuemartPagetitleFormat = Text::_('TPL_AIRIS_COM_VIRTUEMART_JPAGETITLE');

	switch ($joomlaSitenamePagetitlesMode)
	{
		case 1:
			$joomlaDocument->setTitle(Text::sprintf($virtuemartPagetitleFormat, $joomlaSitename, $virtuemartCategoryPagetitle));
			break;
		case 2:
			$joomlaDocument->setTitle(Text::sprintf($virtuemartPagetitleFormat, $virtuemartCategoryPagetitle, $joomlaSitename));
	}
}

// This has to go after Joomla! document title modifications from above to preserve them during dynamic updates of category products
if (vRequest::getInt('dynamic', 0) && vRequest::getInt('virtuemart_product_id', 0)) {

	if (!empty($this->products))
	{
		if ($this->fallback)
		{
			$p = $this->products;
			$this->products = array();
			$this->products[0] = $p;
			vmdebug('Refallback');
		}

		echo shopFunctionsF::renderVmSubLayout($sublayoutPrefix . 'products', array('products' => $this->products, 'currency' => $this->currency, 'products_per_row' => $this->perRow, 'showRating' => $this->showRating, 'airis-template-options' => $airisTemplateOptions));
	}

	return;
}

?>

<div class="category-view category-view-virtuemart-category-id-<?php echo vRequest::getInt('virtuemart_category_id', 0); ?>">

	<div class="page-header virtuemart-page-header category-view-header">
		<h2>
			<?php
				// Display page title taken from COM_VIRTUEMART_HOME language variable for the root category because it usually has no title
				echo $this->category->virtuemart_category_id ? htmlspecialchars(vmText::_($this->category->category_name), ENT_QUOTES, 'UTF-8') : vmText::sprintf('COM_VIRTUEMART_HOME', $this->vendor->vendor_store_name);
			?>
		</h2>
	</div>

	<?php
		vmJsApi::addJScript('vm-hover', '

			jQuery(document).on("ready", function () {

				jQuery(".orderlistcontainer").hover(
					function() { jQuery(this).find(".orderlist").stop().show(); },
					function() { jQuery(this).find(".orderlist").stop().hide(); }
				);

			});

		');
	?>

	<?php if ($this->show_store_desc && !empty($this->vendor->vendor_store_desc)) : ?>
		<div class="airis-item-content virtuemart-description category-view-store-description">
			<?php echo $this->vendor->vendor_store_desc; ?>
		</div>
	<?php endif; ?>

	<?php if ($this->showcategory_desc && !empty($this->category->category_description) && empty($this->keyword)) : ?>

		<div class="airis-item-content virtuemart-description category-view-category-description">
			<?php echo $this->category->category_description; ?>
		</div>

		<?php if (!empty($this->manu_descr)) : ?>
			<div class="airis-item-content virtuemart-description category-view-manufacturer-description">
				<?php echo $this->manu_descr; ?>
			</div>
		<?php endif; ?>

	<?php endif; ?>

	<?php // TODO: Also display this search block if 'customfields' get-parameter is populated ?>
	<?php if ($this->showsearch || $this->keyword !== false) : ?>
		<div class="virtuemart_search virtuemart-search category-view-search" data-nosnippet>
			<form class="virtuemart-search-form category-view-search-form" action="<?php echo Route::_('index.php?option=com_virtuemart&view=category&limitstart=0', false); ?>" method="get">
				
				<?php if (!empty($this->searchCustomList)) : ?>
					<div class="virtuemart-search-form-custom-fields-search-list category-view-search-form-custom-fields-search-list">
						<?php echo $this->searchCustomList; ?>
					</div>
				<?php endif; ?>

				<?php if (!empty($this->searchCustomValuesAr)) : ?>
					<div class="virtuemart-search-form-custom-fields-search-values category-view-search-form-custom-fields-search-values airis-flex-item-rows">
						<?php echo ShopFunctionsF::renderVmSubLayout($sublayoutPrefix . 'searchcustomvalues', array('searchcustomvalues' => $this->searchCustomValuesAr)); ?>
					</div>
				<?php endif; ?>

				<div class="virtuemart-search-form-elements category-view-search-form-elements">

					<div class="virtuemart-search-form-element virtuemart-search-form-element-container-keyword category-view-search-form-element category-view-search-form-element-container-keyword">
						<input type="text" name="keyword" value="<?php echo $this->keyword; ?>" placeholder="<?php echo vmText::_('COM_VIRTUEMART_SEARCH'); ?>" class="virtuemart-search-form-elements-input-text category-view-search-form-elements-input-text">
					</div>

					<div class="virtuemart-search-form-element virtuemart-search-form-element-container-submit category-view-search-form-element category-view-search-form-element-container-submit">
						<button class="virtuemart-search-form-elements-submit-button category-view-search-form-elements-submit-button btn" title="<?php echo vmText::_('COM_VIRTUEMART_SEARCH'); ?>">
							<?php echo vmText::_('COM_VIRTUEMART_SEARCH'); ?>
						</button>
					</div>

				</div>

				<div class="virtuemart-search-form-description category-view-search-form-description">
					<?php echo vmText::_('COM_VM_SEARCH_DESC'); ?>
				</div>

				<?php if (!$joomlaSefEnabled) : ?>

					<input type="hidden" name="option" value="com_virtuemart">
					<input type="hidden" name="view" value="category">
					<input type="hidden" name="virtuemart_category_id" value="<?php echo vRequest::getInt('virtuemart_category_id', 0); ?>">
					<input type="hidden" name="limitstart" value="0">
					<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>">

				<?php endif; ?>

			</form>
		</div>

		<?php
			vmJsApi::addJScript('sendFormChange', '

				jQuery(document).on("ready", function () {

					jQuery(".changeSendForm")
						.off("change", Virtuemart.sendCurrForm)
						.on("change", Virtuemart.sendCurrForm)
					;

				});

			');
		?>

	<?php endif; ?>

	<?php
		// Show child categories
		if ($this->showcategory && !empty($this->category->haschildren) && empty($this->keyword)) echo ShopFunctionsF::renderVmSubLayout($sublayoutPrefix . 'categories', array('categories' => $this->category->children, 'categories_per_row' => $this->categories_per_row));
	?>

	<?php if (($this->showproducts && !empty($this->products)) || ($this->showsearch || $this->keyword !== false)) : ?>

		<div class="browse-view category-view-browse-view">

			<?php if ($this->keyword !== false) : ?>
				<div class="page-header virtuemart-page-header virtuemart-search-results-header browse-view-search-results-header" data-nosnippet>
					<h3><?php echo Text::sprintf('TPL_AIRIS_COM_VIRTUEMART_SEARCH_KEYWORD_FOR', $this->keyword); ?></h3>
				</div>
			<?php endif; ?>

			<?php if (!empty($this->orderByList)) : ?>
				<div class="orderby-displaynumber browse-view-controls airis-flex" data-nosnippet>

					<?php if (!empty($this->products)) : ?>
						<div class="vm-order-list browse-view-control browse-view-control-product-order">
							<?php 
								// Order list links should not be crawled by search engines. Also remove empty keyword GET parameter because it can generate duplicate page warnings in Google and Yandex tools for webmasters.
								$browseViewControlProductOrderPatterns = array('<a', 'class="orderlistcontainer', '?keyword="');
								$browseViewControlProductOrderReplaces = array('<a rel="nofollow"', 'class="orderlistcontainer browse-view-control-product-order-container', '"');

								// The output of both 'orderby' and 'manufacturer' is controlled by the getOrderByList () function from /administrator/components/com_virtuemart/models/product.php so there's no way to influence its output other than string replacement
								// TODO: Replace the output with HTML select + 'onchange' JS
								echo str_replace($browseViewControlProductOrderPatterns, $browseViewControlProductOrderReplaces, $this->orderByList['orderby']);
							?>
						</div>
					<?php endif; ?>

					<?php if (!empty($this->orderByList['manufacturer'])) : ?>
						<div class="vm-order-list browse-view-control browse-view-control-manufacturers">
							<?php
								$browseViewControlManufacturersPatterns = array('<a', 'class="', '?keyword="');
								$browseViewControlManufacturersReplaces = array('<a rel="nofollow"', 'class=" browse-view-control-manufacturers-container', '"');

								// The output of both 'orderby' and 'manufacturer' is controlled by the getOrderByList () function from /administrator/components/com_virtuemart/models/product.php so there's no way to influence its output other than string replacement
								// TODO: Replace the output with HTML select + 'onchange' JS
								echo str_replace($browseViewControlManufacturersPatterns, $browseViewControlManufacturersReplaces, $this->orderByList['manufacturer']); 
							?>
						</div>
					<?php endif; ?>

					<?php if (!empty($this->products)) : ?>
						<div class="display-number browse-view-control browse-view-control-products-per-page airis-flex airis-flex-align-center">

							<div class="browse-view-control-products-per-page-title">
								<?php echo $this->vmPagination->getResultsCounter(); ?>
							</div>

							<div class="browse-view-control-products-per-page-container">
								<?php echo str_replace('class="', 'class="browse-view-control-products-per-page-select ', $this->vmPagination->getLimitBox($this->category->limit_list_step)); ?>
							</div>

						</div>
					<?php endif; ?>

				</div>
			<?php endif; ?>

			<?php if ($this->vmPagination->{'pages.total'} > 1) : ?>
				<div class="pagination virtuemart-pagination virtuemart-pagination-top" data-nosnippet>
					<?php echo $this->vmPagination->getPagesLinks(); ?>
					<div class="counter virtuemart-pagination-counter"><?php echo $this->vmPagination->getPagesCounter(); ?></div>
				</div>
			<?php endif; ?>

			<?php if (!empty($this->products)) : ?>

				<?php
					// revert of the fallback in the view.html.php, will be removed vm3.2
					if ($this->fallback)
					{
						$p = $this->products;
						$this->products = array();
						$this->products[0] = $p;
						vmdebug('Refallback');
					}

					// IMPORTANT: Do not forget to mirror this call for the dynamic product updates section above
					echo shopFunctionsF::renderVmSubLayout($sublayoutPrefix . 'products', array('products' => $this->products, 'currency' => $this->currency, 'products_per_row' => $this->perRow, 'showRating' => $this->showRating, 'airis-template-options' => $airisTemplateOptions));
				?>

				<?php if ($this->vmPagination->{'pages.total'} > 1) : ?>
					<div class="pagination virtuemart-pagination virtuemart-pagination-bottom" data-nosnippet>
						<?php echo $this->vmPagination->getPagesLinks(); ?>
						<div class="counter virtuemart-pagination-counter"><?php echo $this->vmPagination->getPagesCounter(); ?></div>
					</div>
				<?php endif; ?>

				<?php if ($haveDisclaimers) : ?>
					<ul class="virtuemart-disclaimers browse-view-disclaimers unstyled" data-nosnippet>

						<?php if ($displayNotAPublicOfferDisclaimer) : ?>
							<li class="virtuemart-disclaimer virtuemart-disclaimer-not-a-public-offer browse-view-disclaimer browse-view-disclaimer-not-a-public-offer">
								<?php echo Text::_('TPL_AIRIS_COM_VIRTUEMART_CATEGORY_DISCLAIMER_NOT_A_PUBLIC_OFFER'); ?>
							</li>
						<?php endif; ?>

						<?php for ($currentCustomDisclaimerIndex = 1; $currentCustomDisclaimerIndex <= $maximumCustomDisclaimers; $currentCustomDisclaimerIndex++) : ?>

							<?php $currentCustomDisclaimerLanguageConstant = $customDisclaimerLanguageConstantBase . $currentCustomDisclaimerIndex; ?>

							<?php if (Text::_($currentCustomDisclaimerLanguageConstant) !== $currentCustomDisclaimerLanguageConstant) : ?>
								<li class="virtuemart-disclaimer virtuemart-disclaimer-custom-<?php echo $currentCustomDisclaimerIndex; ?> browse-view-disclaimer browse-view-disclaimer-custom-<?php echo $currentCustomDisclaimerIndex; ?>">
									<?php echo Text::_($currentCustomDisclaimerLanguageConstant); ?>
								</li>
							<?php endif; ?>

						<?php endfor; ?>

					</ul>
				<?php endif; ?>

			<?php elseif ($this->keyword !== false) : ?>
				<div class="virtuemart-page-empty"><?php echo vmText::_('COM_VIRTUEMART_NO_RESULT'), '.'; ?></div>
			<?php endif; ?>

		</div>

	<?php endif; ?>

	<?php // Custom empty category message ?>
	<?php if ($this->showproducts && empty($this->category->haschildren) && empty($this->products) && empty($this->keyword)) : ?>
		<div class="virtuemart-page-empty">
			<?php echo Text::_('TPL_AIRIS_COM_VIRTUEMART_NO_PRODUCTS'); ?>
		</div>
	<?php endif; ?>

</div>

<?php

if (VmConfig::get('ajax_category', 0))
{

	vmJsApi::addJScript('ajax_category', '

		Virtuemart.containerSelector = ".category-view";

		jQuery(document).on("ready", function () {
			Virtuemart.container = jQuery(Virtuemart.containerSelector);
		});

	');

	vmJsApi::jDynUpdate();
}