<?php
defined('_JEXEC') or die;

// GROUPS
$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_roles') .' WHERE '. $db->quoteName('state') .' = 1 ORDER BY name';
$db->setQuery($query);
$roles = $db->loadObjectList();

// TAGS
$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_tags') .' WHERE '. $db->quoteName('state') .' = 1';
$db->setQuery($query);
$tags = $db->loadObjectList();

// USUÁRIOS SEM CONTATOS ASSOCIADOS
// IMPORTANTE:
// 1 - Não lista usuários do grupo 'Desenvolvedor' => '8'
// 2 - Não lista usuários que já estão associados a um 'contato'
$query = '
	SELECT DISTINCT(T1.id), T1.name, T1.username
	FROM '. $db->quoteName('#__users') .' T1
		JOIN '. $db->quoteName('#__user_usergroup_map') .' T2
		ON T2.user_id = T1.id
		LEFT OUTER JOIN '. $db->quoteName($cfg['mainTable']) .' T3
		ON T3.user_id = T1.id
	WHERE T2.group_id <> 8 AND T3.name IS NULL
	ORDER BY T1.name
';
$db->setQuery($query);
$users = $db->loadObjectList();

// FORM
?>
<ul class="nav nav-tabs" id="<?php echo $APPTAG?>Tab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="<?php echo $APPTAG?>Tab-general" data-toggle="tab" href="#<?php echo $APPTAG?>TabGeneral" role="tab" aria-controls="general" aria-expanded="true"><?php echo JText::_('TEXT_GENERAL'); ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="<?php echo $APPTAG?>Tab-contact" data-toggle="tab" href="#<?php echo $APPTAG?>TabContact" role="tab" aria-controls="contact"><?php echo JText::_('TEXT_CONTACT_DATA'); ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="<?php echo $APPTAG?>Tab-location" data-toggle="tab" href="#<?php echo $APPTAG?>TabLocation" role="tab" aria-controls="location"><?php echo JText::_('TEXT_LOCATION_DATA'); ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="<?php echo $APPTAG?>Tab-extra" data-toggle="tab" href="#<?php echo $APPTAG?>TabExtra" role="tab" aria-controls="extra"><?php echo JText::_('FIELD_LABEL_EXTRA_INFO'); ?></a>
	</li>
