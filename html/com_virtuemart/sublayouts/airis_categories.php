<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// See if we have any categories to display at all
if (empty($viewData['categories'])) return;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Image\Image;

$ajaxForCategoryContentAttributeName = 'data-dynamic-update';
$ajaxForCategoryContentAttributeValue = VmConfig::get('ajax_category', 0);

// Acquire VirtueMart items per row settings for categories
$categoriesPerRow = !empty($viewData['categories_per_row']) ? $viewData['categories_per_row'] : VmConfig::get('categories_per_row', 3);

// TODO: Update this code with mod_virtuemart_product implementation
// Replace integers with number words
$categoriesPerRowAllowedDigits = range(1, 6);
$categoriesPerRowAllowedNumerals = array('one', 'two', 'three', 'four', 'five', 'six');

// Ignore unacceptable integers
if (!in_array($categoriesPerRow, $categoriesPerRowAllowedDigits)) $categoriesPerRow = 3;

$categoriesPerRow = str_replace($categoriesPerRowAllowedDigits, $categoriesPerRowAllowedNumerals, $categoriesPerRow);

?>

<ul class="virtuemart-categories category-view-categories unstyled airis-flex-item-rows">

	<?php foreach ($viewData['categories'] as $category) : ?>

		<?php
			$categoryTitle = htmlspecialchars(trim(vmText::_($category->category_name)), ENT_QUOTES, 'UTF-8');
			$categoryHref = 'index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id;
		?>

		<li class="virtuemart-category category-view-category airis-flex-item-per-row-<?php echo $categoriesPerRow; ?> category-view-category-per-row-<?php echo $categoriesPerRow; ?>">

			<div class="virtuemart-category-image category-view-category-image">
				<?php
					$categoryImageAttributes = array(
						'class' => 'virtuemart-category-image-file category-view-category-image-file',
						'alt' => $categoryTitle
					);

					// TODO: While getFileUrlThumb() provides correct path to a possible "NO IMAGE" image for this item, the Image class instance above cannot be successfully contructed for such images for some reason, so we can only fallback to configured thumbnail dimensions in such cases because getUrl() returns no valid image path to extract full image dimensions from there since proper aspect ratio is what browsers care about anyway
					if (!empty($category->images[0]->virtuemart_media_id))
					{
						// Construct an Image instance from product thumbnail URI first since VM's VmMediaHandler class currently doesn't provide any properties or methods to get thumbnail width and height
						$categoryImage = new Image($category->images[0]->getFileUrlThumb());

						$categoryImageAttributes['width'] = $categoryImage->getWidth();
						$categoryImageAttributes['height'] = $categoryImage->getHeight();
					}
					else
					{
						$categoryImageAttributes['width'] = VmConfig::get('img_width', 0); 
						$categoryImageAttributes['height'] = VmConfig::get('img_height', 0);
					}

					echo HTMLHelper::link($categoryHref, $category->images[0]->displayMediaThumb($categoryImageAttributes, false), array('title' => $categoryTitle, $ajaxForCategoryContentAttributeName => $ajaxForCategoryContentAttributeValue));
				?>
			</div>

			<div class="virtuemart-category-title category-view-category-title">
				<?php echo HTMLHelper::link($categoryHref, $categoryTitle, array($ajaxForCategoryContentAttributeName => $ajaxForCategoryContentAttributeValue)); ?>
			</div>

		</li>

	<?php endforeach; ?>

</ul>