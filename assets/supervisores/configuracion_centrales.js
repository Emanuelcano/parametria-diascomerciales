var modulos = [];
var user_modulos = []
var calendar;

$(document).ready(function () {
    base_url = $("input#base_url").val();
    cargar_campanias();
    
    
    $("#cargando").css("display", "none");
    $("#input_id_agente").addClass("hide");
    $("#input_skill").addClass("hide");

    $('#registrar-agente').click('on', function (params) {
        registrarAgente();
    });
    $('#actualizar-agente').click('on', function (params) {
        updateAgente();
    });
    $('#SaveDB').addClass('hide');
    $('#SaveDB2').addClass('hide');
    $('#div_id_campania').addClass('hide');



    $('#registrar-campania').click('on', function (params) {
        crearCampania();
    });
    $('#actualizar-campania').click('on', function (params) {
        updateCampania_old();
    });
    $('#registrar-skill').click('on', function (params) {
        registrarSkill();
    });
    $('#actualizar-skill').click('on', function (params) {
        updateSkill();
    });
    $('#asignar-skill').click('on', function (params) {
        asignarSkill();
    });


    $('.daterangepicker').css("display", "none");

    $('#hora_ini_campania').timepicker({
        showMeridian: false
    });
    $('#hora_fin_campania').timepicker({
        showMeridian: false
    });

		$('.weekday').clockTimePicker({ 
			duration: true, 
			minimum: '7:00', 
			maximum: '22:00',
			precision: 5
		});
		
		$('.weekend').clockTimePicker({
			duration: true,
			minimum: '8:00',
			maximum: '18:00',
			precision: 5
		});

		$('#sl_status').select2({
			placeholder: '.: Selecciona los estados :.',
			multiple : true
		});
		
		$("#sl_destino").change(function(){
			if ($(this).val() == CAMPAIGN_RECEIVER_CLIENTES) {
				$("#seccion_solicitantes").hide();
				$("#seccion_clientes").show();
			} else {
				$("#seccion_clientes").hide();
				$("#seccion_solicitantes").show();
			}
		});
		
		$("#sl_clientType").change(function(){
			let selectedValue = $(this).val();
			if (selectedValue == CAMPAIGN_CLIENT_TYPE_ALL || selectedValue == CAMPAIGN_CLIENT_TYPE_RETANQUEO) {
				$("#sl_actions").val('');
				$("#group_actions").show();
				
				$("#group_x_creditos").hide();
				$("#x_creditos").val('');
			} else {
				$("#sl_actions").val('');
				$("#group_actions").hide();
				$("#sl_actions").val('');
				
				$("#group_x_creditos").hide();
				$("#x_creditos").val('');
			}
		});
		
		$("#sl_actions").change(function(){
			let selectedValue = $(this).val();
			if (selectedValue == CAMPAIGN_ACTION_ALL) {
				$("#group_x_creditos").hide();
				$("#x_creditos").val('');
			} else {
				$("#group_x_creditos").show();
			}
		})
	
	$("#sl_filters").change(function(){
		let filter = $(this).val();
		let logic = $("#sl_logics").val();
		if (filter != 0 && logic != 0) {
			filtroYLogicaInputs(filter, logic)
		}
		
	});

	$("#sl_logics").change(function(){
		let filter = $("#sl_filters").val();
		let logic = $(this).val();
		if (filter != 0 && logic != 0) {
			filtroYLogicaInputs(filter, logic)
		}
	});
		
	$("#testQuery").click(function(){
		testQuery();
	});
	
	$("#saveFilters").click(function(){
		saveFilters();
	})
	
	$("#modal-disable").click(function(){
		var parametros = {
			"id_msg_prog": $("#modal-id-msg-prog").val(),
			"id_msg_prog_date": $("#modal-id-msg-prog-date").val()
		};
		
		$.ajax({
			url: base_url + "api/ApiSupervisores/disable_msg_prog/",
			type: "POST",
			data: parametros,
			success: function (response) {
				reloadCalendar();
				$('#ModalView').modal('hide')
			}
		});
	});

	$("#modal-enable").click(function(){
		var parametros = {
			"id_msg_prog": $("#modal-id-msg-prog").val(),
			"id_msg_prog_date": $("#modal-id-msg-prog-date").val()
		};

		$.ajax({
			url: base_url + "api/ApiSupervisores/enable_msg_prog/",
			type: "POST",
			data: parametros,
			success: function (response) {
				reloadCalendar();
				$('#ModalView').modal('hide')
			}
		});
	});

});

$('#slc-operadores').select2({
    placeholder: '.: Selecciona Operador :.',
    multiple: false
});

$('#check_SDB').click(function () {
    if ($(this).is(":checked")) {
        $("#input_id_agente").val("");
        $("#input_skill").val("");
        $("#input_id_agente").removeClass("hide")
        $("#input_skill").removeClass("hide");
        $('#SaveDB').removeClass('hide');
        $('#SaveCentrales').addClass('hide');
    }
    else if ($(this).is(":not(:checked)")) {
        $("#input_id_agente").addClass('hide');
        $("#input_skill").addClass('hide');
        $("#input_id_agente").val("");
        $("#input_skill").val("");
        $('#SaveCentrales').removeClass('hide');
        $('#SaveDB').addClass('hide');
    }
});
$('#check_SDB2').click(function () {
    if ($(this).is(":checked")) {
        $('#div_opt1_campania').addClass('hide');
        $('#div_opt2_campania').addClass('hide');
        $('#div_opt3_campania').addClass('hide');
        $('#div_opt4_campania').addClass('hide');
        $('#div_opt5_campania').addClass('hide');
        $('#div_opt6_campania').addClass('hide');
        $('#div_opt7_campania').addClass('hide');
        $('#div_hora_ini_campania').addClass('hide');
        $('#div_hora_fin_campania').addClass('hide');
        $('#SaveDB2').removeClass('hide');
        $('#SaveCentrales2').addClass('hide');
        $('#div_id_campania').removeClass('hide');


    }
    else if ($(this).is(":not(:checked)")) {
        $('#div_opt1_campania').removeClass('hide');
        $('#div_opt2_campania').removeClass('hide');
        $('#div_opt3_campania').removeClass('hide');
        $('#div_opt4_campania').removeClass('hide');
        $('#div_opt5_campania').removeClass('hide');
        $('#div_opt6_campania').removeClass('hide');
        $('#div_opt7_campania').removeClass('hide');
        $('#div_hora_ini_campania').removeClass('hide');
        $('#div_hora_fin_campania').removeClass('hide');
        $('#SaveCentrales2').removeClass('hide');
        $('#SaveDB2').addClass('hide');
        $('#div_id_campania').addClass('hide');


    }
});

function registrarAgente() {
    let id_operador = $('#slc-operadores').val();
    let central = $('#sl_central').val();
    let id_agente = "";
    let id_skill = "";
    if (id_operador != '' && central != '' && id_operador != null) {
        Swal.fire({
            title: 'Registro de Agente',
            text: 'Estas seguro de que quieres REGISTRAR agente?',
            icon: 'warning',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            showCancelButton: 'true'
        }).then((result) => {
            if (result.value) {
                const formData3 = new FormData();
                formData3.append("id_operador", id_operador);
                formData3.append("central", central);
                base_url = $("input#base_url").val() + 'api/operadores/validacionAgente';
                $.ajax({
                    type: "POST",
                    url: base_url,
                    data: formData3,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        // console.log("responseeeeeee", response)
                        if (response != null) {
                            // console.log(response);
                            if (response.ok) {
                                Swal.fire({
                                    icon: 'error',
                                    text: response.message
                                });
                            } else {
                                if ($('#check_SDB').is(":checked")) {

                                    id_agente = $('#input_id_agente').val();
                                    id_skill = $('#input_skill').val();

                                    guardarDBAgente(id_operador, central, id_agente, id_skill);

                                } else {
                                    if ($('#sl_central').val() == "wolkvox") {
                                        const formData2 = new FormData();
                                        nombre_operador = $("#slc-operadores option:selected").text();
                                        full_name = nombre_operador.split(' ');
                                        count_white = full_name.length;
                                        switch (count_white) {
                                            case 2:
                                                name_agent = full_name[0] + " " + full_name[1];
                                                break;

                                            case 3:
                                                name_agent = full_name[0] + " " + full_name[2];
                                                break;
                                            case 4:
                                                name_agent = full_name[0] + " " + full_name[2];
                                                break;
                                            default: name_agent = full_name[0] + " " + full_name[1];
                                                break;
                                        }
                                        // console.log(("name_agent", name_agent))
                                        formData2.append("name_agent", name_agent);

                                        base_url = $("input#base_url").val() + 'api/ApiSupervisores/createAgenteWolvox';
                                        $.ajax({
                                            type: "POST",
                                            url: base_url,
                                            data: formData2,
                                            processData: false,
                                            contentType: false,
                                            success: function (response) {
                                                // console.log("responseeeeeee", response)
                                                if (response != null) {
                                                    id_agente = response;
                                                    id_skill = 0;
                                                    guardarDBAgente(id_operador, central, id_agente, id_skill);
                                                    // console.log(id_agente);
                                                } else {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        text: response
                                                    });
                                                }
                                            }
                                        });

                                    }

                                }
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                text: response
                            });
                        }
                    }
                });
            }
        });
    } else {
        Swal.fire({
            icon: 'warning',
            text: 'Seleccione Central y Operador'
        });
    }
}
function cancelEditAgente() {
    $("#registrar-agente").css("display", "block");
    $("#actualizar-agente").css("display", "none");
    $("#cancelAgente").css("display", "none");
    $('#slc-operadores').val("");
    $('#sl_central').val("");
    $('#input_id_agente').val("");
    $('#input_id_agente').addClass("hide");
    $("#div_slc-operadores").removeClass("hide");
    $("#form_datos").css("display", "block");
    $('#input_skill').addClass("hide");


}

