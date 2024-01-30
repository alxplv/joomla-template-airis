<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;


if (isset($viewData['product']) === false) {
	return;
}

// Used in including proper sublayouts
$sublayoutPrefix = basename(__FILE__, 'addtocart.php');

// Process product data
$product = $viewData['product'];
$productName = htmlspecialchars(trim(vmText::_($product->product_name)), ENT_QUOTES, 'UTF-8');
$productSku = htmlspecialchars(trim($product->product_sku), ENT_QUOTES, 'UTF-8');

// TODO: Refactor this whole $discaimers deal into a simple array of disclaimers of various types without the need for marker variables and such
// Controls the output of disclaimers container
if (isset($viewData['airis-template-options'])) {
	$haveDisclaimers = false;

	// Controls the display of not-a-public-offer disclaimer
	$displayNotAPublicOfferDisclaimer = false;
	$notAPublicOfferDisclaimerDisplayMode = $viewData['airis-template-options']->get('virtuemartDisplayModeDisclaimerNotAPublicOffer', 'none');

	if ($notAPublicOfferDisclaimerDisplayMode === 'always' || ($notAPublicOfferDisclaimerDisplayMode === 'catalog' && VmConfig::get('use_as_catalog', 0))) {
		$haveDisclaimers = true;
		$displayNotAPublicOfferDisclaimer = true;
	}

	// Controls the display of optional custom category view disclaimers defined using language overrides
	$maximumCustomDisclaimers = 5;
	$customDisclaimerLanguageConstantBase = 'TPL_AIRIS_COM_VIRTUEMART_PRODUCTDETAILS_DISCLAIMER_CUSTOM_';

	// See if we have any custom disclaimers defined
	if ($haveDisclaimers === false) {
		for ($currentCustomDisclaimerIndex = 1; $currentCustomDisclaimerIndex <= $maximumCustomDisclaimers; $currentCustomDisclaimerIndex++) {
			$currentCustomDisclaimerLanguageConstant = $customDisclaimerLanguageConstantBase . $currentCustomDisclaimerIndex;

			if (Text::_($currentCustomDisclaimerLanguageConstant) !== $currentCustomDisclaimerLanguageConstant) {
				$haveDisclaimers = true;
				break;
			}
		}
	}
}

// TODO: Find out what this whole row heights thing actually does
if (isset($viewData['rowHeights'])) {
	$rowHeights = $viewData['rowHeights'];
} else {
	$rowHeights['customfields'] = true;
}

// Default custom field position this layout outputs along with its HTML-structure
$positions = [
	'addtocart',
];

// Alternative custom field positions are also accepted
if (isset($viewData['position'])) {
	if (is_array($viewData['position'])) {
		$positions = $viewData['position'];
	} else {
		$positions = [
			$viewData['position'],
		];
	}
}

?>

<div class="addtocart-area virtuemart-product-addtocart">
	<form method="post" class="product js-recalculate" action="<?php echo Route::_('index.php?option=com_virtuemart', false); ?>" autocomplete="off">

		<?php // TODO: Empty vm-customfields-wrap still gets into the output. Come up with a better conditional. ?>
		<?php // TODO: Find out what this whole $rowHeights thing actually does ?>
		<?php if (isset($rowHeights['customfields'])) : ?>
			<div class="vm-customfields-wrap">
				<?php
					foreach ($positions as $position) {
						echo shopFunctionsF::renderVmSubLayout(
							"{$sublayoutPrefix}customfields",
							[
								'product' => $product,
								'position' => $position,
							],
						);
					}
				?>
			</div>
		<?php endif; ?>

		<?php
			if (VmConfig::get('use_as_catalog', 0) === 0) {
				echo shopFunctionsF::renderVmSubLayout(
					"{$sublayoutPrefix}addtocartbar",
					[
						'product' => $product,
						'airis-template-options' => $viewData['airis-template-options'],
					],
				);
			}
		?>

		<input type="hidden" name="option" value="com_virtuemart">
		<input type="hidden" name="view" value="cart">
		<input type="hidden" name="pname" value="<?php echo $productName; ?>">
		<input type="hidden" name="pid" value="<?php echo $product->virtuemart_product_id; ?>">

		<?php $itemId = vRequest::getInt('Itemid', false); ?>
		<?php if ($itemId) : ?>
			<input type="hidden" name="Itemid" value="<?php echo $itemId; ?>">
		<?php endif; ?>

	</form>

	<?php // Custom Contact Us link with product id, name and sku exposed as data attributes. Optionally replaces the notify page link for out-of-stock products. ?>
	<?php if (isset($viewData['airis-template-options']) && ($viewData['airis-template-options']->get('showVirtuemartProductLinkContactUs') || ($viewData['airis-template-options']->get('showVirtuemartProductLinkContactUs') && $product->show_notify && $viewData['airis-template-options']->get('replaceVirtuemartProductLinkNotifyWithContactUs')))) : ?>
		<?php
			$contactUsLinkAttributes = [
				'class' => 'virtuemart-product-link virtuemart-product-link-contact-us btn',
				'rel' => 'nofollow',
				'data-virtuemart-product-id' => $product->virtuemart_product_id,
				'data-virtuemart-product-name' => $productName,
			];

			if ($productSku === '') {
				$contactUsLinkAttributes['data-virtuemart-product-sku'] = $productSku;
			}
		?>

		<div class="virtuemart-product-addtocart-links" data-nosnippet>
			<?php
				echo HTMLHelper::link(
					'#virtuemart-product-form-contact-us',
					vmText::_('TPL_AIRIS_COM_VIRTUEMART_PRODUCT_LINK_CONTACT_US_TEXT'),
					$contactUsLinkAttributes,
				);
			?>
		</div>

	<?php endif; ?>

	<?php if (isset($viewData['airis-virtuemart-view']) && $viewData['airis-virtuemart-view'] === 'productdetails') : ?>

		<?php if ($haveDisclaimers) : ?>
			<ul class="virtuemart-disclaimers productdetails-disclaimers list-unstyled" data-nosnippet>

				<?php if ($notAPublicOfferDisclaimerDisplayMode === 'always' || ($notAPublicOfferDisclaimerDisplayMode === 'catalog' && VmConfig::get('use_as_catalog', 0))) : ?>
					<li class="virtuemart-disclaimer virtuemart-disclaimer-not-a-public-offer productdetails-disclaimer productdetails-disclaimer-not-a-public-offer">
						<?php echo Text::_('TPL_AIRIS_COM_VIRTUEMART_PRODUCTDETAILS_DISCLAIMER_NOT_A_PUBLIC_OFFER'); ?>
					</li>
				<?php endif; ?>

				<?php for ($currentCustomDisclaimerIndex = 1; $currentCustomDisclaimerIndex <= $maximumCustomDisclaimers; $currentCustomDisclaimerIndex++) : ?>

					<?php $currentCustomDisclaimerLanguageConstant = $customDisclaimerLanguageConstantBase . $currentCustomDisclaimerIndex; ?>

					<?php if (Text::_($currentCustomDisclaimerLanguageConstant) !== $currentCustomDisclaimerLanguageConstant) : ?>
						<li class="virtuemart-disclaimer virtuemart-disclaimer-custom-<?php echo $currentCustomDisclaimerIndex; ?> productdetails-disclaimer productdetails-disclaimer-custom-<?php echo $currentCustomDisclaimerIndex; ?>">
							<?php echo Text::_($currentCustomDisclaimerLanguageConstant); ?>
						</li>
					<?php endif; ?>

				<?php endfor; ?>

			</ul>
		<?php endif; ?>

	<?php endif; ?>

</div>