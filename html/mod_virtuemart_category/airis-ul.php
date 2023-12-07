<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;

// Base category link href string for Joomla! Router
$categoryHrefBase = 'index.php?option=com_virtuemart&view=category&virtuemart_category_id=';

?>

<ul class="virtuemart-module-category-list<?php echo $class_sfx; ?>">

	<?php foreach ($categories as $category) : ?>
		<li class="virtuemart-module-category-list-item">

			<?php echo HTMLHelper::link($categoryHrefBase . $category->virtuemart_category_id, htmlspecialchars(trim(vmText::_($category->category_name))), array('class' => 'virtuemart-module-category-list-item-link')); ?>

			<?php if ($level >= 1 && !empty($category->childs)) : ?>
				<ul class="virtuemart-module-category-list-item-items-list">

					<?php foreach ($category->childs as $childCategory) : ?>
						<li class="virtuemart-module-category-list-item-items-list-item">

							<?php echo HTMLHelper::link($categoryHrefBase . $childCategory->virtuemart_category_id, htmlspecialchars(trim(vmText::_($childCategory->category_name))), array('class' => 'virtuemart-module-category-list-item-link virtuemart-module-category-list-item-items-list-item-link')); ?>

							<?php if ($level >= 2 && !empty($childCategory->childs)) : ?>
								<ul class="virtuemart-module-category-list-item-items-list-item-items-list">

									<?php foreach ($childCategory->childs as $childChildCategory) : ?>
										<li class="virtuemart-module-category-list-item-items-list-item-items-list-item">
											<?php echo HTMLHelper::link($categoryHrefBase . $childChildCategory->virtuemart_category_id, htmlspecialchars(trim(vmText::_($childChildCategory->category_name))), array('class' => 'virtuemart-module-category-list-item-link virtuemart-module-category-list-item-items-list-item-items-list-item-link')); ?>
										</li>
									<?php endforeach; ?>

								</ul>
							<?php endif; ?>

						</li>
					<?php endforeach; ?>

				</ul>
			<?php endif; ?>

		</li>
	<?php endforeach; ?>

</ul>