function guardarDBAgente(id_operador, central, id_agente, id_skill) {

    if (id_agente !== "" && id_skill !== "" && id_skill != null && id_agente != null) {

        const formData = new FormData();
        formData.append("id_operador", id_operador);
        formData.append("central", central);
        formData.append("id_agente", id_agente);
        formData.append("id_skill", id_skill);
        base_url = $("input#base_url").val() + "api/operadores/registar_agente";

        $.ajax({
            type: "POST",
            url: base_url,
            data: formData,
            processData: false,
            contentType: false,

            success: function (response) {
                if (response.ok) {
                    // getAusencias();
                    Swal.fire({
                        icon: 'success',
                        text: response.message
                    });
                    initTableAgenteCentral();
                    $('#slc-operadores').val("");
                    $('#sl_central').val("");
                    $('#input_id_agente').val("");
                    $('#input_skill').val("");


                } else {
                    Swal.fire({
                        icon: 'error',
                        text: response.message
                    });
                }
            }
        });
    } else {
        Swal.fire({
            icon: 'warning',
            text: 'Comple todo los Datos'
        });
        $('#input_id_agente').focus();


    }
}

function updateAgente() {
    $("#registrar-agente").css("display", "block");
    $("#actualizar-agente").css("display", "none");
    let id_operador = $('#slc-operadores').val();
    let central = $('#sl_central').val();
    let id_agente = $('#input_id_agente').val();
    let id_skill = $('#input_skill').val();

    Swal.fire({
        title: 'Cambio de estado',
        text: 'Estas seguro de que quieres ACTUALIZAR Agente?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {
            var id = $("#actualizar-agente").data("id");
            const formData = new FormData();
            formData.append("id", id);
            formData.append("id_operador", id_operador);
            formData.append("central", central);
            formData.append("id_agente", id_agente);
            formData.append("id_skill", id_skill);
            var base_url =
                $("input#base_url").val() + "api/operadores/updateAgente";
            $.ajax({
                type: "POST",
                url: base_url,
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response["errors"]) {
                        toastr["error"]("No se pudo actualizar el Agente", "ERROR");
                    } else {
                        toastr["success"]("Se actualizo correctamente", "Actualizado");
                        $('#slc-operadores').val("");
                        $('#sl_central').val("");
                        $('#input_id_agente').val("");
                        $('#input_id_agente').addClass("hide");
                        $("#div_slc-operadores").removeClass("hide");
                        $("#form_datos").css("display", "block");
                        $('#input_skill').addClass("hide");
                        $("#cancelAgente").css("display", "none");

                        initTableAgenteCentral();
                    }
                }
            });
        }
    });
}

function desactive_agent(id_consultor) {
    const formData3 = new FormData();
    formData3.append("id_agente", id_consultor);
    base_url = $("input#base_url").val() + 'api/ApiSupervisores/desactiveAgentWolvox';
    $.ajax({
        type: "POST",
        url: base_url,
        data: formData3,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response != null) {
                Swal.fire({
                    icon: 'success',
                    text: response
                });

            } else {
                Swal.fire({
                    icon: 'error',
                    text: response
                });
            }
        }
    });
}
function active_agent(id_consultor) {
    const formData3 = new FormData();
    formData3.append("id_agente", id_consultor);
    base_url = $("input#base_url").val() + 'api/ApiSupervisores/activeAgentWolvox';
    $.ajax({
        type: "POST",
        url: base_url,
        data: formData3,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response != null) {
                Swal.fire({
                    icon: 'success',
                    text: response
                });

            } else {
                Swal.fire({
                    icon: 'error',
                    text: response
                });
            }
        }
    });
}


function bajaAgente(id_agente) {
    Swal.fire({
        title: 'Cambio de estado',
        text: 'Estas seguro de que quieres DESHABILITAR Agente?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {
            var id_estado = $("#btnDeshabilitarAgente").attr("data-estado");
            var id_consultor = $("#btnDeshabilitarAgente").attr("data-id_consultor");
            desactive_agent(id_consultor);

            // console.log(id_estado);
            var data = {
                id_agente: id_agente,
                id_estado: id_estado
            }
            var base_url =
                $("input#base_url").val() + "api/operadores/cambioEstadoAgente";
            $.ajax({
                type: "POST",
                url: base_url,
                data: data,
                success: function (response) {
                    if (response["errors"]) {
                        toastr["error"]("No se pudo actualizar el Agente", "ERROR");
                    } else {
                        toastr["success"]("Se actualizo correctamente", "Actualizado");
                        initTableAgenteCentral();
                    }
                }
            });
        }
    });

}
function altaAgente(id_agente) {
    Swal.fire({
        title: 'Cambio de estado',
        text: 'Estas seguro de que quieres HABILITAR Agente?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {
            var id_estado = $("#btnHabilitarAgente").attr("data-estado");
            var id_consultor2 = $("#btnHabilitarAgente").attr("data-id_consultor");
            active_agent(id_consultor2);

            // console.log(id_estado);
            var data = {
                id_agente: id_agente,
                id_estado: id_estado
            }
            var base_url =
                $("input#base_url").val() + "api/operadores/cambioEstadoAgente";
            $.ajax({
                type: "POST",
                url: base_url,
                data: data,
                success: function (response) {
                    if (response["errors"]) {
                        toastr["error"]("No se pudo actualizar el Agente", "ERROR");
                    } else {
                        toastr["success"]("Se actualizo correctamente", "Actualizado");
                        initTableAgenteCentral();
                    }
                }
            });
        }
    });

}

