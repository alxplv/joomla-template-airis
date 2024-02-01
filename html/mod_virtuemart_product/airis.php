<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Image\Image;
use Joomla\CMS\Language\Text;

// TODO: Check if these can be defined by template globally
// Container items per row settings
defined('TPL_AIRIS_ITEMS_PER_ROW_MIN') or define('TPL_AIRIS_ITEMS_PER_ROW_MIN', 1);
defined('TPL_AIRIS_ITEMS_PER_ROW_MAX') or define('TPL_AIRIS_ITEMS_PER_ROW_MAX', 6);
defined('TPL_AIRIS_ITEMS_PER_ROW_DEFAULT') or define('TPL_AIRIS_ITEMS_PER_ROW_DEFAULT', 3);

// Used in including proper sublayouts
$sublayoutPrefix = pathinfo(__FILE__, PATHINFO_FILENAME) . '_';

// Process module data and options
$moduleClassSuffix = htmlspecialchars(rtrim($params->get('moduleclass_sfx', '')), ENT_QUOTES, 'UTF-8'); // Not using trim() here to keep possible and perfectly acceptable leading whitespace
$moduleClassSuffix = preg_replace('/\s{2,}/', ' ', $moduleClassSuffix); // Also remove non-singular whitespaces just in case
$moduleDisplayStyleUseDivs = ($display_style === 'div');
$moduleProductTypeGroup = $Product_group;
$moduleHeaderText = htmlspecialchars(trim($headerText), ENT_QUOTES, 'UTF-8');
$moduleFooterText = htmlspecialchars(trim($footerText), ENT_QUOTES, 'UTF-8');
$productsCount = $totalProd;
$productsPerRow = $products_per_row;
$showAddToCart = $show_addtocart;
$showProductPrice = $show_price;

// Custom options prefix
$airisModuleClassSuffixParamPrefix = ' airis-module-param-virtuemart-';

// Supported custom options of this view
$airisModuleClassSuffixParams = [
    'disable-productdetails-link' => false,
    'products-display-discounted-only' => false,
    'products-display-new-only' => false,
    'products-display-out-of-stock-last' => false,
    'products-limit' => PHP_INT_MAX,
];

// Process custom module options
// TODO: It is better to replace strpos() and some regex-based functions with explode(' ',$moduleClassSuffix) and working with concrete string values
if ($moduleClassSuffix && strpos($moduleClassSuffix, $airisModuleClassSuffixParamPrefix) !== false) {
    // Try to acquire new values for any found custom options
    foreach ($airisModuleClassSuffixParams as $airisModuleClassSuffixParamKey => $airisModuleClassSuffixParamValue) {
        $airisModuleClassSuffixParamPrefixed = $airisModuleClassSuffixParamPrefix . $airisModuleClassSuffixParamKey;

        // Boolean values are simply toggled / inverted as their class strings don't have any attached value in the form of a hyphen-separated suffix
        if (is_bool($airisModuleClassSuffixParamValue)) {
            if (strpos($moduleClassSuffix, $airisModuleClassSuffixParamPrefixed) !== false) {
                $airisModuleClassSuffixParams[$airisModuleClassSuffixParamKey] = !$airisModuleClassSuffixParamValue;
            }
        } else {
            $airisModuleClassSuffixParamMatches = [];

            // Prepare regular expression pattern for non-boolean values
            $airisModuleClassSuffixParamKeyPattern = preg_quote($airisModuleClassSuffixParamPrefixed);
            $airisModuleClassSuffixParamPattern = "/\b$airisModuleClassSuffixParamKeyPattern\-(\w+)\b/";

            // Only last matching custom option will be processed in case if there were duplicates for some reason
            $airisModuleClassSuffixParamMatchResult = preg_match_all($airisModuleClassSuffixParamPattern, $moduleClassSuffix, $airisModuleClassSuffixParamMatches);

            if ($airisModuleClassSuffixParamMatchResult) {
                $airisModuleClassSuffixParamValueSubstring = end($airisModuleClassSuffixParamMatches[1]);

                // Update option with extracted param value
                if ($airisModuleClassSuffixParamValueSubstring) {
                    $airisModuleClassSuffixParams[$airisModuleClassSuffixParamKey] = $airisModuleClassSuffixParamValueSubstring;
                }
            }
        }
    }

    // Display only discounted products
    if ($airisModuleClassSuffixParams['products-display-discounted-only']) {
        for ($i = 0; $i < $productsCount; $i++) {
            if (empty($products[$i]->prices['discountAmount'])) {
                unset($products[$i]);
            }
        }

        // Reindex products array just in case
        $products = array_values($products);

        // Don't forget to update product counter value after all product list alterations
        $productsCount = count($products);
    }

    // Display only products marked as new
    if ($airisModuleClassSuffixParams['products-display-new-only']) {
        for ($i = 0; $i < $productsCount; $i++) {
            $isProductMarkedAsNew = false;

            if (isset($products[$i]->customfieldsSorted['airis-product-markers'])) {
                foreach ($products[$i]->customfieldsSorted['airis-product-markers'] as $airisProductMarker) {
                    if ($airisProductMarker->custom_desc === 'airis-product-marker-new') {
                        $isProductMarkedAsNew = true;
                        break;
                    }
                }
            }

            if (!$isProductMarkedAsNew) {
                unset($products[$i]);
            }
        }

        // Reindex products array just in case
        $products = array_values($products);

        // Don't forget to update product counter value after all product list alterations
        $productsCount = count($products);
    }

    // Push out-of-stock products to the end of the list
    // TODO: First check if out-of-stock products are displayable in VM settings
    if ($airisModuleClassSuffixParams['products-display-out-of-stock-last']) {
        for ($i = 0; $i < $productsCount; $i++) {
            if ($products[$i]->product_in_stock === 0) {
                $tempProduct = $products[$i];
                unset($products[$i]);
                $products[] = $tempProduct;
            }
        }

        // Just in case
        unset($tempProduct);

        // Reindex products array just in case
        $products = array_values($products);

        // Not really need to update the counter here but whatever
        $productsCount = count($products);
    }

    // Limit the final number of products to be displayed. Useful only when limiting the output to discounted products and/or products marked as new since we're forced to request every published product to perform our own filering and limiting. Otherwise we rely on module param "Number of displayed products".
    if ($airisModuleClassSuffixParams['products-limit'] !== PHP_INT_MAX) {
        $products = array_slice($products, 0, $airisModuleClassSuffixParams['products-limit']);

        // Reindex products array just in case
        $products = array_values($products);

        // Don't forget to update product counter value after all product list alterations
        $productsCount = count($products);
    }
}

