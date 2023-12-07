<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\Language\Text;

// Product name, status message, optional error text for each added product along with related products data (for the first added product)
$productsAddedData = array('products' => array(), 'relatedProductsCustomFields' => array());

// Check if a product has been added
// TODO: Replace is_array() by is_countable() for Joomla! 5
if (isset($this->products) && is_array($this->products) && count($this->products) > 0) {

	// Add product added data for each added product
	foreach ($this->products as $product)
	{
		$productsAddedDataProduct = array(
			'productName' => null,
			'productQuantity' => null,
			'statusMessage' => null,
			'errorMessage' => !empty($product->errorMsg) ? $product->errorMsg : null
		);

		if ($product->quantity > 0)
		{
			$productName = trim(vmText::_($product->product_name));
		    $productQuantity = isset($product->quantityAdded) ? $product->quantityAdded : $product->quantity;
			$productsAddedDataProduct['productName'] = $productName;
			$productsAddedDataProduct['productQuantity'] = $productQuantity;
			$productsAddedDataProduct['statusMessage'] = Text::sprintf('TPL_AIRIS_COM_VIRTUEMART_CART_PRODUCT_ADDED', $productName, $productQuantity);
		}

		// Append all acquired product added data for this product
		$productsAddedData['products'][] = $productsAddedDataProduct;
	}
}

// Add related products data (the first added product only)
if (VmConfig::get('popup_rel', 0))
{
	if ($this->products && is_array($this->products) && count($this->products) > 0)
	{
		$product = reset($this->products);
		$customFieldsModel = VmModel::getModel('customfields');
		$product->customfields = $customFieldsModel->getCustomEmbeddedProductCustomFields($product->allIds, 'R');
		$customFieldsModel->displayProductCustomfieldFE($product, $product->customfields);

		if (!empty($product->customfields))
		{
	 		// vmText::_('COM_VIRTUEMART_RELATED_PRODUCTS');

			foreach ($product->customfields as $rFields)
			{
				if (!empty($rFields->display))
				{
					$productsAddedData['relatedProductsCustomFields'][] = array(
						'fieldType' => $rFields->field_type,
						'fieldDisplay' => $rFields->display
					);
				}
			}
		}
	}
}

// Output resulting data as JSON
echo json_encode($productsAddedData);