function editarAgente(id) {
    $("#registrar-agente").css("display", "none");
    $("#actualizar-agente").css("display", "block");
    if (id != null) {
        base_url = $("input#base_url").val() + "api/operadores/get_agente_update/" + id;
        $.ajax({
            type: "GET",
            url: base_url,
            success: function (response) {
                if (typeof (response.data) != 'undefined') {
                    agente = response.data
                    $('#sl_central').val(agente[0].central);
                    $('#slc-operadores').val(agente[0].id_operador);
                    $('#input_id_agente').val(agente[0].id_agente);
                    $('#input_skill').val(agente[0].id_skill);
                    $('#input_skill').removeClass("hide");

                    $('#actualizar-agente').data('id', agente[0].id);
                    $("#div_slc-operadores").addClass("hide");
                    $("#input_id_agente").removeClass("hide");
                    $("#form_datos").css("display", "none");
                    $("#cancelAgente").css("display", "block");

                    // $("#check_SDB").prop("checked", false);

                }
            }
        });
    }

}
function initTableAgenteCentral() {
    let base_url = $("#base_url").val();
    $.ajax({
        url: base_url + 'api/ApiOperadores/tableAgentes',
        type: 'GET',

    })
        .done(function (response) {
            // console.log("response", response)
            $('#tp_agenteCentral').dataTable().fnDestroy();
            if (response.ok) {
                $('#tp_agenteCentral').DataTable({
                    rowId: response.data.id,
                    order: [[2, "asc"]],
                    data: response.data,
                    columns: [
                        { data: "id" },
                        { data: "idoperador" },
                        { data: "nombre_apellido" },
                        {
                            data: "id_agente"
                        },
                        {
                            data: "id_skill",

                        },
                        {
                            data: null,
                            render: function (data, type, row, meta) {
                                central_str = (data['central']).toUpperCase();
                                return central_str;
                            }
                        },
                        {
                            data: null,
                            render: function (data, type, row, meta) {
                                // console.log(data['estado_agente']);
                                switch (data['estado_agente']) {
                                    case "0":
                                        estado = "DESHABILITADO";
                                        break;
                                    case "1":
                                        estado = "HABILITADO";
                                        break;
                                    default:
                                        estado = data['estado_agente'];
                                        break;
                                }
                                return estado;
                            }
                        },

                        {
                            data: null,
                            render: function (data, type, row, meta) {
                                var EditarAgente =
                                    '<a class="btn btn-xs btn-primary" title="EditarAgente" onclick="editarAgente(' +
                                    row.id + ');"><i class="fa fa-pencil-square-o" ></i></a>';
                                var HabilitarAgente =
                                    '<a class="btn btn-xs btn-success" id="btnHabilitarAgente" data-estado="1" data-id_consultor="' + row.id_agente + '" title="Habilitar Agente", onclick="altaAgente(' +
                                    row.id + ');"><i class="fa fa-check-square-o" ></i></a>';
                                var DeshabilitarAgente =
                                    '<a class="btn btn-xs btn-danger" id="btnDeshabilitarAgente" data-estado="0" data-id_consultor="' + row.id_agente + '" title="Deshabilitar Agente" onclick="bajaAgente(' +
                                    row.id + ');"><i class="fa fa-ban" ></i></a>';
                                if (data['estado_agente'] == 1) {
                                    return EditarAgente + " " + DeshabilitarAgente;

                                } else {
                                    return EditarAgente + " " + HabilitarAgente;
                                }
                            }
                        }
                    ],
                    columnDefs: [
                        {
                            targets: [7],
                            createdCell: function (td, cellData, rowData, row, col) {
                                $(td).attr("style", "text-align: center;");
                            }
                        },]
                });

            }

        })
        .fail(function (response) {
            //console.log("error");
        })
        .always(function (response) {
            //console.log("complete");
        });


}
function bajaCampania(id) {
    Swal.fire({
        title: 'Cambio de estado',
        text: 'Estas seguro de que quieres DESHABILITAR Campaña?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {
            var id_estado = $("#btnDeshabilitarCampania").attr("data-estado");
            var id_campania = $("#btnDeshabilitarCampania").attr("data-id_campania");
            // console.log(id_estado);
            var data = {
                id: id,
                id_estado: id_estado
            }
            var base_url =
                $("input#base_url").val() + "api/operadores/cambioEstadoCampania";
            $.ajax({
                type: "POST",
                url: base_url,
                data: data,
                success: function (response) {
                    if (response["errors"]) {
                        toastr["error"]("No se pudo actualizar el Campaña", "ERROR");
                    } else {
                        toastr["success"]("Se actualizo correctamente", "Actualizado");
                        initTableCreateCampania();

                    }
                }
            });
        }
    });

}
function altaCampania(id) {
    Swal.fire({
        title: 'Cambio de estado',
        text: 'Estas seguro de que quieres HABILITAR Campaña?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {
            var id_estado = $("#btnHabilitarCampania").attr("data-estado");
            var id_campania2 = $("#btnHabilitarCampania").attr("data-id_campania");
            var data = {
                id: id,
                id_estado: id_estado
            }
            var base_url =
                $("input#base_url").val() + "api/operadores/cambioEstadoCampania";
            $.ajax({
                type: "POST",
                url: base_url,
                data: data,
                success: function (response) {
                    if (response["errors"]) {
                        toastr["error"]("No se pudo actualizar el Campaña", "ERROR");
                    } else {
                        toastr["success"]("Se actualizo correctamente", "Actualizado");
                        initTableCreateCampania();
                    }
                }
            });
        }
    });

}
function initTableCreateCampania() {
    let base_url = $("#base_url").val();
    $.ajax({
        url: base_url + 'api/ApiOperadores/tableCreateCampania',
        type: 'GET',

    })
        .done(function (response) {
            // console.log("response", response)
            $('#tp_create_campania').dataTable().fnDestroy();
            if (response.ok) {
                $('#tp_create_campania').DataTable({
                    rowId: response.data.id,
                    order: [[2, "asc"]],
                    data: response.data,
                    columns: [
                        { data: "id" },
                        { data: "id_campania" },
                        {
                            data: null,
                            render: function (data, type, row, meta) {
                                central_str = (data['central']).toUpperCase();
                                return central_str;
                            }
                        },
                        {
                            data: "id_skill"
                        },
                        {
                            data: "nombre",

                        },
                        {
                            data: null,
                            render: function (data, type, row, meta) {
                                // fecha_campania = data['fecha'];
                                fecha_campania = moment(data['fecha']).format('DD-MM-YYYY');
                                return fecha_campania;
                            }
                        },
                        {
                            data: null,
                            render: function (data, type, row, meta) {
                                // console.log(data['estado_agente']);
                                switch (data['estado_campania']) {
                                    case "0":
                                        estado = "DESHABILITADO";
                                        break;
                                    case "1":
                                        estado = "HABILITADO";
                                        break;
                                    case "NULL":
                                        estado = "DESHABILITADO";
                                        break;
                                    default:
                                        estado = data['estado_campania'];
                                        break;
                                }
                                return estado;
                            }
                        },

                        {
                            data: null,
                            render: function (data, type, row, meta) {
                                var EditarCampania =
                                    '<a class="btn btn-xs btn-primary" title="EditarCampania" onclick="editarCampania(' +
                                    row.id + ');"><i class="fa fa-pencil-square-o" ></i></a>';
                                var HabilitarCampania =
                                    '<a class="btn btn-xs btn-success" id="btnHabilitarCampania" data-estado="1" data-id_campania="' + row.id_campania + '" title="Habilitar Campania", onclick="altaCampania(' +
                                    row.id + ');"><i class="fa fa-check-square-o" ></i></a>';
                                var DeshabilitarCampania =
                                    '<a class="btn btn-xs btn-danger" id="btnDeshabilitarCampania" data-estado="0" data-id_campania="' + row.id_campania + '" title="Deshabilitar Campania" onclick="bajaCampania(' +
                                    row.id + ');"><i class="fa fa-ban" ></i></a>';
                                if (data['estado_campania'] == 1) {
                                    return EditarCampania + " " + DeshabilitarCampania;

                                } else {
                                    return EditarCampania + " " + HabilitarCampania;
                                }
                            }
                        }
                    ],
                    columnDefs: [
                        {
                            targets: [7],
                            createdCell: function (td, cellData, rowData, row, col) {
                                $(td).attr("style", "text-align: center;");
                            }
                        },]
                });

            }

        })
        .fail(function (response) {
            //console.log("error");
        })
        .always(function (response) {
            //console.log("complete");
        });


}
function crearCampania() {
    let preview = $('#sl_preview_campana').val();
    let id_skill_campania = $('#sl_skill_campania').val();
    let central_campania = $('#sl_central_campania').val();
    let name_campania = $('#input_name_campania').val();
    let descripcion_campania = $('#input_description_campania').val();
    let hora_ini_campania = $('#hora_ini_campania').val();
    let hora_final_campania = $('#hora_fin_campania').val();
    let opcion_1_campania = $('#opt1_campania').val();
    let opcion_2_campania = $('#opt2_campania').val();
    let opcion_3_campania = $('#opt3_campania').val();
    let opcion_4_campania = $('#opt4_campania').val();
    let opcion_5_campania = $('#opt5_campania').val();
    let opcion_6_campania = $('#opt6_campania').val();
    let opcion_7_campania = $('#opt7_campania').val();


    if (id_skill_campania != '' && central_campania != '' && name_campania != '') {
        Swal.fire({
            title: 'Registro de Campaña',
            text: 'Estas seguro de que quieres REGISTRAR campaña?',
            icon: 'warning',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            showCancelButton: 'true'
        }).then((result) => {
            if ($('#check_SDB2').is(":checked")) {
                let id_campania = $('#id_campania').val();

                guardarDBCampana(id_campania, central_campania, name_campania, descripcion_campania, id_skill_campania, preview)

            } else {
                if (result.value) {
                    if ($('#sl_central_campania').val() == "wolkvox") {
                        // alert("wolvox");
                        const formData = new FormData();
                        formData.append("ifpreview", preview);
                        formData.append("name_camp", name_campania);
                        formData.append("desc_camp", descripcion_campania);
                        formData.append("id_skill", id_skill_campania);
                        formData.append("bhour", hora_ini_campania);
                        formData.append("fhour", hora_final_campania);
                        formData.append("opt1", opcion_1_campania);
                        formData.append("opt2", opcion_2_campania);
                        formData.append("opt3", opcion_3_campania);
                        formData.append("opt4", opcion_4_campania);
                        formData.append("opt5", opcion_5_campania);
                        formData.append("opt6", opcion_6_campania);
                        formData.append("opt7", opcion_7_campania);

                        // console.log(formData);
                        base_url = $("input#base_url").val() + 'api/ApiSupervisores/createCampaniaWolvox';
                        $.ajax({
                            type: "POST",
                            url: base_url,
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                if (response != null) {
                                    // console.log(response);
                                    if (response == "ERR001") {
                                        Swal.fire({
                                            icon: 'error',
                                            text: "Existe Campaña con ese NOMBRE"
                                        });
                                    }
                                    id_campania = response;
                                    guardarDBCampana(id_campania, central_campania, name_campania, descripcion_campania, id_skill_campania, preview)
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        text: response
                                    });
                                }
                            }
                        });
                    }
                }
            }
        });
    }
}

function guardarDBCampana(id_campania, central_campania, name_campania, descripcion_campania, id_skill_campania, preview) {
    const formData = new FormData();
    formData.append("id_campania", id_campania);
    formData.append("central_campania", central_campania);
    formData.append("name_campania", name_campania);
    formData.append("descripcion_campania", descripcion_campania);
    formData.append("id_skill_campania", id_skill_campania);
    formData.append("preview", preview);

    base_url = $("input#base_url").val() + "api/operadores/registrar_campania";

    $.ajax({
        type: "POST",
        url: base_url,
        data: formData,
        processData: false,
        contentType: false,

        success: function (response) {
            if (response.ok) {
                // getAusencias();
                Swal.fire({
                    icon: 'success',
                    text: response.message
                });
                initTableCreateCampania();
                $('#sl_preview_campana').val("");
                $('#sl_skill_campania').val("");
                $('#sl_central_campania').val("");
                $('#input_name_campania').val("");
                $('#input_description_campania').val("");
                $('#hora_ini_campania').val("");
                $('#hora_fin_campania').val("");
                $('#opt1_campania').val("");
                $('#opt2_campania').val("");
                $('#opt3_campania').val("");
                $('#opt4_campania').val("");
                $('#opt5_campania').val("");
                $('#opt6_campania').val("");
                $('#opt7_campania').val("");


            } else {
                Swal.fire({
                    icon: 'error',
                    text: response.message
                });
            }
        }
    });
}
function editarCampania(id) {
    $("#registrar-campania").css("display", "none");
    $("#actualizar-campania").css("display", "block");
    if (id != null) {
        base_url = $("input#base_url").val() + "api/operadores/get_campania_update/" + id;
        $.ajax({
            type: "GET",
            url: base_url,
            success: function (response) {
                if (typeof (response.data) != 'undefined') {
                    // console.log(response.data);
                    campanias_update = response.data
                    $("#hora_ini_campania").parent().parent().css("display", "none");
                    $("#hora_fin_campania").parent().parent().css("display", "none");
                    $("#opt1_campania").parent().css("display", "none");
                    $("#opt2_campania").parent().css("display", "none");
                    $("#opt3_campania").parent().css("display", "none");
                    $("#opt4_campania").parent().css("display", "none");
                    $("#opt5_campania").parent().css("display", "none");
                    $("#opt6_campania").parent().css("display", "none");
                    $("#opt7_campania").parent().css("display", "none");
                    $('#sl_central_campania').val(campanias_update[0].central);
                    $("#id_campania").val(campanias_update[0].id_campania)
                    $('#input_name_campania').val(campanias_update[0].nombre);
                    $('#input_description_campania').val(campanias_update[0].descripcion);
                    $('#sl_skill_campania').val(campanias_update[0].id_skill);
                    $('#actualizar-campania').data('id', campanias_update[0].id);
                    $("#id_campania").prop("disabled", true);
                    $("#div_id_campania").removeClass("hide");
                    $("#cancelarEditCampania").css("display", "block");



                }
            }
        });
    }

}
function cancelEditCampania() {
    $("#registrar-campania").css("display", "block");
    $("#actualizar-campania").css("display", "none");
    $("#cancelarEditCampania").css("display", "none");
    $("#hora_ini_campania").parent().parent().css("display", "block");
    $("#hora_fin_campania").parent().parent().css("display", "block");
    $("#opt1_campania").parent().css("display", "block");
    $("#opt2_campania").parent().css("display", "block");
    $("#opt3_campania").parent().css("display", "block");
    $("#opt4_campania").parent().css("display", "block");
    $("#opt5_campania").parent().css("display", "block");
    $("#opt6_campania").parent().css("display", "block");
    $("#opt7_campania").parent().css("display", "block");
    $('#sl_central_campania').val("");
    $('#input_name_campania').val("");
    $('#input_description_campania').val("");
    $('#sl_skill_campania').val("");
    $("#id_campania").prop("disabled", false);
    $("#div_id_campania").addClass("hide");

}

