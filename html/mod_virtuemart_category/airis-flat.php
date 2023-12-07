<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;

?>

<div class="airis-module-menu<?php echo $class_sfx; ?> virtuemart-module-category<?php echo $class_sfx; ?>">
	<?php foreach ($categories as $category) : ?>
		<div class="airis-module-menu-item virtuemart-module-category-item">
			<?php
				$categoryHref = 'index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id;
				echo HTMLHelper::link($categoryHref, htmlspecialchars(trim(vmText::_($category->category_name))), array('class' => 'airis-module-menu-item-link virtuemart-module-category-item-link'));
			?>
		</div>
	<?php endforeach; ?>
</div>