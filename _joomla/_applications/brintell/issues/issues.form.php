<?php
defined('_JEXEC') or die;

// PROJECTS
$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_projects') .' WHERE '.$cProj.'state = 1 ORDER BY name';
$db->setQuery($query);
$projects = $db->loadObjectList();

// TAGS
$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_tasks_tags') .' WHERE '. $db->quoteName('state') .' = 1 ORDER BY name';
$db->setQuery($query);
$tags = $db->loadObjectList();

// FORM
?>
<div class="row">
	<div class="col-lg-8">
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group field-required">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_PROJECT'); ?></label>
					<select name="project_id" id="<?php echo $APPTAG?>-project_id" class="form-control field-id" onchange="<?php echo $APPTAG?>_getAuthor(this.value)">
						<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
						<?php
							foreach ($projects as $obj) {
								echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
							}
						?>
					</select>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group field-required">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_TYPE'); ?></label>
					<span class="btn-group btn-group-justified" data-toggle="buttons">
						<?php
						for($i = 0; $i < 4; $i++) {
							$icon	= JText::_('TEXT_ICON_TYPE_'.$i);
							$color	= ($i == 2) ? 'warning' : JText::_('TEXT_COLOR_TYPE_'.$i);
							echo '
								<label class="base-icon-'.$icon.' btn btn-outline-'.$color.' btn-active-'.$color.' hasTooltip" title="'.JText::_('TEXT_TYPE_'.$i.'_DESC').'">
									<input type="radio" name="type" id="'.$APPTAG.'-type-'.$i.'" value="'.$i.'" />
								</label>
							';
						}
						?>
					</span>
				</div>
			</div>
		</div>
		<div class="form-group field-required">
			<label class="label-sm"><?php echo JText::_('FIELD_LABEL_SUBJECT'); ?></label>
			<input type="text" name="subject" id="<?php echo $APPTAG?>-subject" class="form-control" />
		</div>
		<div class="form-group field-required">
			<label class="label-sm"><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
			<textarea rows="9" name="description" id="<?php echo $APPTAG?>-description" class="form-control"></textarea>
		</div>
		<div class="form-group">
			<hr class="hr-tag" />
			<span class="badge badge-primary base-icon-attach"> <?php echo JText::_('TEXT_ATTACHMENTS'); ?></span>
			<button type="button" class="base-icon-plus btn btn-success float-right hasTooltip" title="<?php echo JText::_('TEXT_ADD'); ?>" onclick="<?php echo $APPTAG?>_setNewFile('#<?php echo $APPTAG?>-files-group', 'file', '')"></button>
			<div class="btn-file">
				<span class="btn-group">
					<button type="button" class="base-icon-search btn btn-default btn-active-success file-action text-truncate hasTooltip" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"> <span><?php echo JText::_('TEXT_FILE_SELECT'); ?></span></button>
				</span>
				<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="form-control" hidden />
			</div>
		</div>
		<div id="<?php echo $APPTAG?>-files-group"></div>
	</div>
	<div class="col-lg-4 b-left b-left-dashed">
		<?php if($hasAppAdmin) :?>
			<div class="form-group">
				<label class="label-sm iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_AUTHOR_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_AUTHOR'); ?></label>
				<select name="author" id="<?php echo $APPTAG?>-author" class="form-control">
					<option value="0"><?php echo JText::_('TEXT_SELECT')?></option>
				</select>
			</div>
		<?php else :?>
			<input type="hidden" name="author" id="<?php echo $APPTAG?>-author" value="0" />
		<?php endif;?>
		<div class="form-group">
			<label class="label-sm"><?php echo JText::_('FIELD_LABEL_PRIORITY'); ?></label>
			<span class="btn-group btn-group-justified" data-toggle="buttons">
				<label class="btn btn-default btn-active-success">
					<input type="radio" name="priority" id="<?php echo $APPTAG?>-priority-0" value="0" />
					<?php echo JText::_('TEXT_PRIORITY_0'); ?>
				</label>
				<label class="btn btn-default btn-active-warning">
					<input type="radio" name="priority" id="<?php echo $APPTAG?>-priority-1" value="1" />
					<?php echo JText::_('TEXT_PRIORITY_1'); ?>
				</label>
				<label class="btn btn-default btn-active-danger">
					<input type="radio" name="priority" id="<?php echo $APPTAG?>-priority-2" value="2" />
					<?php echo JText::_('TEXT_PRIORITY_2'); ?>
				</label>
			</span>
		</div>
		<div id="<?php echo $APPTAG?>-deadline-group" class="form-group">
			<label class="label-sm iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_DEADLINE_DESC'); ?>">
				<?php echo JText::_('FIELD_LABEL_DEADLINE'); ?>
				[ <?php echo JText::_('TEXT_TIME_IN_BRAZIL'); ?>: <iframe src="//free.timeanddate.com/clock/i63smlsf/n45/fs13/fcf80/tct/pct/ahl/ftb/ts1" frameborder="0" width="58" height="16" style="margin-bottom:-3px;" allowTransparency="true"></iframe> ]
			</label>
			<div class="form-inline">
				<input type="text" name="deadline" id="<?php echo $APPTAG?>-deadline" class="field-date mr-1" data-width="142px" data-time="true" data-seconds="false" data-tab-disable="true" data-convert="true" />
				<select name="timePeriod" id="<?php echo $APPTAG?>-timePeriod">
					<option value="<?php echo JText::_('TEXT_AM'); ?>"><?php echo JText::_('TEXT_AM'); ?></option>
					<option value="<?php echo JText::_('TEXT_PM'); ?>"><?php echo JText::_('TEXT_PM'); ?></option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="label-sm"><?php echo JText::_('FIELD_LABEL_TAGS'); ?></label>
			<div class="input-group">
				<select name="tags[]" id="<?php echo $APPTAG?>-tags" class="form-control" multiple>
					<?php
						foreach ($tags as $obj) {
							echo '<option value="'.$obj->name.'">'.baseHelper::nameFormat($obj->name).'</option>';
						}
					?>
				</select>
				<?php if($hasAdmin) :?>
					<span class="input-group-btn">
						<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-<?php echo $APPTAG?>Tags" data-backdrop="static" data-keyboard="false"></button>
					</span>
				<?php endif;?>
			</div>
		</div>
		<div class="form-group">
			<div id="<?php echo $APPTAG?>-alert-toDo">
				<hr class="hr-tag" />
				<span class="badge badge-primary base-icon-menu"> <?php echo JText::_('TEXT_TODO_LIST'); ?></span>
				<div class="alert alert-info text-sm p-2"><?php echo JText::_('MSG_TODO_LIST_AFTER_SAVE'); ?></div>
			</div>
			<div id="<?php echo $APPTAG?>-btn-toDo" hidden>
				<hr />
				<button type="button" class="btn btn-primary btn-block text base-icon-list btn-icon" onclick="<?php echo $APPTAG?>_viewToDo()" data-toggle="modal" data-target="#modal-list-<?php echo $APPTAG?>Todo" data-backdrop="static" data-keyboard="false"> <?php echo JText::_('TEXT_TODO_LIST')?></button>
			</div>
		</div>
	</div>
</div>