function updateCampania_old() {
    $("#registrar-campania").css("display", "block");
    $("#actualizar-campania").css("display", "none");
    let id_skill_campania = $('#sl_skill_campania').val();
    let central_campania = $('#sl_central_campania').val();
    let name_campania = $('#input_name_campania').val();
    let descripcion_campania = $('#input_description_campania').val();
    let preview = $('#sl_preview_campana').val();

    Swal.fire({
        title: 'Cambio de estado',
        text: 'Estas seguro de que quieres ACTUALIZAR Campaña?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {
            var id = $("#actualizar-campania").data("id");
            const formData = new FormData();
            formData.append("id", id);
            formData.append("central_campania", central_campania);
            formData.append("name_campania", name_campania);
            formData.append("descripcion_campania", descripcion_campania);
            formData.append("id_skill_campania", id_skill_campania);
            formData.append("preview", preview);

            var base_url =
                $("input#base_url").val() + "api/operadores/updateCamapania";
            $.ajax({
                type: "POST",
                url: base_url,
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.ok) {
                        toastr["success"]("Se actualizo correctamente", "Actualizado");
                        initTableCreateCampania();
                        $("#hora_ini_campania").parent().parent().css("display", "block");
                        $("#hora_fin_campania").parent().parent().css("display", "block");
                        $("#opt1_campania").parent().css("display", "block");
                        $("#opt2_campania").parent().css("display", "block");
                        $("#opt3_campania").parent().css("display", "block");
                        $("#opt4_campania").parent().css("display", "block");
                        $("#opt5_campania").parent().css("display", "block");
                        $("#opt6_campania").parent().css("display", "block");
                        $("#opt7_campania").parent().css("display", "block");
                        $('#sl_central_campania').val("");
                        $('#input_name_campania').val("");
                        $('#input_description_campania').val("");
                        $('#sl_skill_campania').val("");
                        $("#id_campania").prop("disabled", false);
                        $("#div_id_campania").addClass("hide");
                        $("#cancelarEditCampania").css("display", "none");



                    } else {
                        toastr["error"]("No se pudo actualizar Campaña", "ERROR");
                    }
                }
            });
        }
    });
}


function initTableSkillCentral() {
    let base_url = $("#base_url").val();
    $.ajax({
        url: base_url + 'api/ApiOperadores/tableSkill',
        type: 'GET',

    })
        .done(function (response) {
            // console.log("response", response)
            $('#tp_skillCentral').dataTable().fnDestroy();
            if (response.ok) {
                $('#tp_skillCentral').DataTable({
                    rowId: response.data.id,
                    order: [[2, "asc"]],
                    data: response.data,
                    columns: [
                        { data: "id" },
                        { data: "id_skill" },
                        {
                            data: "id_grupos_operadores"
                        },
                        {
                            data: "descripcion",

                        },
                        {
                            data: null,
                            render: function (data, type, row, meta) {
                                central_skill_str = (data['central']).toUpperCase();
                                return central_skill_str;
                            }
                        },
                        {
                            data: null,
                            render: function (data, type, row, meta) {
                                // console.log("estado_skill", data['estado_skill']);
                                if (data['estado_skill'] == "" || data['estado_skill'] == "NULL" || data['estado_skill'] == null) {

                                    data['estado_skill'] = 0;

                                }

                                switch (data['estado_skill']) {
                                    case "0":
                                        estado = "DESHABILITADO";
                                        break;
                                    case "1":
                                        estado = "HABILITADO";
                                        break;
                                    case "NULL":
                                        estado = "DESHABILITADO";
                                        break;
                                    default:
                                        estado = data['estado_skill'];
                                        break;
                                }
                                return estado;
                            }
                        },

                        {
                            data: null,
                            render: function (data, type, row, meta) {
                                // var id_skill2 = '"' + row.id_skill + '"';
                                var EditarSkill =
                                    '<a class="btn btn-xs btn-primary" title="Editarskill" onclick="editarSkill(' +
                                    row.id + ');"><i class="fa fa-pencil-square-o" ></i></a>';
                                var AsignarSkill =
                                    '<a class="btn btn-xs btn-info" title="Asignarskill" onclick="modalAsignarSkill(`' + row.id_skill + '`);"><i class="fa fa-user-plus" ></i></a>';
                                var HabilitarSkill =
                                    '<a class="btn btn-xs btn-success" id="btnHabilitarskill" data-estado="1" title="Habilitar skill", onclick="altaskill(' +
                                    row.id + ');"><i class="fa fa-check-square-o" ></i></a>';
                                var DeshabilitarSkill =
                                    '<a class="btn btn-xs btn-danger" id="btnDeshabilitarskill" data-estado="0" title="Deshabilitar skill" onclick="bajaskill(' +
                                    row.id + ');"><i class="fa fa-ban" ></i></a>';
                                if (data['estado_skill'] == 1) {
                                    return EditarSkill + " " + AsignarSkill + " " + DeshabilitarSkill;

                                } else {
                                    return EditarSkill + " " + HabilitarSkill;
                                }
                            }
                        }
                    ],
                    columnDefs: [
                        {
                            targets: [6],
                            createdCell: function (td, cellData, rowData, row, col) {
                                $(td).attr("style", "text-align: center;");
                            }
                        },]
                });

            }

        })
        .fail(function (response) {
            //console.log("error");
        })
        .always(function (response) {
            //console.log("complete");
        });


}

function registrarSkill() {
    let id_skill_create_skill = $('#id_skill_create_skill').val();
    let id_grupo_operadores_create_skill = $('#id_grupo_operadores_create_skill').val();
    let descripcion_create_skill = $('#descripcion_create_skill').val();
    let sl_central_create_skill = $('#sl_central_create_skill').val();
    if (id_skill_create_skill != '' && sl_central_create_skill != '' && id_grupo_operadores_create_skill != '' && descripcion_create_skill != '') {
        Swal.fire({
            title: 'Registro de Skill',
            text: 'Estas seguro de que quieres REGISTRAR Skill?',
            icon: 'warning',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            showCancelButton: 'true'
        }).then((result) => {
            if (result.value) {
                if ($('#sl_central_create_skill').val() == "wolkvox") {
                    // alert("wolvox");
                    const formData = new FormData();
                    formData.append("id_skill", id_skill_create_skill);
                    formData.append("id_grupos_operadores", id_grupo_operadores_create_skill);
                    formData.append("descripcion", descripcion_create_skill);
                    formData.append("central", sl_central_create_skill);
                    base_url = $("input#base_url").val() + 'api/operadores/registrarSkill';
                    $.ajax({
                        type: "POST",
                        url: base_url,
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.ok) {

                                Swal.fire({
                                    icon: 'success',
                                    text: "Existo se registro SKILL"
                                });
                                initTableSkillCentral();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    text: response.message
                                });
                            }
                        }
                    });
                }
            }
        });
    } else {
        Swal.fire({
            icon: 'error',
            text: "Complete todos los campos"
        });
    }
}
function bajaskill(id) {
    Swal.fire({
        title: 'Cambio de estado',
        text: 'Estas seguro de que quieres DESHABILITAR Skill?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {
            var id_estado = $("#btnDeshabilitarskill").attr("data-estado");
            // console.log(id_estado);
            var data = {
                id: id,
                id_estado: id_estado
            }
            var base_url =
                $("input#base_url").val() + "api/operadores/cambioEstadoSkill";
            $.ajax({
                type: "POST",
                url: base_url,
                data: data,
                success: function (response) {
                    if (response["errors"]) {
                        toastr["error"]("No se pudo actualizar el Skill", "ERROR");
                    } else {
                        toastr["success"]("Se actualizo correctamente", "Actualizado");
                        initTableSkillCentral();

                    }
                }
            });
        }
    });

}
function altaskill(id) {
    Swal.fire({
        title: 'Cambio de estado',
        text: 'Estas seguro de que quieres HABILITAR Skill?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {
            var id_estado = $("#btnHabilitarskill").attr("data-estado");

            // console.log(id_estado);
            var data = {
                id: id,
                id_estado: id_estado
            }
            var base_url =
                $("input#base_url").val() + "api/operadores/cambioEstadoSkill";
            $.ajax({
                type: "POST",
                url: base_url,
                data: data,
                success: function (response) {
                    if (response["errors"]) {
                        toastr["error"]("No se pudo actualizar el Skill", "ERROR");
                    } else {
                        toastr["success"]("Se actualizo correctamente", "Actualizado");
                        initTableSkillCentral();
                    }
                }
            });
        }
    });

}

