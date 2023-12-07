<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;

// Output only items with these menu item types
$allowedItemTypes = array('alias', 'component', 'url');

// Selection of Joomla!'s menu item options to be used as HTML link tag attributes
$usableLinkAttributes = array(
	'class' => 'anchor_css',
	'title' => 'anchor_title',
	'rel' => 'anchor_rel'
);

?>

<div class="<?php echo 'airis-module-menu', $class_sfx; ?>" id="<?php echo $params->get('tag_id', 'airis-module-menu-' . $module->id); ?>">
	<?php foreach ($list as $menu_item) : ?>
		<?php if (in_array($menu_item->type, $allowedItemTypes)) : ?>

			<?php
				$itemContainerClasses = "item-$menu_item->id airis-module-menu-item";

				$itemAnchorCSS = htmlspecialchars(trim($menu_item->anchor_css));

				// Prefix all defined menu item classes for their use by item container
				if (!empty($itemAnchorCSS))
				{
					$itemAnchorCSSChunks = explode(' ', $itemAnchorCSS);

					foreach ($itemAnchorCSSChunks as $itemAnchorCSSChunk)
					{
						$itemContainerClasses .= ' ' . $itemAnchorCSSChunk;
					}
				}
			?>

			<div class="<?php echo $itemContainerClasses; ?>">
				<?php
					$menuItemLinkAttributes = array('class' => 'airis-module-menu-item-link ');

					// Process menu item options into array of applicable link attributes
					foreach ($usableLinkAttributes as $linkAttributeName => $linkAttributeValueRaw)
					{
						// Joomla! doesn't care about whitespace or special characters but we do
						$linkAttributeValue = htmlspecialchars(trim($menu_item->$linkAttributeValueRaw));

						if (!empty($linkAttributeValue))
						{
							$menuItemLinkAttributes[$linkAttributeName] .= $linkAttributeValue;
						}
					}

					$menuItemLinkAttributes['class'] = trim($menuItemLinkAttributes['class']);

					// Express both positive Target Window option values as target="_blank" attribute
					if ($menu_item->browserNav) $menuItemLinkAttributes['target'] = '_blank';

					echo HTMLHelper::link($menu_item->flink, htmlspecialchars(trim($menu_item->title)), $menuItemLinkAttributes);
				?>
			</div>

		<?php endif; ?>
	<?php endforeach; ?>
</div>