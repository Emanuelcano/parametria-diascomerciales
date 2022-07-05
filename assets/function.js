var auxTabla ;
var formatNumber = {
	separador: ".", // separador para los miles
	sepDecimal: ",", // separador para los decimales
	formatear: function(num) {
		num += "";
		var splitStr = num.split(".");
		var splitLeft = splitStr[0];
		var splitRight = splitStr.length > 1 ? this.sepDecimal + splitStr[1] : "";
		var regx = /(\d+)(\d{3})/;
		while (regx.test(splitLeft)) {
			splitLeft = splitLeft.replace(regx, "$1" + this.separador + "$2");
		}
		return this.simbol + splitLeft + splitRight;
	},
	new: function(num, simbol) {
		this.simbol = simbol || "";
		return this.formatear(num);
	}
};

//Object setup
var modalOptions = {
	general: {
		id: "",
		callback: ""
	},
	header: {
		title: ""
	},
	body: {
		alert: {
			type: "",
			message: []
		},
		pContent: ""
	},
	footer: {}
};

function tiempoRespuesta(tiempo) {
	let timerInterval;
	Swal.fire({
		title: "Tiempo respuesta!",
		html: "Cierra en <b></b> mili segundos.",
		timer: tiempo * 1000,

		onBeforeOpen: () => {
			Swal.showLoading();
			timerInterval = setInterval(() => {
				const content = Swal.getContent();
				if (content) {
					const b = content.querySelector("b");
					if (b) {
						b.textContent = Swal.getTimerLeft();
					}
				}
			}, 100);
		},
		onClose: () => {
			clearInterval(timerInterval);
		}
	}).then(result => {
		/* Read more about handling dismissals below */
		if (result.dismiss === Swal.DismissReason.timer) {
			console.log("I was closed by the timer");
		}
	});
}

function validarSession(response) {
	cabecera = response.substr(2, 12);
	if (cabecera == "DOCTYPE html") {
		window.location.assign($("input#base_url").val());
	}
}

function seleccionarTipo(idmodulo) {
	var ajax = nuevoAjax();
	ajax.open("POST", "lib/funciones.php", true);
	ajax.setRequestHeader(
		"Content-Type",
		"application/x-www-form-urlencoded; charset=ISO-8859-1"
	);
	ajax.onreadystatechange = function() {
		if (ajax.readyState == 4) {
			document.getElementById("celda_tipo_documento").innerHTML =
				ajax.responseText;
			document.getElementById("divCargando").style.display = "none";
		}
	};
	ajax.send("idmodulo=" + idmodulo + "&ejecutar=seleccionarTipo");
}

function mostrarMensajes(tipo, texto) {
	document.getElementById("cuadroMensajes").style.display = "block";
	if (tipo == "exito") {
		document.getElementById("cuadroMensajes").innerHTML =
			"<table align='center' style='margin-top:0px; background-color:#00CCFF; border:#003366 solid 2px; -moz-border-radius: 8px;'><tr><td><img src='imagenes/validar.png'></td><td style='color:#FFF; font-family:Arial, Helvetica, sans-serif; font-size:12px'>" +
			texto +
			"</td></tr></table>";
	} else {
		document.getElementById("cuadroMensajes").innerHTML =
			"<table align='center' style='margin-top:0px; background-color:#FFFF66; border:#FFCC00 solid 2px; -moz-border-radius: 8px;'><tr><td><img src='imagenes/reject.gif'></td><td style='color:#000; font-family:Arial, Helvetica, sans-serif; font-size:12px'>" +
			texto +
			"</td></tr></table>";
	}
	setTimeout(
		"document.getElementById('cuadroMensajes').style.display='none'",
		8000
	);
}

function setCheckBox(form, name, value) {
	elts = document.forms[form].elements;
	if (typeof elts != "undefined") {
		for (var i = 0; i < elts.length; i++) {
			if (elts[i].name.indexOf(name) != -1) elts[i].checked = value;
		}
	}
}