function editarSkill(id) {
    $("#registrar-skill").css("display", "none");
    $("#actualizar-skill").css("display", "block");
    if (id != null) {
        base_url = $("input#base_url").val() + "api/operadores/get_skill_update/" + id;
        $.ajax({
            type: "GET",
            url: base_url,
            success: function (response) {
                if (typeof (response.data) != 'undefined') {
                    skill = response.data;
                    $('#id_skill_create_skill').val(skill[0].id_skill);
                    $('#id_grupo_operadores_create_skill').val(skill[0].id_grupos_operadores);
                    $('#descripcion_create_skill').val(skill[0].descripcion);
                    $('#sl_central_create_skill').val(skill[0].central);
                    $('#actualizar-skill').data('id', skill[0].id);
                    $("#id_skill_create_skill").prop("disabled", true);
                    $("#cancelarEditSkill").css("display", "block");


                }
            }
        });
    }

}

function cancelEditSkill() {
    $("#registrar-skill").css("display", "block");
    $("#actualizar-skill").css("display", "none");
    $('#id_skill_create_skill').val("");
    $('#id_grupo_operadores_create_skill').val("");
    $('#descripcion_create_skill').val("");
    $("#id_skill_create_skill").prop("disabled", false);
    $('#sl_central_create_skill').val("");
    $("#cancelarEditSkill").css("display", "none");

}
function updateSkill() {
    $("#registrar-skill").css("display", "block");
    $("#actualizar-skill").css("display", "none");
    let id_skill_create_skill = $('#id_skill_create_skill').val();
    let id_grupo_operadores_create_skill = $('#id_grupo_operadores_create_skill').val();
    let descripcion_create_skill = $('#descripcion_create_skill').val();
    let sl_central_create_skill = $('#sl_central_create_skill').val();

    Swal.fire({
        title: 'Actualizar',
        text: 'Estas seguro de que quieres ACTUALIZAR Skill?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {
            var id = $("#actualizar-skill").data("id");
            const formData = new FormData();
            formData.append("id", id);
            formData.append("id_skill", id_skill_create_skill);
            formData.append("id_grupos_operadores", id_grupo_operadores_create_skill);
            formData.append("descripcion", descripcion_create_skill);
            formData.append("central", sl_central_create_skill);
            var base_url =
                $("input#base_url").val() + "api/operadores/updateSkill";
            $.ajax({
                type: "POST",
                url: base_url,
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response["errors"]) {
                        toastr["error"]("No se pudo actualizar el Skill", "ERROR");
                    } else {
                        toastr["success"]("Se actualizo correctamente", "Actualizado");
                        $('#id_skill_create_skill').val("");
                        $('#id_grupo_operadores_create_skill').val("");
                        $('#descripcion_create_skill').val("");
                        $("#id_skill_create_skill").prop("disabled", false);
                        $('#sl_central_create_skill').val("");
                        $("#cancelarEditSkill").css("display", "none");

                        initTableSkillCentral();
                    }
                }
            });
        }
    });

}


function obtenerSeleccion() {
    var selection = $(".right-panel").find('div.dsl-panel-item');
    var asignados = "";

    for (var n = 0; n < selection.length; ++n) {
        if (n > 0)
            asignados += ',';
        asignados += selection.eq(n).text().split('-')[0];
    }
    return asignados;
}

function cargarModulos(id_skill) {
    base_url = $("input#base_url").val() + "api/operadores/get_operador_skill/" + id_skill;

    $.ajax({
        type: "GET",
        url: base_url,

        success: function (response) {
            if (response.ok) {
                // console.log(response);
                modulos = response.item;
                user_modulos = response.item_disponible;
                // console.log(modulos);
                // console.log(user_modulos);
                modulosInit();

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: response.message,
                });
            }
        }
    });
}
function modulosInit() {
    var items = [];
    var items_asignados = [];
    $('#dualSelectExample').empty();
    // document.getElementById("dualSelectExample").remove();
    for (var n = 0; n < modulos.length; ++n) {
        items_asignados.push(modulos[n].id_operador + "-" + modulos[n].nombre_apellido);

    }
    for (var j = 0; j < user_modulos.length; ++j) {
        items.push(user_modulos[j].idoperador + "-" + user_modulos[j].nombre_apellido);

    }
    var dsl = $('#dualSelectExample').DualSelectList({
        'candidateItems': items,
        'selectionItems': items_asignados,
        'colors': {
            'itemText': 'black',
            'itemBackground': '#f7f1fb ',
            'itemHoverBackground': 'rgb(156, 153, 216)'
        }
    });
}

function asignarSkill() {
    Swal.fire({
        title: 'Asignar Skill',
        text: 'Estas seguro de que quieres ASIGNAR Skill?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {
            let asignados = obtenerSeleccion();
            var id_skill = $("#asignar-skill").data("id_skill");
            // console.log(asignados);
            // console.log(id_skill);
            const formData = new FormData();
            formData.append("id_skill", id_skill);
            formData.append("skills", asignados);
            var base_url =
                $("input#base_url").val() + "api/operadores/asignarSkills";
            $.ajax({
                type: "POST",
                url: base_url,
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.ok) {
                        Swal.fire({
                            icon: 'success',
                            text: response.message
                        });
                        $('#modalA_asignarSkill').modal("hide");

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message,
                        });


                    }
                }
            });
        }
    });
}

function modalAsignarSkill(id_skill) {
    // console.log(id_skill);
    $('#modalA_asignarSkill').modal("show");
    // agentesInit();
    cargarModulos(id_skill);
    $('#asignar-skill').data('id_skill', id_skill);
}

function solo_numeros(event) {
    let keycode = event.keyCode;
    if (keycode >= 48 && keycode <= 57) {
        return true;
    }
    return false;
}

function cargar_campanias(event) {

    let base_url = $("#base_url").val();
    
    $.ajax({
        url: base_url + 'api/ApiSupervisores/get_all_campanias',
        type: 'POST',
    })
    .done(function (response) {
            var table = $('#table_campania').DataTable({ 

                    "language": {
                                "sProcessing":     "Procesando...",
                                "sLengthMenu":     "Mostrar _MENU_ registros",
                                "sZeroRecords":    "No se encontraron resultados",
                                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                                "sInfo":           "Del _START_ al _END_ de un total de _TOTAL_ reg.",
                                "sInfoEmpty":      "0 registros",
                                "sInfoFiltered":   "(filtrado de _MAX_ reg.)",
                                "sInfoPostFix":    "",
                                "sSearch":         "Buscar:",
                                "sUrl":            "",
                                "sInfoThousands":  ",",
                                "sLoadingRecords": "Cargando...",
                                "oPaginate": {
                                        "sFirst":    "Primero",
                                        "sLast":     "Último",
                                        "sNext":     "Sig",
                                        "sPrevious": "Ant"
                                },
                                "oAria": {
                                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                                }
                    },
                    'pageLength': 10,
                    'order': [[ 0, "asc" ]],
                 });

                /*$(".ajustar-credito").click('on', function () {
                    let cuota = $(this).data('credito_detalle');
                    cargar_cuota(cuota, $(this).data('index'));
                });
                $(".reprocesar-credito").click('on', function () {
                    let credito = $(this).data('credito');
                    reprocesarCredito(credito, $(this).data('index'));
                });*/

                


           
        })
        .fail(function () {
        })
        .always(function () {
        });
}


function cargar_mensajes(id_campania) {
		$('#table_message').DataTable().ajax.reload();
		refreshMensajesSelect($("#txt_hd_id_camp").val());
}

function cargar_mensajes_programados() {
	$('#tabla_mensajes_programados_Lunes').DataTable().ajax.reload();
	$('#tabla_mensajes_programados_Martes').DataTable().ajax.reload();
	$('#tabla_mensajes_programados_Miercoles').DataTable().ajax.reload();
	$('#tabla_mensajes_programados_Jueves').DataTable().ajax.reload();
	$('#tabla_mensajes_programados_Viernes').DataTable().ajax.reload();
	$('#tabla_mensajes_programados_Sabado').DataTable().ajax.reload();
	$('#tabla_mensajes_programados_Domingo').DataTable().ajax.reload();
}

function load_campaing_details(id_campania) {
	$.ajax({
		url:base_url+"api/ApiSupervisores/get_campania/",
		type:"POST",
		data:{id_campania:id_campania},
		success:function(response){
	
			$("#nombre_logica").val(response.data[0].nombre_logica);
			$("#sl_proveedor").val(response.data[0].id_proveedor);
			$("#sl_estado_campain").val(response.data[0].estado);
			$("#sl_tipo_servicio").val(response.data[0].type_logic);
			$("#sl_color").val(response.data[0].color);
			$("#sl_modalidad").val(response.data[0].modalidad);
			$("#sl_metodo").val(response.data[0].metodo);
			
			if (response.data[0].metodo == CAMPAIGN_METODO_ENVIO_SLACK) {
				$("#groupSlackUsers").show();
				$("#groupEmails").hide();
			} else {
				$("#groupEmails").show();
				$("#groupSlackUsers").hide();
			}
			
			if (response.ok) {

			} else {

			}
		}
	});
	
	
}

function edit_campaing_modal($id_campania) {
	//si se carga el calendario desde dentro de una campaña ( donde solo se ven sus propios eventos )
	//solo se cerrara el modal. Ya que sino intentara reinicializar las datatables y genera error
	if ($("#txt_hd_id_camp").val() == '') {
		edit_campaing($id_campania);
	}
}