// Apply required CSS class for products per row style variant based on module settings by replacing provided integer values with numeral words to be used as a class suffix
// $productsPerRowAllowedDigits = range(1, 6);
// $productsPerRowAllowedNumerals = ['one', 'two', 'three', 'four', 'five', 'six'];
$productsPerRowAllowedNumerals = [2 => 'two', 'three', 'four', 'five', 'six']; // 2-based array for additional code simplicity

// Ignore nonsensical product per row values
// if (!in_array($productsPerRow, $productsPerRowAllowedDigits)) $productsPerRow = 3;
if ($productsPerRow < TPL_AIRIS_ITEMS_PER_ROW_MIN || $productsPerRow > TPL_AIRIS_ITEMS_PER_ROW_MAX) {
    $productsPerRow = TPL_AIRIS_ITEMS_PER_ROW_DEFAULT;
}

// Reduce the number of products per row if there is not enough products in total to fill out the entire row to reduce horizontal empty space
if ($productsCount < $productsPerRow) {
    $productsPerRow = $productsCount;
}

// Prepare HTML-class strings for item container and its items
$productsContainerClasses = 'airis-module-virtuemart-product__products module-virtuemart-product__products productdetails';
$productItemClasses = 'airis-module-virtuemart-product__product module-virtuemart-product__product product-container virtuemart-product module-virtuemart-product';

// Replace the final integer value with a number word
// $productsPerRow = str_replace($productsPerRowAllowedDigits, $productsPerRowAllowedNumerals, $productsPerRow);

if ($productsPerRow !== TPL_AIRIS_ITEMS_PER_ROW_MIN) {
    $productsContainerClasses .= ' airis-flex-item-rows';
    $productItemClasses .= " airis-flex-item-per-row-$productsPerRowAllowedNumerals[$productsPerRow]";
}
else {
    $productsContainerClasses .= ' airis-block-items';
    $productItemClasses .= ' airis-block-item';
}

?>

