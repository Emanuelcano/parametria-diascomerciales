class Event {
	constructor(event) {
		this.id = event.id;
		this.type = event.type;
		this.run_date = event.run_date;
		this.run_hour = event.run_hour;
		this.run_weak_days = event.run_weak_days;
		this.run_day = event.run_day;
		this.run_month = event.run_month;
		this.enabled = event.enabled;
		this.origin = event.origin;
		this.params = JSON.parse(event.params);
		this.type_env = event.type_env;
		this.data_sms = event.data_sms;
	}

	getEvent() {
		return {
			'id': this.id,
			'disableUrl': base_url + 'api/evento/disable',
			'enableUrl': base_url + 'api/evento/enable',
			'deleteUrl': base_url + 'api/evento/delete',
			'frecuencia': this.getFrecuencia(),
			'momento': this.getMomento(),
		}
	}

	getMomento() {
		if (this.type === 'unique') {
			let m = moment(this.run_date);
			return 'El ' + m.format('DD/MM/YYYY') + ' a las ' + m.format('HH:mm');
		}
		let h = moment(this.run_hour, 'HH:mm:ss').format('HH:mm');
		if (this.type === 'day') {
			return 'A las ' + h;
		}
		if (this.type === 'weak') {
			return 'A las ' + h;
		}
		if (this.type === 'month') {
			return 'Dia ' + this.run_day + ' a las ' + h;
		}
		if (this.type === 'year') {
			return 'Dia ' + this.run_month + ' a las ' + h;
		}
	}

	getFrecuencia() {
		if (this.type === 'unique') {
			return 'Unico';
		}
		if (this.type === 'day') {
			return 'Todos los dias';
		}
		if (this.type === 'weak') {
			let w = new String(this.run_weak_days);
			let result = '';
			let weakDayCharacter = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];

			for (let i = 0; i < 7; i++) {
				if (w.charAt(i) === '0') {
					result += ' - ';
				} else {
					result += ' ' + weakDayCharacter[i];
				}
			}

			return 'Dias ' + result;
		}
		if (this.type === 'month') {
			return 'mensualmente';
		}
		if (this.type === 'year') {
			return 'Anualmente';
		}
	}

	delete() {
		fetch(base_url + 'api/evento/delete', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({
				'id': this.id
			})
		}).then(response => {
			return response.json();
		}).then(response => {

		});
	}

	disable() {
		fetch(base_url + 'api/evento/disable', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({
				'id': this.id
			})
		}).then(response => {
			return response.json();
		}).then(response => {
			if (response.status === 200) {
				this.enabled = '0';

			}
		});
	}

	enable() {
		fetch(base_url + 'api/evento/enable', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({
				'id': this.id
			})
		}).then(response => {
			return response.json();
		}).then(response => {
			if (response.status === 200) {
				this.enabled = '1';

			}
		});
	}

	info() {
		fetch(base_url + 'ApiDataMostrar', {
		// fetch(base_url + 'ApiGetWhatsappTemplateById', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({
				'id': this.id
			})
		}).then(response => {
			return response.json();
		}).then(response => {
			$("#loadingInfoTemplate").hide();
			$("#modal-info-text").show();
			$("#modal-info pre").html(response.data.msg_string);
		});

		$('#modal-info').modal();
	}

}