function edit_campaing(id_campania) {
	// console.log(id_campania);
	$("#txt_hd_id_camp").val(id_campania);
	
	load_campaing_details(id_campania);
	iniciarTablaMensajes(id_campania);
	cargar_mensajes(id_campania);
	init_mensajes_programados(id_campania);
	iniciarTagInput();
	loadFilters();
	loadSlackSelect(id_campania);
	loadComoEnviar(id_campania);
	
	$("#view-calendar").addClass("hide");
	$("#btn_save_campaing").removeClass("hide");
	$("#result").addClass("hide");
	$("#view-new_campain").removeClass("hide");
	$("#detalle_campania").show();
}

function refreshMensajesSelect(id_campania) {
	var parametros = {
		'id_camp': id_campania
	};
	$.ajax({
		type: "POST",
		url: base_url + 'api/ApiSupervisores/get_all_active_mensajes',
		data:  parametros,
		success: function (response) {
			loadMensajesSelect(response)
		}
	});
}

function loadMensajesSelect(data) {
	loadMensajeOptions('Lunes', data);
	loadMensajeOptions('Martes', data);
	loadMensajeOptions('Miercoles', data);
	loadMensajeOptions('Jueves', data);
	loadMensajeOptions('Viernes', data);
	loadMensajeOptions('Sabado', data);
	loadMensajeOptions('Domingo', data);
}

function loadMensajeOptions(day, data) {
	var array = data.data;
	$("#select-mensaje-"+day).ddslick('destroy');
	$("#select-mensaje-"+day).html('');
	if (array != '') {
		for (i in array) {
			let predet = ''
			if (array[i].pre == 1) {
				predet = "selected='selected'";
			}
			
			let html = "<option value='"+array[i].id_mensaje+"' "+predet+" data-imagesrc='' data-description='"+array[i].mensaje+"'></option>";
			// console.log(html);
			$("#select-mensaje-"+day).append(html);
		}
	}
	$("#select-mensaje-"+day).ddslick({
		width: '100%'
	});
	
	$(".dd-options").removeAttr('style')
}

$("#btn_new_campaing" ).click(function() {
    $("#result").addClass("hide");
    $("#view-calendar").addClass("hide");
    $("#view-new_campain").removeClass("hide");
});

$("#btn_calendar" ).click(function() {
    $("#result").addClass("hide");
    $("#view-calendar").removeClass("hide");
    var initialLocaleCode = 'es';
            $calendar = $('#fullCalendar');
    
            today = new Date();
            y = today.getFullYear();
            m = today.getMonth();
            d = today.getDate();
    
        var calendarEl = document.getElementById('fullCalendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
										locale: 'es',
										slotEventOverlap: false,
										allDayDefault: false,
                    events: getCalendarUrl(),
										disableResizing:true,
                    initialDate: today,
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    buttonText: {
                        prev: "Ant",
                        next: "Sig",
                        today: "Hoy",
                        month: "Mes",
                        week: "Semana",
                        day: "Día",
                        list: "Agenda"
                    },
    
                  allDayText: "Todo el día",
                  navLinks: true, // can click day/week names to navigate views
                  selectable: true,
                  editable: true,
                  dayMaxEvents: true, // allow "more" link when too many events
                  selectMirror: true,
                  select: function(arg) {
                    //$('#ModalAdd #start').val(arg.start);
                    //$('#ModalAdd #end').val(arg.end);
                    // $('#ModalAdd').modal('show');
                  },
                    eventClick: function(info) {
                    //alert('Event: ' + info.event.title);
                    //alert('Coordinates: ' + info.jsEvent.pageX + ',' + info.jsEvent.pageY);
                    //alert('View: ' + info.view.type);
										// 	console.log('aaaaaaaaaa');
										// 	console.log(info.event.extendedProps.tipo);
											let auxHour = info.event.extendedProps.hora.split( " ");
											
                      $('#ModalView #modal-id-msg-prog').val(info.event.extendedProps.id_msg_prog);
                      $('#ModalView #modal-campain-type').html(info.event.extendedProps.tipo);
                      $('#ModalView #modal-campain-date').html('Todos los ' + info.event.extendedProps.dia + ' a las ' + auxHour[1].slice(0, -3));
                      $('#ModalView #modal-campain-msg').html(info.event.extendedProps.mensaje);
                      $('#ModalView #modal-id-campain').val(info.event.extendedProps.id_campain);
                      $('#ModalView #modal-canceled').val(info.event.extendedProps.canceled);
                      $('#ModalView #modal-id-msg-prog-date').val(auxHour[0]);
                      if (info.event.extendedProps.canceled == true) {
												$('#ModalView #modal-disable').hide();
												$('#ModalView #modal-enable').show();
											} else {
												$('#ModalView #modal-disable').show();
												$('#ModalView #modal-enable').hide();
											}
                      $('#ModalView').modal('show');
                    // change the border color just for fun
                    	info.el.style.borderColor = 'red';
                    },
                  eventRemove: function(arg){
                      alert(arg.event);
                  },
									eventContent: function(arg) {
											// console.log(arg);
										let divEl = document.createElement('div');
										divEl.setAttribute('class', 'fc-event-title');
										
										let htmlTitle = arg.event._def.extendedProps['html'];
										if (arg.event.extendedProps.isHTML) {
											divEl.innerHTML = htmlTitle
										} else {
											divEl.innerHTML = arg.event.title
										}
										
										let divColor = document.createElement('div');
										divColor.setAttribute('class', 'fc-daygrid-event-dot');
										divColor.setAttribute('style', 'border-color:' + arg.event.backgroundColor);

										let divHour = document.createElement('div');
										divHour.setAttribute('class', 'fc-event-time');
										let auxHour = arg.event.extendedProps.hora.split( " ");
										divHour.innerHTML = auxHour[1].slice(0, -3);
											
										let arrayOfDomNodes = [ divColor, divHour,divEl ]
										return { domNodes: arrayOfDomNodes }
									},
									eventDidMount: function(info) {
										
										// $('.fc-daygrid-event').each(function (index, value) {
										// 	console.log(value)
											// var title_header = $(this).children('fc-event-title').clone();
											//
											// $(title_header).insertAfter($(this).children('fc-event-title').first());
											// And I was going to remove the elements which have the classes fc-list-day-side-text and fc-list-day-text from the two <th> elements.
										// })
										
										// info.el.find('span.fc-event-title').html(info.el.find('span.fc-event-title').text());
										// console.log('dasdasd');
										
										// var tooltip = new Tooltip(info.el, {
										// 	title: info.event.extendedProps.description,
										// 	placement: 'top',
										// 	trigger: 'hover',
										// 	container: 'body'
										// });
									}
                  
                  
            });
            calendar.render();
});

$("#nombre_logica" ).change(function() {     
    
    $("#btn_save_campaing").removeClass('hide');

});

$("#btn_save_campaing" ).click(function() {    
event.preventDefault();
base_url = $("input#base_url").val();
        $id_campania = $("#txt_hd_id_camp").val();
        $titulo= $("#nombre_logica").val();
        $estado= $("#sl_estado_campain").val();
        $color= $("#sl_color").val();
        $pro= $("#sl_proveedor").val();
        $servicio= $("#sl_tipo_servicio").val();
        $modalidad= $("#sl_modalidad").val();
        
        
        if ($titulo==""){
            swal("Verifica","No ingreso nombre de la campaña","error");
            return false;
            
        // }else if ($estado==0){
        		// comentado porque no permite guardar el estado "deshabilitado"
            // swal("Verifica","No selecciono estado de la campaña","error");
            // return false;
        }else if ($color==0){
            swal("Verifica","No selecciono un color para la campaña","error");
            return false;
            
        }else if ($pro==0){
            swal("Verifica","No selecciono proveedor","error");
            return false;
        }else if ($servicio==0){
            swal("Verifica","No selecciono servicio","error");
            return false;
        }else if ($modalidad==0){
            swal("Verifica","No selecciono modalidad","error");
            return false;
        
        }else{
					if ($id_campania != '') {
						//update
						updateCampania();
					} else {
						guardarCampania();
					}
        	
            //debugger;
            /*EVALUO SI LA CAMPANIA EXISTE Y SI TIENE ALGUN PASO GUARDADO PARA DIRIGIRLO A LA FUNCION CORRESPONDIENTE*/
            // $.ajax({
            //     url:base_url+"api/ApiSupervisores/search_campania/",
            //     type:"POST",
            //     data:{id_campania:$id_campania},
            //     success:function(response){
            //        
            //         // console.log(response);
            //         if (response.ok) {
            //            
            //             guardarCampaniaGeneral(response.paso);
            //            
						//
            //         } else {
            //             guardarCampaniaPaso0();
            //            
            //         }
            //     }
            // });
            
            
        }
});


$("#btn_reg_mensaje" ).click(function() {    
event.preventDefault();

});

function guardarCampania() {
	var formData= new FormData($("#frm_campania")[0]);
	$.ajax({
			url:base_url+"api/ApiSupervisores/save_campain/",
			type:"POST",
			data:formData,
			cache: false,
			async:false,
			contentType: false,
			processData:false,
			success:function(response){
					// console.log(response);
					if (response.ok) {
							$("#detalle_campania").show();
							$("#txt_hd_id_camp").val(response.id_campaing_return);
							iniciarTablaMensajes(response.id_campaing_return);
						  init_mensajes_programados(response.id_campaing_return)
							loadSlackSelect(response.id_campaing_return)
						  loadComoEnviar(response.id_campaing_return)
					} else {
							Swal.fire({
									icon: 'error',
									title: 'Ocurrio un error en el guardado de la campaña',
									text: response.message,
							});
					}
			}
	});
}

