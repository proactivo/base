<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', 2, 'int');
	$db->quoteName('T1.state');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// TYPE -> select
	$fType	= $app->input->get('fType', 0, 'int');
	if($fType != 0) $where .= ' AND '.$db->quoteName('T1.type_id').' = '.$fType;

	// DATE
	$dateMin	= $app->input->get('dateMin', '', 'string');
	$dateMax	= $app->input->get('dateMax', '', 'string');
	$dtmin = !empty($dateMin) ? $dateMin : '0000-00-00';
	$dtmax = !empty($dateMax) ? $dateMax : '9999-12-31';
	if(!empty($dateMin) || !empty($dateMax)) $where .= ' AND '.$db->quoteName('T1.created_date').' BETWEEN '.$db->quote($dtmin).' AND '.$db->quote($dtmax);
	// PRICE
	$priceMin	= $app->input->get('priceMin', '', 'string');
	$priceMax	= $app->input->get('priceMax', '', 'string');
	$prmin = !empty($priceMin) ? $priceMin : '0.00';
	$prmax = (!empty($priceMax) && $priceMax != '0.00') ? $priceMax : '9999999999.99';
	if(!empty($priceMin) || !empty($priceMax)) $where .= ' AND '.$db->quoteName('T1.price').' BETWEEN '.$prmin.' AND '.$prmax;

	// Search 'Text fields'
	$search	= $app->input->get('fSearch', '', 'string');
	$sQuery = ''; // query de busca
	$sLabel = array(); // label do campo de busca
	$searchFields = array(
		'T1.name'				=> 'FIELD_LABEL_NAME',
		'T1.email'				=> 'E-mail',
		'T1.cpf'				=> 'CPF',
		'T1.rg'					=> 'RG',
		'T1.place_birth'		=> 'FIELD_LABEL_PLACE_BIRTH',
		'T1.description'		=> 'FIELD_LABEL_DESCRIPTION'
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

	$orderDef = ''; // não utilizar vírgula no inicio ou fim
	if(!isset($_SESSION[$APPTAG.'oF'])) : // DEFAULT ORDER
		$_SESSION[$APPTAG.'oF'] = 'T1.name';
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

// FILTER'S DINAMIC FIELDS

	// types -> select
	$flt_type = '';
	$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_types') .' ORDER BY name';
	$db->setQuery($query);
	$types = $db->loadObjectList();
	foreach ($types as $obj) {
		$flt_type .= '<option value="'.$obj->id.'"'.($obj->id == $fType ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}

// VISIBILITY
// Elementos visíveis apenas quando uma consulta é realizada

	$hasFilter = $app->input->get($APPTAG.'_filter', 0, 'int');
	// Estado inicial dos elementos
	$btnClearFilter		= ''; // botão de resetar
	$textResults		= ''; // Texto informativo
	// Filtro ativo
	if($hasFilter) :
		$btnClearFilter = '
			<a href="'.JURI::current().'" class="btn btn-sm btn-danger base-icon-cancel-circled btn-icon">
				'.JText::_('TEXT_CLEAR').' '.JText::_('TEXT_FILTER').'
			</a>
		';
		$textResults = '<span class="base-icon-down-big text-muted d-none d-sm-inline"> '.JText::_('TEXT_SEARCH_RESULTS').'</span>';
	endif;

// VIEW
$htmlFilter = '
	<form id="filter-'.$APPTAG.'" class="hidden-print collapse '.((isset($_GET[$APPTAG.'_filter']) || $cfg['showFilter']) ? 'show' : '').'" method="get">
		<fieldset class="fieldset-embed fieldset-sm pt-3 pb-0">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
				<div class="col-sm-6 col-md-4">
					<div class="form-group">
						<label class="label-sm">'.JText::_('FIELD_LABEL_TYPE').'</label>
						<select name="fType" id="fType" class="form-control form-control-sm set-filter">
							<option value="0">- '.JText::_('TEXT_SELECT').' -</option>
							'.$flt_type.'
						</select>
					</div>
				</div>
				<div class="col-sm-6 col-md-2">
					<div class="form-group">
						<label class="label-sm">'.JText::_('TEXT_STATE').'</label>
						<select name="active" id="active" class="form-control form-control-sm set-filter">
							<option value="2">- '.JText::_('TEXT_ALL').' -</option>
							<option value="1"'.($active == 1 ? ' selected' : '').'>'.JText::_('TEXT_ACTIVES').'</option>
							<option value="0"'.($active == 0 ? ' selected' : '').'>'.JText::_('TEXT_INACTIVES').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="label-sm text-truncate">'.implode(', ', $sLabel).'</label>
						<input type="text" name="fSearch" value="'.$search.'" class="form-control form-control-sm" />
					</div>
				</div>
			</div>
			<div id="base-app-filter-buttons" class="row py-3 b-top align-items-center">
				<div class="col-sm">
					'.$textResults.'
				</div>
				<div class="col-sm text-right">
					<button type="submit" class="btn btn-sm btn-primary base-icon-search btn-icon">
						'.JText::_('TEXT_SEARCH').'
					</button>
					'.$btnClearFilter.'
				</div>
			</div>
		</fieldset>
	</form>
';

?>