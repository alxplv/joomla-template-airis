<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;

foreach ($this->product->manufacturers as $manufacturer) : ?>
	<li class="productdetails__manufacturer">
		<?php
			$manufacturerName = htmlspecialchars(trim($manufacturer->mf_name), ENT_QUOTES, 'UTF-8');
			$manufacturerHref = "index.php?option=com_virtuemart&view=manufacturer&virtuemart_manufacturer_id=$product_manufacturer->virtuemart_manufacturer_id";

			echo HTMLHelper::link(
				$manufacturerHref,
				$manufacturerName,
				[
					'class' => 'productdetails__manufacturer-link',
				],
			);
		?>
	</li>
<?php endforeach;