function cleanNullField(form, name) {
	elts = document.forms[form].elements;
	if (typeof elts != "undefined") {
		for (var i = 0; i < elts.length; i++) {
			if (elts[i].name.indexOf(name) != -1) elts[i].value = "";
		}
	}
}

function setTableAction(form, index, action) {
	document.forms[form].elements["modify[" + index + "]"].checked = true;
	document.forms[form].action.value = action;
	document.forms[form].submit();
}

function afficheCalque(calque) {
	if (document.getElementById) {
		document.getElementById(calque).style.visibility = "visible";
	} else {
		eval(
			layerRef + '["' + calque + '"]' + styleRef + '.visibility = "visible"'
		);
	}
}

function cacheCalque(calque) {
	if (document.getElementById) {
		document.getElementById(calque).style.visibility = "hidden";
	} else {
		eval(layerRef + '["' + calque + '"]' + styleRef + '.visibility = "hidden"');
	}
}

function ftype() {
	if (document.functprop.FunctType.selectedIndex == 0) {
		cacheCalque("Pfinal1");
		cacheCalque("Pfinal2");
	} else {
		afficheCalque("Pfinal1");
		afficheCalque("Pfinal2");
	}
}

function checkPath() {
	if (document.database.dbRealpath.value) {
		document.database.dbpath.value = document.database.dbRealpath.value;
		document.database.dbRealpath.value = "";
	}
}

var tabRow = new Array();
function setRowColor(
	RowObj,
	numRow,
	Action,
	OrigColor,
	OverColor,
	ClickColor,
	bUseClassName
) {
	if (typeof document.getElementsByTagName != "undefined")
		TheCells = RowObj.getElementsByTagName("td");
	else return false;
	if (!in_array(numRow, tabRow)) {
		if (Action == "over") setColor = OverColor;
		else if (Action == "out") setColor = OrigColor;
		else if (Action == "click") {
			setColor = ClickColor;
			tabRow.push(numRow);
		}
	} else if (Action == "click") {
		tabIndex = in_array(numRow, tabRow);
		if (tabIndex > 0) {
			tabRow[tabIndex - 1] = "";
			setColor = OrigColor;
		}
	} else return;
	for (i = 0; i < TheCells.length; i++)
		if (bUseClassName) {
			if (bUseClassName && TheCells[i].className != setColor)
				TheCells[i].className = setColor;
		} else if (TheCells[i].style.backgroundColor != setColor)
			TheCells[i].style.backgroundColor = setColor;
	return;
}

function in_array(needle, haystack) {
	for (i = 0; i < haystack.length; i++) if (haystack[i] == needle) return i + 1;
	return false;
}

function insertColumn() {
	sourceSel = document.sql.columnTable;
	destSQL = document.sql.DisplayQuery;
	var i = sourceSel.options.length;
	var first = true;
	var stringToDisplay = "";
	while (i >= 0) {
		if (sourceSel.options[i] && sourceSel.options[i].selected) {
			if (first) {
				stringOut = "";
				first = false;
			} else {
				stringOut = ", ";
			}
			stringToDisplay += stringOut + sourceSel.options[i].value;
			sourceSel.options[i].selected = false;
		}
		i--;
	}
	if (document.selection) {
		destSQL.focus();
		selection = document.selection.createRange();
		if (selection.findText("*")) selection.text = stringToDisplay;
		else if (selection.findText(" FROM"))
			selection.text = ", " + stringToDisplay + " FROM";
		else selection.text = stringToDisplay;
		selection.empty();
		document.sql.insertButton.focus();
	} else if (destSQL.selectionStart || destSQL.selectionStart == "0") {
		destSQL.value =
			destSQL.value.substring(0, destSQL.selectionStart) +
			stringToDisplay +
			destSQL.value.substring(destSQL.selectionEnd, destSQL.value.length);
	} else {
		destSQL += stringToDisplay;
	}
}

function clickIE4() {
	if (event.button == 2) {
		return false;
	}
}

function clickNS4(e) {
	if (document.layers || (document.getElementById && !document.all)) {
		if (e.which == 2 || e.which == 3) {
			return false;
		}
	}
}
if (document.layers) {
	document.captureEvents(Event.MOUSEDOWN);
	document.onmousedown = clickNS4;
} else if (document.all && !document.getElementById) {
	document.onmousedown = clickIE4;
}