function updateCampania() {
	var formData= new FormData($("#frm_campania")[0]);
	$.ajax({
		url:base_url+"api/ApiSupervisores/update_campain/",
		type:"POST",
		data:formData,
		cache: false,
		async:false,
		contentType: false,
		processData:false,
		success:function(response){
			// console.log(response);
			if (!response.ok) {
				Swal.fire({
					icon: 'error',
					title: 'Ocurrio un error en el guardado de la campaña',
					text: response.message,
				});
			} else {
				Swal.fire({
					icon: 'success',
					title: 'Campaña actualizada',
					text: 'la campaña fue actualizada correctamente.'
				});
			}
		}
	});
}

function iniciarTablaMensajes(id_camp) {
	var tableMessage = $('#table_message').DataTable({
		ajax: {
			url: base_url + 'api/ApiSupervisores/get_all_mensajes',
			'data': {'id_camp': id_camp},
			"type": "POST",
			// "dataSrc": function ( json ) { //debug ajax response
			// 	console.log(json);
			// 	return json;
			// }
		},
		"columns": [
			{"data": "mensaje"}, //mensaje
			{"data": "estado"}, //estado
			{"data": "pre"} //predeterminado
		],

		"columnDefs": [
			{
				"targets": 0,
				"width": "400px",
			},
			{
				"targets": 1,
				// "width": "50px",
				"data": "estado",
				"render": function (data, type, row, meta) {
					if (data == 1) {
						return '<span class="label label-success">ACTIVO</span>';
					} else {
						return '<span class="label label-default">INACTIVO</span>';
					}
				}
			},
			{
				"targets": 2,
				"width": "20px",
				"data": "pre",
				"render": function (data, type, row, meta) {
					if (data == 1) {
						return '<i class="fa fa-check"></i>';
					} else {
						return '';
					}
				}
			},
			{
				"targets": 3,
				"width": "100px",
				"data": "id_mensaje",
				"render": function (data, type, row, meta) {
					return '<button onclick="editarMensaje(' + data + ')" class="btn btn-success btn-sm" title="Editar Mensaje" ><i class="fa fa-pencil"></i></button>' +
						'&nbsp;<button onclick="deleteMensaje(' + data + ')" class="btn btn-danger btn-sm" title="Borrar mensaje" ><i class="fa fa-trash"></i></button>';
				}
			},
		],

		"language": {
			"sProcessing": "Procesando...",
			"sLengthMenu": "Mostrar _MENU_ registros",
			"sZeroRecords": "No se encontraron resultados",
			"sEmptyTable": "Ningún dato disponible en esta tabla",
			"sInfo": "Del _START_ al _END_ de un total de _TOTAL_ reg.",
			"sInfoEmpty": "0 registros",
			"sInfoFiltered": "(filtrado de _MAX_ reg.)",
			"sInfoPostFix": "",
			"sSearch": "Buscar:",
			"sUrl": "",
			"sInfoThousands": ",",
			"sLoadingRecords": "Cargando...",
			"oPaginate": {
				"sFirst": "Primero",
				"sLast": "Último",
				"sNext": "Sig",
				"sPrevious": "Ant"
			},
			"oAria": {
				"sSortAscending": ": Activar para ordenar la columna de manera ascendente",
				"sSortDescending": ": Activar para ordenar la columna de manera descendente"
			}
		},
		'pageLength': 10,
		'order': [[0, "asc"]],
	});
}

function editarMensaje(idMensaje) {
	base_url = $("input#base_url").val();
	var parametros = {
		"id_mensaje": idMensaje,
	};

	$.ajax({
		url: base_url + "api/ApiSupervisores/get_mensaje/",
		type: "POST",
		data: parametros,
		success: function (response) {
			if (response.status == 200) {
				if (!$.trim(response.data)) {
					Swal.fire({
						icon: 'error',
						title: 'Mensaje no encontrado',
						text: response.message
					});
				} else {
					loadMensajeForEdit(response.data[0]);
				}
			} else {
				Swal.fire({
					icon: 'error',
					title: 'Ocurrio un error al cargar el mensaje',
					text: response.message
				});
			}
		}
	});
}

function deleteMensaje(idMensaje) {
	Swal.fire({
		title: 'Borrar mensaje',
		text: 'Estas seguro de que quieres BORRAR el mensaje?',
		icon: 'warning',
		confirmButtonText: 'Aceptar',
		cancelButtonText: 'Cancelar',
		showCancelButton: 'true'
	}).then((result) => {

		$id_campania = $("#txt_hd_id_camp").val();

		

		if (result.value) {
			base_url = $("input#base_url").val();
			var parametros = {
				"id_campania": $id_campania,
				"id_mensaje": idMensaje,
			};
			
			$.ajax({
				url: base_url + "api/ApiSupervisores/check_delete_msg/",
				type: "POST",
				data: parametros,
				success: function (response) {
					if (response == true) {
						Swal.fire({
							icon: 'error',
							title: 'Error',
							text: 'No se puede borrar el mensaje. Esta siendo utilizado como mensaje programado. Si aun desea eliminar este mensaje debera eliminar primero los mensajes programados en la seccion "¿Cuando enviar?"'
						});
					} else {
						var parametros = {
							"id_mensaje": idMensaje,
						};

						$.ajax({
							url: base_url + "api/ApiSupervisores/delete_message/",
							type: "POST",
							data: parametros,
							success: function (response) {
								if (response.status == 200) {
									cargar_mensajes();
								} else {
									Swal.fire({
										icon: 'error',
										title: 'Ocurrio un error al borrar el mensaje',
										text: response.message
									});
								}
							}
						});
					}
				}
			});
		}
	});
}

function loadMensajeForEdit(data) {
	$("#message_id").val(data.id_mensaje);
	$("#mensaje").val(data.mensaje);
	$("#sl_estado_mensaje").val(data.estado);
	if (data.prederterminado == 1) {
		$("#chk_predeterminado").prop('checked', true);
	} else {
		$("#chk_predeterminado").prop('checked', false);
	}
}


function guardarCampaniaGeneral(paso)
{
    
    base_url = $("input#base_url").val();
    var pass = parseInt(paso);
    switch (pass) 
    {
        case 1:
        save_message();
        break;
        case 2:
        alert("paso: "+pass)
        break;
        case 3:
        alert("paso: "+pass)
        break;
        case 4:
        alert("paso: "+pass)
        break;
        case 5:
        alert("paso: "+pass)
        break;
        default:
        console.log('Sin pasos Pendientes');

    }

        
        
}


function save_message() {
	$mensaje = $("#mensaje").val();
	$estado = $("#sl_estado_mensaje").val();
	$chkPre = $("#chk_predeterminado").is(":checked");
	$id_campania = $("#txt_hd_id_camp").val();
	$id_mensaje = $("#message_id").val();

	var parametros = {
		"mensaje": $mensaje,
		"estado": $estado,
		"chkPre": $chkPre ? 1 : 0,
		"id_campania": $id_campania,
		"id_mensaje": $id_mensaje,
	};

	$.ajax({
		url: base_url + "api/ApiSupervisores/save_message/",
		type: "POST",
		data: parametros,
		success: function (response) {

			if (response.ok) {
				cargar_mensajes();
				// refreshMensajesSelect($("#txt_hd_id_camp").val());
				$("#box_view_a_quien_enviar").removeClass('hide');

			} else {
				Swal.fire({
					icon: 'error',
					title: 'Ocurrio un error en el guardado el mensaje',
					text: response.message
				});
			}
		}
	});
}

function check_mensaje_predet() {
	$id_campania = $("#txt_hd_id_camp").val();
	$id_mensaje = $("#message_id").val();

	var parametros = {
		"id_campania": $id_campania,
		"id_mensaje": $id_mensaje,
	};

	$.ajax({
		url: base_url + "api/ApiSupervisores/check_campain_predet/",
		type: "POST",
		data: parametros,
		success: function (response) {
			$chkPre = $("#chk_predeterminado").is(":checked");
			// console.log(response)
			if (response == true && $chkPre) {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'Solo puede haber un mensaje predeterminado por campaña'
				});
			} else {
				save_message();
				$('#mensaje').val('');
				$('#message_id').val('');
				$("#textarea_mensaje_last_position").val(0);
				$('#sl_criterios').val(null).trigger('change');
			}
		}
	});
}

function save_mensaje_programado(day) {
	
	$id_campania = $("#txt_hd_id_camp").val();
	$hour = $("#hour_" + day).val();
	$id_mensaje = $('#select-mensaje-' + day).data('ddslick').selectedData.value;
	
	var parametros = {
		"id_camp": $id_campania,
		"id_msg": $id_mensaje,
		"hour": $hour,
		"day": day,
	};

	$.ajax({
		url: base_url + "api/ApiSupervisores/check_mensaje_programado_day_hour/",
		type: "POST",
		data: parametros,
		success: function (response) {
			// console.log(response)
			if (response == true) {
				
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'No pede haber dos mensajes programados para el mismo horario. Por favor cambie la hora o elimine el existente.'
				});
				
			} else {
				
				$.ajax({
					url: base_url + "api/ApiSupervisores/save_mensaje_programado/",
					type: "POST",
					data: parametros,
					success: function (response) {
						if (response.ok) {
							cargar_mensajes_programados();
							reloadCalendar();
						} else {
							Swal.fire({
								icon: 'error',
								title: 'Ocurrio un error en el guardado el mensaje programado',
								text: response.message
							});
						}
					}
				});
				
			}
		}
	});
}

function init_mensajes_programados(id_campania) {
	init_mensajes_programados_table(id_campania,'Lunes');
	init_mensajes_programados_table(id_campania,'Martes');
	init_mensajes_programados_table(id_campania,'Miercoles');
	init_mensajes_programados_table(id_campania,'Jueves');
	init_mensajes_programados_table(id_campania,'Viernes');
	init_mensajes_programados_table(id_campania,'Sabado');
	init_mensajes_programados_table(id_campania,'Domingo');
}

