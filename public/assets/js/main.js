$.fn.hasAttr = function(name) {
    return this.attr(name) !== undefined;
};

$(document).ready(function () {
    $(".nav-treeview .nav-link, .nav-link").each(function () {
        var location2 = window.location.protocol + '//' + window.location.host + window.location.pathname;
        var link = this.href;
        if(link == location2){
            $(this).addClass('active');
            $(this).parent().parent().parent().addClass('menu-is-opening menu-open');

        }
    });

    $('.delete-btn').click(function () {
        var res = confirm('Подтвердите действия');
        if(!res){
            return false;
        }
    });
    
	$('.editor').tinymce({ 
		theme: "silver", 
		plugins: ['advlist autolink lists link image charmap print preview hr anchor pagebreak', 'searchreplace wordcount visualblocks visualchars code fullscreen, insertdatetime media nonbreaking save table directionality', 'emoticons template paste textpattern codesample importcss help'], 
		toolbar1: "undo redo | styleselect formatselect fontselect fontsizeselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink image media code preview table | cut copy paste | forecolor backcolor | hr removeformat | subscript superscript | pagebreak codesample emoticons", 
		image_advtab: true, 
		image_title: true, 
		menubar: "edit insert format view table help", 
		toolbar_items_size: "small", 
		insertdatetime_dateformat: "%d.%m.%Y", 
		insertdatetime_formats: ["%d.%m.%Y", "%H:%M:%S"], 
		insertdatetime_timeformat: "%H:%M:%S", 
		valid_elements: "*[*]", 
		extended_valid_elements: "meta[*],i[*],noindex[*]", 
		file_picker_callback: function (callback, value, meta) { elFinderBrowser(callback, value, meta) }, 
		convert_urls: false, 
		relative_urls: false, 
		remove_script_host: false, 
		forced_root_block: "p", 
		entity_encoding: "", 
		verify_html: false, 
		valid_children: "+body[style|meta],+footer[meta],+a[div|h1|h2|h3|h4|h5|h6|p|#text]", 
		browser_spellcheck: true, 
		importcss_append: true, 
		schema: "html5", 
		allow_unsafe_link_target: false, 
		script_url: '/assets/plugins/wysiwyg/tinymce.min.js', 
		language: "ru", 
		language_url: "/assets/plugins/wysiwyg/langs/ru.js", 
		init_instance_callback: function(editor) { $('body').trigger('afterTinyMceInit', [editor]);}, 
		height: "340px",  
	}); 
  
    
    if ($("#formEdit").length) {
        mainFieldChecker.checkAll("formEdit"); 
        var $form = $('#id_content form[id ^= "formEdit"]');
        $form.on('keyup change paste blur', ':input[data-required]', function(e) { mainFieldChecker.check($(this)) });
    }


	$("body").on("click", ".admin-table .check-all", function(){
		let checker = $(this);

		$(".admin-table").find("tbody").find(".check-item").each(function(){
			if (checker.prop("checked")) {
				$(this).prop("checked", true);
			} else {
				$(this).prop("checked", false);
			}	
		});
	});

	
	$(".group-deleting").click(function() {
		let form = $(this).parents("form");
		Swal.fire({
			title: "Вы уверенны, что хотите удалить?",
			showCancelButton: true,
			confirmButtonText: 'Да',
			cancelButtonText: 'Отмена',
		}).then((result) => {
			if (result.isConfirmed) {
				$(".admin-table").find("[name='group_delete']").val(1);
				return form.submit();
			} else if (result.isDenied) {
				return false
			}
		})

		return false;
	});

	$(".deleting").click(function() {
		let form = $(this).parents("form"),
			$this = $(this);
		Swal.fire({
			title: "Вы уверенны, что хотите удалить?",
			showCancelButton: true,
			confirmButtonText: 'Да',
			cancelButtonText: 'Отмена',
		}).then((result) => {
			if (result.isConfirmed) {
				$this.parents("tr").find(".check-item").prop("checked", true);
				$(".admin-table").find("[name='group_delete']").val(1);
				return form.submit();
			} else if (result.isDenied) {
				return false
			}
		})

		return false;
	});

});