document.oncontextmenu = new Function("return false");

function abreVentana(pagina) {
	var ajax = nuevoAjax();
	ajax.open("POST", "funciones/contador_ventanas_ajax.php", true);
	ajax.setRequestHeader(
		"Content-Type",
		"application/x-www-form-urlencoded; charset=ISO-8859-1"
	);
	ajax.onreadystatechange = function() {
		if (ajax.readyState == 4) {
			eval(
				"ventana" +
					ajax.responseText +
					"=window.open(pagina,'ventana'+ajax.responseText,'scrollbars = yes, resizabled = no, width = 900, height=500')"
			);
		}
	};
	ajax.send("ejecutar=sumar");
}

function validar_minutos_transcurridos() {
	var ajax = nuevoAjax();
	ajax.open("POST", "funciones/validar_minutos_transcurridos_ajax.php", true);
	ajax.setRequestHeader(
		"Content-Type",
		"application/x-www-form-urlencoded; charset=ISO-8859-1"
	);
	ajax.onreadystatechange = function() {
		if (ajax.readyState == 4) {
			if (ajax.responseText == "mayor") {
				cerrarSession("tiempo");
				return false;
			}
		}
	};
	ajax.send(null);
}

// SE LE PASA EL NOMBRE DEL CAMPO QUE EN ESTE CASO SOLO SE CONCATENA PORQUE VARIOS SE LLAMAN IGUAL Y SE LE ASA EL EVENTO event
function validarFecha(campo, e) {
	var valor = document.getElementById("fecha_factura" + campo).value;
	var tamanio = valor.length;
	var tcl = document.all ? e.keyCode : e.which;
	var mydate = new Date();
	var anio = mydate.getFullYear();
	var dia = mydate.getDate();
	var mes = mydate.getMonth() + 1;
	var checkOK = "0123456789-";
	var linea = "";
	var arrayMes = new Array();
	arrayMes[1] = 31;
	if (anio % 4 == 0) arrayMes[2] = 29;
	else arrayMes[2] = 28;
	arrayMes[3] = 31;
	arrayMes[4] = 30;
	arrayMes[5] = 31;
	arrayMes[6] = 30;
	arrayMes[7] = 31;
	arrayMes[8] = 31;
	arrayMes[9] = 30;
	arrayMes[10] = 31;
	arrayMes[11] = 30;
	arrayMes[12] = 31;
	if (tcl != 8) {
		if (
			(tcl >= 48 && tcl <= 57) ||
			(tcl >= 96 && tcl <= 105) ||
			tcl == 8 ||
			tcl == 0 ||
			tcl == 109
		) {
			var bloques = valor.split("-");
			if (tamanio == 4) {
				if (parseInt(bloques[0]) > anio) {
					alert("Disculpe el A&ntilde;O no puede mayor al actual");
					var newStr = valor.substring(0, valor.length - 4);
					document.getElementById("fecha_factura" + campo).value = newStr;
				} else {
					document.getElementById("fecha_factura" + campo).value = valor + "-";
				}
			} else if (tamanio == 7) {
				if (parseInt(bloques[0]) == anio && parseInt(bloques[1]) > mes) {
					alert("Disculpe el MES no puede ser mayor al actual");
					var newStr = valor.substring(0, valor.length - 2);
					document.getElementById("fecha_factura" + campo).value = newStr;
				} else {
					document.getElementById("fecha_factura" + campo).value = valor + "-";
				}
			} else if (tamanio == 10) {
				if (
					parseInt(bloques[0]) == anio &&
					parseInt(bloques[1]) == mes &&
					parseInt(bloques[2]) > dia
				) {
					alert("Disculpe el DIA no puede ser mayor al actual");
					var newStr = valor.substring(0, valor.length - 2);
					document.getElementById("fecha_factura" + campo).value = newStr;
				} else {
					var m = parseInt(bloques[1]);
					var d = parseInt(bloques[2]);
					if (d > arrayMes[m]) {
						alert("Disculpe el DIA es incorrecto");
						var newStr = valor.substring(0, valor.length - 2);
						document.getElementById("fecha_factura" + campo).value = newStr;
					}
				}
			}
		} else {
			//alert("no es numero");
			if (valor.length != 0) {
				for (i = 0; i < tamanio; i++) {
					ch = valor.charAt(i);
					for (j = 0; j < checkOK.length; j++)
						if (ch == checkOK.charAt(j)) linea = linea + ch;
				}
				document.getElementById("fecha_factura" + campo).value = linea;
			}
		}
	}
}