function init_mensajes_programados_table(id_camp, day) {
		var tableMessage =$('#tabla_mensajes_programados_'+day).DataTable({
			ajax: {
				url: base_url + 'api/ApiSupervisores/get_all_mensajes_programados',
				'data': {'id_camp': id_camp, 'day': day},
				"type": "POST",
				// "dataSrc": function ( json ) { //debug ajax response
				// 	console.log(json);
				// 	return json;
				// }
			},
			"columns": [
				{"data": "hour"}, //mensaje
			],

			"columnDefs": [
				{
					"targets": 0,
					"orderable": false,
					"width": "100px",
				},
				{
					"targets": 1,
					"orderable": false,
					"width": "210px",
					"data": "id",
					"render": function (data, type, row, meta) {
						return '<button  title="Generar CSV" onclick="window.open(base_url + \'api/ApiSupervisores/generate_csv/' +row.id+ '/' +row.id_campania +'\' ,\'_blank\');" class="btn btn-xs btn-warning"><i class="fa fa-file" aria-hidden="true"></i></button>&nbsp;' +
							'<button title="Ver Mensaje" onclick=\'showMensajesProgramadosModal("' +row.mensaje+ '")\' class="btn btn-xs btn-info btn_info_msg_prog" ><i class="fa fa-eye"></i></button>&nbsp;' +
							' <button title="Borrar Mensaje programado" onclick="deleteMensajeProgramador(' + data + ')" class="btn btn-xs btn-danger" ><i class="fa fa-trash"></i></button>';
					}
				},
			],
			"paging": false,
			"searching": false,
			"bInfo" : false,
			order: [],
		});

}

function deleteMensajeProgramador(id) {
	Swal.fire({
		title: 'Borrar mensaje',
		text: 'Estas seguro de que quieres BORRAR el mensaje programado?',
		icon: 'warning',
		confirmButtonText: 'Aceptar',
		cancelButtonText: 'Cancelar',
		showCancelButton: 'true'
	}).then((result) => {
		if (result.value) {
			base_url = $("input#base_url").val();
			var parametros = {
				"id_mensaje": id,
			};

			$.ajax({
				url: base_url + "api/ApiSupervisores/delete_mensaje_programado/",
				type: "POST",
				data: parametros,
				success: function (response) {
					if (response.status == 200) {
						cargar_mensajes_programados();
						reloadCalendar();
					} else {
						Swal.fire({
							icon: 'error',
							title: 'Ocurrio un error al borrar el mensaje programado',
							text: response.message
						});
					}
				}
			});
		}
	});
}

function showMensajesProgramadosModal(msg) {
	$("#modal-mensjes-body").html(msg);
	$("#modal-mensajes-programados").modal('show');
}

function getCalendarUrl() {
	let url = base_url+"api/ApiSupervisores/buscar_cronogramas";
	
	let camp_id = $("#txt_hd_id_camp").val();
	if ( camp_id != '') {
		url = url+'/'+camp_id;
	}
	return url ;
}

function filtroYLogicaInputs(filtro, logica) {
	if (filtro == CAMPAIGN_FILTER_DIAS_ATRASO) {
		if(logica == CAMPAIGN_LOGIC_ENTRE) {
			$("#valor_1_only").show();
			$("#valor_2_only").show();

			$("#valor_1_origen_desde").hide();
			$("#sl_origen_desde").val('');
			$("#origen_desde_valor").val('');
			
			$("#valor_2_origen_hasta").hide();
			$("#sl_origen_hasta").val('');
			$("#origen_hasta_valor").val('');
		} else {
			$("#valor_1_only").show();
			$("#valor_2_only").hide();
			$("#valor_2").val('');

			$("#valor_1_origen_desde").hide();
			$("#sl_origen_desde").val('');
			$("#origen_desde_valor").val('');
			
			$("#valor_2_origen_hasta").hide();
			$("#sl_origen_hasta").val('');
			$("#origen_hasta_valor").val('');
		}
	} else if (filtro == CAMPAIGN_FILTER_FECHA_VENCIMIENTO) {
		if ( logica == CAMPAIGN_LOGIC_ENTRE ) {
			$("#valor_1_only").hide();
			$("#valor_1").val('');
			$("#valor_2_only").hide();
			$("#valor_2").val('');

			$("#valor_1_origen_desde").show();
			$("#valor_2_origen_hasta").show();
		} else {
			$("#valor_1_only").hide();
			$("#valor_1").val('');
			$("#valor_2_only").hide();
			$("#valor_2").val('');

			$("#valor_1_origen_desde").show();
			$("#valor_2_origen_hasta").hide();
			$("#sl_origen_hasta").val('');
			$("#origen_hasta_valor").val('');
		}
	} else if (filtro == CAMPAIGN_FILTER_MONTO_COBRAR) {
		if (logica == CAMPAIGN_LOGIC_ENTRE) {
			$("#valor_1_only").show();
			$("#valor_2_only").show();

			$("#valor_1_origen_desde").hide();
			$("#sl_origen_desde").val('');
			$("#origen_desde_valor").val('');
			
			$("#valor_2_origen_hasta").hide();
			$("#sl_origen_hasta").val('');
			$("#origen_hasta_valor").val('');
		} else {
			$("#valor_1_only").show();
			$("#valor_2_only").hide();
			$("#valor_2").val('');

			$("#valor_1_origen_desde").hide();
			$("#sl_origen_desde").val('');
			$("#origen_desde_valor").val('');
			
			$("#valor_2_origen_hasta").hide();
			$("#sl_origen_hasta").val('');
			$("#origen_hasta_valor").val('');
		}
	}
	
}

function testQuery() {
	let destiny = $("#sl_destino").val();
	let clientType = $("#sl_clientType").val();
	let action = $("#sl_actions").val();
	let xCredits = $("#x_creditos").val();
	let status = $("#sl_status").val();
	let filter = $("#sl_filters").val();
	let logic = $("#sl_logics").val();
	let origen_desde = $("#sl_origen_desde").val();
	let origen_desde_valor = $("#origen_desde_valor").val();
	let valor1 = $("#valor_1").val();
	let origen_hasta = $("#sl_origen_hasta").val();
	let origen_hasta_valor = $("#origen_hasta_valor").val();
	let valor2 = $("#valor_2").val();
	
	let data = {
		'id_campania': $("#txt_hd_id_camp").val(),
		'destiny': destiny,
		'clientType': clientType,
		'action': action,
		'xCredits': xCredits,
		'status': status,
		'filter': filter,
		'logic': logic,
		'origen_desde': origen_desde,
		'origen_desde_valor': origen_desde_valor,
		'valor1': valor1,
		'origen_hasta': origen_hasta,
		'origen_hasta_valor': origen_hasta_valor,
		'valor2': valor2
	}

	$("#spinnerTestQuery").show();

	$.ajax({
		url: base_url + "api/ApiSupervisores/test_query/",
		type: "POST",
		data: data,
		success: function (response) {
			let msg = 'Se encontraron ' + response + " registros."
			$("#spinnerTestQuery").hide();
			Swal.fire({
				icon: 'success',
				text: msg
			});
			// if (response.status == 200) {
			// } else {
			// }
		}
	});
	
		
}

function saveFilters() {
	let destiny = $("#sl_destino").val();
	let clientType = $("#sl_clientType").val();
	let action = $("#sl_actions").val();
	let xCredits = $("#x_creditos").val();
	let status = $("#sl_status").val();
	let filter = $("#sl_filters").val();
	let logic = $("#sl_logics").val();
	let origen_desde = $("#sl_origen_desde").val();
	let origen_desde_valor = $("#origen_desde_valor").val();
	let valor1 = $("#valor_1").val();
	let origen_hasta = $("#sl_origen_hasta").val();
	let origen_hasta_valor = $("#origen_hasta_valor").val();
	let valor2 = $("#valor_2").val();

	let data = {
		'id_campania': $("#txt_hd_id_camp").val(),
		'destiny': destiny,
		'clientType': clientType,
		'action': action,
		'xCredits': xCredits,
		'status': status,
		'filter': filter,
		'logic': logic,
		'origen_desde': origen_desde,
		'origen_desde_valor': origen_desde_valor,
		'valor1': valor1,
		'origen_hasta': origen_hasta,
		'origen_hasta_valor': origen_hasta_valor,
		'valor2': valor2
	}

	$.ajax({
		url: base_url + "api/ApiSupervisores/save_campain_filter/",
		type: "POST",
		data: data,
		success: function (response) {
			let msg = 'Se guardaron los filtros correctamente'
			$("#spinnerTestQuery").hide();
			Swal.fire({
				icon: 'success',
				text: msg
			});
		}
	});
}

function saveMethod() {

	let data = {
		'camp_id': $("#txt_hd_id_camp").val(),
		'metodo': $("#sl_metodo").val(),
	}
	
	$.ajax({
		url: base_url + "api/ApiSupervisores/save_method/",
		type: "POST",
		data: data,
		success: function (response) {
			console.log(response);
			// let msg = 'Se encontraron ' + response + " registros."
			// $("#spinnerTestQuery").hide();
			// Swal.fire({
			// 	icon: 'success',
			// 	text: msg
			// });
			// if (response.status == 200) {
			// } else {
			// }
		}
	});
}

function saveFormat() {
	let data = {
		'camp_id': $("#txt_hd_id_camp").val(),
		'formato': $("#sl_formato").val(),
	}

	$.ajax({
		url: base_url + "api/ApiSupervisores/save_format/",
		type: "POST",
		data: data,
		success: function (response) {
			console.log(response);
			// let msg = 'Se encontraron ' + response + " registros."
			// $("#spinnerTestQuery").hide();
			// Swal.fire({
			// 	icon: 'success',
			// 	text: msg
			// });
			// if (response.status == 200) {
			// } else {
			// }
		}
	});
}

function reloadCalendar() {
	calendar.refetchEvents();
}