</ul>
<div class="tab-content" id="<?php echo $APPTAG?>TabContent">
	<div class="tab-pane fade show active" id="<?php echo $APPTAG?>TabGeneral" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-general">
		<div class="row">
			<div class="col-sm-9">
				<div class="row">
					<div class="col-lg-8">
						<div class="form-group field-required">
							<label class="label-sm"><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
							<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label class="label-sm iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_NICKNAME_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_NICKNAME'); ?></label>
							<input type="text" name="nickname" id="<?php echo $APPTAG?>-nickname" class="form-control upper" />
						</div>
					</div>
					<div class="col-lg-8">
						<div class="form-group field-required">
							<label class="label-sm">E-mail</label>
							<input type="email" name="email" id="<?php echo $APPTAG?>-email" class="form-control field-email" />
							<input type="hidden" name="cmail" id="<?php echo $APPTAG?>-cmail" />
						</div>
					</div>
					<div class="col-sm-6 col-lg-4">
						<div class="form-group field-required">
							<label class="label-sm"><?php echo JText::_('FIELD_LABEL_GENDER'); ?></label>
							<span class="btn-group btn-group-justified" data-toggle="buttons">
								<label class="btn btn-default btn-active-success">
									<input type="radio" name="gender" id="<?php echo $APPTAG?>-male" value="1" />
									<?php echo JText::_('TEXT_GENDER_1_ABBR'); ?>
								</label>
								<label class="btn btn-default btn-active-success">
									<input type="radio" name="gender" id="<?php echo $APPTAG?>-female" value="2" />
									<?php echo JText::_('TEXT_GENDER_2_ABBR'); ?>
								</label>
							</span>
						</div>
					</div>
				</div>
				<hr class="mt-0 b-primary-lighter" />
				<div class="row">
					<div class="col-sm-6 col-lg-6">
						<div class="form-group field-required">
							<label class="label-sm"><?php echo JText::_('FIELD_LABEL_TYPE'); ?></label>
							<span class="btn-group btn-group-justified" data-toggle="buttons">
								<label class="btn btn-default btn-active-success">
									<input type="radio" name="type" id="<?php echo $APPTAG?>-type-0" value="0" onchange="<?php echo $APPTAG?>_setType(0)" />
									<?php echo JText::_('TEXT_TYPE_0'); ?>
								</label>
								<label class="btn btn-default btn-active-success">
									<input type="radio" name="type" id="<?php echo $APPTAG?>-type-1" value="1" onchange="<?php echo $APPTAG?>_setType(1)" />
									<?php echo JText::_('TEXT_TYPE_1'); ?>
								</label>
							</span>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group">
							<label class="label-sm"><?php echo JText::_('FIELD_LABEL_ROLE'); ?></label>
							<div class="input-group">
								<select name="role_id" id="<?php echo $APPTAG?>-role_id" class="form-control field-id">
									<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
									<?php
										foreach ($roles as $obj) {
											echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
										}
									?>
								</select>
								<span class="input-group-btn">
									<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-<?php echo $APPTAG?>Roles" data-backdrop="static" data-keyboard="false"></button>
									<button type="button" class="base-icon-cog btn btn-primary hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="<?php echo $APPTAG?>Roles_listReload(false)" data-toggle="modal" data-target="#modal-list-<?php echo $APPTAG?>Roles" data-backdrop="static" data-keyboard="false"></button>
								</span>
							</div>
						</div>
					</div>
					<div class="col-12">
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
								<span class="input-group-btn">
									<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-<?php echo $APPTAG?>Tags" data-backdrop="static" data-keyboard="false"></button>
									<button type="button" class="base-icon-cog btn btn-primary hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="<?php echo $APPTAG?>Tags_listReload(false)" data-toggle="modal" data-target="#modal-list-<?php echo $APPTAG?>Tags" data-backdrop="static" data-keyboard="false"></button>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_PHOTO'); ?></label>
					<div class="image-file">
						<a href="#" class="image-action">
							<div class="image-file-label">
								<span class="image-file-off base-icon-file-image"><small>200 x 200</small></span>
								<span class="image-file-on text-sm base-icon-ok" hidden></span>
								<span class="image-file-edit base-icon-pencil" hidden></span>
							</div>
						</a>
						<span class="btn-group mt-2"></span>
						<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="field-image" hidden />
					</div>
				</div>
			</div>
		</div>
		<hr class="hr-tag" />
		<span class="badge badge-primary"><?php echo JText::_('TEXT_ACCESS_STATUS'); ?></span>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<span class="btn-group btn-group-justified" data-toggle="buttons">
						<label class="btn btn-default btn-active-danger">
							<input type="radio" name="access" id="<?php echo $APPTAG?>-access-0" value="0" onchange="<?php echo $APPTAG?>_accessForm(0)" class="auto-tab" data-target="#<?php echo $APPTAG?>-reasonStatus" />
							<span class="<?php echo $APPTAG?>-no-user"><?php echo JText::_('TEXT_NO_ACCESS'); ?></span>
							<span class="<?php echo $APPTAG?>-is-user" hidden><?php echo JText::_('TEXT_BLOCKED'); ?></span>
						</label>
						<label class="btn btn-default btn-active-success">
							<input type="radio" name="access" id="<?php echo $APPTAG?>-access-1" value="1" onchange="<?php echo $APPTAG?>_accessForm(1)" />
							<span class="<?php echo $APPTAG?>-no-user"><?php echo JText::_('TEXT_ALLOW_ACCESS'); ?></span>
							<span class="<?php echo $APPTAG?>-is-user" hidden><?php echo JText::_('TEXT_ACTIVE'); ?></span>
						</label>
					</span>
					<input type="hidden" name="user_id" id="<?php echo $APPTAG?>-user_id" />
				</div>
			</div>
			<div class="col-md-6">
				<div id="<?php echo $APPTAG?>-reasonStatus-group" class="collapse <?php echo $APPTAG?>-is-user">
					<input type="text" name="reasonStatus" id="<?php echo $APPTAG?>-reasonStatus" class="form-control mb-3" maxlength="50" placeholder="<?php echo JText::_('FIELD_LABEL_REASON'); ?>" />
				</div>
			</div>
			<div class="col-12">
				<div id="accessFields" class="collapse">
					<hr class="mt-0 new-user-data" hidden />
					<div class="row">
						<?php
							// IMPORTANTE:
							// O campo abaixo fica 'hidden' para evitar erros na hora de cadastrar
							// Teoricamente, todos os usuários serão cadastrados pela App
							// => PARA HABILITAR NOVAMENTE O CAMPO, APENAS ADICIONE A CLASS 'new-user-data' NA DIV ABAIXO
						?>
						<div class="col-lg-6" hidden>
							<div class="form-group">
								<label class="label-sm iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_USER_ACCESS_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_USER_ACCESS'); ?></label>
								<div class="input-group">
									<select name="newUser" id="<?php echo $APPTAG?>-newUser" class="form-control auto-tab" data-target=".<?php echo $APPTAG?>-user-create-data">
										<option value="0" data-target-display="true"><?php echo JText::_('TEXT_NEW').' '.JText::_('TEXT_USER'); ?></option>
										<?php
											foreach ($users as $obj) {
												echo '<option value="'.$obj->id.'" data-target-display="false">'.baseHelper::nameFormat($obj->name).'</option>';
											}
										?>
									</select>
									<span class="input-group-addon base-icon-right-big <?php echo $APPTAG?>-user-create-data"></span>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-lg-3">
							<div class="form-group <?php echo $APPTAG?>-user-create-data field-required">
								<label class="label-sm iconTip hasPopover" title="<?php echo JText::_('TEXT_USER_ACCESS_LEVEL')?>" data-content="<?php echo JText::_('MSG_USER_ACCESS_LEVEL')?>"><?php echo JText::_('FIELD_LABEL_USERGROUP'); ?></label>
								<select name="usergroup" id="<?php echo $APPTAG?>-usergroup" class="form-control field-id">
									<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
								</select>
								<input type="hidden" name="cusergroup" id="<?php echo $APPTAG?>-cusergroup" />
							</div>
						</div>
						<div class="col-sm-6 col-lg-3 new-user-data" hidden>
							<div class="form-group <?php echo $APPTAG?>-user-create-data field-required">
								<label class="label-sm"><?php echo JText::_('FIELD_LABEL_USERNAME'); ?></label>
								<div class="input-group">
									<input type="text" name="username" id="<?php echo $APPTAG?>-username" class="form-control field-username lower" />
									<span class="input-group-addon hasPopover" data-content="<?php echo JText::_('MSG_USERNAME_INFO'); ?>" data-placement="top">
										<span class="base-icon-info-circled"></span>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6 col-lg-3">
							<div class="form-group">
								<label class="label-sm d-block">
									<?php echo JText::_('FIELD_LABEL_PASSWORD'); ?>
									<small class="text-live font-condensed float-right pt-1"><?php echo JText::_('TEXT_OPTIONAL'); ?></small>
								</label>
								<div class="input-group">
									<input type="password" name="password" id="<?php echo $APPTAG?>-password" class="form-control" />
									<span class="input-group-addon hasPopover" data-content="<?php echo JText::_('MSG_PASSWORD_INFO'); ?>" data-placement="top">
										<span class="base-icon-info-circled"></span>
									</span>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-lg-3">
							<div class="form-group">
								<label class="label-sm iconTip hasTooltip" title="<?php echo JText::_('MSG_REPASSWORD'); ?>"><?php echo JText::_('FIELD_LABEL_REPASSWORD'); ?></label>
								<input type="password" name="repassword" id="<?php echo $APPTAG?>-repassword" class="form-control" />
							</div>
						</div>
						<div class="col-lg-6 new-user-data" hidden>
							<div class="form-group">
								<label class="label-sm"><?php echo JText::_('FIELD_LABEL_REGISTRATION_EMAIL_CONFIRM'); ?></label>
								<div class="input-group">
									<span class="input-group-addon">
										<input type="checkbox" name="emailConfirm" id="<?php echo $APPTAG?>-emailConfirm" value="1" class="auto-tab" data-target="<?php echo $APPTAG?>-emailInfo" data-target-disabled="false" />
									</span>
									<input type="text" name="emailInfo" id="<?php echo $APPTAG?>-emailInfo" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_REGISTRATION_EMAIL_INFO'); ?>" />
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="<?php echo $APPTAG?>TabContact" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-contact">
		<div class="row">
			<div class="col-lg-9">
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_PHONE'); ?></label>
					<div class="row">
						<div class="col-sm-6 col-lg-4">
							<input type="text" name="phone[]" id="<?php echo $APPTAG?>-phone" class="form-control field-phone" data-toggle-mask="true" />
						</div>
						<div class="col-sm-6 col-lg-8">
							<div class="input-group">
								<span class="input-group-btn btn-group" data-toggle="buttons">
									<label class="btn btn-outline-success btn-active-success hasTooltip" title="<?php echo JText::_('TEXT_HAS_WHATSAPP'); ?>">
										<input type="checkbox" name="wapp[]" id="<?php echo $APPTAG?>-wapp" value="1" class="auto-tab" data-target="#<?php echo $APPTAG?>-whatsapp" data-target-value="1" data-target-value-reset="" data-tab-disabled="true" />
										<span class="base-icon-whatsapp icon-default"></span>
										<input type="hidden" name="whatsapp[]" id="<?php echo $APPTAG?>-whatsapp" />
									</label>
								</span>
								<input type="text" name="phone_desc[]" id="<?php echo $APPTAG?>-phone_desc" class="form-control upper" placeholder="<?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?>" maxlength="50" />
							</div>
						</div>
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-phoneGroups" class="newFieldsGroup"></div>
				<div class="text-sm-right">
					<button type="button" class="btn btn-sm btn-success base-icon-plus" onclick="<?php echo $APPTAG?>_phoneAdd()"> <?php echo JText::_('TEXT_PHONES_ADD')?></button>
				</div>
				<hr class="hr-tag" />
				<span class="badge badge-primary"><?php echo JText::_('TEXT_INTERNET')?></span>
				<div class="form-group">
					<label class="label-sm iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_CHAT_DESC')?>"><?php echo JText::_('FIELD_LABEL_CHAT')?></label>
					<div class="row">
						<div class="col-sm-4">
							<input type="text" name="chat_name[]" id="<?php echo $APPTAG?>-chat_name" class="form-control upper" placeholder="<?php echo JText::_('FIELD_LABEL_CHAT_NAME'); ?>" />
						</div>
						<div class="col-sm-8">
							<div class="input-group">
								<input type="text" name="chat_user[]" id="<?php echo $APPTAG?>-chat_user" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_CHAT_USER'); ?>" />
								<span class="input-group-btn">
									<button type="button" class="btn btn-success base-icon-plus hasTooltip" title="<?php echo JText::_('TEXT_ADD_CHAT')?>" onclick="<?php echo $APPTAG?>_chatAdd()"></button>
								</span>
							</div>
						</div>
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-chatGroups" class="newFieldsGroup"></div>
				<div class="form-group">
					<label class="label-sm iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_WEBLINK_DESC')?>">Weblink</label>
					<div class="row">
						<div class="col-sm-4">
							<input type="text" name="weblink_text[]" id="<?php echo $APPTAG?>-weblink_text" class="form-control upper" placeholder="<?php echo JText::_('FIELD_LABEL_WEBLINK_TEXT'); ?>" />
						</div>
						<div class="col-sm-8">
							<div class="input-group">
								<input type="text" name="weblink_url[]" id="<?php echo $APPTAG?>-weblink_url" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_WEBLINK_URL'); ?>" />
								<span class="input-group-btn">
									<button type="button" class="btn btn-success base-icon-plus hasTooltip" title="<?php echo JText::_('TEXT_ADD_WEBLINK')?>" onclick="<?php echo $APPTAG?>_linkAdd()"></button>
								</span>
							</div>
						</div>
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-linkGroups" class="newFieldsGroup"></div>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="<?php echo $APPTAG?>TabLocation" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-location">
		<div class="row">
			<div class="col-sm-8 col-lg-7">
				<div class="form-group field-required">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_ADDRESS_STREET'); ?></label>
					<input type="text" name="address" id="<?php echo $APPTAG?>-address" class="form-control upper" />
				</div>
			</div>
			<div class="col-sm-4 col-lg-3">
				<div class="form-group field-required">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_ADDRESS_NUMBER'); ?></label>
					<input type="text" name="address_number" id="<?php echo $APPTAG?>-address_number" class="form-control upper" />
				</div>
			</div>
			<div class="col-sm-8 col-lg-7">
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_ADDRESS_INFO'); ?></label>
					<input type="text" name="address_info" id="<?php echo $APPTAG?>-address_info" class="form-control" />
				</div>
			</div>
			<div class="col-sm-4 col-lg-3">
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_ADDRESS_ZIP_CODE'); ?></label>
					<input type="text" name="zip_code" id="<?php echo $APPTAG?>-zip_code" class="form-control field-cep" />
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6 col-lg">
				<div class="form-group field-required">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_ADDRESS_STATE'); ?></label>
					<input type="text" name="address_state" id="<?php echo $APPTAG?>-address_state" class="form-control upper" />
				</div>
			</div>
			<div class="col-sm-6 col-lg">
				<div class="form-group field-required">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_ADDRESS_CITY'); ?></label>
					<input type="text" name="address_city" id="<?php echo $APPTAG?>-address_city" class="form-control upper" />
				</div>
			</div>
			<div class="col-sm-6 col-lg">
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_ADDRESS_DISTRICT'); ?></label>
					<input type="text" name="address_district" id="<?php echo $APPTAG?>-address_district" class="form-control upper" />
				</div>
			</div>
			<div class="col-sm-6 col-lg field-required">
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_ADDRESS_COUNTRY'); ?></label>
					<input type="text" name="address_country" id="<?php echo $APPTAG?>-address_country" class="form-control upper" />
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="<?php echo $APPTAG?>TabExtra" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-extra">

		<div class="row">
			<div class="col-sm-8">
				<div class="row">
					<div class="col-sm-6 col-lg-4">
						<div class="form-group">
							<label class="label-sm"><?php echo JText::_('FIELD_LABEL_BIRTHDAY'); ?></label>
							<input type="text" name="birthday" id="<?php echo $APPTAG?>-birthday" class="form-control field-date birthday" data-mindate="-100y" data-maxdate="0d" data-year-range="-100:c" data-convert="true" />
						</div>
					</div>
					<div class="col-sm-6 col-lg-4">
						<div class="form-group">
							<label class="label-sm"><?php echo JText::_('FIELD_LABEL_MARITAL_STATUS'); ?></label>
							<select name="marital_status" id="<?php echo $APPTAG?>-marital_status" class="form-control auto-tab" data-target="<?php echo $APPTAG?>-group-children" data-tab-disabled="true">
								<option value="0" data-target-display="false"><?php echo JText::_('TEXT_SELECT'); ?></option>
								<option value="1" data-target-display="false"><?php echo JText::_('TEXT_MARITAL_STATUS_1'); ?></option>
								<option value="2" data-target-display="true"><?php echo JText::_('TEXT_MARITAL_STATUS_2'); ?></option>
								<option value="3" data-target-display="true"><?php echo JText::_('TEXT_MARITAL_STATUS_3'); ?></option>
								<option value="4" data-target-display="false"><?php echo JText::_('TEXT_MARITAL_STATUS_4'); ?></option>
								<option value="5" data-target-display="false"><?php echo JText::_('TEXT_MARITAL_STATUS_5'); ?></option>
							</select>
						</div>
					</div>
					<div id="<?php echo $APPTAG?>-group-children" class="col-sm-6 col-lg-4" hidden>
						<div class="form-group">
							<label class="label-sm"><?php echo JText::_('FIELD_LABEL_CHILDREN'); ?></label>
							<select type="text" name="children" id="<?php echo $APPTAG?>-children" class="form-control">
								<option value="0"><?php echo JText::_('TEXT_SELECT'); ?></option>
								<?php
									for($i = 1; $i < 50; $i++) {
										echo '<option value="'.$i.'">'.$i.'</option>';
									}
								?>
							</select>
						</div>
					</div>
					<div class="col-sm-6 col-lg-4">
						<div class="form-group">
							<label class="label-sm"><?php echo JText::_('FIELD_LABEL_OCCUPATION'); ?></label>
							<input type="text" name="occupation" id="<?php echo $APPTAG?>-occupation" class="form-control upper" />
						</div>
					</div>
					<div class="col-6 col-md-4">
						<div class="form-group">
							<label class="label-sm iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_PRICE_HOUR_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_PRICE_HOUR'); ?></label>
							<input type="text" name="price_hour" id="<?php echo $APPTAG?>-price_hour" class="form-control field-price" data-convert="true" />
						</div>
					</div>
					<div class="col-sm-6 col-lg-4">
						<div class="form-group">
							<label class="label-sm"><?php echo JText::_('FIELD_LABEL_RESUME'); ?></label>
							<div class="btn-file">
								<span class="btn-group w-100">
									<button type="button" class="base-icon-search btn btn-default btn-active-success file-action text-truncate hasTooltip" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"> <span><?php echo JText::_('TEXT_FILE_SELECT'); ?></span></button>
								</span>
								<input type="file" name="file[1]" id="<?php echo $APPTAG?>-file1" class="form-control" hidden />
							</div>
						</div>
					</div>
				</div>
				<hr class="hr-tag" />
				<span class="badge badge-primary"><?php echo JText::_('TEXT_CONTRACTUAL_DATA'); ?></span>
				<div class="row">
					<div class="col-sm-6 col-lg-4">
						<div class="form-group">
							<label class="label-sm">CPF</label>
							<input type="text" name="cpf" id="<?php echo $APPTAG?>-cpf" class="form-control field-cpf" />
						</div>
					</div>
					<div class="col-sm-6 col-lg-4">
						<div class="form-group">
							<label class="label-sm">CNPJ</label>
							<input type="text" name="cnpj" id="<?php echo $APPTAG?>-cnpj" class="form-control field-cnpj" />
						</div>
					</div>
					<div class="col-sm-6 col-lg-4">
						<div class="form-group">
							<label class="label-sm"><?php echo JText::_('FIELD_LABEL_CONTRACT'); ?></label>
							<div class="btn-file">
								<span class="btn-group w-100">
									<button type="button" class="base-icon-search btn btn-default btn-active-success file-action text-truncate hasTooltip" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"> <span><?php echo JText::_('TEXT_FILE_SELECT'); ?></span></button>
								</span>
								<input type="file" name="file[2]" id="<?php echo $APPTAG?>-file2" class="form-control" hidden />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_ABOUT'); ?></label>
					<textarea name="about_me" id="<?php echo $APPTAG?>-about_me" rows="12" class="form-control"></textarea>
				</div>
			</div>
		</div>
		<hr class="hr-tag" />
		<span class="badge badge-primary"><?php echo JText::_('TEXT_BANK_ACCOUNT_DATA'); ?></span>
		<div class="row">
			<div class="col-sm-6 col-lg-4">
				<div class="form-group">
					<label class="label-sm field-required"><?php echo JText::_('FIELD_LABEL_BANK'); ?></label>
					<input type="text" name="bank_name" id="<?php echo $APPTAG?>-bank_name" class="form-control upper" />
				</div>
			</div>
			<div class="col-sm-6 col-lg-2">
				<div class="form-group">
					<label class="label-sm field-required"><?php echo JText::_('FIELD_LABEL_AGENCY'); ?></label>
					<input type="text" name="agency" id="<?php echo $APPTAG?>-agency" class="form-control" />
				</div>
			</div>
			<div class="col-sm-6 col-lg-2">
				<div class="form-group">
					<label class="label-sm field-required"><?php echo JText::_('FIELD_LABEL_OPERATION'); ?></label>
					<input type="text" name="operation" id="<?php echo $APPTAG?>-operation" class="form-control" />
				</div>
			</div>
			<div class="col-sm-6 col-lg-4">
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_ACCOUNT'); ?></label>
					<input type="text" name="account" id="<?php echo $APPTAG?>-account" class="form-control" />
				</div>
			</div>
		</div>
	</div>
</div>