function soloNumeros(e) {
	var key = window.Event ? e.which : e.keyCode;
	return (key >= 48 && key <= 57) || key == 8 || key == 46 || key == 13;
}

function formatoNumeroo(idcampo, campoOculto) {
	alert(idcampo);
	var res = document.getElementById(idcampo).value;

	document.getElementById(campoOculto).value = res;
	if (
		document.getElementById(idcampo).value >= 0 &&
		document.getElementById(idcampo).value <= 99999999999
	) {
		resultado = parseFloat(res)
			.toFixed(2)
			.toString();
		resultado = resultado.split(".");
		var cadena = "";
		cont = 1;
		for (m = resultado[0].length - 1; m >= 0; m--) {
			cadena = resultado[0].charAt(m) + cadena;
			cont % 3 == 0 && m > 0 ? (cadena = "." + cadena) : (cadena = cadena);
			cont == 3 ? (cont = 1) : cont++;
		}
		document.getElementById(idcampo).value = cadena + "," + resultado[1];
	} else if (document.getElementById(idcampo).value == undefined) {
		document.getElementById(idcampo).value = "0";
		//alert ("Debes indicar valores n&uacute;mericos en el campo "+idcampo);
		document.getElementById(idcampo).focus();
	} else {
		document.getElementById(idcampo).value = "0";
		//alert ("Debes indicar valores n&uacute;mericos en el campo "+idcampo);
		document.getElementById(idcampo).focus();
	}
}

