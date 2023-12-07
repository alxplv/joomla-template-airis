<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;

// Base category link href string for Joomla! Router
$categoryHrefBase = 'index.php?option=com_virtuemart&view=category&virtuemart_category_id=';

// Marker used to skip unnecessary checks
$isActiveCategoryNotFound = true;

?>

<div class="airis-module-menu virtuemart-module-category<?php echo $class_sfx; ?>">

	<?php foreach ($categories as $category) : ?>

		<?php
			// Apply additional classes for all elements related to active category link
			$categoryMenuItemClasses = 'airis-module-menu-item virtuemart-module-category-item';

			if ($isActiveCategoryNotFound)
			{
				if ($category->virtuemart_category_id == $active_category_id)
				{
					$isActiveCategoryNotFound = false;
					$isActiveCategory = true;
					$categoryMenuItemClasses .= ' airis-module-menu-item-active virtuemart-module-category-item-active';
				}
			}
		?>

		<div class="<?php echo $categoryMenuItemClasses; ?>">

			<?php
				$categoryLinkClasses = 'airis-module-menu-item-link virtuemart-module-category-item-link';
				if ($isActiveCategory) $categoryLinkClasses .= ' airis-module-menu-item-link-active virtuemart-module-category-item-link-active';
				$categoryLink = HTMLHelper::link($categoryHrefBase . $category->virtuemart_category_id, htmlspecialchars(trim(vmText::_($category->category_name))), array('class' => $categoryLinkClasses));
			?>

			<?php if ($level >= 1 && !empty($category->childs)) : ?>

				<?php
					$categoryLinkContainerClasses = 'airis-module-menu-item-link-container virtuemart-module-category-item-link-container';
					if ($isActiveCategory) $categoryLinkContainerClasses .= ' airis-module-menu-item-link-container-active virtuemart-module-category-item-link-container-active';
				?>

				<div class="<?php echo $categoryLinkContainerClasses; ?>">
					<?php echo $categoryLink; ?>
				</div>

				<div class="airis-module-menu-item-items virtuemart-module-category-item-items">

					<?php foreach ($category->childs as $childCategory) : ?>

						<?php
							$childCategoryMenuItemClasses = 'airis-module-menu-item-items-item virtuemart-module-category-item-items-item';

							if ($isActiveCategoryNotFound)
							{
								if (in_array($childCategory->virtuemart_category_id, $parentCategories))
								{
									$isActiveChildCategory = true;
									$childCategoryMenuItemClasses .= ' airis-module-menu-item-items-item-active virtuemart-module-category-item-items-item-active';
								}
							}
						?>

						<div class="<?php echo $childCategoryMenuItemClasses; ?>">

							<?php
								$childCategoryLinkClasses = 'airis-module-menu-item-link airis-module-menu-item-items-item-link virtuemart-module-category-item-link virtuemart-module-category-item-items-item-link';
								if ($isActiveChildCategory) $childCategoryLinkClasses .= ' airis-module-menu-item-link-active airis-module-menu-item-items-item-link-active virtuemart-module-category-item-link-active virtuemart-module-category-item-items-item-link-active';
								$childCategoryLink = HTMLHelper::link($categoryHrefBase . $childCategory->virtuemart_category_id, htmlspecialchars(trim(vmText::_($childCategory->category_name))), array('class' => $childCategoryLinkClasses));
							?>

							<?php if ($level >= 2 && !empty($childCategory->childs)) : ?>

								<?php
									$childCategoryLinkContainerClasses = 'airis-module-menu-item-items-item-link-container virtuemart-module-category-item-items-item';
									if ($isActiveChildCategory) $childCategoryLinkContainerClasses .= 'airis-module-menu-item-items-item-link-container-active virtuemart-module-category-item-items-item-active';
								?>

								<div class="<?php echo $childCategoryLinkContainerClasses; ?>">
									<?php echo $childCategoryLink; ?>
								</div>

								<div class="airis-module-menu-item-items-item-items virtuemart-module-category-item-items">

									<?php foreach ($childCategory->childs as $childChildCategory) : ?>

										<?php
											$isActiveChildChildCategory = in_array($childChildCategory->virtuemart_category_id, $parentCategories);
											
											$childChildCategoryMenuItemClasses = 'airis-module-menu-item-items-item-items-item virtuemart-module-category-item-items-item';
											$childChildCategoryLinkClasses = 'airis-module-menu-item-link airis-module-menu-item-items-item-items-item-link virtuemart-module-category-item-items-item-link';
											
											if ($isActiveChildChildCategory)
											{
												$childChildCategoryMenuItemClasses .= ' airis-module-menu-item-items-item-items-item-active virtuemart-module-category-item-items-item-active';
												$childChildCategoryLinkClasses .= ' airis-module-menu-item-link-active airis-module-menu-item-items-item-items-item-link-active virtuemart-module-category-item-items-item-link-active';
											}
										?>

										<div class="<?php echo $childChildCategoryMenuItemClasses; ?>">
											<?php echo HTMLHelper::link($categoryHrefBase . $childChildCategory->virtuemart_category_id, htmlspecialchars(trim(vmText::_($childChildCategory->category_name))), array('class' => $childChildCategoryLinkClasses)); ?>
										</div>
									<?php endforeach; ?>

								</div>

							<?php else : ?>
								<?php echo $childCategoryLink; ?>
							<?php endif; ?>

						</div>
					<?php endforeach; ?>

				</div>

			<?php else : ?>
				<?php echo $categoryLink; ?>
			<?php endif; ?>

		</div>
	<?php endforeach; ?>

</div>