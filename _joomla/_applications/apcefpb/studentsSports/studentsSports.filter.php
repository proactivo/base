<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', 2, 'int');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// STUDENT -> select
	$fStud	= $app->input->get('fStud', 0, 'int');
	if($fStud != 0) $where .= ' AND '. $db->quoteName('T1.student_id').' = '.$fStud;
	// SPORT -> select
	$fSport	= $app->input->get('fSport', 0, 'int');
	if($fSport != 0) $where .= ' AND '. $db->quoteName('T1.sport_id').' = '.$fSport;
	// COUPON FREE -> select
	$fCoupon	= $app->input->get('fCoupon', 2, 'int');
	$where .= ($fCoupon == 2) ? '' : ' AND '. $db->quoteName('T1.coupon_free').' = '.$fCoupon;
	// GENDER -> select
	$fGender	= $app->input->get('fGender', 0, 'int');
	$where .= ($fGender == 0) ? '' : ' AND '. $db->quoteName('T2.gender').' = '.$fGender;
	// HEALTH -> select
	$fHealth	= $app->input->get('fHealth', 2, 'int');
	$where .= ($fHealth == 2) ? '' : $db->quoteName('T2.has_disease').' = '.$fHealth;
	// ALLERGY -> select
	$fAllergy	= $app->input->get('fAllergy', 2, 'int');
	$where .= ($fAllergy == 2) ? '' : $db->quoteName('T2.has_allergy').' = '.$fAllergy;
	// DATE
	$dateMin	= $app->input->get('dateMin', '', 'string');
	$dateMax	= $app->input->get('dateMax', '', 'string');
	$dtmin = !empty($dateMin) ? $dateMin : '0000-00-00';
	$dtmax = !empty($dateMax) ? $dateMax : '9999-12-31';
	if(!empty($dateMin) || !empty($dateMax)) $where .= ' AND '.$db->quoteName('T1.registry_date').' BETWEEN '.$db->quote($dtmin).' AND '.$db->quote($dtmax);

	// Search 'Text fields'
	$search	= $app->input->get('fSearch', '', 'string');
	$sQuery = ''; // query de busca
	$sLabel = array(); // label do campo de busca
	$searchFields = array(
		'T2.name'				=> 'FIELD_LABEL_NAME',
		'T2.mother_name'		=> 'FIELD_LABEL_MOTHER_NAME',
		'T2.father_name'		=> 'FIELD_LABEL_FATHER_NAME',
		'T2.email'				=> 'Email',
		'T2.disease_desc'		=> 'FIELD_LABEL_DISEASE',
		'T2.allergy_desc'		=> 'FIELD_LABEL_ALLERGY',
		'T3.name'				=> 'FIELD_LABEL_GROUP' // sport
	);
	$i = 0;
	foreach($searchFields as $key => $value) {
		$_OR = ($i > 0) ? ' OR ' : '';
		$sQuery .= $_OR.'LOWER('.$db->quoteName($key).') LIKE LOWER("%'.$search.'%")';
		if(!empty($value)) $sLabel[] .= JText::_($value);
		$i++;
	}
	if(!empty($search)) $where .= ' AND ('.$sQuery.')';

// ORDER BY

	$ordf	= $app->input->get($APPTAG.'oF', '', 'string'); // campo a ser ordenado
	$ordt	= $app->input->get($APPTAG.'oT', '', 'string'); // tipo de ordem: 0 = 'ASC' default, 1 = 'DESC'

	$orderDef = 'T3.name ASC'; // não utilizar vírgula no inicio ou fim
	unset($_SESSION[$APPTAG.'oF']);
	if(!isset($_SESSION[$APPTAG.'oF'])) : // DEFAULT ORDER
		$_SESSION[$APPTAG.'oF'] = 'T2.name';
		$_SESSION[$APPTAG.'oT'] = 'ASC';
	endif;
	if(!empty($ordf)) :
		$_SESSION[$APPTAG.'oF'] = $ordf;
		$_SESSION[$APPTAG.'oT'] = $ordt;
	endif;
	$orderList = !empty($_SESSION[$APPTAG.'oF']) ? $db->quoteName($_SESSION[$APPTAG.'oF']).' '.$_SESSION[$APPTAG.'oT'] : '';
	// fixa a ordenação em caso de itens com o mesmo valor (ex: mesma data)
	$orderList .= (!empty($orderList) && !empty($orderDef) ? ', ' : '').$orderDef;
	$orderList .= (!empty($orderList) ? ', ' : '').$db->quoteName('T1.id').' DESC';
	// set order by
	$orderList = !empty($orderList) ? ' ORDER BY '.$orderList : '';

	$SETOrder = $APPTAG.'setOrder';

// ACTION

	$btnAction = $cfg['listFull'] ? 'type="submit"' : 'type="button" onclick="'.$APPTAG.'_listReload(false);"';

