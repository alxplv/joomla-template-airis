<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\Language\Text;

// Product name, status message, optional error text for each added product along with related products data (for the first added product)
$productsAddedData = [
    'products' => [],
    'relatedProductsCustomFields' => [],
];

// Check if a product has been added
if (isset($this->products) && is_countable($this->products) && count($this->products) > 0) {
    // Add product added data for each added product
    foreach ($this->products as $product) {
        $productsAddedDataProduct = [
            'productName' => '',
            'productQuantity' => 0,
            'statusMessage' => '',
            'errorMessage' => '',
        ];

        if (isset($product->errorMsg) && $product->errorMsg !== '') {
            $productsAddedDataProduct['errorMessage'] = $product->errorMsg;
        }

        if (isset($product->quantity) && $product->quantity > 0) {
            $productsAddedDataProduct['productName'] = trim(vmText::_($product->product_name));

            if (isset($product->quantityAdded)) {
                $productsAddedDataProduct['productQuantity'] = $product->quantityAdded;
            } else {
                $productsAddedDataProduct['productQuantity'] = $product->quantity;
            }

            $productsAddedDataProduct['statusMessage'] = Text::sprintf(
                'TPL_AIRIS_COM_VIRTUEMART_CART_PRODUCT_ADDED',
                $productsAddedDataProduct['productName'],
                $productsAddedDataProduct['productQuantity'],
            );
        }

        // Append all acquired product added data for this product
        $productsAddedData['products'][] = $productsAddedDataProduct;
    }

    // Add related products data (related to the first added product only)
    if (VmConfig::get('popup_rel', 0)) {
        $product = reset($this->products);
        $customFieldsModel = VmModel::getModel('customfields');
        $product->customfields = $customFieldsModel->getCustomEmbeddedProductCustomFields($product->allIds, 'R');
        $customFieldsModel->displayProductCustomfieldFE($product, $product->customfields);

        if (isset($product->customfields) && is_countable($product->customfields) && count($product->customfields) > 0) {
            foreach ($product->customfields as $customField) {
                if (isset($customField->display) && $customField->display !== '') {
                    $productsAddedData['relatedProductsCustomFields'][] = [
                        'fieldType' => $customField->field_type,
                        'fieldDisplay' => $customField->display,
                    ];
                }
            }
        }
    }
}

// Output resulting data as JSON
echo json_encode($productsAddedData);