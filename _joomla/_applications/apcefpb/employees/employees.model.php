<?php
// BLOCK DIRECT ACCESS
if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) AND strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") :

	header( 'Cache-Control: no-cache' );
	header( 'content-type: application/json; charset=utf-8' );

	function is_valid_callback($subject) {
		$identifier_syntax = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

		$reserved_words = array(
			'break', 'do', 'instanceof', 'typeof', 'case',
			'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue',
			'for', 'switch', 'while', 'debugger', 'function', 'this', 'with',
			'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum',
			'extends', 'super', 'const', 'export', 'import', 'implements', 'let',
			'private', 'public', 'yield', 'interface', 'package', 'protected',
			'static', 'null', 'true', 'false'
		);

		return preg_match($identifier_syntax, $subject) && ! in_array(mb_strtolower($subject, 'UTF-8'), $reserved_words);
	}

	// load Joomla's framework
	// _DIR_ => apps/THIS_APP
	require(__DIR__.'/../../libraries/envolute/_init.joomla.php');
	defined('_JEXEC') or die;
	$app = JFactory::getApplication('site');

	$ajaxRequest = true;
	require('config.php');

	// IMPORTANTE: Carrega o arquivo 'helper' do template
	JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');
    // classes customizadas para usuários Joomla
    JLoader::register('baseUserHelper',  JPATH_CORE.DS.'helpers/user.php');
	$lang->load('lib_joomla', JPATH_ADMINISTRATOR, $_SESSION[$APPTAG.'langDef'], true);

	// get current user's data
	$user		= JFactory::getUser();
	$groups		= $user->groups;

	//joomla get request data
	$input      = $app->input;

	// Default Params
	$APPTAG		= $input->get('aTag', $APPTAG, 'str');
	$RTAG		= $input->get('rTag', $APPTAG, 'str');
	$task       = $input->get('task', null, 'str');
	$data       = array();

	if($task != null) :

		// database connect
		$db = JFactory::getDbo();

		// Carrega o arquivo de tradução
		// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
		// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
		// Para possibilitar o carregamento da language 'default' de forma dinâmica,
		// é necessário passar na sessão ($_SESSION[$APPTAG.'langDef'])
		if(isset($_SESSION[$APPTAG.'langDef'])) :
			$lang->load('base_apps', JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
			$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
		endif;

		// params requests
		$id         = $input->get('id', 0, 'int');

		// fields 'List' requests
		$listIds    = $input->get($APPTAG.'_ids', array(), 'array');
		$ids        = (count($listIds) > 0) ? implode($listIds, ',') : $id;
		$state      = $input->get('st', 2, 'int');

		// upload actions
		$fileMsg 	= '';
		if($cfg['hasUpload']) :
			$fname		= $input->get('fname', '', 'string');
			$fileId		= $input->get('fileId', 0, 'int');
			// image groups
			$fileGrp	= isset($_POST[$cfg['fileField'].'Group']) ? $_POST[$cfg['fileField'].'Group'] : '';
			$fileGtp	= isset($_POST[$cfg['fileField'].'Gtype']) ? $_POST[$cfg['fileField'].'Gtype'] : '';
			$fileCls	= isset($_POST[$cfg['fileField'].'Class']) ? $_POST[$cfg['fileField'].'Class'] : '';
			// image description
			$fileLbl	= isset($_POST[$cfg['fileField'].'Label']) ? $_POST[$cfg['fileField'].'Label'] : '';
			// load 'uploader' class
			JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
		endif;

		// fields 'Form' requests
		$request						= array();
		// default
		$request['relationId']			= $input->get('relationId', 0, 'int');
		$request['state']				= $input->get('state', 1, 'int');
		// app
	    $request['group_id']     		= $input->get('group_id', 0, 'int');
		$request['name']				= $input->get('name', '', 'string');
		$request['card_name']			= $input->get('card_name', '', 'string');
		$request['email']				= $input->get('email', '', 'string');
	  	$request['cpf']					= $input->get('cpf', '', 'string');
	  	$request['rg']					= $input->get('rg', '', 'string');
	  	$request['rg_orgao']			= $input->get('rg_orgao', '', 'string');
	  	$request['pis']					= $input->get('pis', '', 'string');
	  	$request['ctps']				= $input->get('ctps', '', 'string');
	  	$request['blood_type']			= $input->get('blood_type', '', 'string');
	  	$request['driver']				= isset($_POST['driver']) ? implode(',', $_POST['driver']) : '';
	  	$request['occupation']			= $input->get('occupation', '', 'string');
	  	$request['start_date']			= $input->get('start_date', '', 'string');
		$request['gender']				= $input->get('gender', 1, 'int');
		$request['birthday']			= $input->get('birthday', '', 'string');
		$request['place_birth']			= $input->get('place_birth', '', 'string');
		$request['marital_status']		= $input->get('marital_status', 0, 'int');
		$request['partner']				= $input->get('partner', '', 'string');
	  	$request['partner_cpf']			= $input->get('cpf', '', 'string');
		$request['partner_birthday']	= $input->get('partner_birthday', '', 'string');
	  	$request['children']			= $input->get('children', 0, 'int');
		$request['zip_code']			= $input->get('zip_code', '', 'string');
		$request['address']				= $input->get('address', '', 'string');
		$request['address_number']		= $input->get('address_number', '', 'string');
		$request['address_info']		= $input->get('address_info', '', 'string');
		$request['address_district']	= $input->get('address_district', '', 'string');
		$request['address_city']		= $input->get('address_city', '', 'string');
		$request['address_state']		= $input->get('address_state', 'PB', 'string');
		$request['address_country']		= $input->get('address_country', 'BRASIL', 'string');
		$phone							= $input->get('phone', array(), 'array');
		$phone							= str_replace(';', '.', $phone); // formata
		$request['phone']				= implode(';', $phone);
		$whatsapp						= $input->get('whatsapp', array(), 'array');
		$whatsapp						= str_replace(';', '.', $whatsapp); // formata
		$request['whatsapp']			= implode(';', $whatsapp);
		$phone_desc						= $input->get('phone_desc', array(), 'array');
		$phone_desc						= str_replace(';', '.', $phone_desc); // formata
		$request['phone_desc']			= implode(';', $phone_desc);
	  	$request['bank_id']				= $input->get('bank_id', 0, 'int');
		$request['agency']				= $input->get('agency', '', 'string');
		$request['account']				= $input->get('account', '', 'string');
		$request['operation']			= $input->get('operation', '', 'string');
		$request['note']				= $input->get('note', '', 'string');

		// SAVE CONDITION
		// Condição para inserção e atualização dos registros
		$save_condition = (!empty($request['name']) && $request['group_id'] != 0);

		if($id || (!empty($ids) && $ids != 0)) :  //UPDATE OR DELETE

			$exist = 0;
			if($id) :
				// GET FORM DATA
				$query	= 'SELECT * FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' = '. $id;
				$db->setQuery($query);
				$item	= $db->loadObject();
	    		$exist	= (isset($item->id) && !empty($item->id) && $item->id > 0);
				// get previous ID
				$query = 'SELECT MAX(id) FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' < '. $id;
				$db->setQuery($query);
				$prev = $db->loadResult();
				$prev = !empty($prev) ? $prev : 0;
				// get next ID
				$query = 'SELECT MIN(id) FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' > '. $id;
				$db->setQuery($query);
				$next = $db->loadResult();
				$next = !empty($next) ? $next : 0;
				if($cfg['hasUpload']) :
					// get files
					$query = 'SELECT *, TO_BASE64('. $db->quoteName('filename') .') fn, TO_BASE64('. $db->quoteName('mimetype') .') mt FROM '. $db->quoteName($cfg['fileTable']) .' WHERE '. $db->quoteName('id_parent') .' = '. $id . ' ORDER BY '. $db->quoteName('index');
					$db->setQuery($query);
					$listFiles = $db->loadAssocList();
				endif;
			else :
				// COUNT LIST IDS
				$query = 'SELECT COUNT(*) FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' IN ('.$ids.')';
				$db->setQuery($query);
				$exist = $db->loadResult();
			endif;

			if($exist) : // verifica se existe

				// GET FORM DATA
				if($task == 'get') :

					$data[] = array(
						// Default Fields
						'id'				=> $item->id,
						'state'				=> $item->state,
						'prev'				=> $prev,
						'next'				=> $next,
						// App Fields
						'group_id'			=> $item->group_id,
			            'name'				=> $item->name,
			            'card_name'			=> $item->card_name,
						'email'				=> $item->email,
	    				'cpf'				=> $item->cpf,
						'rg'				=> $item->rg,
						'rg_orgao'			=> $item->rg_orgao,
						'pis'				=> $item->pis,
						'ctps'				=> $item->ctps,
						'blood_type'		=> $item->blood_type,
						'driver'			=> explode(',', $item->driver),
						'occupation'		=> $item->occupation,
						'start_date'		=> $item->start_date,
						'gender'			=> $item->gender,
						'birthday'			=> $item->birthday,
						'place_birth'		=> $item->place_birth,
						'marital_status'	=> $item->marital_status,
						'partner'			=> $item->partner,
						'partner_cpf'		=> $item->partner_cpf,
						'partner_birthday'	=> $item->partner_birthday,
						'children'			=> $item->children,
						'zip_code'			=> $item->zip_code,
						'address'			=> $item->address,
						'address_number'	=> $item->address_number,
						'address_info'		=> $item->address_info,
						'address_district'	=> $item->address_district,
						'address_city'		=> $item->address_city,
						'address_state'		=> $item->address_state,
						'address_country'	=> $item->address_country,
						'phone'				=> $item->phone,
						'whatsapp'			=> $item->whatsapp,
						'phone_desc'		=> $item->phone_desc,
						'bank_id'			=> $item->bank_id,
						'agency'			=> $item->agency,
						'account'			=> $item->account,
						'operation'			=> $item->operation,
						'note'				=> $item->note,
						'files'				=> $listFiles
					);

				// UPDATE
				elseif($task == 'save' && $id) :

					if($save_condition) {

						$query  = 'UPDATE '.$db->quoteName($cfg['mainTable']).' SET ';
						$query .=
							$db->quoteName('group_id')			.'='. $request['group_id'] .','.
							$db->quoteName('name')				.'='. $db->quote($request['name']) .','.
							$db->quoteName('email')				.'='. $db->quote($request['email']) .','.
				            $db->quoteName('rg')				.'='. $db->quote($request['rg']) .','.
				            $db->quoteName('rg_orgao')			.'='. $db->quote($request['rg_orgao']) .','.
				            $db->quoteName('pis')				.'='. $db->quote($request['pis']) .','.
				            $db->quoteName('ctps')				.'='. $db->quote($request['ctps']) .','.
				            $db->quoteName('blood_type')		.'='. $db->quote($request['blood_type']) .','.
				            $db->quoteName('driver')			.'='. $db->quote($request['driver']) .','.
				            $db->quoteName('occupation')		.'='. $db->quote($request['occupation']) .','.
				            $db->quoteName('start_date')		.'='. $db->quote($request['start_date']) .','.
							$db->quoteName('gender')			.'='. $request['gender'] .','.
							$db->quoteName('birthday')			.'='. $db->quote($request['birthday']) .','.
							$db->quoteName('place_birth')		.'='. $db->quote($request['place_birth']) .','.
							$db->quoteName('marital_status') 	.'='. $request['marital_status'] .','.
							$db->quoteName('partner')			.'='. $db->quote($request['partner']) .','.
							$db->quoteName('partner_cpf')		.'='. $db->quote($request['partner_cpf']) .','.
							$db->quoteName('partner_birthday')	.'='. $db->quote($request['partner_birthday']) .','.
							$db->quoteName('children')			.'='. $request['children'] .','.
							$db->quoteName('zip_code')			.'='. $db->quote($request['zip_code']) .','.
							$db->quoteName('address')			.'='. $db->quote($request['address']) .','.
							$db->quoteName('address_number')	.'='. $db->quote($request['address_number']) .','.
							$db->quoteName('address_info')		.'='. $db->quote($request['address_info']) .','.
							$db->quoteName('address_district')	.'='. $db->quote($request['address_district']) .','.
							$db->quoteName('address_city')		.'='. $db->quote($request['address_city']) .','.
							$db->quoteName('address_state')		.'='. $db->quote($request['address_state']) .','.
							$db->quoteName('address_country')	.'='. $db->quote($request['address_country']) .','.
							$db->quoteName('phone')				.'='. $db->quote($request['phone']) .','.
							$db->quoteName('whatsapp')			.'='. $db->quote($request['whatsapp']) .','.
							$db->quoteName('phone_desc')		.'='. $db->quote($request['phone_desc']) .','.
							$db->quoteName('card_name')			.'='. $db->quote($request['card_name']) .','.
							$db->quoteName('bank_id')			.'='. $request['bank_id'] .','.
							$db->quoteName('agency')			.'='. $db->quote($request['agency']) .','.
							$db->quoteName('account')			.'='. $db->quote($request['account']) .','.
							$db->quoteName('operation')			.'='. $db->quote($request['operation']) .','.
							$db->quoteName('note')				.'='. $db->quote($request['note']) .','.
							$db->quoteName('state')				.'='. $request['state'] .','.
							$db->quoteName('alter_date')		.'= NOW(),'.
							$db->quoteName('alter_by')			.'='. $user->id
						;
						$query .= ' WHERE '. $db->quoteName('id') .'='. $id;

						try {

							$db->setQuery($query);
							$db->execute();

							// Upload
							if($cfg['hasUpload'])
							$fileMsg = uploader::uploadFile($id, $cfg['fileTable'], $_FILES[$cfg['fileField']], $fileGrp, $fileGtp, $fileCls, $fileLbl, $cfg);

							// UPDATE FIELD
							$element = $elemVal = $elemLabel = '';
							if(!empty($_SESSION[$RTAG.'FieldUpdated']) && !empty($_SESSION[$RTAG.'TableField'])) :
								$element = $_SESSION[$RTAG.'FieldUpdated'];
								$elemVal = $id;
								$query = 'SELECT '. (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $_SESSION[$RTAG.'TableField']) ? $db->quoteName($_SESSION[$RTAG.'TableField']) : $_SESSION[$RTAG.'TableField']) .' FROM '. $db->quoteName($cfg['mainTable']).' WHERE '. $db->quoteName('id') .' = '.$id.' AND state = 1';
								$db->setQuery($query);
								$elemLabel = $db->loadResult();
							endif;

							$data[] = array(
								'status'			=> 2,
								'msg'				=> JText::_('MSG_SAVED'),
								'uploadError'		=> $fileMsg,
								'parentField'		=> $element,
								'parentFieldVal'	=> $elemVal,
								'parentFieldLabel'	=> baseHelper::nameFormat($elemLabel)
							);

						} catch (RuntimeException $e) {

							// Error treatment
							switch($e->getCode()) {
								case '1062':
								$sqlErr = JText::_('MSG_SQL_DUPLICATE_KEY');
								break;
								default:
								$sqlErr = 'Erro: '.$e->getCode().'. '.$e->getMessage();
							}

							$data[] = array(
								'status'			=> 0,
								'msg'				=> $sqlErr,
								'uploadError'		=> $fileMsg
							);

						}

					} else {

						$data[] = array(
							'status'				=> 0,
							'msg'					=> JText::_('MSG_ERROR'),
							'uploadError'			=> $fileMsg
						);

					}

				// DELETE
				elseif($task == 'del') :

					// Lista os usuários associados aos clientes
		            $query = 'SELECT '. $db->quoteName('user_id') .' FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' IN ('.$ids.')';
					$db->setQuery($query);
					$uList = $db->loadObjectList();

					$query = 'DELETE FROM '. $db->quoteName($cfg['mainTable']) .' WHERE '. $db->quoteName('id') .' IN ('.$ids.')';

					try {

						$db->setQuery($query);
						$db->execute();

						// FILE: remove o(s) arquivo(s)
						if($cfg['hasUpload'] && !empty($ids) && $ids != 0)
						$fileMsg = uploader::deleteFiles($ids, $cfg['fileTable'], $cfg['uploadDir'], JText::_('MSG_FILEERRODEL'));

						// DELETE RELATIONSHIP
						if(!empty($_SESSION[$RTAG.'RelTable']) && !empty($_SESSION[$RTAG.'AppNameId'])) :
							$query = 'DELETE FROM '. $db->quoteName($_SESSION[$RTAG.'RelTable']) .' WHERE '. $db->quoteName($_SESSION[$RTAG.'AppNameId']) .' IN ('.$ids.')';
							$db->setQuery($query);
							$db->execute();
						endif;

						// FORCE DELETE RELATIONSHIPS
						// força a exclusão do(s) relacionamento(s) caso os parâmetros não sejam setados
						// isso é RECOMENDÁVEL sempre que houver um ou mais relacionamentos
						// MOVIMENTAÇÕES RECORRENTES -> remove os registros relacionados às movimentações recorrentes
						// $query = 'DELETE FROM '. $db->quoteName('#__'.$cfg['project'].'_transactions') .' WHERE '. $db->quoteName('client_id') .' IN ('.$ids.') AND '. $db->quoteName('fixed') .' = 1';
						// $db->setQuery($query);
						// $db->execute();

						// UPDATE FIELD
						// executa apenas com valores individuais
						$element = $elemVal = $elemLabel = '';
						if(!empty($_SESSION[$RTAG.'FieldUpdated']) && !empty($_SESSION[$RTAG.'TableField'])) :
							$element = $_SESSION[$RTAG.'FieldUpdated'];
							$elemVal = $ids;
						endif;

						$setIds = explode(',', $ids);
						if(count($setIds) > 1) :
							$_SESSION[$APPTAG.'baseAlert']['message'] = JText::_('MSG_ITEMS_DELETED_SUCCESS');
							$_SESSION[$APPTAG.'baseAlert']['context'] = 'success';
						endif;

						$data[] = array(
							'status'			=> 3,
							'ids'				=> $setIds,
							'msg'				=> JText::_('MSG_DELETED'),
							'uploadError'		=> $fileMsg,
							'parentField'		=> $element,
							'parentFieldVal'	=> $elemVal
						);

					} catch (RuntimeException $e) {

						$data[] = array(
							'status'			=> 0,
							'msg'				=> $e->getMessage(),
							'uploadError'		=> $fileMsg
						);

					}

				// STATE
				elseif($task == 'state') :

					$stateVal = ($state == 2 ? 'IF(state = 1, 0, 1)' : $state);
					$query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET '. $db->quoteName('state') .' = '.$stateVal.', '. $db->quoteName('alter_date') .' = NOW() WHERE '. $db->quoteName('id') .' IN ('.$ids.')';

					try {

						$db->setQuery($query);
						$db->execute();

						// UPDATE FIELD
						// executa apenas com valores individuais
						$element = $elemVal = $elemLabel = '';
						if(!empty($_SESSION[$RTAG.'FieldUpdated']) && !empty($_SESSION[$RTAG.'TableField'])) :
							$element = $_SESSION[$RTAG.'FieldUpdated'];
							$elemVal = $ids;
							$query = 'SELECT '. (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $_SESSION[$RTAG.'TableField']) ? $db->quoteName($_SESSION[$RTAG.'TableField']) : $_SESSION[$RTAG.'TableField']) .' FROM '. $db->quoteName($cfg['mainTable']).' WHERE '. $db->quoteName('id') .' = '.$ids;
							$db->setQuery($query);
							$elemLabel = $db->loadResult();
						endif;

						$setIds = explode(',', $ids);
						if(count($setIds) > 1) :
							$_SESSION[$APPTAG.'baseAlert']['message'] = JText::_('MSG_ITEMS_ALTER_SUCCESS');
							$_SESSION[$APPTAG.'baseAlert']['context'] = 'success';
						endif;

						$data[] = array(
							'status'			=> 4,
							'state'				=> $state,
							'ids'				=> $setIds,
							'msg'				=> '',
							'parentField'		=> $element,
							'parentFieldVal'	=> $elemVal,
							'parentFieldLabel'	=> baseHelper::nameFormat($elemLabel)
						);

					} catch (RuntimeException $e) {

						$data[] = array(
							'status'			=> 0,
							'msg'				=> $e->getMessage()
						);

					}

				// DELETE FILE
				elseif($cfg['hasUpload'] && $task == 'delFile' && $fname) :

					// FILE: remove o arquivo
					$fileMsg = uploader::deleteFile($fname, $cfg['fileTable'], $cfg['uploadDir'], JText::_('MSG_FILEERRODEL'));

					if(empty($fileMsg)) {
						// IMPORTANTE: Reorganiza a ordem
						// remove os saltos entre os "index", pois não deve haver!!
						$sIndex = $cfg['indexFileInit'] - 1;
						uploader::rebuildIndexFiles($cfg['fileTable'], $id, $sIndex);
					}

					$data[] = array(
						'status'				=> 5,
						'msg'					=> JText::_('MSG_FILE_DELETED'),
						'uploadError'			=> $fileMsg
					);

				// DELETE FILES
				elseif($cfg['hasUpload'] && $task == 'delFiles' && $fileId) :

					// FILE: remove o arquivo
					$fileMsg = uploader::deleteFiles($fileId, $cfg['fileTable'], $cfg['uploadDir'], JText::_('MSG_FILEERRODEL'));

					$data[] = array(
						'status'				=> 6,
						'msg'					=> JText::_('MSG_FILES_DELETED'),
						'uploadError'			=> $fileMsg
					);

				endif; // end task

			endif; // num rows

		else :

			// INSERT
			if($task == 'save') :

				// validation
				if($save_condition) :

					// Prepare the insert query
					$query  = '
						INSERT INTO '. $db->quoteName($cfg['mainTable']) .'('.
							$db->quoteName('group_id') .','.
							$db->quoteName('name') .','.
							$db->quoteName('card_name') .','.
							$db->quoteName('email') .','.
		            		$db->quoteName('cpf') .','.
		    				$db->quoteName('rg') .','.
		    				$db->quoteName('rg_orgao') .','.
		    				$db->quoteName('pis') .','.
		    				$db->quoteName('ctps') .','.
		    				$db->quoteName('blood_type') .','.
		    				$db->quoteName('driver') .','.
		    				$db->quoteName('occupation') .','.
		    				$db->quoteName('start_date') .','.
							$db->quoteName('gender') .','.
							$db->quoteName('birthday') .','.
							$db->quoteName('place_birth') .','.
							$db->quoteName('marital_status') .','.
							$db->quoteName('partner') .','.
							$db->quoteName('partner_cpf') .','.
							$db->quoteName('partner_birthday') .','.
							$db->quoteName('children') .','.
							$db->quoteName('zip_code') .','.
							$db->quoteName('address') .','.
							$db->quoteName('address_number') .','.
							$db->quoteName('address_info') .','.
							$db->quoteName('address_district') .','.
							$db->quoteName('address_city') .','.
							$db->quoteName('address_state') .','.
							$db->quoteName('address_country') .','.
							$db->quoteName('phone') .','.
							$db->quoteName('whatsapp') .','.
							$db->quoteName('phone_desc') .','.
							$db->quoteName('bank_id') .','.
							$db->quoteName('agency') .','.
							$db->quoteName('account') .','.
							$db->quoteName('operation') .','.
							$db->quoteName('note') .','.
							$db->quoteName('state') .','.
							$db->quoteName('created_by')
						.') VALUES ('.
							$request['group_id'] .','.
							$db->quote($request['name']) .','.
							$db->quote($request['card_name']) .','.
							$db->quote($request['email']) .','.
				            $db->quote($request['cpf']) .','.
				            $db->quote($request['rg']) .','.
				            $db->quote($request['rg_orgao']) .','.
				            $db->quote($request['pis']) .','.
				            $db->quote($request['ctps']) .','.
				            $db->quote($request['blood_type']) .','.
				            $db->quote($request['driver']) .','.
				            $db->quote($request['occupation']) .','.
				            $db->quote($request['start_date']) .','.
							$request['gender'] .','.
							$db->quote($request['birthday']) .','.
							$db->quote($request['place_birth']) .','.
							$request['marital_status'] .','.
							$db->quote($request['partner']) .','.
				            $db->quote($request['partner_cpf']) .','.
							$db->quote($request['partner_birthday']) .','.
							$request['children'] .','.
							$db->quote($request['zip_code']) .','.
							$db->quote($request['address']) .','.
							$db->quote($request['address_number']) .','.
							$db->quote($request['address_info']) .','.
							$db->quote($request['address_district']) .','.
							$db->quote($request['address_city']) .','.
							$db->quote($request['address_state']) .','.
							$db->quote($request['address_country']) .','.
							$db->quote($request['phone']) .','.
							$db->quote($request['whatsapp']) .','.
							$db->quote($request['phone_desc']) .','.
							$request['bank_id'] .','.
							$db->quote($request['agency']) .','.
							$db->quote($request['account']) .','.
							$db->quote($request['operation']) .','.
							$db->quote($request['note']) .','.
							$request['state'] .','.
							$user->id
						.')
					';

					try {

						$db->setQuery($query);
						$db->execute();
						$id = $db->insertid();
						// Upload
						if($cfg['hasUpload'] && $id)
						$fileMsg = uploader::uploadFile($id, $cfg['fileTable'], $_FILES[$cfg['fileField']], $fileGrp, $fileGtp, $fileCls, $fileLbl, $cfg);

						// CREATE RELATIONSHIP
						if(!empty($_SESSION[$RTAG.'RelTable']) && !empty($_SESSION[$RTAG.'RelNameId']) && !empty($_SESSION[$RTAG.'AppNameId']) && !empty($request['relationId'])) :
							$query  = '
								INSERT INTO '. $db->quoteName($_SESSION[$RTAG.'RelTable']) .'('.
									$db->quoteName($_SESSION[$RTAG.'AppNameId']) .','.
									$db->quoteName($_SESSION[$RTAG.'RelNameId'])
								.') VALUES ('.
									$id .','.
									$request['relationId']
								.')
							';
							$db->setQuery($query);
							$db->execute();
						endif;

						// UPDATE FIELD
						$element = $elemVal = $elemLabel = '';
						if(!empty($_SESSION[$RTAG.'FieldUpdated']) && !empty($_SESSION[$RTAG.'TableField'])) :
							$element = $_SESSION[$RTAG.'FieldUpdated'];
							$elemVal = $id;
							$query = 'SELECT '. (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $_SESSION[$RTAG.'TableField']) ? $db->quoteName($_SESSION[$RTAG.'TableField']) : $_SESSION[$RTAG.'TableField']) .' FROM '. $db->quoteName($cfg['mainTable']).' WHERE '. $db->quoteName('id') .' = '.$id.' AND state = 1';
							$db->setQuery($query);
							$elemLabel = $db->loadResult();
						endif;

						$data[] = array(
							'status'			=> 1,
							'msg'				=> JText::_('MSG_SAVED'),
							'regID'				=> $id,
							'uploadError'		=> $fileMsg,
							'parentField'		=> $element,
							'parentFieldVal'	=> $elemVal,
							'parentFieldLabel'	=> baseHelper::nameFormat($elemLabel)
						);

					} catch (RuntimeException $e) {

						// Error treatment
						switch($e->getCode()) {
							case '1062':
							$sqlErr = JText::_('MSG_SQL_DUPLICATE_KEY');
							break;
							default:
							$sqlErr = 'Erro: '.$e->getCode().'. '.$e->getMessage();
						}

						$data[] = array(
							'status'			=> 0,
							'msg'				=> $sqlErr,
							'uploadError'		=> $fileMsg
						);

					}

				else :

					$data[] = array(
						'status'				=> 0,
						'msg'					=> JText::_('MSG_ERROR'),
						'uploadError'			=> $fileMsg
					);

				endif; // end validation

			endif; // end 'task'

		endif; // end 'id'

		$json = json_encode($data);

		# JSON if no callback
		if(!isset($_GET['callback'])) exit($json);

		# JSONP if valid callback
		if(is_valid_callback($_GET['callback'])) exit("{$_GET['callback']}($json)");

		# Otherwise, bad request
		header('status: 400 Bad Request', true, 400);

	endif;

else :

	# Otherwise, bad request
	header('status: 400 Bad Request', true, 400);

endif;

?>