<div class="airis-module-virtuemart-product module-virtuemart-product">

    <?php if ($moduleHeaderText) : ?>
        <div class="airis-module-virtuemart-product__module-header module-virtuemart-product__module-header">
            <?php echo $moduleHeaderText; ?>
        </div>
    <?php endif; ?>

    <?php if ($productsCount) : ?>

        <?php if ($moduleDisplayStyleUseDivs) : ?>
            <div class="<?php echo $productsContainerClasses; ?>">
        <?php else : ?>
            <ul class="<?php echo $productsContainerClasses; ?> list-unstyled">
        <?php endif; ?>

            <?php foreach ($products as $product) : ?>

                <?php
                    // Make the product name string safe for usage in HTML attributes
                    $productName = htmlspecialchars(trim(vmText::_($product->product_name)), ENT_QUOTES, 'UTF-8');

                    // Apply any additional product marker classes and an optional badge text used by products
                    $productMarkerClasses = '';
                    $productBadgeClasses = '';
                    $productBadgeText = '';

                    // Check if this product is discounted, this marker always takes preference
                    if (!empty($product->prices['discountAmount'])) {
                        $productMarkerClasses = 'airis-module-virtuemart-product__product_discounted module-virtuemart-product__product_discounted virtuemart-product_discounted';
                        $productBadgeClasses = 'airis-module-virtuemart-product__product-badge_discounted module-virtuemart-product__product-badge_discounted virtuemart-product__product-badge_discounted';
                        $productBadgeText = Text::_('TPL_AIRIS_COM_VIRTUEMART_PRODUCT_BADGE_DISCOUNT');
                    }

                    // Any other possible markers get to set their badge text only in the absense of the discounted product marker
                    // TODO: Replace the (is_array() || istanceof Countable) check with is_countable() calle once were on PHP 7.3+ for good
                    // TODO: Add support for a list of badges that can be displayed by each product instead of a single badge
                    if (isset($product->customfieldsSorted['airis-product-markers']) && (is_array($product->customfieldsSorted['airis-product-markers']) || $product->customfieldsSorted['airis-product-markers'] instanceof Countable)) {
                        // $productMarkers = [];
                        // $productMarkersStrings = [];

                        // All product markers or grouped up by a special template position into an associative array for easy lookup with isset()
                        /* foreach ($product->customfieldsSorted['airis-product-markers'] as $productMarker) {
                            $productMarkers[$productMarker->custom_desc] = $productMarker;
                        } */

                        // Then, any marker that requires custom logics can be checked upon like this
                        // if (isset($productMarkers['airis-product-marker-example'])) {}

                        // Or all of them can be processed in the same way
                        /* foreach ($product->customfieldsSorted['airis-product-markers'] as $productMarker) {
                            $productMarkersStrings[] = [
                                'class' => '',
                                'href' => '',
                                'text' => '',
                            ];
                        } */

                        // A new product marker, only used if this module is not set to display latest products
                        if ($moduleProductTypeGroup !== 'latest')
                        {
                            $productMarkerClasses = 'airis-module-virtuemart-product__product_new module-virtuemart-product__product_new virtuemart-product_new';
                            $productBadgeClasses = 'airis-module-virtuemart-product__product-badge_new module-virtuemart_product__product-badge_new virtuemart-product__product-badge_new';
                            if (empty($productBadgeText)) $productBadgeText = Text::_('TPL_AIRIS_COM_VIRTUEMART_PRODUCT_BADGE_NEW');
                        }
                    }

                    // Use canonical link for this product if available
                    $productHref = empty($product->link) ? $product->canonical : $product->link;

                    // TODO: Check if it is actually needed here and not only really useable in category products sublayout
                    // Joomla! Itemid GET parameter used for proper module placement when SEF URIs are not in use
                    if (!empty($lastVisitedItemId)) $productHref .= '&Itemid=' . shopFunctionsF::getLastVisitedItemId();
                ?>

                <?php if ($moduleDisplayStyleUseDivs) : ?>
                    <div class="<?php echo ($productMarkerClasses) ? "$productMarkerClasses $productItemClasses" : $productItemClasses; ?>">
                <?php else : ?>
                    <li class="<?php echo ($productMarkerClasses) ? "$productMarkerClasses $productItemClasses" : $productItemClasses; ?>">
                <?php endif; ?>

                    <?php if ($productBadgeText) : ?>
                        <div class="<?php if ($productBadgeClasses) echo $productBadgeClasses, ' '; ?>airis-module-virtuemart-product__product-badge module-virtuemart-product__product-badge virtuemart-product__product-badge">
                            <?php echo $productBadgeText; ?>
                        </div>
                    <?php endif; ?>

                    <div class="airis-module-virtuemart-product__product-image module-virtuemart-product__product-image virtuemart-product__product-image text-center">
                        <?php
                            $productImageAttributes = [
                                'class' => 'airis-virtuemart-module-product__product-image-file module-virtuemart-product__product-image-file virtuemart-product__product-image-file',
                                'alt' => $productName,
                            ];

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

                            echo HTMLHelper::link(
                                $productHref,
                                $product->images[0]->displayMediaThumb($productImageAttributes, false),
                                [
                                    'class' => 'airis-virtuemart-module-product__product-link_image module-virtuemart-product__product-link_image virtuemart-product__product-link_image airis-module-virtuemart-product__product-link module-virtuemart-product__product-link virtuemart-product__product-link',
                                    'title' => $productName,
                                ],
                            );
                        ?>
                    </div>

                    <div class="airis-module-virtuemart-product__product-title module-virtuemart-product__product-title virtuemart-product__product-title">
                        <?php
                            echo HTMLHelper::link(
                                $productHref,
                                $productName,
                                ['class' => 'airis-module-virtuemart-product__product-link_title module-virtuemart-product__product-link_title virtuemart-product__product-link'],
                            );
                        ?>
                    </div>

                    <?php if ($showProductPrice && !empty($product->prices['salesPrice'])) : ?>
                        <div class="airis-module-virtuemart-product__product-prices module-virtuemart-product__product-prices virtuemart-product__product-prices">
                            <?php // TODO: Replace the call to a custom prices sublayout once (and if ever) it is ready ?>
                            <?php // echo shopFunctionsF::renderVmSubLayout($sublayoutPrefix . 'prices', ['product' => $product, 'currency' => $currency]); ?>
                            <?php
                                echo shopFunctionsF::renderVmSubLayout(
                                    'prices',
                                    [
                                        'product' => $product,
                                        'currency' => $currency,
                                    ],
                                );
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($showAddToCart) : ?>
                        <div class="airis-module-virtuemart-product__product-controls module-virtuemart-product__product-controls virtuemart-product__product-controls">
                            <?php // TODO: Enable airis-template-options propagation for proper disclaimer displays inside of the sublayout ?>
                            <?php echo shopFunctionsF::renderVmSubLayout($sublayoutPrefix . 'addtocart', ['product' => $product]); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!$airisModuleClassSuffixParams['disable-productdetails-link']) : ?>
                        <div class="airis-module-virtuemart-product__product-links module-virtuemart-product__product-links virtuemart-product__product-links">
                            <?php echo HTMLHelper::link($productHref, vmText::_('COM_VIRTUEMART_PRODUCT_DETAILS'), ['class' => 'airis-module-virtuemart-product__product-link_details module-virtuemart-product__product-link_details virtuemart-product__link_details airis-module-virtuemart-product__product-link module-virtuemart-product__product-link virtuemart-product__product-link btn']); ?>
                        </div>
                    <?php endif; ?>

                <?php if ($moduleDisplayStyleUseDivs) : ?>
                    </div>
                <?php else : ?>
                    </li>
                <?php endif; ?>

            <?php endforeach; ?>

        <?php if ($moduleDisplayStyleUseDivs) : ?>
            </div>
        <?php else : ?>
            </ul>
        <?php endif; ?>

    <?php else : ?>

        <?php // TODO: Not reachable at the moment due to module code returning early in case of an empty product list. ?>
        <div class="airis-module-empty airis-module-virtuemart-product-empty module-virtuemart-empty" data-nosnippet>
            <p class="airis-module-empty__message airis-module-virtuemart-product-empty__message module-virtuemart-empty__message"><?php echo Text::_('TPL_AIRIS_MOD_VIRTUEMART_PRODUCT_NO_PRODUCTS'); ?></p>
        </div>

    <?php endif; ?>

    <?php if ($moduleFooterText) : ?>
        <div class="airis-module-virtuemart-product__module-footer module-virtuemart-product__module-footer">
            <?php echo $moduleFooterText; ?>
        </div>
    <?php endif; ?>

</div>

<?php
/*
Disable the stock VM lightbox scripts and their CSS enqueued in \administrator\components\com_virtuemart\helpers\vmjsapi.php
TODO: Find a better way to reliabliy disable assets with version numbers in filenames because the current way is very fragile
towards VM updates that is until VM switches over to Web Assets
TODO: This approach is incompatible with module cache for this VM module because upon subsequent calls to this layout only the
resulting HTML sturcture is output and not these API calls. Find a better way to disable these scripts regardless of module cache option
status. Probably should transform these into an template param and move them to index.php.
*/
vmJsApi::removeJScript('facebox');
vmJsApi::removeJScript('fancybox/jquery.fancybox-1.3.4.2.pack');
vmJsApi::removeJScript('popups');
vmJsApi::removeJScript('imagepopup');

unset(Factory::getApplication()->getDocument()->_styleSheets['/components/com_virtuemart/assets/css/facebox.css?vmver=' . VM_JS_VER]);
unset(Factory::getApplication()->getDocument()->_styleSheets['/components/com_virtuemart/assets/css/jquery.fancybox-1.3.4.css?vmver=' . VM_JS_VER]);