function addslashes(str) {
	str = str.replace(/\\/g, "\\\\");
	str = str.replace(/\'/g, "\\'");
	str = str.replace(/\"/g, '\\"');
	str = str.replace(/\0/g, "\\0");
	return str;
}

function TablaPaginada(
	nombreTabla,
	colOrdenar,
	fOrdenar,
	colOrdenar2 = "",
	fOrdenar2 = "",
	ajax = null,
	columns = null,
	columnDefs = null,
	options_dt = null,
	createdRow = null,
	pageLength = null,
	footerCallback = null,
	extras = null
) {
	var tabla = "#" + nombreTabla;
	var columnaOrdenar = colOrdenar;
	var formaOrdenar = fOrdenar;

	if (colOrdenar2 == "") {
		var columnaOrdenar2 = colOrdenar;
		var formaOrdenar2 = fOrdenar;
	} else {
		var columnaOrdenar2 = colOrdenar2;
		var formaOrdenar2 = fOrdenar2;
	}
	
	//alert(columnaOrdenar2+formaOrdenar2)

	let options = {
		
		lengthMenu: [
			[5, 10, 15, 25, 50],
			[5, 10, 15, 25, 50],
			
		],
	
		//"aaSorting": [[columnaOrdenar,formaOrdenar], [columnaOrdenar2,formaOrdenar2]],
		order: [],
		language: {
			
			sProcessing: "Procesando...",
			sLengthMenu: "Mostrar _MENU_ registros",
			sZeroRecords: "No se encontraron resultados",
			sEmptyTable: "Ningún dato disponible en esta tabla",
			sInfo: "Del _START_ al _END_ de un total de _TOTAL_ reg.",
			sInfoEmpty: "0 registros",
			sInfoFiltered: "(filtrado de _MAX_ reg.)",
			sInfoPostFix: "",
			sSearch: "Buscar:",
			sUrl: "",
			sInfoThousands: ",",
			sLoadingRecords: "Cargando...",
			oPaginate: {
				sFirst: "Primero",
				sLast: "Último",
				sNext: "Sig",
				sPrevious: "Ant"
			},
			oAria: {
				sSortAscending:
					": Activar para ordenar la columna de manera ascendente",
				sSortDescending:
					": Activar para ordenar la columna de manera descendente"
			}
		}
	};
	
	if (ajax !== null) {
		options.ajax = ajax;
	}
	if (columns !== null) {
		options.columns = columns;
	}
	if (columnDefs !== null) {
		options.columnDefs = columnDefs;
	}

	if (options_dt !== null) {
		options.order = options_dt.order;
		options.createdRow = options_dt.createdRow;
	}
	if (createdRow !== null) {
		options.createdRow = createdRow;
	}

	if(pageLength !== null){
		options.displayLength = pageLength;
	}

	if(footerCallback !== null){
		options.footerCallback = footerCallback;
	}
	if(extras !== null){
		$.each(extras, function(i,el){
			options[i] = el
		})
	}
	auxTabla = $(tabla).DataTable(options);
}

function number_format(amount, decimals) {
	amount += ""; // por si pasan un numero en vez de un string
	amount = parseFloat(amount.replace(/[^0-9\.]/g, "")); // elimino cualquier cosa que no sea numero o punto

	decimals = decimals || 0; // por si la variable no fue fue pasada

	// si no es un numero o es igual a cero retorno el mismo cero
	if (isNaN(amount) || amount === 0) return parseFloat(0).toFixed(decimals);

	// si es mayor o menor que cero retorno el valor formateado como numero
	amount = "" + amount.toFixed(decimals);

	var amount_parts = amount.split("."),
		regexp = /(\d+)(\d{3})/;

	while (regexp.test(amount_parts[0]))
		amount_parts[0] = amount_parts[0].replace(regexp, "$1" + "," + "$2");

	return amount_parts.join(".");
}

function intlRound(numero, decimales = 2, usarComa = false) {
	var opciones = {
		maximumFractionDigits: decimales,
		useGrouping: false
	};
	usarComa = usarComa ? "es" : "en";
	return new Intl.NumberFormat(usarComa, opciones).format(numero);
}

//ADD 7/11/19
function mayus(e) {
	e.value = e.value.toUpperCase();
}

function copiar(element) {
	var $temp = $("<input>");
	$("body").append($temp);
	$temp.val(element).select();
	document.execCommand("copy");
	$temp.remove();
}

function modalDialogSetup(options) {
	$(options.general.id + " .modal-header")
		.html("")
		.html('<h4 class = "modal-title">' + options.header.title + "</h4>");
	$(options.general.id + " .modal-body p")
		.html("")
		.html(options.body.pContent);
	$(options.general.id + " .modal-footer #btnConfirmar").attr(
		"callback",
		options.general.callback
	);
	$(options.general.id + " .modal-footer #btnCancelar").attr(
		"callback",
		options.general.callback
	);

	$("#modalAlert").addClass("hidden");

	$(options.general.id).modal({
		backdrop: "static",
		keyboard: false
	});
}

function modalAlertMessage(options) {
	$("#modalAlert").removeClass("hidden");
	$("#modalAlert .alert").addClass(options.body.alert.type);
	$("#alertMessage").html("");
	$.each(options.body.alert.message, function(i, el) {
		$("#alertMessage").append(el);
	});
}

function disabledButtons(startAjax = false) {
	if (startAjax) {
		$("#overlay").show();
		$("#btnConfirmar").attr("disabled", "disabled");
		$("#btnCancelar").attr("disabled", "disabled");
	} else {
		$("#overlay").hide();
		$("#btnConfirmar").removeAttr("disabled");
		$("#btnCancelar").removeAttr("disabled");
	}
}

jQuery.fn.dataTable.Api.register("processing()", function(show) {
	return this.iterator("table", function(ctx) {
		ctx.oApi._fnProcessingDisplay(ctx, show);
	});
});

function timerSweetAlert($title){  
    Swal.fire({
        title: $title,
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: function () {
            Swal.showLoading()
        }
    })
}