function elFinderBrowser (callback, value, meta) {
    tinymce.activeEditor.windowManager.openUrl({
        title: 'File Manager',
        url: '/elfinder/tinymce5',
        /**
         * On message will be triggered by the child window
         * 
         * @param dialogApi
         * @param details
         * @see https://www.tiny.cloud/docs/ui-components/urldialog/#configurationoptions
         */
        onMessage: function (dialogApi, details) {
            if (details.mceAction === 'fileSelected') {
                const file = details.data.file;
                
                // Make file info
                const info = file.name;
                
                // Provide file and text for the link dialog
                if (meta.filetype === 'file') {
                    callback(file.url, {text: info, title: info});
                }
                
                // Provide image and alt text for the image dialog
                if (meta.filetype === 'image') {
                    callback(file.url, {alt: info});
                }
                
                // Provide alternative source and posted for the media dialog
                if (meta.filetype === 'media') {
                    callback(file.url);
                }
                
                dialogApi.close();
            }
        }
    });
}

var Operation = {
	set: function(value) {
		$("[name='operation']").val(value);
	}
}

var i18n = new Array();
i18n['Minimum'] = 'Минимум';
i18n['one_letter'] = 'символ';
i18n['some_letter2'] = 'символа';
i18n['some_letter1'] = 'символов';
i18n['current_length'] = 'Текущая длина';
i18n['Maximum'] = 'Максимум';
i18n['wrong_value_format'] = 'Значение поля не соответствует формату.';
i18n['different_fields_value'] = 'Введенные пароли не совпадают.';

function fieldChecker()
{
	this._formFields = [];

	this.check = function($object) {

		var $form = $object.parents('form'),
			formId = $form.attr('id'),
			value = $object.val(),
			fieldId = $object.attr('id'),
			message = '',
			minlength = $object.data('min'),
			maxlength = $object.data('max'),
			reg = $object.data('reg'),
			equality = $object.data('equality'),
			required = $object.data('required');

		// Проверка на минимальную длину
		if (typeof minlength != 'undefined' && minlength && value.length < minlength)
		{
			message += i18n['Minimum'] + ' ' + minlength + ' '
				+ declension(minlength, i18n['one_letter'], i18n['some_letter2'], i18n['some_letter1']) + '. '
				+ i18n['current_length'] + ' ' + value.length + '. ';
		}

		// Проверка на максимальную длину
		if (typeof maxlength != 'undefined' && maxlength && value.length > maxlength)
		{
			message += i18n['Maximum'] + ' ' + maxlength + ' '
				+ declension(maxlength, i18n['one_letter'], i18n['some_letter2'], i18n['some_letter1']) + '. '
				+ i18n['current_length'] + ' ' + value.length + '. ';
		}

		// Проверка на регулярное выражение
		if (typeof reg != 'undefined' && reg.length && value.length)
		{
			var regEx = new RegExp(reg);

			if (!value.match(regEx))
			{
				var reg_message = $object.data('reg-message');

				message += typeof reg_message != 'undefined' && reg_message.length
					? reg_message
					: i18n['wrong_value_format'] + ' ';
			}
		}

		// Проверка на соответствие значений 2-х полей
		if (typeof equality != 'undefined' && equality.length)
		{
			// Пытаемся получить значение поля, которому должны соответствовать
			var $field2 = $form.find('#' + equality);

			if (value != $field2.val())
			{
				var equality_message = $object.data('equality-message');

				message += typeof equality_message != 'undefined' && equality_message.length
					? equality_message
					: i18n['different_fields_value'] + ' ';
			}
		}

		// Проверка на select
		var type = $object.get(0).tagName;

		if (typeof type != 'undefined' && type.toLowerCase() == 'select' && typeof required != 'undefined' )
		{
			if (value <= 0)
			{
				message += 'value is empty';
			}
		}

		// Insert message into the message div
		setTimeout(function() {
			//$object.nextAll("#" + fieldId + '_error').html(message);
			$("#" + fieldId + '_error', $form).html(message);
		}, 50);

		// Устанавливаем флаг несоответствия
		if (typeof this._formFields[formId] == 'undefined')
		{
			this._formFields[formId] = [];
		}

		this._formFields[formId][fieldId] = (message.length > 0);

		// if (this._formFields[formId][fieldId])
		// {
		// 	$object
		// 		.css('border-style', 'solid')
		// 		.css('border-width', '1px')
		// 		.css('border-color', '#ff1861')
		// 		.css('background-image', "url('/admin_panel/images/bullet_red.gif')")
		// 		.css('background-position', 'center right')
		// 		.css('background-repeat', 'no-repeat');
		// }
		// else
		// {
		// 	$object
		// 		.css('border-style', '')
		// 		.css('border-width', '')
		// 		.css('border-color', '')
		// 		.css('background-image', "url('/admin_panel/images/bullet_green.gif')")
		// 		.css('background-position', 'center right')
		// 		.css('background-repeat', 'no-repeat');
		// }

		this.checkFormButtons($form);

		return this;
	}

	this.checkFormButtons = function($form) {
		// Отображать контрольные элементы
		var formId = $form.attr('id'),
			disableButtons = false;

		for (itemIndex in this._formFields[formId])
		{
			// если есть хоть одно несоответствие - выключаем управляющие элементы
			if (this._formFields[formId][itemIndex])
			{
				disableButtons = true;
				break;
			}
		}

		//$.toogleInputsActive($form, disableButtons);
		$form.find('.card-footer button').attr('disabled', disableButtons);
	}

	this.removeField = function($object) {
		var fieldId = $object.attr('id'),
			$form = $object.parents('form'),
			formId = $form.attr('id');

		if (typeof this._formFields[formId] != 'undefined' && typeof this._formFields[formId][fieldId] != 'undefined')
		{
			this._formFields[formId][fieldId] = false;
		}

		this.checkFormButtons($form);
	}

	this.checkAll = function(formId) {

		$(" #" + formId + " :input[type !='submit']").each(function(){
            mainFieldChecker.check($(this))
		});
	}
}

