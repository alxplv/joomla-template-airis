<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;

// Enable category image support
$categoryModel->addImages($categories);

// Base category link href string for Joomla! Router
$categoryHrefBase = 'index.php?option=com_virtuemart&view=category&virtuemart_category_id=';

?>

<div class="virtuemart-module-category-with-images<?php echo $class_sfx; ?> airis-flex airis-flex-wrap">
	<?php foreach ($categories as $category) : ?>

		<?php
			$categoryTitle = htmlspecialchars(trim(vmText::_($category->category_name)));
			$categoryHref = $categoryHrefBase . $category->virtuemart_category_id;
			$categoryImage = $category->images[0]->displayMediaThumb(array('class' => 'virtuemart-module-category-with-images-item-image-file', 'alt' => $categoryTitle), false, '', true, false, false, 400, 400);
		?>

		<div class="virtuemart-module-category-with-images-item airis-flex-item-per-row-three">

			<div class="virtuemart-module-category-with-images-item-image">
				<?php echo HTMLHelper::link($categoryHref, $categoryImage, array('class' => 'virtuemart-module-category-with-images-item-image-link', 'title' => $categoryTitle)); ?>
			</div>

			<div class="virtuemart-module-category-with-images-item-title">
				<?php echo HTMLHelper::link($categoryHref, $categoryTitle, array('class' => 'virtuemart-module-category-with-images-link virtuemart-module-category-with-images-item-title-link')); ?>
			</div>

			<?php if ($level >= 1 && !empty($category->childs)) : ?>

				<div class="virtuemart-module-category-with-images-item-child-items">

					<?php foreach ($category->childs as $childCategory) : ?>
						<div class="virtuemart-module-category-with-images-item-child-item">

							<div class="virtuemart-module-category-with-images-item-child-item-title">
								<?php echo HTMLHelper::link($categoryHrefBase . $childCategory->virtuemart_category_id, htmlspecialchars(trim(vmText::_($childCategory->category_name))), array('class' => 'virtuemart-module-category-with-images-link virtuemart-module-category-with-images-item-child-item-title-link')); ?>
							</div>

							<?php if ($level >= 2 && !empty($childCategory->childs)) : ?>
								<div class="virtuemart-module-category-with-images-item-child-item-child-items">

									<?php foreach ($childCategory->childs as $childChildCategory) : ?>
										<div class="virtuemart-module-category-with-images-item-child-item-child-item">
											<div class="virtuemart-module-category-with-images-item-child-item-child-item-title">
												<?php echo HTMLHelper::link($categoryHrefBase . $childChildCategory->virtuemart_category_id, htmlspecialchars(trim(vmText::_($childChildCategory->category_name))), array('class' => 'virtuemart-module-category-with-images-link virtuemart-module-category-with-images-item-child-item-child-item-title-link')); ?>
											</div>
										</div>
									<?php endforeach; ?>

								</div>
							<?php endif; ?>

						</div>
					<?php endforeach; ?>

				</div>

			<?php endif; ?>

		</div>

	<?php endforeach; ?>
</div>