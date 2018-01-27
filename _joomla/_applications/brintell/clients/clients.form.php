<?php
defined('_JEXEC') or die;

// BANKS
$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_groups') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$groups = $db->loadObjectList();

// FORM
?>
<div class="form-group field-required">
	<label><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
	<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
</div>
<div class="form-group">
	<label><?php echo JText::_('FIELD_LABEL_GROUP'); ?></label>
	<div class="input-group">
		<select name="group_id" id="<?php echo $APPTAG?>-group_id" class="form-control field-id upper auto-tab" data-target="<?php echo $APPTAG?>-agency">
			<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
			<?php
				foreach ($groups as $obj) {
					echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
				}
			?>
		</select>
		<span class="input-group-btn">
			<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-<?php echo $APPNAME?>Groups" data-backdrop="static" data-keyboard="false"></button>
			<button type="button" class="base-icon-cog btn btn-warning hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="<?php echo $APPNAME?>Groups_listReload(false)" data-toggle="modal" data-target="#modal-list-<?php echo $APPNAME?>Groups" data-backdrop="static" data-keyboard="false"></button>
		</span>
	</div>
</div>