// FILTER'S DINAMIC FIELDS

	// student -> select
	$flt_stud = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_students') .' ORDER BY name';
	$db->setQuery($query);
	$students = $db->loadObjectList();
	foreach ($students as $obj) {
		$flt_stud .= '<option value="'.$obj->id.'"'.($obj->id == $fStud ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}

	// sport -> select
	$flt_sport = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_sports') .' ORDER BY name';
	$db->setQuery($query);
	$sports = $db->loadObjectList();
	foreach ($sports as $obj) {
		$flt_sport .= '<option value="'.$obj->id.'"'.($obj->id == $fSport ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}

// VISIBILITY
// Elementos visíveis apenas quando uma consulta é realizada

	$hasFilter = $app->input->get($APPTAG.'_filter', 0, 'int');
	// Estado inicial dos elementos
	$btnClearFilter		= ''; // botão de resetar
	$textResults		= ''; // Texto informativo
	// Filtro ativo
	if($hasFilter || $cfg['ajaxFilter']) :
		$btnClearFilter = '
			<a href="'.JURI::current().'" class="btn btn-sm btn-danger base-icon-cancel-circled btn-icon">
				'.JText::_('TEXT_CLEAR').' '.JText::_('TEXT_FILTER').'
			</a>
		';
		$textResults = '<span class="base-icon-down-big text-muted d-none d-sm-inline"> '.JText::_('TEXT_SEARCH_RESULTS').'</span>';
	endif;

// VIEW
$htmlFilter = '
	<form id="filter-'.$APPTAG.'" class="hidden-print collapse '.((isset($_GET[$APPTAG.'_filter']) || $cfg['openFilter']) ? 'show' : '').'" method="get">
		<fieldset class="fieldset-embed fieldset-sm pt-3 pb-0">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
				<div class="col-sm-4 col-md-2">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_STUDENT').'</label>
						<select name="fStud" id="fStud" class="form-control form-control-sm set-filter">
							<option value="0">- '.JText::_('TEXT_ALL').' -</option>
							'.$flt_stud.'
						</select>
					</div>
				</div>
				<div class="col-sm-4 col-md-2">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_SPORT').'</label>
						<select name="fSport" id="fSport" class="form-control form-control-sm set-filter">
							<option value="0">- '.JText::_('TEXT_ALL').' -</option>
							'.$flt_sport.'
						</select>
					</div>
				</div>
				<div class="col-sm-4 col-md-2">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_TYPE').'</label>
						<select name="fCoupon" id="fCoupon" class="form-control form-control-sm set-filter">
							<option value="2">- '.JText::_('TEXT_ALL').' -</option>
							<option value="1"'.($fCoupon == 1 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_COUPON_FREE').'</option>
							<option value="0"'.($fCoupon == 0 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_PAYING').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-4 col-md-2">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_DISEASE').'</label>
						<select name="fHealth" id="fHealth" class="form-control form-control-sm set-filter">
							<option value="2">- '.JText::_('TEXT_ALL').' -</option>
							<option value="1"'.($fHealth == 1 ? ' selected' : '').'>'.JText::_('TEXT_YES').'</option>
							<option value="0"'.($fHealth == 0 ? ' selected' : '').'>'.JText::_('TEXT_NO').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-4 col-md-2">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_ALLERGY').'</label>
						<select name="fAllergy" id="fAllergy" class="form-control form-control-sm set-filter">
							<option value="2">- '.JText::_('TEXT_ALL').' -</option>
							<option value="1"'.($fAllergy == 1 ? ' selected' : '').'>'.JText::_('TEXT_YES').'</option>
							<option value="0"'.($fAllergy == 0 ? ' selected' : '').'>'.JText::_('TEXT_NO').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-4 col-md-2">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_GENDER').'</label>
						<select name="fGender" id="fGender" class="form-control form-control-sm set-filter">
							<option value="0">- '.JText::_('TEXT_ALL').' -</option>
							<option value="1"'.($fGender == 1 ? ' selected' : '').'>'.JText::_('TEXT_MALE').'</option>
							<option value="2"'.($fGender == 2 ? ' selected' : '').'>'.JText::_('TEXT_FEMALE').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-4 col-md-2">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_ITEM_STATE').'</label>
						<select name="active" id="active" class="form-control form-control-sm set-filter">
							<option value="2">- '.JText::_('TEXT_ALL').' -</option>
							<option value="1"'.($active == 1 ? ' selected' : '').'>'.JText::_('TEXT_ACTIVES').'</option>
							<option value="0"'.($active == 0 ? ' selected' : '').'>'.JText::_('TEXT_INACTIVES').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-8 col-md-6 col-lg-4">
					<div class="form-group">
						<label class="label-xs text-muted text-truncate">'.implode(', ', $sLabel).'</label>
						<input type="text" name="fSearch" value="'.$search.'" class="form-control form-control-sm" />
					</div>
				</div>
			</div>
			<div id="base-app-filter-buttons" class="row pt-3 b-top align-items-center">
				<div class="col-sm">
					<div class="form-group">
						'.$textResults.'
					</div>
				</div>
				<div class="col-sm text-right">
					<div class="form-group">
						<button '.$btnAction.' id="'.$APPTAG.'-submit-filter" class="btn btn-sm btn-primary base-icon-search btn-icon">
							'.JText::_('TEXT_SEARCH').'
						</button>
						'.$btnClearFilter.'
					</div>
				</div>
			</div>
		</fieldset>
	</form>
';

?>
