<?php
/* SISTEMA PARA CADASTRO DE TELEFONES
 * AUTOR: IVO JUNIOR
 * EM: 18/02/2016
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');
// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');
JLoader::register('baseAppHelper', JPATH_CORE.DS.'helpers/apps.php');

$app = JFactory::getApplication('site');

// GET CURRENT USER'S DATA
$user = JFactory::getUser();
$groups = $user->groups;

// init general css/js files
require(JPATH_CORE.DS.'apps/_init.app.php');

// DATABASE CONNECT
$db = JFactory::getDbo();

?>

<script>
jQuery(function() {

	<?php // Default 'JS' Vars
	require(JPATH_CORE.DS.'apps/snippets/initVars.js.php');
	?>

	// APP FIELDS
	var user_id				= jQuery('#<?php echo $APPTAG?>-user_id');
	var name 				= jQuery('#<?php echo $APPTAG?>-name');
	var email				= jQuery('#<?php echo $APPTAG?>-email');
	var cmail				= jQuery('#<?php echo $APPTAG?>-cmail');
	var cpf					= jQuery('#<?php echo $APPTAG?>-cpf');
	var rg					= jQuery('#<?php echo $APPTAG?>-rg');
	var rg_orgao			= jQuery('#<?php echo $APPTAG?>-rg_orgao');
	var gender 				= mainForm.find('input[name=gender]:radio'); // radio group
	var birthday			= jQuery('#<?php echo $APPTAG?>-birthday');
	var marital_status		= jQuery('#<?php echo $APPTAG?>-marital_status');
	var partner				= jQuery('#<?php echo $APPTAG?>-partner');
	// Company data
	var cx_code				= jQuery('#<?php echo $APPTAG?>-cx_code'); // matrícula
	var cx_email			= jQuery('#<?php echo $APPTAG?>-cx_email');
	var cx_role				= jQuery('#<?php echo $APPTAG?>-cx_role'); // cargo
	var cx_situated			= jQuery('#<?php echo $APPTAG?>-cx_situated'); // lotação
	var cx_date				= jQuery('#<?php echo $APPTAG?>-cx_date'); // data de admissão
	// Address
	var zip_code 			= jQuery('#<?php echo $APPTAG?>-zip_code');
	var address				= jQuery('#<?php echo $APPTAG?>-address');
	var address_number		= jQuery('#<?php echo $APPTAG?>-address_number');
	var address_info		= jQuery('#<?php echo $APPTAG?>-address_info');
	var address_district	= jQuery('#<?php echo $APPTAG?>-address_district');
	var address_city		= jQuery('#<?php echo $APPTAG?>-address_city');
	// contact
	var phone1				= jQuery('#<?php echo $APPTAG?>-phone1');
	var phone2				= jQuery('#<?php echo $APPTAG?>-phone2');
	// Billing data
	var agency				= jQuery('#<?php echo $APPTAG?>-agency');
	var account				= jQuery('#<?php echo $APPTAG?>-account');
	var operation			= jQuery('#<?php echo $APPTAG?>-operation');
	// Joomla Registration
	var access				= mainForm.find('input[name=access]:radio'); // radio group
	var newUser				= jQuery('#<?php echo $APPTAG?>-newUser');
	var password			= jQuery('#<?php echo $APPTAG?>-password');
	var repassword			= jQuery('#<?php echo $APPTAG?>-repassword');
	var emailConfirm		= jQuery('#<?php echo $APPTAG?>-emailConfirm');
	var emailInfo			= jQuery('#<?php echo $APPTAG?>-emailInfo');
	var reasonStatus		= jQuery('#<?php echo $APPTAG?>-reasonStatus');

	// PARENT FIELD
	// informe, se houver, o campo que representa a chave estrangeira principal
	var parentFieldId		= null; // 'null', caso não exista...
	var parentFieldGroup	= elementExist(parentFieldId) ? parentFieldId.closest('[class*="col"]') : null;

	// GROUP RELATION'S BUTTONS -> grupo de botões de relacionamentos no form
	var groupRelations		= jQuery('#<?php echo $APPTAG?>-group-relation');

	// FORM CONTROLLERS
	// métodos controladores do formulário

		// ON FOCUS
		// campo que recebe o focus no carregamento
		var firstField		= name;

		// ON MODAL OPEN -> Ações quando o modal do form é aberto
		popup.on('shown.bs.modal', function () {
			<?php // Default Actions
			require(JPATH_CORE.DS.'apps/snippets/form/onModalOpen.def.js.php');
			?>
		});

		// ON MODAL CLOSE -> Ações quando o modal do form é fechado
		popup.on('hidden.bs.modal', function () {
			<?php // Default Actions
			require(JPATH_CORE.DS.'apps/snippets/form/onModalClose.def.js.php');
			?>
		});

		<?php // FORM PAGINATOR -> Implementa os botões de paginação do formulário
		require(JPATH_CORE.DS.'apps/snippets/form/formPaginator.js.php');
		?>

		<?php // CHANGE STATE -> Seta o valor do campo 'state' no form
		require(JPATH_CORE.DS.'apps/snippets/form/changeState.js.php');
		?>

		// FORM EXECUTE -> Indicadores de execução do form
		window.<?php echo $APPTAG?>_formExecute = function(loader, disabled, e) {
			<?php // Default Actions
			require(JPATH_CORE.DS.'apps/snippets/form/formExecute.def.js.php');
			?>
	 	};

		// FORM RESET -> Reseta o form e limpa as mensagens de validação
		window.<?php echo $APPTAG?>_formReset = function() {

			<?php // Init Actions
			require(JPATH_CORE.DS.'apps/snippets/form/formReset.init.js.php');
			?>

			// App Fields
			// IMPORTANTE:
			// => SE HOUVER UM CAMPO INDICADO NA VARIÁVEL 'parentFieldId', NÃO RESETÁ-LO NA LISTA ABAIXO
			user_id.val('');
			name.val('');
			email.val('');
			cmail.val('');
			cpf.val('');
			rg.val('');
			rg_orgao.val('');
			checkOption(gender, 0); // radio
			birthday.val('');
			marital_status.val('').selectUpdate(); // select
			partner.val('');
			cx_code.val('');
			cx_email.val('');
			cx_role.val('');
			cx_situated.val('');
			cx_date.val('');
			zip_code.val('');
			address.val('');
			address_number.val('');
			address_info.val('');
			address_district.val('');
			address_city.val('');
			phone1.val('');
			phone2.val('');
			agency.val('');
			account.val('');
			operation.val('');
			checkOption(access, 1);
			reasonStatus.val('');

			<?php // Closure Actions
			require(JPATH_CORE.DS.'apps/snippets/form/formReset.end.js.php');
			?>

		};

		// CUSTOM -> Reset Registration Fields
		window.<?php echo $APPTAG?>_accessForm = function(val) {
			newUser.val('0').selectUpdate(); // select
			password.val('');
			repassword.val('');
			emailInfo.val('');
			checkOption(emailConfirm, (val && user_id.val() != 0 ? 0 : 1));
			jQuery('#accessFields').collapse((val ? 'show' : 'hide'));
			jQuery('#reasonStatus').collapse((!val ? 'show' : 'hide'));
			setHidden('.new-user-data', (val && user_id.val() != 0 ? true : false), '.edit-user-data');
		};

		// CUSTOM -> Sincroniza com os contatos
		window.<?php echo $APPTAG?>_userSync = function() {
			toggleLoader(); // start loader
			jQuery.ajax({
				url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=userSync",
				dataType: 'json',
				type: 'POST',
				cache: false,
				success: function(data) {
					jQuery.map( data, function( res ) {
						if(res.status == 7) showAlertBalloon('#<?php echo $APPTAG?>-syncAlert');
						toggleLoader(); // end loader
						setTimeout(function() {
							<?php $redir = baseHelper::setUrlParam(JURI::current(), 'sync=1'); ?>
							window.location.href = "<?php echo $redir?>";
						}, 1000);
					});
				},
				error: function(xhr, status, error) {
					console.log(xhr);
					console.log(status);
					console.log(error);
					toggleLoader(); // end loader
				}
			});
			return false;
		};

		<?php if($cfg['hasUpload']) : ?>

			<?php // LOAD FILES -> Carrega os campos 'file' gerados dinâmicamente
			require(JPATH_CORE.DS.'apps/snippets/form/filesLoad.js.php');
			?>

			<?php // ADD NEW FILE -> Gera um novo campo para envio de arquivo
			require(JPATH_CORE.DS.'apps/snippets/form/fileAdd.js.php');
			?>

			// RESET FILES -> Reseta os campos 'file'
			window.<?php echo $APPTAG?>_resetFiles = function(inputFiles, single) {
				if(inputFiles.length) {
					<?php // Default Actions
					require(JPATH_CORE.DS.'apps/snippets/form/filesReset.def.js.php');
					?>
				}
			};

		<?php endif; ?>

		// CLEAR VALIDATION ->  Limpa os erros de validação
		window.<?php echo $APPTAG?>_clearValidation = function(formElement){
			<?php // Default Actions
			require(JPATH_CORE.DS.'apps/snippets/form/clearValidation.def.js.php');
			?>
		}

		// SET RELATION -> Atribui valor ao ID de relacionamento
		window.<?php echo $APPTAG?>_setRelation = function(id) {
			<?php // Default Actions
			require(JPATH_CORE.DS.'apps/snippets/form/setRelation.def.js.php');
			?>
		};

		// SET PARENT -> Seta o valor do elemento pai (foreign key) do relacionamento
		window.<?php echo $APPTAG?>_setParent = function(id) {
			<?php // Default Actions
			require(JPATH_CORE.DS.'apps/snippets/form/setRelation.def.js.php');
			?>
		};

	// LIST CONTROLLERS
	// ações & métodos controladores da listagem

		// ON MODAL CLOSE -> Ações quando o modal da listagem é fechado
		listPopup.on('hidden.bs.modal', function () {
			<?php // Default Actions
			require(JPATH_CORE.DS.'apps/snippets/list/onModalClose.def.js.php');
			?>
		});

		<?php // CHECK ALL -> Seleciona todas as linhas (checkboxes) da listagem
		require(JPATH_CORE.DS.'apps/snippets/list/checkAll.js.php');
		?>

		<?php // BTN STATUS -> habilita/desabilita botões se houver, ou não, checkboxes marcados na Listagem
		require(JPATH_CORE.DS.'apps/snippets/list/btnStatus.js.php');
		?>

		<?php // SET FILTER -> Submit o filtro no evento 'onchange'
		require(JPATH_CORE.DS.'apps/snippets/list/setFilter.js.php');
		?>

		<?php // LIST ORDER -> Seta a ação de ordenamento da listagem
		require(JPATH_CORE.DS.'apps/snippets/list/listOrder.js.php');
		?>

		<?php // LIST LIMIT -> Altera o limite de itens visualizados na listagem
		require(JPATH_CORE.DS.'apps/snippets/list/listLimit.js.php');
		?>

		// FILTER SUBMIT ACTIONS -> Seta ações no submit do filtro
		formFilter.on('submit', function() {
		  <?php
		    // FORMAT VALUES -> Formatação de valores para inclusão no banco
		    require(JPATH_CORE.DS.'apps/snippets/form/formatValues.js.php');
		  ?>
		});

	// AJAX CONTROLLERS
	// métodos controladores das ações referente ao banco de dados e envio de arquivos

		<?php // LIST RELOAD -> (Re)carrega a listagem AJAX dos dados
		require(JPATH_CORE.DS.'apps/snippets/ajax/listReload.js.php');
		?>

		// LOAD EDIT
		// Prepara o formulário para a edição dos dados
		window.<?php echo $APPTAG?>_loadEditFields = function(appID, reload, formDisable) {
			var id = (appID ? appID : displayId.val());
			if(isEmpty(id) || id == 0) {
				<?php echo $APPTAG?>_formReset();
				return false;
			}
			<?php echo $APPTAG?>_formExecute(true, formDisable, true); // inicia o loader
			jQuery.ajax({
				url: "<?php echo $URL_APP_FILE ?>.model.php?aTag=<?php echo $APPTAG?>&rTag=<?php echo $RTAG?>&task=get&id="+id,
				dataType: 'json',
				type: 'POST',
				cache: false,
				success: function(data) {
					if(!data.length) {
						<?php echo $APPTAG?>_formReset();
						<?php echo $APPTAG?>_formExecute(true, formDisable, false); // encerra o loader
						return false;
					}
					jQuery.map( data, function( item ) {

						<?php // Init Actions
						require(JPATH_CORE.DS.'apps/snippets/form/loadEdit.init.js.php');
						?>

						// App Fields
						user_id.val(item.user_id);
						name.val(item.name);
						email.val(item.email);
						cmail.val(item.email);
						cpf.val(item.cpf);
						rg.val(item.rg);
						rg_orgao.val(item.rg_orgao);
						checkOption(gender, item.gender); // radio
						birthday.val(dateFormat(item.birthday)); // DATE -> conversão de data
						marital_status.val(item.marital_status).selectUpdate(); // select
						// mostra/esconde o campo 'motivo'
						if(item.marital_status != '') setHidden('#mstatus_desc-group', true);
						else setHidden('#mstatus_desc-group', false);
						partner.val(item.partner);
						cx_code.val(item.cx_code);
						cx_email.val(item.cx_email);
						cx_role.val(item.cx_role);
						cx_situated.val(item.cx_situated);
						cx_date.val(dateFormat(item.cx_date)); // DATE -> conversão de data
						zip_code.val(item.zip_code);
						address.val(item.address);
						address_number.val(item.address_number);
						address_info.val(item.address_info);
						address_district.val(item.address_district);
						address_city.val(item.address_city);
							// phones
							var p = item.phones.split(",");
							phone1.val(p[0]);
							phone2.val(p[1]);
						agency.val(item.agency);
						account.val(item.account);
						operation.val(item.operation);
						checkOption(access, item.access);
						reasonStatus.val(item.reasonStatus);

						<?php // Closure Actions
						require(JPATH_CORE.DS.'apps/snippets/form/loadEdit.end.js.php');
						?>

					});
					// mostra dos botões 'salvar & novo' e 'delete'
					setHidden('#btn-<?php echo $APPTAG?>-delete', false);
					// limpa as mensagens de erro de validação
					<?php echo $APPTAG?>_clearValidation(mainForm);
				},
				error: function(xhr, status, error) {
					<?php // ERROR STATUS -> Executa quando houver um erro na requisição ajax
					require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxError.js.php');
					?>
					<?php echo $APPTAG?>_formExecute(true, formDisable, false); // encerra o loader
				}
			});
		};

		<?php // SAVE -> executa a ação de inserção ou atualização dos dados no banco
		require(JPATH_CORE.DS.'apps/snippets/ajax/save.js.php');
		?>

		<?php // SET STATE -> seta o valor do campo 'state' do registro
		require(JPATH_CORE.DS.'apps/snippets/ajax/setState.js.php');
		?>

		<?php // DELETE -> Exclui o registro
		require(JPATH_CORE.DS.'apps/snippets/ajax/del.js.php');
		?>

		<?php if($cfg['hasUpload']) : ?>

			<?php // DELETE FILES -> exclui o registro e deleta o arquivo
			require(JPATH_CORE.DS.'apps/snippets/ajax/delFile.js.php');
			?>

		<? endif; ?>

}); // CLOSE JQUERY->READY

jQuery(window).load(function() {

	// JQUERY VALIDATION
	window.<?php echo $APPTAG?>_validator = mainForm_<?php echo $APPTAG?>.validate({
		rules: {
			email: {
				remote: {
					url: '<?php echo _CORE_?>helpers/users/checkEmail.php',
					type: 'post',
					data: {
						cmail: function() {
							return jQuery('#<?php echo $APPTAG?>-email').val();
						},
						cmail: function() {
							return jQuery('#<?php echo $APPTAG?>-cmail').val();
						}
					}
				}
			},
			password : {
				minlength : 6
			},
			repassword: {
				equalTo: '#<?php echo $APPTAG?>-password'
			}
		},
		messages: {
			email: {
				remote: '<?php echo JText::_('MSG_EMAIL_EXISTS')?>'
			},
			username : {
				remote: '<?php echo JText::_('MSG_USERNAME_EXISTS')?>'
			},
			repassword: {
				equalTo: '<?php echo JText::_('MSG_PASS_NOT_EQUAL')?>'
			}
		},
		//don't remove this
		invalidHandler: function(event, validator) {
			//if there is error,
			//set custom preferences
		},
		submitHandler: function(form){
			return false;
		}
	});

	<?php
	// JQUERY VALIDATION DEFAULT FOR INPUT FILES
	// Validação básica para campos de envio de arquivo
	require(JPATH_CORE.DS.'apps/snippets/form/validationFile.def.js.php');
	?>

});

</script>

<div class="base-app<?php echo ' base-list-'.($cfg['listFull'] ? 'full' : 'ajax')?> clearfix">
	<?php
	$addText = $cfg['showList'] ? JText::_('TEXT_ADD') : JText::_('TEXT_ADD_UNLIST');
	$tipText = $cfg['addText'] ? '' : $addText;
	$relAdd	= !empty($_SESSION[$RTAG.'RelTable']) ? $APPTAG.'_setRelation('.$APPTAG.'rID);' : $APPTAG.'_setParent('.$APPTAG.'rID);';
	$addBtn = '
		<button class="base-icon-plus btn-add btn btn-sm btn-success hasTooltip" title="'.$tipText.'" onclick="'.$relAdd.'" data-toggle="modal" data-target="#modal-'.$APPTAG.'" data-backdrop="static" data-keyboard="false">
			'.($cfg['addText'] ? ' <span class="text-add">'.$addText.'</span>': '').'
		</button>
	';
	?>
	<?php if($cfg['showApp']) :?>
		<div class="list-toolbar<?php echo ($cfg['staticToolbar'] ? '' : ' floating')?> hidden-print">
			<?php
			if($cfg['showAddBtn'] && $hasAdmin) echo $addBtn;
			if($cfg['showList'] && $cfg['listFull']) :
			?>
				<?php if($hasAdmin) : ?>
					<button class="btn btn-sm btn-success <?php echo $APPTAG?>-btn-action" disabled onclick="<?php echo $APPTAG?>_setState(0, 1)">
						<span class="base-icon-ok-circled"></span> <?php echo JText::_('TEXT_ACTIVE'); ?>
					</button>
					<button class="btn btn-sm btn-warning <?php echo $APPTAG?>-btn-action" disabled onclick="<?php echo $APPTAG?>_setState(0, 0)">
						<span class="base-icon-cancel"></span> <?php echo JText::_('TEXT_INACTIVE'); ?>
					</button>
					<button class="btn btn-sm btn-danger <?php echo $APPTAG?>-btn-action d-none d-sm-inline-block" disabled onclick="<?php echo $APPTAG?>_del(0)">
						<span class="base-icon-trash"></span> <?php echo JText::_('TEXT_DELETE'); ?>
					</button>
				<?php endif; ?>
				<button class="btn btn-sm btn-default toggle-state <?php echo ((isset($_GET[$APPTAG.'_filter']) || $cfg['showFilter']) ? 'active' : '')?>" data-toggle="collapse" data-target="<?php echo '#filter-'.$APPTAG?>" aria-expanded="<?php echo ((isset($_GET[$APPTAG.'_filter']) || $cfg['showFilter']) ? 'true' : '')?>" aria-controls="<?php echo 'filter'.$APPTAG?>">
					<span class="base-icon-filter"></span> <?php echo JText::_('TEXT_FILTER'); ?> <span class="base-icon-sort"></span>
				</button>
			<?php endif; ?>
		</div>
	<?php endif; // showApp ?>

	<?php
	$list = '';
	if($cfg['showList']) :
		$listContent = $cfg['listFull'] ? require($PATH_APP_FILE.'.list.php') : '';
		if($cfg['showListDesc']) $list .= '<div class="base-list-description">'.JText::_('LIST_DESCRIPTION').'</div>';
		$list .= '<div id="list-'.$APPTAG.'" class="base-app-list">'.$listContent.'</div>';
	endif; // end noList

	if($cfg['listModal']) :
		if($cfg['showAddBtn'] && !$cfg['showApp']) $addBtn = '<div class="modal-list-toolbar">'.$addBtn.'</div>';
	?>
			<div class="modal fade" id="modal-list-<?php echo $APPTAG?>" tabindex="-1" role="dialog" aria-labelledby="modal-list-<?php echo $APPTAG?>Label">
				<div class="modal-dialog modal-sm" role="document">
					<div class="modal-content">
						<?php require(JPATH_CORE.DS.'apps/layout/form/modal.header.php'); ?>
						<div class="modal-body">
							<?php echo $addBtn.$list; ?>
						</div>
					</div>
				</div>
			</div>
	<?php
	else :
		if($cfg['showApp']) echo $list;
	endif;
	?>

	<?php if($hasAdmin) : ?>
		<div class="modal fade" id="modal-<?php echo $APPTAG?>" tabindex="-1" role="dialog" aria-labelledby="modal-<?php echo $APPTAG?>Label">
			<div class="modal-dialog modal-lg set-shadow-lg" role="document">
				<div class="modal-content">
					<form name="form-<?php echo $APPTAG?>" id="form-<?php echo $APPTAG?>" method="post" enctype="multipart/form-data">
						<?php require(JPATH_CORE.DS.'apps/layout/form/modal.header.php'); ?>
						<div class="modal-body">
							<fieldset>
								<?php
								require(JPATH_CORE.DS.'apps/layout/form/toolbar.php');
								if($newInstance) require($PATH_APP_FILE.'.form.php');
								else require_once($PATH_APP_FILE.'.form.php');
								?>
							</fieldset>
							<?php require(JPATH_CORE.DS.'apps/layout/form/alert.error.php'); ?>
						</div>
						<?php require(JPATH_CORE.DS.'apps/layout/form/modal.footer.php'); ?>
					</form>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>