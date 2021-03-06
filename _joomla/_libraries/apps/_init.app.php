<?php

// ACCESS
// Grupos de acesso declarados
$hasViewer = $hasAuthor = $hasEditor = $hasAdmin = false;
if($cfg['isPublic']) {
	if($cfg['isPublic'] == 1) {
		$hasViewer = true;			// visualizador
	} else if(isset($user) && !$user->guest) {
		if($cfg['isPublic'] == 2) $hasAuthor = true;	// autor
		else if($cfg['isPublic'] == 3) $hasEditor = true;	// editor
		else if($cfg['isPublic'] == 4) $hasAdmin = true;	// admin
	}
}
if(isset($cfg['groupId'])) {
	//$viewer = array_merge($cfg['groupId']['viewer'], $cfg['groupId']['admin']);
	if($cfg['isPublic'] != 1 && count($cfg['groupId']['viewer'])) $hasViewer = array_intersect($groups, $cfg['groupId']['viewer']); // se está na lista de grupos permitidos
	if($cfg['isPublic'] != 2 && count($cfg['groupId']['author'])) $hasAuthor = array_intersect($groups, $cfg['groupId']['author']); // se está na lista de autores permitidos
	if($cfg['isPublic'] != 3 && count($cfg['groupId']['editor'])) $hasEditor = array_intersect($groups, $cfg['groupId']['editor']); // se está na lista de editores permitidos
	if($cfg['isPublic'] != 4 && count($cfg['groupId']['admin'])) $hasAdmin = array_intersect($groups, $cfg['groupId']['admin']); // se está na lista de administradores permitidos
}
if(!$cfg['isPublic']) :
	if(isset($user) && $user->guest) :
		$app->redirect(JURI::root(true).'/login?return='.urlencode(base64_encode(JURI::current())));
		exit();
	elseif(!$hasViewer && !$hasAuthor && !$hasEditor && !$hasAdmin) :
		$APPTAG;
		$app->enqueueMessage(JText::_('MSG_NOT_PERMISSION'), 'warning');
		$app->redirect(JURI::root(true));
		exit();
	endif;
endif;

// define permissões de execução
$cfg['canAdd']		= ($hasAuthor || $hasEditor || $hasAdmin);
$cfg['canEdit']		= ($hasEditor || $hasAdmin);
$cfg['canDelete']	= $hasAdmin;

// LOAD SCRIPTS
$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::base(true).'/templates/base/css/style.app.css');
$doc->addScript(JURI::base(true).'/templates/base/js/forms.js');
$doc->addScript(JURI::base(true).'/templates/base/js/validate.js');

// Carrega Jquery.ui datepicker;
if($cfg['dateConvert'] || $cfg['load_UI']) {
	$doc->addStyleSheet(JURI::base(true).'/templates/base/libs/template/jquery/jquery-ui.min.css');
	$doc->addScript(JURI::base(true).'/templates/base/libs/template/jquery/jquery-ui.min.js');
}
if($cfg['htmlEditor']) {
	$doc->addStyleSheet(JURI::base(true).'/templates/base/libs/forms/editor/ui/trumbowyg.min.css');
	$doc->addScript(JURI::base(true).'/templates/base/libs/forms/editor/trumbowyg.min.js');
	$doc->addScript(JURI::base(true).'/templates/base/libs/forms/editor/langs/pt.min.js');
	if($cfg['htmlEditorFull']) {
		// Base64
		$doc->addScript(JURI::base(true).'/templates/base/libs/forms/editor/plugins/base64/trumbowyg.base64.min.js');
		// Colors
		$doc->addScript(JURI::base(true).'/templates/base/libs/forms/editor/plugins/colors/trumbowyg.colors.min.js');
		$doc->addStyleSheet(JURI::base(true).'/templates/base/libs/forms/editor/plugins/colors/ui/trumbowyg.colors.min.css');
		// Noembed
		$doc->addScript(JURI::base(true).'/templates/base/libs/forms/editor/plugins/noembed/trumbowyg.noembed.min.js');
	}
}

?>
