//JQUERY
jQuery(function() {

	// CHECK AUTO-TAB
	var field_checkAutoTab 	= ".auto-tab input:checkbox, input:checkbox.auto-tab, .auto-tab input:radio, input:radio.auto-tab";

	window.checkAutoTab = function (input, target, group, noTab) {

		input = setElement(input, field_checkAutoTab);
		input.each(function() {

			var obj = jQuery(this);
			var Tg = '';
			if(isSet(obj.data('target')) && !isEmpty(obj.data('target'))) {
				Tg = obj.data('target');
				if(Tg.indexOf('#') == -1 && Tg.indexOf('.') == -1) Tg = '#'+Tg;
			}
			var objFocus = setElement((isSet(target) ? target : Tg));
			var objGroup = setElement((isSet(group) ? group : obj.data('targetGroup')));

			if(elementExist(objFocus)) {

				// Quando o target não é um campo, 'data-target-field' indica o campo que será utilizado...
				// Caso 'data-target-field' não seja informado, será utitlizado o primeiro campo visível dentro do 'target'
				var field = obj.data('targetField');
				if(isSet(field) && !isEmpty(field)) {
					if(field.indexOf('#') == -1 && field.indexOf('.') == -1) field = '#'+field;
				}
				var disable = obj.data('targetDisabled');
				var display = obj.data('targetDisplay');
				var value = obj.data('targetValue');
				var reset = obj.data('targetValueReset');
				var newStatus = true;

				// Set disable tab action
				var tabDisabled = isSet(noTab) ? noTab : (isSet(obj.data('tabDisabled')) ? obj.data('tabDisabled') : false);

				obj.change(function() {
					if(jQuery(this).is(':checked')) {
						// IMPORTANT: this must go before autoTab
						if(elementExist(objFocus)) {
							// Set target disable status
							if(isSet(disable)) {
								newStatus = toggleDisabled(objFocus, disable);
								if(isSet(objGroup)) toggleDisabled(objGroup, newStatus);
							}
							// Set target status display
							if(isSet(display)) {
								newStatus = toggleDisplay(objFocus, display);
								if(isSet(objGroup)) toggleDisplay(objGroup, (newStatus ? false : true));
							}
							// TAB TO TARGET
							var el = objFocus;
							// Verifica se o elemento é um 'campo' ou um 'elemento html'
							if(isSet(field) || (!objFocus.is('input') && !objFocus.is('textarea') && !objFocus.is('select'))) {
								if(elementExist(field)) el = setElement(field);
							} else {
								if(objFocus.find('input, textarea, select.no-chosen').filter(':visible:first').length) {
									el = objFocus.find('input, textarea, select').filter(':visible:first');
								} else if(objFocus.find('select').filter(':first').length) {
									el = objFocus.find('select').filter(':first');
								}
							}
							// Set Tab Action
							// newStatus: (false) habilitado|show / (true) desabilitado|hidden
							if(elementExist(el) && newStatus && !tabDisabled) inputGetFocus(el);
							// Set value
							if(isSet(value)) el.val(value);
							// Verifica se é um select 'chosen'
							if(el.is('select') && el.next('.chosen-container').length) el.trigger("chosen:updated"); // select
						} else {
							// Auto tab
							if(!tabDisabled) setTimeout(function() { obj.autoTab() }, 100);
						}
					} else {
						if(elementExist(objFocus)) {
							// Set target status disable
							if(isSet(disable)) {
								newStatus = toggleDisabled(objFocus, (disable ? false : true));
								if(isSet(objGroup)) toggleDisabled(objGroup, disable);
							}
							// Set target status display
							if(isSet(display)) {
								newStatus = toggleDisplay(objFocus, (display ? false : true));
								if(isSet(objGroup)) toggleDisplay(objGroup, display);
							}
							// TAB TO TARGET
							var el = objFocus;
							// Verifica se o elemento é um 'campo' ou um 'elemento html'
							if(isSet(field) || (!objFocus.is('input') && !objFocus.is('textarea') && !objFocus.is('select'))) {
								if(elementExist(field)) el = setElement(field);
							} else {
								if(objFocus.find('input, textarea, select.no-chosen').filter(':visible:first').length) {
									el = objFocus.find('input, textarea, select').filter(':visible:first');
								} else if(objFocus.find('select').filter(':first').length) {
									el = objFocus.find('select').filter(':first');
								}
							}
							// Set Tab Action
							// newStatus: (false) habilitado|show / (true) desabilitado|hidden
							if(elementExist(el) && newStatus && !tabDisabled) inputGetFocus(el);
							// Set value
							if(isSet(reset)) el.val(reset);
							else if(isSet(value)) el.val(value);
							// Verifica se é um select 'chosen'
							if(el.is('select') && el.next('.chosen-container').length) el.trigger("chosen:updated"); // select
						} else {
							// Auto tab
							if(!tabDisabled) setTimeout(function() { obj.autoTab() }, 100);
						}
					}
				});
			}
		});
	};

});
