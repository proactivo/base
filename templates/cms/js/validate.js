!function(e){"function"==typeof define&&define.amd?define(["jquery"],e):e(jQuery)}(function(e){e.extend(e.fn,{validate:function(t){if(!this.length)return void(t&&t.debug&&window.console&&console.warn("Nothing selected, can't validate, returning nothing."));var a=e.data(this[0],"validator");return a?a:(this.attr("novalidate","novalidate"),a=new e.validator(t,this[0]),e.data(this[0],"validator",a),a.settings.onsubmit&&(this.validateDelegate(":submit","click",function(t){a.settings.submitHandler&&(a.submitButton=t.target),e(t.target).hasClass("cancel")&&(a.cancelSubmit=!0),void 0!==e(t.target).attr("formnovalidate")&&(a.cancelSubmit=!0)}),this.submit(function(t){function i(){var i,r;return a.settings.submitHandler?(a.submitButton&&(i=e("<input type='hidden'/>").attr("name",a.submitButton.name).val(e(a.submitButton).val()).appendTo(a.currentForm)),r=a.settings.submitHandler.call(a,a.currentForm,t),a.submitButton&&i.remove(),void 0!==r?r:!1):!0}return a.settings.debug&&t.preventDefault(),a.cancelSubmit?(a.cancelSubmit=!1,i()):a.form()?a.pendingRequest?(a.formSubmitted=!0,!1):i():(a.focusInvalid(),!1)})),a)},valid:function(){var t,a;return e(this[0]).is("form")?t=this.validate().form():(t=!0,a=e(this[0].form).validate(),this.each(function(){t=a.element(this)&&t})),t},removeAttrs:function(t){var a={},i=this;return e.each(t.split(/\s/),function(e,t){a[t]=i.attr(t),i.removeAttr(t)}),a},rules:function(t,a){var i,r,n,s,d,o,u=this[0];if(t)switch(i=e.data(u.form,"validator").settings,r=i.rules,n=e.validator.staticRules(u),t){case"add":e.extend(n,e.validator.normalizeRule(a)),delete n.messages,r[u.name]=n,a.messages&&(i.messages[u.name]=e.extend(i.messages[u.name],a.messages));break;case"remove":return a?(o={},e.each(a.split(/\s/),function(t,a){o[a]=n[a],delete n[a],"required"===a&&e(u).removeAttr("aria-required")}),o):(delete r[u.name],n)}return s=e.validator.normalizeRules(e.extend({},e.validator.classRules(u),e.validator.attributeRules(u),e.validator.dataRules(u),e.validator.staticRules(u)),u),s.required&&(d=s.required,delete s.required,s=e.extend({required:d},s),e(u).attr("aria-required","true")),s.remote&&(d=s.remote,delete s.remote,s=e.extend(s,{remote:d})),s}}),e.extend(e.expr[":"],{blank:function(t){return!e.trim(""+e(t).val())},filled:function(t){return!!e.trim(""+e(t).val())},unchecked:function(t){return!e(t).prop("checked")}}),e.validator=function(t,a){this.settings=e.extend(!0,{},e.validator.defaults,t),this.currentForm=a,this.init()},e.validator.format=function(t,a){return 1===arguments.length?function(){var a=e.makeArray(arguments);return a.unshift(t),e.validator.format.apply(this,a)}:(arguments.length>2&&a.constructor!==Array&&(a=e.makeArray(arguments).slice(1)),a.constructor!==Array&&(a=[a]),e.each(a,function(e,a){t=t.replace(new RegExp("\\{"+e+"\\}","g"),function(){return a})}),t)},e.extend(e.validator,{defaults:{messages:{},groups:{},rules:{},errorClass:"error",validClass:"valid",errorElement:"label",focusCleanup:!1,focusInvalid:!0,errorContainer:e([]),errorLabelContainer:e([]),onsubmit:!0,ignore:":hidden",ignoreTitle:!1,onfocusin:function(e){this.lastActive=e,this.settings.focusCleanup&&(this.settings.unhighlight&&this.settings.unhighlight.call(this,e,this.settings.errorClass,this.settings.validClass),this.hideThese(this.errorsFor(e)))},onfocusout:function(e){this.checkable(e)||!(e.name in this.submitted)&&this.optional(e)||this.element(e)},onkeyup:function(e,t){(9!==t.which||""!==this.elementValue(e))&&(e.name in this.submitted||e===this.lastElement)&&this.element(e)},onclick:function(e){e.name in this.submitted?this.element(e):e.parentNode.name in this.submitted&&this.element(e.parentNode)},highlight:function(t,a,i){"radio"===t.type?this.findByName(t.name).addClass(a).removeClass(i):e(t).addClass(a).removeClass(i)},unhighlight:function(t,a,i){"radio"===t.type?this.findByName(t.name).removeClass(a).addClass(i):e(t).removeClass(a).addClass(i)}},setDefaults:function(t){e.extend(e.validator.defaults,t)},messages:{required:"This field is required.",remote:"Please fix this field.",email:"Please enter a valid email address.",url:"Please enter a valid URL.",date:"Please enter a valid date.",dateISO:"Please enter a valid date ( ISO ).",number:"Please enter a valid number.",digits:"Please enter only digits.",creditcard:"Please enter a valid credit card number.",equalTo:"Please enter the same value again.",maxlength:e.validator.format("Please enter no more than {0} characters."),minlength:e.validator.format("Please enter at least {0} characters."),rangelength:e.validator.format("Please enter a value between {0} and {1} characters long."),range:e.validator.format("Please enter a value between {0} and {1}."),max:e.validator.format("Please enter a value less than or equal to {0}."),min:e.validator.format("Please enter a value greater than or equal to {0}.")},autoCreateRanges:!1,prototype:{init:function(){function t(t){var a=e.data(this[0].form,"validator"),i="on"+t.type.replace(/^validate/,""),r=a.settings;r[i]&&!this.is(r.ignore)&&r[i].call(a,this[0],t)}this.labelContainer=e(this.settings.errorLabelContainer),this.errorContext=this.labelContainer.length&&this.labelContainer||e(this.currentForm),this.containers=e(this.settings.errorContainer).add(this.settings.errorLabelContainer),this.submitted={},this.valueCache={},this.pendingRequest=0,this.pending={},this.invalid={},this.reset();var a,i=this.groups={};e.each(this.settings.groups,function(t,a){"string"==typeof a&&(a=a.split(/\s/)),e.each(a,function(e,a){i[a]=t})}),a=this.settings.rules,e.each(a,function(t,i){a[t]=e.validator.normalizeRule(i)}),e(this.currentForm).validateDelegate(":text, [type='password'], [type='file'], select, textarea, [type='number'], [type='search'] ,[type='tel'], [type='url'], [type='email'], [type='datetime'], [type='date'], [type='month'], [type='week'], [type='time'], [type='datetime-local'], [type='range'], [type='color'], [type='radio'], [type='checkbox']","focusin focusout keyup",t).validateDelegate("select, option, [type='radio'], [type='checkbox']","click",t),this.settings.invalidHandler&&e(this.currentForm).bind("invalid-form.validate",this.settings.invalidHandler),e(this.currentForm).find("[required], [data-rule-required], .required").attr("aria-required","true")},form:function(){return this.checkForm(),e.extend(this.submitted,this.errorMap),this.invalid=e.extend({},this.errorMap),this.valid()||e(this.currentForm).triggerHandler("invalid-form",[this]),this.showErrors(),this.valid()},checkForm:function(){this.prepareForm();for(var e=0,t=this.currentElements=this.elements();t[e];e++)this.check(t[e]);return this.valid()},element:function(t){var a=this.clean(t),i=this.validationTargetFor(a),r=!0;return this.lastElement=i,void 0===i?delete this.invalid[a.name]:(this.prepareElement(i),this.currentElements=e(i),r=this.check(i)!==!1,r?delete this.invalid[i.name]:this.invalid[i.name]=!0),e(t).attr("aria-invalid",!r),this.numberOfInvalids()||(this.toHide=this.toHide.add(this.containers)),this.showErrors(),r},showErrors:function(t){if(t){e.extend(this.errorMap,t),this.errorList=[];for(var a in t)this.errorList.push({message:t[a],element:this.findByName(a)[0]});this.successList=e.grep(this.successList,function(e){return!(e.name in t)})}this.settings.showErrors?this.settings.showErrors.call(this,this.errorMap,this.errorList):this.defaultShowErrors()},resetForm:function(){e.fn.resetForm&&e(this.currentForm).resetForm(),this.submitted={},this.lastElement=null,this.prepareForm(),this.hideErrors(),this.elements().removeClass(this.settings.errorClass).removeData("previousValue").removeAttr("aria-invalid")},numberOfInvalids:function(){return this.objectLength(this.invalid)},objectLength:function(e){var t,a=0;for(t in e)a++;return a},hideErrors:function(){this.hideThese(this.toHide)},hideThese:function(e){e.not(this.containers).text(""),this.addWrapper(e).hide()},valid:function(){return 0===this.size()},size:function(){return this.errorList.length},focusInvalid:function(){if(this.settings.focusInvalid)try{e(this.findLastActive()||this.errorList.length&&this.errorList[0].element||[]).filter(":visible").focus().trigger("focusin")}catch(t){}},findLastActive:function(){var t=this.lastActive;return t&&1===e.grep(this.errorList,function(e){return e.element.name===t.name}).length&&t},elements:function(){var t=this,a={};return e(this.currentForm).find("input, select, textarea").not(":submit, :reset, :image, [disabled], [readonly]").not(this.settings.ignore).filter(function(){return!this.name&&t.settings.debug&&window.console&&console.error("%o has no name assigned",this),this.name in a||!t.objectLength(e(this).rules())?!1:(a[this.name]=!0,!0)})},clean:function(t){return e(t)[0]},errors:function(){var t=this.settings.errorClass.split(" ").join(".");return e(this.settings.errorElement+"."+t,this.errorContext)},reset:function(){this.successList=[],this.errorList=[],this.errorMap={},this.toShow=e([]),this.toHide=e([]),this.currentElements=e([])},prepareForm:function(){this.reset(),this.toHide=this.errors().add(this.containers)},prepareElement:function(e){this.reset(),this.toHide=this.errorsFor(e)},elementValue:function(t){var a,i=e(t),r=t.type;return"radio"===r||"checkbox"===r?e("input[name='"+t.name+"']:checked").val():"number"===r&&"undefined"!=typeof t.validity?t.validity.badInput?!1:i.val():(a=i.val(),"string"==typeof a?a.replace(/\r/g,""):a)},check:function(t){t=this.validationTargetFor(this.clean(t));var a,i,r,n=e(t).rules(),s=e.map(n,function(e,t){return t}).length,d=!1,o=this.elementValue(t);for(i in n){r={method:i,parameters:n[i]};try{if(a=e.validator.methods[i].call(this,o,t,r.parameters),"dependency-mismatch"===a&&1===s){d=!0;continue}if(d=!1,"pending"===a)return void(this.toHide=this.toHide.not(this.errorsFor(t)));if(!a)return this.formatAndAdd(t,r),!1}catch(u){throw this.settings.debug&&window.console&&console.log("Exception occurred when checking element "+t.id+", check the '"+r.method+"' method.",u),u}}return d?void 0:(this.objectLength(n)&&this.successList.push(t),!0)},customDataMessage:function(t,a){return e(t).data("msg"+a.charAt(0).toUpperCase()+a.substring(1).toLowerCase())||e(t).data("msg")},customMessage:function(e,t){var a=this.settings.messages[e];return a&&(a.constructor===String?a:a[t])},findDefined:function(){for(var e=0;e<arguments.length;e++)if(void 0!==arguments[e])return arguments[e];return void 0},defaultMessage:function(t,a){return this.findDefined(this.customMessage(t.name,a),this.customDataMessage(t,a),!this.settings.ignoreTitle&&t.title||void 0,e.validator.messages[a],"<strong>Warning: No message defined for "+t.name+"</strong>")},formatAndAdd:function(t,a){var i=this.defaultMessage(t,a.method),r=/\$?\{(\d+)\}/g;"function"==typeof i?i=i.call(this,a.parameters,t):r.test(i)&&(i=e.validator.format(i.replace(r,"{$1}"),a.parameters)),this.errorList.push({message:i,element:t,method:a.method}),this.errorMap[t.name]=i,this.submitted[t.name]=i},addWrapper:function(e){return this.settings.wrapper&&(e=e.add(e.parent(this.settings.wrapper))),e},defaultShowErrors:function(){var e,t,a;for(e=0;this.errorList[e];e++)a=this.errorList[e],this.settings.highlight&&this.settings.highlight.call(this,a.element,this.settings.errorClass,this.settings.validClass),this.showLabel(a.element,a.message);if(this.errorList.length&&(this.toShow=this.toShow.add(this.containers)),this.settings.success)for(e=0;this.successList[e];e++)this.showLabel(this.successList[e]);if(this.settings.unhighlight)for(e=0,t=this.validElements();t[e];e++)this.settings.unhighlight.call(this,t[e],this.settings.errorClass,this.settings.validClass);this.toHide=this.toHide.not(this.toShow),this.hideErrors(),this.addWrapper(this.toShow).show()},validElements:function(){return this.currentElements.not(this.invalidElements())},invalidElements:function(){return e(this.errorList).map(function(){return this.element})},showLabel:function(t,a){var i,r,n,s=this.errorsFor(t),d=this.idOrName(t),o=e(t).attr("aria-describedby");s.length?(s.removeClass(this.settings.validClass).addClass(this.settings.errorClass),s.html(a)):(s=e("<"+this.settings.errorElement+">").attr("id",d+"-error").addClass(this.settings.errorClass).html(a||""),i=s,this.settings.wrapper&&(i=s.hide().show().wrap("<"+this.settings.wrapper+"/>").parent()),this.labelContainer.length?this.labelContainer.append(i):this.settings.errorPlacement?this.settings.errorPlacement(i,e(t)):i.insertAfter(t),s.is("label")?s.attr("for",d):0===s.parents("label[for='"+d+"']").length&&(n=s.attr("id").replace(/(:|\.|\[|\])/g,"\\$1"),o?o.match(new RegExp("\\b"+n+"\\b"))||(o+=" "+n):o=n,e(t).attr("aria-describedby",o),r=this.groups[t.name],r&&e.each(this.groups,function(t,a){a===r&&e("[name='"+t+"']",this.currentForm).attr("aria-describedby",s.attr("id"))}))),!a&&this.settings.success&&(s.text(""),"string"==typeof this.settings.success?s.addClass(this.settings.success):this.settings.success(s,t)),this.toShow=this.toShow.add(s)},errorsFor:function(t){var a=this.idOrName(t),i=e(t).attr("aria-describedby"),r="label[for='"+a+"'], label[for='"+a+"'] *";return i&&(r=r+", #"+i.replace(/\s+/g,", #")),this.errors().filter(r)},idOrName:function(e){return this.groups[e.name]||(this.checkable(e)?e.name:e.id||e.name)},validationTargetFor:function(t){return this.checkable(t)&&(t=this.findByName(t.name)),e(t).not(this.settings.ignore)[0]},checkable:function(e){return/radio|checkbox/i.test(e.type)},findByName:function(t){return e(this.currentForm).find("[name='"+t+"']")},getLength:function(t,a){switch(a.nodeName.toLowerCase()){case"select":return e("option:selected",a).length;case"input":if(this.checkable(a))return this.findByName(a.name).filter(":checked").length}return t.length},depend:function(e,t){return this.dependTypes[typeof e]?this.dependTypes[typeof e](e,t):!0},dependTypes:{"boolean":function(e){return e},string:function(t,a){return!!e(t,a.form).length},"function":function(e,t){return e(t)}},optional:function(t){var a=this.elementValue(t);return!e.validator.methods.required.call(this,a,t)&&"dependency-mismatch"},startRequest:function(e){this.pending[e.name]||(this.pendingRequest++,this.pending[e.name]=!0)},stopRequest:function(t,a){this.pendingRequest--,this.pendingRequest<0&&(this.pendingRequest=0),delete this.pending[t.name],a&&0===this.pendingRequest&&this.formSubmitted&&this.form()?(e(this.currentForm).submit(),this.formSubmitted=!1):!a&&0===this.pendingRequest&&this.formSubmitted&&(e(this.currentForm).triggerHandler("invalid-form",[this]),this.formSubmitted=!1)},previousValue:function(t){return e.data(t,"previousValue")||e.data(t,"previousValue",{old:null,valid:!0,message:this.defaultMessage(t,"remote")})}},classRuleSettings:{required:{required:!0},email:{email:!0},url:{url:!0},date:{date:!0},dateISO:{dateISO:!0},number:{number:!0},digits:{digits:!0},creditcard:{creditcard:!0}},addClassRules:function(t,a){t.constructor===String?this.classRuleSettings[t]=a:e.extend(this.classRuleSettings,t)},classRules:function(t){var a={},i=e(t).attr("class");return i&&e.each(i.split(" "),function(){this in e.validator.classRuleSettings&&e.extend(a,e.validator.classRuleSettings[this])}),a},attributeRules:function(t){var a,i,r={},n=e(t),s=t.getAttribute("type");for(a in e.validator.methods)"required"===a?(i=t.getAttribute(a),""===i&&(i=!0),i=!!i):i=n.attr(a),/min|max/.test(a)&&(null===s||/number|range|text/.test(s))&&(i=Number(i)),i||0===i?r[a]=i:s===a&&"range"!==s&&(r[a]=!0);return r.maxlength&&/-1|2147483647|524288/.test(r.maxlength)&&delete r.maxlength,r},dataRules:function(t){var a,i,r={},n=e(t);for(a in e.validator.methods)i=n.data("rule"+a.charAt(0).toUpperCase()+a.substring(1).toLowerCase()),void 0!==i&&(r[a]=i);return r},staticRules:function(t){var a={},i=e.data(t.form,"validator");return i.settings.rules&&(a=e.validator.normalizeRule(i.settings.rules[t.name])||{}),a},normalizeRules:function(t,a){return e.each(t,function(i,r){if(r===!1)return void delete t[i];if(r.param||r.depends){var n=!0;switch(typeof r.depends){case"string":n=!!e(r.depends,a.form).length;break;case"function":n=r.depends.call(a,a)}n?t[i]=void 0!==r.param?r.param:!0:delete t[i]}}),e.each(t,function(i,r){t[i]=e.isFunction(r)?r(a):r}),e.each(["minlength","maxlength"],function(){t[this]&&(t[this]=Number(t[this]))}),e.each(["rangelength","range"],function(){var a;t[this]&&(e.isArray(t[this])?t[this]=[Number(t[this][0]),Number(t[this][1])]:"string"==typeof t[this]&&(a=t[this].replace(/[\[\]]/g,"").split(/[\s,]+/),t[this]=[Number(a[0]),Number(a[1])]))}),e.validator.autoCreateRanges&&(null!=t.min&&null!=t.max&&(t.range=[t.min,t.max],delete t.min,delete t.max),null!=t.minlength&&null!=t.maxlength&&(t.rangelength=[t.minlength,t.maxlength],delete t.minlength,delete t.maxlength)),t},normalizeRule:function(t){if("string"==typeof t){var a={};e.each(t.split(/\s/),function(){a[this]=!0}),t=a}return t},addMethod:function(t,a,i){e.validator.methods[t]=a,e.validator.messages[t]=void 0!==i?i:e.validator.messages[t],a.length<3&&e.validator.addClassRules(t,e.validator.normalizeRule(t))},methods:{required:function(t,a,i){if(!this.depend(i,a))return"dependency-mismatch";if("select"===a.nodeName.toLowerCase()){var r=e(a).val();return r&&r.length>0}return this.checkable(a)?this.getLength(t,a)>0:e.trim(t).length>0},email:function(e,t){return this.optional(t)||/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/.test(e)},url:function(e,t){return this.optional(t)||/^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(e)},date:function(e,t){return this.optional(t)||!/Invalid|NaN/.test(new Date(e).toString())},dateISO:function(e,t){return this.optional(t)||/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/.test(e)},number:function(e,t){return this.optional(t)||/^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test(e)},digits:function(e,t){return this.optional(t)||/^\d+$/.test(e)},creditcard:function(e,t){if(this.optional(t))return"dependency-mismatch";if(/[^0-9 \-]+/.test(e))return!1;var a,i,r=0,n=0,s=!1;if(e=e.replace(/\D/g,""),e.length<13||e.length>19)return!1;for(a=e.length-1;a>=0;a--)i=e.charAt(a),n=parseInt(i,10),s&&(n*=2)>9&&(n-=9),r+=n,s=!s;return r%10===0},minlength:function(t,a,i){var r=e.isArray(t)?t.length:this.getLength(t,a);return this.optional(a)||r>=i},maxlength:function(t,a,i){var r=e.isArray(t)?t.length:this.getLength(t,a);return this.optional(a)||i>=r},rangelength:function(t,a,i){var r=e.isArray(t)?t.length:this.getLength(t,a);return this.optional(a)||r>=i[0]&&r<=i[1]},min:function(e,t,a){return this.optional(t)||e>=a},max:function(e,t,a){return this.optional(t)||a>=e},range:function(e,t,a){return this.optional(t)||e>=a[0]&&e<=a[1]},equalTo:function(t,a,i){var r=e(i);return this.settings.onfocusout&&r.unbind(".validate-equalTo").bind("blur.validate-equalTo",function(){e(a).valid()}),t===r.val()},remote:function(t,a,i){if(this.optional(a))return"dependency-mismatch";var r,n,s=this.previousValue(a);return this.settings.messages[a.name]||(this.settings.messages[a.name]={}),s.originalMessage=this.settings.messages[a.name].remote,this.settings.messages[a.name].remote=s.message,i="string"==typeof i&&{url:i}||i,s.old===t?s.valid:(s.old=t,r=this,this.startRequest(a),n={},n[a.name]=t,e.ajax(e.extend(!0,{url:i,mode:"abort",port:"validate"+a.name,dataType:"json",data:n,context:r.currentForm,success:function(i){var n,d,o,u=i===!0||"true"===i;r.settings.messages[a.name].remote=s.originalMessage,u?(o=r.formSubmitted,r.prepareElement(a),r.formSubmitted=o,r.successList.push(a),delete r.invalid[a.name],r.showErrors()):(n={},d=i||r.defaultMessage(a,"remote"),n[a.name]=s.message=e.isFunction(d)?d(t):d,r.invalid[a.name]=!0,r.showErrors(n)),s.valid=u,r.stopRequest(a,u)}},i)),"pending")}}}),e.format=function(){throw"$.format has been deprecated. Please use $.validator.format instead."};var t,a={};e.ajaxPrefilter?e.ajaxPrefilter(function(e,t,i){var r=e.port;"abort"===e.mode&&(a[r]&&a[r].abort(),a[r]=i)}):(t=e.ajax,e.ajax=function(i){var r=("mode"in i?i:e.ajaxSettings).mode,n=("port"in i?i:e.ajaxSettings).port;return"abort"===r?(a[n]&&a[n].abort(),a[n]=t.apply(this,arguments),a[n]):t.apply(this,arguments)}),e.extend(e.fn,{validateDelegate:function(t,a,i){return this.bind(a,function(a){var r=e(a.target);return r.is(t)?i.apply(r,arguments):void 0})}})}),!function(e){"function"==typeof define&&define.amd?define(["jquery","./jquery.validate.min"],e):e(jQuery)}(function(e){!function(){function t(e){return e.replace(/<.[^<>]*?>/g," ").replace(/&nbsp;|&#160;/gi," ").replace(/[.(),;:!?%#$'\"_+=\/\-“”’]*/g,"")}e.validator.addMethod("maxWords",function(e,a,i){return this.optional(a)||t(e).match(/\b\w+\b/g).length<=i},e.validator.format("Please enter {0} words or less.")),e.validator.addMethod("minWords",function(e,a,i){return this.optional(a)||t(e).match(/\b\w+\b/g).length>=i},e.validator.format("Please enter at least {0} words.")),e.validator.addMethod("rangeWords",function(e,a,i){var r=t(e),n=/\b\w+\b/g;return this.optional(a)||r.match(n).length>=i[0]&&r.match(n).length<=i[1]},e.validator.format("Please enter between {0} and {1} words."))}(),e.validator.addMethod("accept",function(t,a,i){var r,n,s="string"==typeof i?i.replace(/\s/g,"").replace(/,/g,"|"):"image/*",d=this.optional(a);if(d)return d;if("file"===e(a).attr("type")&&(s=s.replace(/\*/g,".*"),a.files&&a.files.length))for(r=0;r<a.files.length;r++)if(n=a.files[r],!n.type.match(new RegExp(".?("+s+")$","i")))return!1;return!0},e.validator.format("Please enter a value with a valid mimetype.")),e.validator.addMethod("alphanumeric",function(e,t){return this.optional(t)||/^\w+$/i.test(e)},"Letters, numbers, and underscores only please"),e.validator.addMethod("bankaccountNL",function(e,t){if(this.optional(t))return!0;if(!/^[0-9]{9}|([0-9]{2} ){3}[0-9]{3}$/.test(e))return!1;var a,i,r,n=e.replace(/ /g,""),s=0,d=n.length;for(a=0;d>a;a++)i=d-a,r=n.substring(a,a+1),s+=i*r;return s%11===0},"Please specify a valid bank account number"),e.validator.addMethod("bankorgiroaccountNL",function(t,a){return this.optional(a)||e.validator.methods.bankaccountNL.call(this,t,a)||e.validator.methods.giroaccountNL.call(this,t,a)},"Please specify a valid bank or giro account number"),e.validator.addMethod("bic",function(e,t){return this.optional(t)||/^([A-Z]{6}[A-Z2-9][A-NP-Z1-2])(X{3}|[A-WY-Z0-9][A-Z0-9]{2})?$/.test(e)},"Please specify a valid BIC code"),e.validator.addMethod("cifES",function(e){"use strict";var t,a,i,r,n,s,d=[];if(e=e.toUpperCase(),!e.match("((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$)"))return!1;for(i=0;9>i;i++)d[i]=parseInt(e.charAt(i),10);for(a=d[2]+d[4]+d[6],r=1;8>r;r+=2)n=(2*d[r]).toString(),s=n.charAt(1),a+=parseInt(n.charAt(0),10)+(""===s?0:parseInt(s,10));return/^[ABCDEFGHJNPQRSUVW]{1}/.test(e)?(a+="",t=10-parseInt(a.charAt(a.length-1),10),e+=t,d[8].toString()===String.fromCharCode(64+t)||d[8].toString()===e.charAt(e.length-1)):!1},"Please specify a valid CIF number."),e.validator.addMethod("creditcardtypes",function(e,t,a){if(/[^0-9\-]+/.test(e))return!1;e=e.replace(/\D/g,"");var i=0;return a.mastercard&&(i|=1),a.visa&&(i|=2),a.amex&&(i|=4),a.dinersclub&&(i|=8),a.enroute&&(i|=16),a.discover&&(i|=32),a.jcb&&(i|=64),a.unknown&&(i|=128),a.all&&(i=255),1&i&&/^(5[12345])/.test(e)?16===e.length:2&i&&/^(4)/.test(e)?16===e.length:4&i&&/^(3[47])/.test(e)?15===e.length:8&i&&/^(3(0[012345]|[68]))/.test(e)?14===e.length:16&i&&/^(2(014|149))/.test(e)?15===e.length:32&i&&/^(6011)/.test(e)?16===e.length:64&i&&/^(3)/.test(e)?16===e.length:64&i&&/^(2131|1800)/.test(e)?15===e.length:128&i?!0:!1},"Please enter a valid credit card number."),e.validator.addMethod("currency",function(e,t,a){var i,r="string"==typeof a,n=r?a:a[0],s=r?!0:a[1];return n=n.replace(/,/g,""),n=s?n+"]":n+"]?",i="^["+n+"([1-9]{1}[0-9]{0,2}(\\,[0-9]{3})*(\\.[0-9]{0,2})?|[1-9]{1}[0-9]{0,}(\\.[0-9]{0,2})?|0(\\.[0-9]{0,2})?|(\\.[0-9]{1,2})?)$",i=new RegExp(i),this.optional(t)||i.test(e)},"Please specify a valid currency"),e.validator.addMethod("dateFA",function(e,t){return this.optional(t)||/^[1-4]\d{3}\/((0?[1-6]\/((3[0-1])|([1-2][0-9])|(0?[1-9])))|((1[0-2]|(0?[7-9]))\/(30|([1-2][0-9])|(0?[1-9]))))$/.test(e)},"Please enter a correct date"),e.validator.addMethod("dateITA",function(e,t){var a,i,r,n,s,d=!1,o=/^\d{1,2}\/\d{1,2}\/\d{4}$/;return o.test(e)?(a=e.split("/"),i=parseInt(a[0],10),r=parseInt(a[1],10),n=parseInt(a[2],10),s=new Date(n,r-1,i,12,0,0,0),d=s.getUTCFullYear()===n&&s.getUTCMonth()===r-1&&s.getUTCDate()===i?!0:!1):d=!1,this.optional(t)||d},"Please enter a correct date"),e.validator.addMethod("dateNL",function(e,t){return this.optional(t)||/^(0?[1-9]|[12]\d|3[01])[\.\/\-](0?[1-9]|1[012])[\.\/\-]([12]\d)?(\d\d)$/.test(e)},"Please enter a correct date"),e.validator.addMethod("extension",function(e,t,a){return a="string"==typeof a?a.replace(/,/g,"|"):"png|jpe?g|gif",this.optional(t)||e.match(new RegExp(".("+a+")$","i"))},e.validator.format("Please enter a value with a valid extension.")),e.validator.addMethod("giroaccountNL",function(e,t){return this.optional(t)||/^[0-9]{1,7}$/.test(e)},"Please specify a valid giro account number"),e.validator.addMethod("iban",function(e,t){if(this.optional(t))return!0;var a,i,r,n,s,d,o,u,l,h=e.replace(/ /g,"").toUpperCase(),c="",f=!0,m="",g="";if(!/^([a-zA-Z0-9]{4} ){2,8}[a-zA-Z0-9]{1,4}|[a-zA-Z0-9]{12,34}$/.test(h))return!1;if(a=h.substring(0,2),d={AL:"\\d{8}[\\dA-Z]{16}",AD:"\\d{8}[\\dA-Z]{12}",AT:"\\d{16}",AZ:"[\\dA-Z]{4}\\d{20}",BE:"\\d{12}",BH:"[A-Z]{4}[\\dA-Z]{14}",BA:"\\d{16}",BR:"\\d{23}[A-Z][\\dA-Z]",BG:"[A-Z]{4}\\d{6}[\\dA-Z]{8}",CR:"\\d{17}",HR:"\\d{17}",CY:"\\d{8}[\\dA-Z]{16}",CZ:"\\d{20}",DK:"\\d{14}",DO:"[A-Z]{4}\\d{20}",EE:"\\d{16}",FO:"\\d{14}",FI:"\\d{14}",FR:"\\d{10}[\\dA-Z]{11}\\d{2}",GE:"[\\dA-Z]{2}\\d{16}",DE:"\\d{18}",GI:"[A-Z]{4}[\\dA-Z]{15}",GR:"\\d{7}[\\dA-Z]{16}",GL:"\\d{14}",GT:"[\\dA-Z]{4}[\\dA-Z]{20}",HU:"\\d{24}",IS:"\\d{22}",IE:"[\\dA-Z]{4}\\d{14}",IL:"\\d{19}",IT:"[A-Z]\\d{10}[\\dA-Z]{12}",KZ:"\\d{3}[\\dA-Z]{13}",KW:"[A-Z]{4}[\\dA-Z]{22}",LV:"[A-Z]{4}[\\dA-Z]{13}",LB:"\\d{4}[\\dA-Z]{20}",LI:"\\d{5}[\\dA-Z]{12}",LT:"\\d{16}",LU:"\\d{3}[\\dA-Z]{13}",MK:"\\d{3}[\\dA-Z]{10}\\d{2}",MT:"[A-Z]{4}\\d{5}[\\dA-Z]{18}",MR:"\\d{23}",MU:"[A-Z]{4}\\d{19}[A-Z]{3}",MC:"\\d{10}[\\dA-Z]{11}\\d{2}",MD:"[\\dA-Z]{2}\\d{18}",ME:"\\d{18}",NL:"[A-Z]{4}\\d{10}",NO:"\\d{11}",PK:"[\\dA-Z]{4}\\d{16}",PS:"[\\dA-Z]{4}\\d{21}",PL:"\\d{24}",PT:"\\d{21}",RO:"[A-Z]{4}[\\dA-Z]{16}",SM:"[A-Z]\\d{10}[\\dA-Z]{12}",SA:"\\d{2}[\\dA-Z]{18}",RS:"\\d{18}",SK:"\\d{20}",SI:"\\d{15}",ES:"\\d{20}",SE:"\\d{20}",CH:"\\d{5}[\\dA-Z]{12}",TN:"\\d{20}",TR:"\\d{5}[\\dA-Z]{17}",AE:"\\d{3}\\d{16}",GB:"[A-Z]{4}\\d{14}",VG:"[\\dA-Z]{4}\\d{16}"},s=d[a],"undefined"!=typeof s&&(o=new RegExp("^[A-Z]{2}\\d{2}"+s+"$",""),!o.test(h)))return!1;for(i=h.substring(4,h.length)+h.substring(0,4),u=0;u<i.length;u++)r=i.charAt(u),"0"!==r&&(f=!1),f||(c+="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ".indexOf(r));for(l=0;l<c.length;l++)n=c.charAt(l),g=""+m+n,m=g%97;return 1===m},"Please specify a valid IBAN"),e.validator.addMethod("integer",function(e,t){return this.optional(t)||/^-?\d+$/.test(e)},"A positive or negative non-decimal number please"),e.validator.addMethod("ipv4",function(e,t){return this.optional(t)||/^(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)$/i.test(e)},"Please enter a valid IP v4 address."),e.validator.addMethod("ipv6",function(e,t){return this.optional(t)||/^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$/i.test(e)},"Please enter a valid IP v6 address."),e.validator.addMethod("lettersonly",function(e,t){return this.optional(t)||/^[a-z]+$/i.test(e)},"Letters only please"),e.validator.addMethod("letterswithbasicpunc",function(e,t){return this.optional(t)||/^[a-z\-.,()'"\s]+$/i.test(e)},"Letters or punctuation only please"),e.validator.addMethod("mobileNL",function(e,t){return this.optional(t)||/^((\+|00(\s|\s?\-\s?)?)31(\s|\s?\-\s?)?(\(0\)[\-\s]?)?|0)6((\s|\s?\-\s?)?[0-9]){8}$/.test(e)},"Please specify a valid mobile number"),e.validator.addMethod("mobileUK",function(e,t){return e=e.replace(/\(|\)|\s+|-/g,""),this.optional(t)||e.length>9&&e.match(/^(?:(?:(?:00\s?|\+)44\s?|0)7(?:[1345789]\d{2}|624)\s?\d{3}\s?\d{3})$/)},"Please specify a valid mobile number"),e.validator.addMethod("nieES",function(e){"use strict";return e=e.toUpperCase(),e.match("((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$)")?/^[T]{1}/.test(e)?e[8]===/^[T]{1}[A-Z0-9]{8}$/.test(e):/^[XYZ]{1}/.test(e)?e[8]==="TRWAGMYFPDXBNJZSQVHLCKE".charAt(e.replace("X","0").replace("Y","1").replace("Z","2").substring(0,8)%23):!1:!1},"Please specify a valid NIE number."),e.validator.addMethod("nifES",function(e){"use strict";return e=e.toUpperCase(),e.match("((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$)")?/^[0-9]{8}[A-Z]{1}$/.test(e)?"TRWAGMYFPDXBNJZSQVHLCKE".charAt(e.substring(8,0)%23)===e.charAt(8):/^[KLM]{1}/.test(e)?e[8]===String.fromCharCode(64):!1:!1},"Please specify a valid NIF number."),e.validator.addMethod("nowhitespace",function(e,t){return this.optional(t)||/^\S+$/i.test(e)},"No white space please"),e.validator.addMethod("pattern",function(e,t,a){return this.optional(t)?!0:("string"==typeof a&&(a=new RegExp("^(?:"+a+")$")),a.test(e))},"Invalid format."),e.validator.addMethod("phoneNL",function(e,t){return this.optional(t)||/^((\+|00(\s|\s?\-\s?)?)31(\s|\s?\-\s?)?(\(0\)[\-\s]?)?|0)[1-9]((\s|\s?\-\s?)?[0-9]){8}$/.test(e)},"Please specify a valid phone number."),e.validator.addMethod("phoneUK",function(e,t){return e=e.replace(/\(|\)|\s+|-/g,""),this.optional(t)||e.length>9&&e.match(/^(?:(?:(?:00\s?|\+)44\s?)|(?:\(?0))(?:\d{2}\)?\s?\d{4}\s?\d{4}|\d{3}\)?\s?\d{3}\s?\d{3,4}|\d{4}\)?\s?(?:\d{5}|\d{3}\s?\d{3})|\d{5}\)?\s?\d{4,5})$/)},"Please specify a valid phone number"),e.validator.addMethod("phoneUS",function(e,t){return e=e.replace(/\s+/g,""),this.optional(t)||e.length>9&&e.match(/^(\+?1-?)?(\([2-9]([02-9]\d|1[02-9])\)|[2-9]([02-9]\d|1[02-9]))-?[2-9]([02-9]\d|1[02-9])-?\d{4}$/);
},"Please specify a valid phone number"),e.validator.addMethod("phonesUK",function(e,t){return e=e.replace(/\(|\)|\s+|-/g,""),this.optional(t)||e.length>9&&e.match(/^(?:(?:(?:00\s?|\+)44\s?|0)(?:1\d{8,9}|[23]\d{9}|7(?:[1345789]\d{8}|624\d{6})))$/)},"Please specify a valid uk phone number"),e.validator.addMethod("postalCodeCA",function(e,t){return this.optional(t)||/^[ABCEGHJKLMNPRSTVXY]\d[A-Z] \d[A-Z]\d$/.test(e)},"Please specify a valid postal code"),e.validator.addMethod("postalcodeBR",function(e,t){return this.optional(t)||/^\d{2}.\d{3}-\d{3}?$|^\d{5}-?\d{3}?$/.test(e)},"Informe um CEP válido."),e.validator.addMethod("postalcodeIT",function(e,t){return this.optional(t)||/^\d{5}$/.test(e)},"Please specify a valid postal code"),e.validator.addMethod("postalcodeNL",function(e,t){return this.optional(t)||/^[1-9][0-9]{3}\s?[a-zA-Z]{2}$/.test(e)},"Please specify a valid postal code"),e.validator.addMethod("postcodeUK",function(e,t){return this.optional(t)||/^((([A-PR-UWYZ][0-9])|([A-PR-UWYZ][0-9][0-9])|([A-PR-UWYZ][A-HK-Y][0-9])|([A-PR-UWYZ][A-HK-Y][0-9][0-9])|([A-PR-UWYZ][0-9][A-HJKSTUW])|([A-PR-UWYZ][A-HK-Y][0-9][ABEHMNPRVWXY]))\s?([0-9][ABD-HJLNP-UW-Z]{2})|(GIR)\s?(0AA))$/i.test(e)},"Please specify a valid UK postcode"),e.validator.addMethod("require_from_group",function(t,a,i){var r=e(i[1],a.form),n=r.eq(0),s=n.data("valid_req_grp")?n.data("valid_req_grp"):e.extend({},this),d=r.filter(function(){return s.elementValue(this)}).length>=i[0];return n.data("valid_req_grp",s),e(a).data("being_validated")||(r.data("being_validated",!0),r.each(function(){s.element(this)}),r.data("being_validated",!1)),d},e.validator.format("Please fill at least {0} of these fields.")),e.validator.addMethod("skip_or_fill_minimum",function(t,a,i){var r=e(i[1],a.form),n=r.eq(0),s=n.data("valid_skip")?n.data("valid_skip"):e.extend({},this),d=r.filter(function(){return s.elementValue(this)}).length,o=0===d||d>=i[0];return n.data("valid_skip",s),e(a).data("being_validated")||(r.data("being_validated",!0),r.each(function(){s.element(this)}),r.data("being_validated",!1)),o},e.validator.format("Please either skip these fields or fill at least {0} of them.")),jQuery.validator.addMethod("stateUS",function(e,t,a){var i,r="undefined"==typeof a,n=r||"undefined"==typeof a.caseSensitive?!1:a.caseSensitive,s=r||"undefined"==typeof a.includeTerritories?!1:a.includeTerritories,d=r||"undefined"==typeof a.includeMilitary?!1:a.includeMilitary;return i=s||d?s&&d?"^(A[AEKLPRSZ]|C[AOT]|D[CE]|FL|G[AU]|HI|I[ADLN]|K[SY]|LA|M[ADEINOPST]|N[CDEHJMVY]|O[HKR]|P[AR]|RI|S[CD]|T[NX]|UT|V[AIT]|W[AIVY])$":s?"^(A[KLRSZ]|C[AOT]|D[CE]|FL|G[AU]|HI|I[ADLN]|K[SY]|LA|M[ADEINOPST]|N[CDEHJMVY]|O[HKR]|P[AR]|RI|S[CD]|T[NX]|UT|V[AIT]|W[AIVY])$":"^(A[AEKLPRZ]|C[AOT]|D[CE]|FL|GA|HI|I[ADLN]|K[SY]|LA|M[ADEINOST]|N[CDEHJMVY]|O[HKR]|PA|RI|S[CD]|T[NX]|UT|V[AT]|W[AIVY])$":"^(A[KLRZ]|C[AOT]|D[CE]|FL|GA|HI|I[ADLN]|K[SY]|LA|M[ADEINOST]|N[CDEHJMVY]|O[HKR]|PA|RI|S[CD]|T[NX]|UT|V[AT]|W[AIVY])$",i=n?new RegExp(i):new RegExp(i,"i"),this.optional(t)||i.test(e)},"Please specify a valid state"),e.validator.addMethod("strippedminlength",function(t,a,i){return e(t).text().length>=i},e.validator.format("Please enter at least {0} characters")),e.validator.addMethod("time",function(e,t){return this.optional(t)||/^([01]\d|2[0-3])(:[0-5]\d){1,2}$/.test(e)},"Please enter a valid time, between 00:00 and 23:59"),e.validator.addMethod("time12h",function(e,t){return this.optional(t)||/^((0?[1-9]|1[012])(:[0-5]\d){1,2}(\ ?[AP]M))$/i.test(e)},"Please enter a valid time in 12-hour am/pm format"),e.validator.addMethod("url2",function(e,t){return this.optional(t)||/^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)*(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(e)},e.validator.messages.url),e.validator.addMethod("vinUS",function(e){if(17!==e.length)return!1;var t,a,i,r,n,s,d=["A","B","C","D","E","F","G","H","J","K","L","M","N","P","R","S","T","U","V","W","X","Y","Z"],o=[1,2,3,4,5,6,7,8,1,2,3,4,5,7,9,2,3,4,5,6,7,8,9],u=[8,7,6,5,4,3,2,10,0,9,8,7,6,5,4,3,2],l=0;for(t=0;17>t;t++){if(r=u[t],i=e.slice(t,t+1),8===t&&(s=i),isNaN(i)){for(a=0;a<d.length;a++)if(i.toUpperCase()===d[a]){i=o[a],i*=r,isNaN(s)&&8===a&&(s=d[a]);break}}else i*=r;l+=i}return n=l%11,10===n&&(n="X"),n===s?!0:!1},"The specified vehicle identification number (VIN) is invalid."),e.validator.addMethod("zipcodeUS",function(e,t){return this.optional(t)||/^\d{5}(-\d{4})?$/.test(e)},"The specified US ZIP Code is invalid"),e.validator.addMethod("ziprange",function(e,t){return this.optional(t)||/^90[2-5]\d\{2\}-\d{4}$/.test(e)},"Your ZIP-code must be in the range 902xx-xxxx to 905xx-xxxx")}),jQuery(function(){jQuery.validator&&(jQuery.validator.setDefaults({debug:!0,errorElement:"span",success:"valid",ignore:":hidden:not(select), .chosen-choices input, .chosen-search input, .chzn-choices input, .chzn-search input"}),jQuery.extend(jQuery.validator.messages,{required:"Campo Obrigat&oacute;rio",remote:"Por favor, verifique esse campo",email:"Informe uma e-mail v&aacute;lido",url:"Informe uma URL v&aacute;lida",date:"Informe uma data v&aacute;lida",dateISO:"Informe uma data v&aacute;lida (ISO).",number:"Informe um n&uacute;mero v&aacute;lido",digits:"Informe apenas digitos",creditcard:"Informe um n&uacute;mero de cart&atilde;o de cr&eacute;dito v&aacute;lido",equalTo:"Campos com valores diferentes",accept:"Extens&atilde;o Inv&aacute;lida!",maxlength:jQuery.validator.format("Informe no m&aacute;ximo {0} caracteres."),minlength:jQuery.validator.format("Informe ao menos {0} caracteres."),rangelength:jQuery.validator.format("Informe um valor de {0} &agrave; {1} caracteres."),range:jQuery.validator.format("Informe um valor entre {0} e {1}."),max:jQuery.validator.format("Informe um valor menor igual &agrave; {0}."),min:jQuery.validator.format("Informe um valor maior igual &agrave; {0}.")})),window.baseValidation=function(e){setTimeout(function(){var t=e.find(".btn-group label input:radio");t.length&&t.hasClass("required error")&&t.parents(".btn-group").addClass("has-error");var t=e.find("label input:checkbox + .error");t.length&&t.addClass("pull-right left-expand");var t=e.find(".error + .chosen-container, .error + .chzn-container");t.length&&t.each(function(){jQuery(this).prev(".error").insertAfter(jQuery(this))})},100)}}),jQuery(window).load(function(){jQuery("input.required").length&&jQuery("input.required, select.required, textarea.required").each(function(){jQuery(this).rules("add",{required:!0})}),jQuery("input.field-email").length&&jQuery("input.field-email").each(function(){jQuery(this).rules("add",{email:!0})}),jQuery("input.field-url").length&&jQuery("input.field-url").each(function(){jQuery(this).rules("add",{url:!0})})});