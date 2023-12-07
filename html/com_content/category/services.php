<?php 

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>

<div class="services<?php echo $this->pageclass_sfx; ?>">

	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
		</div>
	<?php endif; ?>

	<?php if ($this->params->get('show_description') && $this->category->description) : ?>
		<div class="services-description">
			<?php echo HTMLHelper::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
		</div>
	<?php endif; ?>

	<div class="services-items">

		<?php if (empty($this->intro_items)) : ?>
			<p><?php echo Text::_('COM_CONTENT_NO_ARTICLES'); ?></p>
		<?php endif; ?>

		<?php if (!empty($this->intro_items)) : ?>
			<?php foreach ($this->intro_items as &$item) : ?>
				<div class="service" itemscope itemtype="https://schema.org/Service">
					<?php
						$this->item = &$item;
						echo $this->loadTemplate('item');
					?>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	
	</div>

</div>