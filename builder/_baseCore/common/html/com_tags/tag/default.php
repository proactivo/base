<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
// Note that there are certain parts of this layout used only when there is exactly one tag.

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
$isSingleTag = (count($this->item) == 1);
?>
<div class="tag-category">

	<?php if ($this->params->get('show_page_heading')) : ?>
		<h4 class="page-header">
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h4>
	<?php endif; ?>
	<?php if($this->params->get('show_tag_title', 1)) : ?>
		<?php 
		if($this->params->get('show_page_heading')) {
			$head = '3';
			$hClass = '';
		} else {
			$head = '4'; 
			$hClass = ' page-header';
		}
		?>
		<h<?php echo $head;?> class="clearfix<?php echo $hClass;?>">
			<span class="base-icon-tag"></span> 
			<?php echo mb_strtoupper(JHtml::_('content.prepare', $this->item[0]->title, '', 'com_tag.tag'), 'UTF-8'); ?>
		</h<?php echo $head?>>
	<?php endif; ?>

	<?php // We only show a tag description if there is a single tag. ?>
	<?php 
	if (
	count($this->item) == 1 && 
	(
		($this->params->get('tag_list_show_tag_image', 1) == 1 && !empty($images->image_fulltext)) || 
		($this->params->get('tag_list_show_tag_description') == 1 && $this->item[0]->description)
	)
	) : ?>
		<div class="category-desc clearfix">
			<?php $images = json_decode($this->item[0]->images); ?>
			<?php if ($this->params->get('tag_list_show_tag_image', 1) == 1 && !empty($images->image_fulltext)) : ?>
				<img src="<?php echo htmlspecialchars($images->image_fulltext);?>">
			<?php endif; ?>
			<?php if ($this->params->get('tag_list_show_tag_description') == 1 && $this->item[0]->description) : ?>
				<?php echo JHtml::_('content.prepare', $this->item[0]->description, '', 'com_tags.tag'); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	
	<?php // If there are multiple tags and a description or image has been supplied use that. ?>
	<?php if ($this->params->get('tag_list_show_tag_description', 1) || $this->params->get('show_description_image', 1)): ?>
			<?php if ($this->params->get('show_description_image', 1) == 1 && $this->params->get('tag_list_image')) :?>
				<img src="<?php echo $this->params->get('tag_list_image');?>">
			<?php endif; ?>
			<?php if ($this->params->get('tag_list_description', '') > '') :?>
				<?php echo JHtml::_('content.prepare', $this->params->get('tag_list_description'), '', 'com_tags.tag'); ?>
			<?php endif; ?>
	
	<?php endif; ?>
	
	<?php echo $this->loadTemplate('items'); ?>
	<?php if (($this->params->def('show_pagination', 1) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
	<div class="text-center">
		<?php  if ($this->params->def('show_pagination_results', 1) && $this->pagination->getPagesCounter()) : ?>
			<p class="counter">
				<span class="label label-default"><?php echo $this->pagination->getPagesCounter(); ?></label>
			</p>
		<?php endif; ?>
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	<?php  endif; ?>
	
</div>