function declension(number, nominative, genitive_singular, genitive_plural)
{
	var last_digit = number % 10;
	var last_two_digits = number % 100;

	if (last_digit == 1 && last_two_digits != 11)
	{
		var result = nominative;
	}
	else
	{
		var result = (last_digit == 2 && last_two_digits != 12) || (last_digit == 3 && last_two_digits != 13) || (last_digit == 4 && last_two_digits != 14)
			? genitive_singular
			: genitive_plural;
	}

	return result;
}

mainFieldChecker = new fieldChecker();

function toggleBlocks (div, div2) {
	$('#' + div).addClass("hidden");
	$('#' + div2).removeClass("hidden");
}

function confirmDelete(form, title = 'Вы уверенны, что хотите удалить?') {

	Swal.fire({
		title: title,
		showCancelButton: true,
		confirmButtonText: 'Да',
		cancelButtonText: 'Отмена',
	}).then((result) => {
		if (result.isConfirmed) {
			return form.submit();
		} else if (result.isDenied) {
			return false
		}
	})
}

function confirmCopy(elem, title = 'Вы уверенны, что хотите копировать?') {

	Swal.fire({
		title: title,
		showCancelButton: true,
		confirmButtonText: 'Да',
		cancelButtonText: 'Отмена',
	}).then((result) => {
		if (result.isConfirmed) {
			window.location.href = elem.attr("href");
		} else if (result.isDenied) {
			return false
		}
	})
}

var delay = (function(){
	var timer = 0;
	return function(callback, ms){
		clearTimeout (timer);
		timer = setTimeout(callback, ms);
	};
})();

var Spiner = {

    show: function() {
        $("body").append('<div class="spinner spinner-border spinner-border-custom-5 border-info"></div>');
    },

    hide: function() {
        $(".spinner").remove();
    }
}

$.datepicker.setDefaults({
	dateFormat: 'dd.mm.yy', 
	showAnim: "fold",
	showTodayButton: true, 
	showButtonPanel: true,
	showClear: true,
	monthNames: [ "Янв", "Фев", "Мар", "Апр", "Май", "Июнь", "Июль", "Авг", "Сен", "Окт", "Ноя", "Дек" ],
	dayNamesMin: [ "Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб" ],
	firstDay: 1,
	changeYear: true,
	onSelect: function(datetext) {
		
		var now = new Date();

		var h = now.getHours();
		h = (h < 10) ? ("0" + h) : h;

		var m = now.getMinutes();
		m = (m < 10) ? ("0" + m) : m;

		var s = now.getSeconds();
		s = (s < 10) ? ("0" + s) : s;

		datetext = datetext + " " + h + ":" + m + ":" + s;

		$(this).val(datetext);
	}
});