(function ($) {
	$.fn.cronograma = function (options) {
		const EVENT_TYPE_UNIQUE = 'unique';
		const EVENT_TYPE_REPETITIVE = 'repetitive';
		const REPETITIVE_PERIOD_DAY = 'day';
		const REPETITIVE_PERIOD_Weak = 'weak';
		const REPETITIVE_PERIOD_MONTH = 'month';
		const REPETITIVE_PERIOD_YEAR = 'year';

		let selectedEventType = EVENT_TYPE_UNIQUE;
		let selectedRepetitivePeriod = REPETITIVE_PERIOD_DAY;

		let events = [];

		let settings = $.extend({
			//default options
			title: 'titulo',
			color: 'primary',
			origin: '',
			events_render_target: '',
			endpoint: {
				url: '',
				method: 'GET',
				params: {},
			},
			event: {},
		}, options);

		if ($(this).data('endpoint') !== undefined) {
			settings.endpoint.url = $(this).data('endpoint');
		}
		if ($(this).data('method') !== undefined) {
			settings.endpoint.method = $(this).data('method');
		}
		if ($(this).data('origin') !== undefined) {
			settings.origin = $(this).data('origin');
		}
		if ($(this).data('params') !== undefined) {
			settings.endpoint.data = $(this).data('params');
		}
		
		$(this).html(getCronogramaHtml());

		if (settings.origin !== '') {
			getEvents();
		}

		$('.cronograma-container .iCheck').iCheck({
			checkboxClass: 'icheckbox_flat-green',
			radioClass: 'iradio_flat-green'
		});

		$('.cronograma-container .time-picker').datetimepicker({
			format: 'HH:mm',
		});

		$('.cronograma-container .day-picker').datetimepicker({
			format: 'DD',
		});

		$('.cronograma-container .daymonth-picker').datetimepicker({
			format: 'DD/MM',
		});

		$('.cronograma-container .datetime-picker').datetimepicker({
			// format: 'DD/MM/YYYY HH:mm',
			format: 'DD/MM/YYYY HH:mm',
		});

		function getCronogramaHtml() {
			let container = $('<div>');
			container.addClass('cronograma-container');
			container.append(getPanelHtml());
			return container;
		}

		function getPanelHtml() {
			let html = $('<div>');
			html.addClass('box');
			html.addClass('box-' + settings.color);
			html.append(getPanelHeaderHtml());
			html.append($('<div>').addClass('multi-purpose-container'));
			html.append(getPanelBodyHtml());
			html.append(getPanelFooterHtml());
			html.append(getPanelLoadingHtml());

			return html;
		}

		function getPanelLoadingHtml() {
			return `<div class="overlay" id="loadingCronograma" style="display: none"><i class="fa fa-refresh fa-spin"></i></div>`
		}
		

		function getPanelHeaderHtml() {
			return `<div class="box-header with-border"><h3 class="box-title">${settings.title}</h3></div>`;
		}

		function getPanelFooterHtml() {
			let html = createDiv('box-footer');
			let button = createButton('btn btn-success pull-right', 'Guardar', 'Guardar', () => {
				save()
			});

			html.append(button);
			return html;
		}

		function getPanelBodyHtml() {
			let separation = createDiv('row').append('&nbsp;');
			let html = $('<div>');
			html.addClass('box-body').attr('id', 'cronograma-main-container');
			html.append(getEventTypeHtml());
			html.append(separation);
			html.append(getContainerUnique());
			html.append(getcontainerRepetitive());
			return html;
		}

		function getEventTypeHtml() {

			let row = createDiv('row', 'eventTypeContainer');
			let col61 = createDiv('col-md-6');
			let col62 = createDiv('col-md-6');
			let uniqueEventBtn = createButton('btn btn-info active', 'uniqueEvent', 'Evento Unico', showUniqueEvent);
			let repetitiveEventBtn = createButton('btn btn-info', 'repetitiveEvent', 'Evento Repetitivo', showRepetitiveEvent);
			col61.append(uniqueEventBtn);
			col62.append(repetitiveEventBtn);
			row.append(col61);
			row.append(col62);

			return row;
		}

		function getContainerUnique() {
			let container = createDiv('text-center', 'containerUnique');
			let inputDate = createDateInput('uniqueDate', 'datetime-picker');
			container.html('Dia: ' + inputDate.prop('outerHTML'));
			return container;
		}

		function getcontainerRepetitive() {
			let container = createDiv('', 'containerRepetitive');

			let periodOptions = getPeriodSelector();
			let day = getContainerDay();
			let weak = getContainerWeak().hide();
			let month = getContainerMonth().hide();
			let year = getContainerYear().hide();

			container.append(periodOptions);
			container.append(day);
			container.append(weak);
			container.append(month);
			container.append(year);

			// container.append('repetitivo');
			return container;
		}

		function getPeriodSelector() {
			let row = createDiv('row');
			let div = createDiv('col-md-10 col-md-offset-1');
			let row2 = createDiv('row');
			let div2 = createDiv('col-md-3');
			let btnDay = createButton('btn btn-primary btn-period active', 'selectDay', 'Dia', () => {
				showPeriod('day')
			});
			let btnWeak = createButton('btn btn-primary btn-period', 'selectWeak', 'Semana', () => {
				showPeriod('weak')
			});
			let btnMonth = createButton('btn btn-primary btn-period', 'selectMonth', 'Mes', () => {
				showPeriod('month')
			});
			let btnYear = createButton('btn btn-primary btn-period', 'selectYear', 'Año', () => {
				showPeriod('year')
			});
			let containerDay = div2.clone().append(btnDay);
			let containerWeak = div2.clone().append(btnWeak);
			let containerMonth = div2.clone().append(btnMonth);
			let containerYear = div2.clone().append(btnYear);
			row2.append(containerDay);
			row2.append(containerWeak);
			row2.append(containerMonth);
			row2.append(containerYear);
			div.append(row2);
			row.append(div);
			return row;
		}

		function getContainerDay() {
			let container = createDiv('', 'containerDay');
			let row = createDiv('row');
			let div = createDiv('col-md-12 text-center');
			let title = $('<h4>DIA</h4>');
			let input = createDateInput('horaDiaria', 'time-picker');

			div.append(title);
			div.append('Diariamente a las ' + input.prop('outerHTML'));
			row.append(div);
			container.append(row);
			return container;
		}

		function getContainerWeak() {
			let container = createDiv('', 'containerWeak');
			let row = createDiv('row');
			let div = createDiv('col-md-12 text-center');
			let title = $('<h4>SEMANA</h4>');

			//Weak Character Container
			let divWeakCharDay = createDiv('center-block', 'weakCharDays');
			let divContainerWeakDay = createDiv('text-center', 'divContainerWeakDay');
			divWeakCharDay.append(divContainerWeakDay.clone().append(createLabel('semana-l', 'L')));
			divWeakCharDay.append(divContainerWeakDay.clone().append(createLabel('semana-m', 'M')));
			divWeakCharDay.append(divContainerWeakDay.clone().append(createLabel('semana-x', 'X')));
			divWeakCharDay.append(divContainerWeakDay.clone().append(createLabel('semana-j', 'J')));
			divWeakCharDay.append(divContainerWeakDay.clone().append(createLabel('semana-v', 'V')));
			divWeakCharDay.append(divContainerWeakDay.clone().append(createLabel('semana-s', 'S')));
			divWeakCharDay.append(divContainerWeakDay.clone().append(createLabel('semana-d', 'D')));

			//weak checkboxes
			let divWeakCheckboxes = createDiv('center-block', 'weaksCheckboxesDiv');
			divWeakCheckboxes.append(divContainerWeakDay.clone().append(createCheckInput('semana-l', 'L', 'iCheck', false)));
			divWeakCheckboxes.append(divContainerWeakDay.clone().append(createCheckInput('semana-m', 'M', 'iCheck', false)));
			divWeakCheckboxes.append(divContainerWeakDay.clone().append(createCheckInput('semana-x', 'X', 'iCheck', false)));
			divWeakCheckboxes.append(divContainerWeakDay.clone().append(createCheckInput('semana-j', 'J', 'iCheck', false)));
			divWeakCheckboxes.append(divContainerWeakDay.clone().append(createCheckInput('semana-v', 'V', 'iCheck', false)));
			divWeakCheckboxes.append(divContainerWeakDay.clone().append(createCheckInput('semana-s', 'S', 'iCheck', false)));
			divWeakCheckboxes.append(divContainerWeakDay.clone().append(createCheckInput('semana-d', 'D', 'iCheck', false)));

			//hour
			let divHour = createDiv('col-md-12 text-center', 'divHours');
			divHour.append('A las ' + createDateInput('horaSemana', 'time-picker').prop('outerHTML'));

			div.append(title, divWeakCharDay, divWeakCheckboxes, divHour);
			row.append(div);
			container.append(row);
			return container;

		}

		function getContainerMonth() {
			let container = createDiv('', 'containerMonth');
			let row = createDiv('row');
			let div = createDiv('col-md-12 text-center');
			let title = $('<h4>MES</h4>');
			let time = createDateInput('timeMonth', 'time-picker');
			let day = createDateInput('dayMonth', 'day-picker');


			div.append(title);
			div.append('El dia ' + day.prop('outerHTML') + ' a las ' + time.prop('outerHTML'));
			row.append(div);
			container.append(row);
			return container;
		}

		function getContainerYear() {
			let container = createDiv('', 'containerYear');
			let row = createDiv('row');
			let div = createDiv('col-md-12 text-center');
			let title = $('<h4>AÑO</h4>');
			let time = createDateInput('timeYear', 'time-picker');
			let day = createDateInput('dateYear', 'daymonth-picker');


			div.append(title);
			div.append('Cada ' + day.prop('outerHTML') + ' a las ' + time.prop('outerHTML'));
			row.append(div);
			container.append(row);
			return container;
		}

		function createLabel(forId, text) {
			let label = $('<label>');
			label.attr('for', forId);
			label.text(text);
			label.addClass('weakCharacterLabel');
			return label;
		}


		function showPeriod(period) {
			if (period === 'day') {
				selectedRepetitivePeriod = REPETITIVE_PERIOD_DAY;
				$("#selectDay").addClass('active');
				$("#selectWeak").removeClass('active');
				$("#selectMonth").removeClass('active');
				$("#selectYear").removeClass('active');

				$('#containerDay').show();
				$('#containerWeak').hide();
				$('#containerMonth').hide();
				$('#containerYear').hide();
			} else if (period === 'weak') {
				selectedRepetitivePeriod = REPETITIVE_PERIOD_Weak;
				$("#selectDay").removeClass('active');
				$("#selectWeak").addClass('active');
				$("#selectMonth").removeClass('active');
				$("#selectYear").removeClass('active');

				$('#containerDay').hide();
				$('#containerWeak').show();
				$('#containerMonth').hide();
				$('#containerYear').hide();
			} else if (period === 'month') {
				selectedRepetitivePeriod = REPETITIVE_PERIOD_MONTH;
				$("#selectDay").removeClass('active');
				$("#selectWeak").removeClass('active');
				$("#selectMonth").addClass('active');
				$("#selectYear").removeClass('active');

				$('#containerDay').hide();
				$('#containerWeak').hide();
				$('#containerMonth').show();
				$('#containerYear').hide();
			} else if (period === 'year') {
				selectedRepetitivePeriod = REPETITIVE_PERIOD_YEAR;
				$("#selectDay").removeClass('active');
				$("#selectWeak").removeClass('active');
				$("#selectMonth").removeClass('active');
				$("#selectYear").addClass('active');

				$('#containerDay').hide();
				$('#containerWeak').hide();
				$('#containerMonth').hide();
				$('#containerYear').show();
			}
		}


		function createButton(classes, id, text, click) {
			let button = $('<button>');
			button.addClass(classes);
			button.attr('id', id);
			button.attr('type', 'button');
			button.text(text);
			button.click(click);
			return button;
		}

		function createIconButton(classes, id, icon, click) {
			let button = $('<button>');
			let i = $('<i>');
			i.addClass(icon);
			i.addClass('fa');
			button.addClass(classes);
			button.attr('id', id);
			button.attr('type', 'button');
			button.click(click);
			button.append(i);
			return button;
		}

		function createDiv(classes, id) {
			let div = $('<div>');
			div.addClass(classes);
			div.attr('id', id);
			return div;
		}

		function createCheckInput(id, name, className, checked) {
			let input = $('<input>');
			input.attr('id', id);
			input.attr('name', name);
			input.addClass(className);
			input.attr('type', 'checkbox');
			input.prop('checked', checked);
			return input;
		}


		function createDateInput(id, className) {
			let input = $('<input>');
			input.attr('id', id);
			input.addClass(className);
			input.attr('type', 'text');
			return input;
		}

		function showUniqueEvent() {
			selectedEventType = EVENT_TYPE_UNIQUE;
			$('#uniqueEvent').addClass('active');
			$('#repetitiveEvent').removeClass('active');
			$('#containerUnique').show();
			$('#containerRepetitive').hide();
		}

		function showRepetitiveEvent() {
			selectedEventType = EVENT_TYPE_REPETITIVE;
			$('#uniqueEvent').removeClass('active');
			$('#repetitiveEvent').addClass('active');
			$('#containerUnique').hide();
			$('#containerRepetitive').show();
		}

		function save() {
			if ($(".select-template").val() == "") {
				Swal.fire('Se debe seleccionar un template', '', 'warning');
			}else{

				let guardarBtn = $("#Guardar");
				let icon = $('<i>');
				
				//reset settings;
				settings.event = {};
				let result = false;
				if (selectedEventType === EVENT_TYPE_UNIQUE) {
					result = validateAndSetUniqueEvent();
				} else {
					if (selectedRepetitivePeriod === REPETITIVE_PERIOD_DAY) {
						result = validateAndSetRepetitiveDay();
					}
					if (selectedRepetitivePeriod === REPETITIVE_PERIOD_Weak) {
						result = validateAndSetRepetitiveWeak();
					}
					if (selectedRepetitivePeriod === REPETITIVE_PERIOD_MONTH) {
						result = validateAndSetRepetitiveMonth();
					}
					if (selectedRepetitivePeriod === REPETITIVE_PERIOD_YEAR) {
						result = validateAndSetRepetitiveYear();
					}
				}
				
				if (!result) {
					return;
				}
				
				icon.addClass('fa fa-spinner fa-spin');
				guardarBtn.html(icon);
				guardarBtn.prop('disabled', true);
	
				if (settings.endpoint.params.type_env == "WSP") {
					settings.endpoint.params.templateId = $(".select-template").val()
				}
	
				let requestBody = {
					"endpoint": settings.endpoint,
					"moment": settings.event,
					"origin": settings.origin
				};
				// console.log(requestBody);
	
				fetch(base_url + 'api/evento/save', {
					method: settings.endpoint.method,
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify(requestBody)
				}).then(function (response) {
					return response.json();
				}).then(function (response) {
					if (response.status === 201) {
						// console.log('OK');
					} else {
						console.log(response);
						alert('Error');
						// console.log(response);
					}
					guardarBtn.prop('disabled', false);
					guardarBtn.html('Guardar');
					getEvents();
				});
			}
		}

		function validateAndSetUniqueEvent() {
			let date = $('#uniqueDate').val();
			if (date !== '') {
				let formatedDate = moment(date, 'DD/MM/YYYY HH:mm').format('YYYY-MM-DD HH:mm');
				settings.event.type = EVENT_TYPE_UNIQUE;
				settings.event.datetime = formatedDate;
				return true;
			} else {
				alert('Debe seleccionar una fecha');
				return false;
			}
		}

		function validateAndSetRepetitiveDay() {
			let hour = $('#horaDiaria').val();
			if (hour === '') {
				alert('Debe seleccionar una hora');
				return false;
			}
			settings.event.type = REPETITIVE_PERIOD_DAY;
			settings.event.hour = hour;

			return true;
		}

		function validateAndSetRepetitiveWeak() {
			let weak = formatWeak();
			if (weak === '0000000') {
				alert('Debe seleccionar al menos un día de la semana');
				return false;
			}
			let hour = $('#horaSemana').val();
			if (hour === '') {
				alert('Debe seleccionar una hora');
				return false
			}

			settings.event.type = REPETITIVE_PERIOD_Weak;
			settings.event.weak = formatWeak();
			settings.event.hour = hour;
			return true;
		}

		function validateAndSetRepetitiveMonth() {
			let month = $('#dayMonth').val();
			if (month === '') {
				alert('Debe seleccionar un día del mes');
				return false;
			}
			let hour = $('#timeMonth').val();
			if (hour === '') {
				alert('Debe seleccionar una hora');
				return false;
			}

			settings.event.type = REPETITIVE_PERIOD_MONTH;
			settings.event.day = $("#dayMonth").val();
			settings.event.hour = $("#timeMonth").val();

			return true;
		}

		function validateAndSetRepetitiveYear() {
			let date = $('#dateYear').val();
			if (date === '') {
				alert('Debe seleccionar una fecha');
				return false;
			}
			let hour = $('#timeYear').val();
			if (hour === '') {
				alert('Debe seleccionar una hora');
				return false;
			}

			settings.event.type = REPETITIVE_PERIOD_YEAR;
			settings.event.date = moment(date, 'DD/MM').format('MM/DD');
			settings.event.hour = $("#timeYear").val();

			return true;
		}

		function formatWeak() {
			let weak = [
				$('#semana-l').prop('checked'),
				$('#semana-m').prop('checked'),
				$('#semana-x').prop('checked'),
				$('#semana-j').prop('checked'),
				$('#semana-v').prop('checked'),
				$('#semana-s').prop('checked'),
				$('#semana-d').prop('checked')
			];
			let result = '';
			weak.forEach(function (value) {
				if (value) {
					result += '1';
				} else {
					result += '0';
				}
			});

			return result;
		}
		
		function getEvents() {
			var _this = this;
			$('#loadingCronograma').show();
			$('#loadingListCronograma').show();
			
			events = [];
			fetch(base_url + 'api/evento/getAllOrigin', {
				method: settings.endpoint.method,
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({'origin': settings.origin})
			}).then(function (response) {
				return response.json();
			}).then(function (response) {
				if (response.status === 200) {
					response.data.forEach(function (event) {
						let evento = new Event(event);
						events.push(evento);
					});
				} else {
					alert('Error');
					console.log(response);
				}
				$('#loadingCronograma').hide();
				$('#loadingListCronograma').hide();
				if (settings.events_render_target !== '') {
					renderEventList();
				}
			});
		}
		
		function renderEventList() {
			let target = $(settings.events_render_target);
			let html = $('<div>');
			html.addClass('box');
			html.addClass('box-' + settings.color);
			let header = getPanelHeaderListHtml();
			let body = getPanelBodyListHtml();
			// let footer = getPanelFooterListHtml();
			let loading = getPanelLoadingListHtml();
			
			html.append(header, body, loading);
			target.html(html);
		};

		function getPanelLoadingListHtml() {
			return `<div class="overlay" id="loadingListCronograma" style="display: none"><i class="fa fa-refresh fa-spin"></i></div>`
		}


		function getPanelHeaderListHtml() {
			let header = createDiv('box-header with-border');
			let title = $('<h3>').addClass('box-title').text('Eventos Programados');
			let tools = $('<div>').addClass('box-tools pull-right');
			let button = $('<button>').addClass('btn btn-box-tool').attr('id', 'refreshEventListBtn').attr('type','button').click( getEvents );
			let icon = $('<i>').addClass('fa fa-refresh');
			button.append(icon);
			tools.append(button);
			header.append(title);
			header.append(tools);
			return header;
		}

		function getPanelFooterListHtml() {
			let html = createDiv('box-footer');
			let button = createButton('btn btn-success pull-right', 'Guardar', 'Guardar', () => {
				save()
			});

			html.append(button);
			return html;
		}

		function getPanelBodyListHtml() {
			let table = $('<table>').addClass('table table-condensed').attr('id', 'table-events');
			let thead = $('<thead>');
			let tbody = $('<tbody>');
			let trHead = $('<tr>');
			let thFrecuencia = $('<th>').text('Frecuencia');
			let thMomento = $('<th>').text('Momento');
			let thTemplate = $('<th>').text('Template Id').attr('id', 'id_template');
			let thAcciones = $('<th>').text('Acciones').attr('id', 'action-column');
			trHead.append(thFrecuencia, thMomento, thTemplate, thAcciones);
			thead.append(trHead);

			events.forEach(function (valor) {
				let tr = $('<tr>');
				let td = $('<td>');

				let disableBtn = '';
				if(valor.enabled === '0') {
					tr.addClass('disabled');
					disableBtn = createIconButton('btn btn-success btn-xs', 'disBtn-'+valor.id, 'fa-ban', () => {
						valor.enable();
						let icon = $('<i>').addClass('fa fa-spinner fa-spin');
						$('#disBtn-'+valor.id).html(icon)
						getEvents();
					});
				} else {
					disableBtn = createIconButton('btn btn-warning btn-xs', 'disBtn-'+valor.id, 'fa-ban', () => {
						valor.disable();
						let icon = $('<i>').addClass('fa fa-spinner fa-spin');
						$('#disBtn-'+valor.id).html(icon)
						getEvents();
					}); 
				}

				let infoBtn = createIconButton('btn btn-info  btn-xs', 'infoBtn-'+valor.id, 'fa-eye', () => {
					$("#loadingInfoTemplate").show();
					$("#modal-info-text").hide();
					valor.info();
				});
				
				let deleteBtn = createIconButton('btn btn-danger btn-xs', 'delbtn-'+valor.id, 'fa-trash', () => {
					valor.delete();
					let icon = $('<i>').addClass('fa fa-spinner fa-spin');
					$('#delbtn-'+valor.id).html(icon)
					getEvents();
				});
				let aux = $(`<div id="acciones_btn" style="display:flex;">`);
			
				tr.append( td.clone().html(valor.getFrecuencia()));
				tr.append( td.clone().html(valor.getMomento()));
				if (valor.type_env != "WSP") {
					thTemplate.text("Mensaje");
					if(valor.data_sms.length >= 50){
						let extraida = valor.data_sms.substring(0, 50);
						tr.append( td.clone().html(extraida+"...").attr("style", "font-size:12px"));
					}else{
						tr.append( td.clone().html(valor.data_sms).attr("style", "font-size:12px"));
					}
					let id_campania = valor.params.idCampania;
					let id_mensaje = valor.params.templateId;
					let ExportBtn = createIconButton('btn btn-success btn-xs ExportBtn', 'ExportBtn-'+valor.id, 'fa-file', () => {
						getExport(id_campania, id_mensaje, valor.id);
					}); 
					aux.prepend(ExportBtn);
				}else{
					tr.append( td.clone().html(valor.params.templateId));
				}
					
				
				
				
				
				aux.append(infoBtn);
				aux.append(disableBtn);
				aux.append(deleteBtn);
				tr.append( td.clone().addClass('action-buttons-cell').html(aux));
				tbody.append(tr);
			});
			
			table.append(thead, tbody);
			
			return table;
		}
		
		this.renderList = () => {
			getEvents();
		}
		
		this.addParameter = function(key, value) {
			settings.endpoint.params[key] = value;
		}

		this.getEventos = () => {
			let result = [];
			events.forEach(function (valor) {
				result.push(valor.getEvent());
			});
			return result;
		}

		return this;
	}

	function getExport(id_campania, id_mensaje, id_evento) {
		if($("#destinatario_slack").val() == ""){
			Swal.fire('Debe agregar un destinatario a notificar', '', 'warning')
			$('#destinatario_slack').focus();
		}else{
			window.open(base_url + "api/ApiSupervisores/generate_csv/"+id_mensaje+"/"+id_campania+"/"+id_evento, "_blank");
		}
	}
}(jQuery));
