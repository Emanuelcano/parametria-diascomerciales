$(document).ready(function(event){
    $.ajax({
        type: "post",
        url: base_url + "admin_sistemas/obtenerBases",
        success: function (response) {
            let resp = eval(response)
            for (let i = 0; i < resp.length; i++) {
                $("#slc_baseDatos").append("<option value='"+resp[i].base+"'>"+resp[i].nombre+"</option>");
            }
        }
    });
})

crearTable();
function crearTable() {
    let ajax = {
        'type': "POST",
        'url': base_url + 'admin_sistemas/obtenerVariables'
    };

    let columns = [
        { "data": "id" },
        { "data": "nombre_variable" },
        { "data": "estado" },
        { "data": "nombre_apellido" },
        { "data": "fecha_create" },
        { "data": null,
            "render": function(data, type, row, meta ){
                var btnAcciones = "";
            btnAcciones = "<div style='display:flex'><button class='btn btn-primary ejemplo' onclick='edit_variable("+data.id+");' style='margin-right:5px;'><i class='fa fa-cog'></i></button><button class='btn btn-warning' onclick='cambiar_estado("+data.id+")'><i class='fa fa-exchange'></i></button></div> ";
            return btnAcciones;
        }}
    ];
    TablaPaginada("table_variables", 1, "asc", '', '', ajax, columns);
}

$("#btn_new_variable").on("click", function () {
    $("#formulario_variable").css("display", "block");
})

$("#slc_baseDatos").on("change", function () {
    $(".condiciones_busqueda").empty();
    $(".condiciones_busqueda").append("<option value='' selected>Selecione</option>");
    $(".comp_condicion").val("");
    $("#slc_tabla").empty();
    $("#slc_tabla").append("<option value='' selected>Selecione</option>");
    let base = $("#slc_baseDatos").val();
    $.ajax({
        type: "post",
        url: base_url + "admin_sistemas/obtenerTablas",
        data: {"base":base},
        success: function (response) {
            let resp = eval(response)
            for (let i = 0; i < resp.length; i++) {
                $("#slc_tabla").append("<option value='"+resp[i].nombre+"'>"+resp[i].nombre+"</option>");
            }
        }
    });
})

$("#slc_tabla").on("change", function () {
    $(".condiciones_busqueda").empty();
    $(".condiciones_busqueda").append("<option value='' selected>Selecione</option>");
    $(".comp_condicion").val("");
    $("#slc_campo").empty();
    $("#slc_campo").append("<option value='' selected>Selecione</option>");
    let base = $("#slc_baseDatos").val();
    let tabla = $("#slc_tabla").val();
    obtener_tablas(base, tabla);
})

$("#slc_filtro").on("change", function () {
    $("#content_filtros_estatico").css("display","none");
    $("#inp_documento").val("");
    if ($("#slc_filtro").val() == "0") {
        $("#inp_documento").prop("disabled", true);
        $("#content_filtros_estatico").css("display", "flow-root");
    }else if($("#slc_filtro").val() == "1"){
        $("#inp_documento").prop("disabled", false);
    }
})

$("#op_comparacion0").on("change", function () {
    $("#condicionAnd0").remove();
    $("#where_condicion_and").remove();
    if ($("#op_comparacion0").val() == "7") {

        let base = $("#slc_baseDatos").val();
        let tabla = $("#slc_tabla").val();
        let columna = $("#slc_campo_estatico0").val();
        $.ajax({
            type: "post",
            url: base_url + "admin_sistemas/obtenerTipoDato",
            data: {"base":base, "tabla":tabla, "columna":columna},
            success: function (response) {
                let resp = eval(response)
                $(".content_filtro0").append('<div class="col-lg-1 andCond" id="condicionAnd0" style="padding-top:3%;"><h4>AND</h4></div>');
                $(".content_filtro0").append('<div class="col-lg-2 where_condicion" id="where_condicion_and" style="padding-top:3%;"><input type="text" class="form-control inp_where_condicion" placeholder=".::Ingresar valor::." autocomplete="off" id="inp_cond_and0"></div>');
                if (resp[0]["tipo_dato"] == "date" || resp[0]["tipo_dato"] == "datetime") {
                    $("#inp_cond_and0" ).datepicker({
                        dateFormat: 'yy-mm-dd'
                    });
                }else{
                    $('#inp_cond_and0').removeClass('calendarclass');
                    $('#inp_cond_and0').removeClass('hasDatepicker');
                    $('#inp_cond_and0').unbind();
                }
            }
        });

    }
})

$("#op_comparacion1").on("change", function () {
    $("#condicionAnd1").remove();
    $("#where_condicion_and1").remove();
    if ($("#op_comparacion1").val() == "7") {

        let base = $("#slc_baseDatos").val();
        let tabla = $("#slc_tabla").val();
        let columna = $("#slc_campo_estatico1").val();
        $.ajax({
            type: "post",
            url: base_url + "admin_sistemas/obtenerTipoDato",
            data: {"base":base, "tabla":tabla, "columna":columna},
            success: function (response) {
                let resp = eval(response)
                $(".content_filtro1").append('<div class="col-lg-1 andCond" id="condicionAnd1" style="padding-top:3%;"><h4>AND</h4></div>');
                $(".content_filtro1").append('<div class="col-lg-2 where_condicion" id="where_condicion_and1" style="padding-top:3%;"><input type="text" class="form-control inp_where_condicion" placeholder=".::Ingresar valor::." autocomplete="off" id="inp_cond_and1"></div>');
                if (resp[0]["tipo_dato"] == "date" || resp[0]["tipo_dato"] == "datetime") {
                    $("#inp_cond_and1").datepicker({
                        dateFormat: 'yy-mm-dd'
                    });
                }else{
                    $('#inp_cond_and1').removeClass('calendarclass');
                    $('#inp_cond_and1').removeClass('hasDatepicker');
                    $('#inp_cond_and1').unbind();
                }
            }
        });
    }
});

$("#op_comparacion2").on("change", function () {
    $("#condicionAnd2").remove();
    $("#where_condicion_and2").remove();
    if ($("#op_comparacion2").val() == "7") {
        let base = $("#slc_baseDatos").val();
        let tabla = $("#slc_tabla").val();
        let columna = $("#slc_campo_estatico2").val();
        $.ajax({
            type: "post",
            url: base_url + "admin_sistemas/obtenerTipoDato",
            data: {"base":base, "tabla":tabla, "columna":columna},
            success: function (response) {
                let resp = eval(response)
                $(".content_filtro2").append('<div class="col-lg-1 andCond" id="condicionAnd2" style="padding-top:3%;"><h4>AND</h4></div>');
                $(".content_filtro2").append('<div class="col-lg-2 where_condicion" id="where_condicion_and2" style="padding-top:3%;"><input type="text" class="form-control inp_where_condicion" placeholder=".::Ingresar valor::." autocomplete="off" id="inp_cond_and2"></div>');
                if (resp[0]["tipo_dato"] == "date" || resp[0]["tipo_dato"] == "datetime") {
                    $("#inp_cond_and2" ).datepicker({
                        dateFormat: 'yy-mm-dd'
                    });
                }else{
                    $('#inp_cond_and2').removeClass('calendarclass');
                    $('#inp_cond_and2').removeClass('hasDatepicker');
                    $('#inp_cond_and2').unbind();
                }
            }
        });
    }
});

$("#slc_tipo").on("change", function () {
    $("#chk_formato").empty();
    $("#chk_valor").empty();
    if($("#slc_tipo").val() == "caracter"){
        $("#chk_formato").append("<input class='form-check-input check_formato' type='radio' name='dato_formato' value='mayuscula'><label class='form-check-label' style='padding-left:2%;'>Todo Mayuscola</label><br>")
        $("#chk_formato").append("<input class='form-check-input check_formato' type='radio' name='dato_formato' value='minuscula'><label class='form-check-label' style='padding-left:2%;'>Todo Minúscula</label><br>")
        $("#chk_formato").append("<input class='form-check-input check_formato' type='radio' name='dato_formato' value='titulo'><label class='form-check-label' style='padding-left:2%;'>Tipo Titulo</label><br>")
        $("#chk_formato").append("<input class='form-check-input check_formato' type='radio' name='dato_formato' value='oracion'><label class='form-check-label' style='padding-left:2%;'>Tipo oración</label>")
    }else if($("#slc_tipo").val() == "num"){
        $("#chk_formato").append("<input class='form-check-input check_formato' type='radio' name='dato_formato' value='sp_miles'><label class='form-check-label' style='padding-left:2%;'>Separador miles</label><br>")
        $("#chk_formato").append("<input class='form-check-input check_formato' type='radio' name='dato_formato' value='sin_decimales'><label class='form-check-label' style='padding-left:2%;'>Sin decimales</label>")

        $("#chk_valor").append("<input class='form-check-input check_valor' type='radio' name='dato_valor' value='redondeado'><label class='form-check-label' style='padding-left:2%;'>Redondeado</label><br>")
        $("#chk_valor").append("<input class='form-check-input check_valor' type='radio' name='dato_valor' value='entero'><label class='form-check-label' style='padding-left:2%;'>Entero</label>")
    }else if ($("#slc_tipo").val() == "fecha") {
        $("#chk_formato").append("<input class='form-check-input check_formato' type='radio' name='dato_formato' value='fecha_corta'><label class='form-check-label' style='padding-left:2%;'>Fecha corta dd/mm/aaaa</label><br>")
        $("#chk_formato").append("<input class='form-check-input check_formato' type='radio' name='dato_formato' value='fecha_larga'><label class='form-check-label' style='padding-left:2%;'>Fecha larga</label><br>")
        $("#chk_formato").append("<input class='form-check-input check_formato' type='radio' name='dato_formato' value='nombre_dia'><label class='form-check-label' style='padding-left:2%;'>Nombre dia</label><br>")
        $("#chk_formato").append("<input class='form-check-input check_formato' type='radio' name='dato_formato' value='nombre_mes'><label class='form-check-label' style='padding-left:2%;'>Nombre mes</label>")
    }
})

$('#inp_denominacion').keypress(function(e) {  
    if (e.keyCode == 32) {
        let deno =$.trim($('#inp_denominacion').val());
        deno +="_";
        $("#inp_denominacion").val(deno);
    }
    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
    if (!regex.test(key)) {
        event.preventDefault();
        return false;
    }
})
.on(function(e){
    e.preventDefault();
}); 


$("#btn_cerrar").on("click", function () {
    $("#formulario_variable").css("display", "none");
    $("#inp_denominacion").val("");
    $("#slc_baseDatos").val("");
    $("#slc_tabla").val("");
    $("#slc_campo").val("");
    $("#slc_filtro").val("dinamico");
    $("#content_filtros_estatico").css("display","none");
    $("#slc_tipo").val("");
    $("#slc_estados").val("1");
    $("#inp_valor_variable").val("");
    $("#chk_formato").empty();
    $("#chk_valor").empty();
    $(".condiciones_busqueda").val("");
    $(".operadores_comp").val("");
    $(".comp_condicion").val("");
    $(".andCond").remove();
    $(".where_condicion").remove();
})

$('#inp_documento').click(function (event) {
    $(function(){

        $('#inp_documento').keypress(function(e) {
            if(isNaN(this.value + String.fromCharCode(e.charCode))) 
            return false;
        })
        .on(function(e){
            e.preventDefault();
        });          
    });
});

$(".btn_accion").on("click", function () {
    let accion = $(this).attr('boton');
    let formato = $('[name="dato_formato"]:checked').map(function(){
        return this.value;
    }).get();

    let valor = $('[name="dato_valor"]:checked').map(function(){
        return this.value;
    }).get();

    if($("#inp_denominacion").val() == ""){
        Swal.fire({
            title: 'Atencion!',
            text: 'Debe ingresar un nombre para la variable',
        });
        $('#inp_denominacion').focus();
    }else if ($("#slc_baseDatos").val() == "") {
        Swal.fire({
            title: 'Atencion!',
            text: 'Debe seleccionar una base de datos para la variable',
        });
        $('#slc_baseDatos').focus();
    }else if ($("#slc_tabla").val() == "") {
        Swal.fire({
            title: 'Atencion!',
            text: 'Debe seleccionar una tabla para la variable',
        });
        $('#slc_tabla').focus();
    }else if ($("#slc_campo").val() == "") {
        Swal.fire({
            title: 'Atencion!',
            text: 'Debe seleccionar un campo para la variable',
        });
        $('#slc_campo').focus();
    }else if ($("#slc_tipo").val() == "") {
        Swal.fire({
            title: 'Atencion!',
            text: 'Debe seleccionar un tipo de variable',
        });
        $('#slc_tipo').focus();
    }else if (formato == "") {
        Swal.fire({
            title: 'Atencion!',
            text: 'Debe seleccionar el formato para la variable',
        });
    }else if ($("#slc_tipo").val() == "num" && valor == "") {
        Swal.fire({
            title: 'Atencion!',
            text: 'Debe seleccionar el valor para la variable',
        });
    }else{
        let deno = $("#inp_denominacion").val();
        let denominacion = deno.replace(/ /g, "");
        let base = $("#slc_baseDatos").val();
        let tabla = $("#slc_tabla").val();
        let campo = $("#slc_campo").val();
        let tipo = $("#slc_tipo").val();
        let estado = $("#slc_estados").val();
        let data = [accion, denominacion, base, tabla, campo, estado, tipo, formato];

        data.push($("#slc_filtro").val());
        if($("#slc_filtro").val() == "0"){
            
            let condicionA = [$("#slc_campo_estatico0").val(), $("#slc_campo_estatico1").val(), $("#slc_campo_estatico2").val()]
            data.push(condicionA);
            
            let condicionB = [$("#op_comparacion0").val(), $("#op_comparacion1").val(), $("#op_comparacion2").val()];
            data.push(condicionB);
            
            let condicionC = [$("#inp_estatico0").val(), $("#inp_estatico1").val(), $("#inp_estatico2").val()];
            data.push(condicionC);

            if ($("#inp_cond_and0").val() != "" || $("#inp_cond_and1").val() != "" || $("#inp_cond_and2").val() != "")  {
                let condiciones_where = [$("#inp_cond_and0").val(), $("#inp_cond_and1").val(), $("#inp_cond_and2").val()];                    
                data.push(condiciones_where);
            }
        }else{
            let documento = $("#inp_documento").val();
            data.push(documento);
        }

        if (valor.length != 0) {
            data.push(valor);
        }
        $.ajax({
            type: "post",
            url: base_url + "admin_sistemas/accionesGuardarProbar",
            data: {"data":data},
            success: function (response) {
                let resp = JSON.parse(response);
                if (accion == "obtener") {
                    if (resp.status == 200) {
                        $("#inp_valor_variable").val(resp.mensaje);
                    }else{
                        Swal.fire({
                            title: 'Error al probar variable!',
                            text: resp.mensaje,
                        });
                    }
                }else{
                    if (resp.status == 200) {
                        $("#table_variables").dataTable().fnDestroy();
                        Swal.fire({
                            title: 'Registro Guardado!',
                            text: 'La variable se guardo con exito',
                        });
                        $("#data").empty();
                        crearTable();
                        $("#btn_cerrar").trigger("click");
                    }else{
                        Swal.fire({
                            title: 'Error al Guardar!',
                            text: resp.mensaje,
                        });
                    }
                }
            }
        });
    }
})

function cambiar_estado(id){
    Swal.fire({
        title: '¿Desea actualizar el estado de la variable?',
        showCancelButton: true,
        showCancelButton: true,
        confirmButtonText: `Si`,
        cancelButtonText: `Cancelar`,
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: "post",
                url: base_url + "admin_sistemas/updateEstado",
                data: {"id_variable":id},
                success: function (response) {
                    let resp = JSON.parse(response);    
                    if (resp.status == 200) {
                        $("#table_variables").dataTable().fnDestroy();
                        Swal.fire('Se ha realizado la actualizacion', resp.mensaje, 'success');
                        $("#data").empty();
                        crearTable();
                    }else{
                        Swal.fire('Error en la actualizacion', resp.mensaje, 'error');
                    }
                }
            });
        } else {
            Swal.fire('Se ha cancelado la acción', '', 'info')
        }
    })
}

function edit_variable(id){
    $("#base_update").empty();
    $("#tabla_update").empty();
    $("#campo_update").empty();
    $("#div_condiciones").empty();
    $("#inp_valor_variable_modal").val("");
    $.ajax({
        type: "post",
        url: base_url + "admin_sistemas/getDataEditVariable",
        data: {"id_variable":id},
        success: function (response) {
            let data = JSON.parse(response)
            $("#slc_tipo_modal").val(data[0]["tipo"]);
            valorFormato();
            var radiosFormato = $('input:radio[name=dato_formato_m]');
            radiosFormato.filter('[value='+data[0]["formato"]+']').prop('checked', true);

            var radiosValor = $('input:radio[name=dato_valor_m]');
            radiosValor.filter('[value='+data[0]["valor"]+']').prop('checked', true);

            $("#denominacion_update").val(data[0]["nombre_variable"]);
            $("#div_condiciones").append('<label> Condiciones:</label>');
            $("#slc_estado_modal").val(data[0]["estado"]);
            $("#slc_filtro_modal").val(data[0]["filtro"]);
            let z = 1;
            if (data[0]["filtro"] == 0) {
                for (let i = 0; i < 3; i++) {
                    $("#div_condiciones").append('<div class="col-lg-12" id="content_filtro_'+i+'" style="margin-left:-3%;"></div>');
                    $("#content_filtro_"+i).append('<div class="col-lg-2"><select class="form-control campos_select" n_slc="'+i+'" name="Campo" id="condicion_slc_'+i+'"></select></div>');
                    $("#content_filtro_"+i).append('<div class="col-lg-2"><select class="form-control op_condiciones" n_slc="'+i+'" id="condicion_comp_'+i+'"></select></div>');
                    $("#condicion_comp_"+i).append('<option value="" selected>Seleccione</option>');
                    $("#condicion_comp_"+i).append('<option value="1">IGUAL</option>');
                    $("#condicion_comp_"+i).append('<option value="2">MAYOR A</option>');
                    $("#condicion_comp_"+i).append('<option value="3">MAYOR IGUAL</option>');
                    $("#condicion_comp_"+i).append('<option value="4">MENOR A</option>');
                    $("#condicion_comp_"+i).append('<option value="5">MENOR IGUAL</option>');
                    $("#condicion_comp_"+i).append('<option value="6">DISTINTO</option>');
                    $("#condicion_comp_"+i).append('<option value="7">ENTRE</option>');
                    $("#content_filtro_"+i).append('<div class="col-lg-2"><input type="text" class="form-control inp_condiciones" autocomplete="off" id="inp_condicion_'+i+'" placeholder=".::Ingresar valor::."></div>');
                    
                    
                    if (i > 0) {
                        $("#content_filtro_"+i).css("padding-top","1%");
                    }
                    if (data[0]["condicion_"+z][z] != undefined) {
                        if (data[0]["condicion_"+z][z][1] == 7) {
                            $("#content_filtro_"+i).append('<div class="col-lg-1 op_and" id="op_and_'+i+'"><h4>AND</h4></div>');
                            $("#content_filtro_"+i).append('<div class="col-lg-2 div_inp_condiciones_2" id="div_inp_condiciones_2_'+i+'"><input type="text" class="form-control inp_condiciones_2" autocomplete="off" id="inp_condicion_2_'+i+'"></div>');
                            $('#op_and_'+i).css("text-align","center");
                        }
                    }
                    z++;
                }
            }else{
                $("#div_condiciones").append('<div class="col-lg-12" id="div_documento" style="margin-left:-1.4%;"></div>');
                $("#div_documento").append('<div class="col-lg-2"><input type="text" class="form-control" autocomplete="off" disabled value="Documento"></div>');
                $("#div_documento").append('<div class="col-lg-3"><input type="text" class="form-control" autocomplete="off" id="inp_documento_modal" placeholder=".::Documento::."></div>');
            }
            $("#div_denominacion").append('<input type="hidden" class="form-control" id="id_variable" value="'+data[0]["id"]+'">');
            for (let i = 0; i < data["bases"].length; i++) {
                if (data["bases"][i]["base"] == data[0]["base_variable"]) {
                    $("#base_update").append("<option value='"+data["bases"][i]["base"]+"' selected>"+data["bases"][i]["nombre"]+"</option>");
                }else{
                    $("#base_update").append("<option value='"+data["bases"][i]["base"]+"'>"+data["bases"][i]["nombre"]+"</option>");
                }
            }   
            for (let i = 0; i < data["tabla"].length; i++) {
                if (data["tabla"][i]["nombre"] == data[0]["tabla_variable"]) {
                    $("#tabla_update").append("<option value='"+data["tabla"][i]["nombre"]+"' selected>"+data["tabla"][i]["nombre"]+"</option>");
                }else{
                    $("#tabla_update").append("<option value='"+data["tabla"][i]["nombre"]+"'>"+data["tabla"][i]["nombre"]+"</option>");
                }
            }
            $("#condicion_slc_0").append("<option value=''>Seleccione</option>");
            $("#condicion_slc_1").append("<option value=''>Seleccione</option>");
            $("#condicion_slc_2").append("<option value=''>Seleccione</option>");
            for (let i = 0; i < data["campos"].length; i++) {
                    $("#campo_update").append("<option value='"+data["campos"][i]["nombre"]+"'>"+data["campos"][i]["nombre"]+"</option>");

                let x = 1;
                for (let a = 0; a < 3 ; a++) {
                    
                    $("#condicion_slc_"+a).append("<option value='"+data["campos"][i]["nombre"]+"'>"+data["campos"][i]["nombre"]+"</option>");
                    
                    if (data[0]["condicion_"+x][x] != undefined) {
                        if (data["campos"][i]["nombre"] == data[0]["condicion_"+x][x][0]) {
                            $("#condicion_slc_"+a).val(data[0]["condicion_"+x][x][0]);

                            $.ajax({
                                type: "post",
                                url: base_url + "admin_sistemas/obtenerTipoDato",
                                data: {"base":data[0]["base_variable"], "tabla":data[0]["tabla_variable"], "columna":data[0]["condicion_"+x][x][0]},
                                success: function (response) {
                                    let resp = eval(response)
                                    if (resp[0]["tipo_dato"] == "date" || resp[0]["tipo_dato"] == "datetime") {
                                        $("#inp_condicion_"+a).datepicker({
                                            dateFormat: 'yy-mm-dd'
                                        });
                                        
                                        $("#inp_condicion_2_"+a).datepicker({
                                            dateFormat: 'yy-mm-dd'
                                        });
                                    }
                                }
                            });
                        }
                    }
                    if (data[0]["condicion_"+x] != "" || data[0]["condicion_"+x] != undefined) {
                        if (data[0]["condicion_"+x][x] != undefined) {
                            $("#condicion_comp_"+a).val(data[0]["condicion_"+x][x][1]);
                            if (data[0]["condicion_"+x][x][1] == 7) {
                                $("#inp_condicion_2_"+a).val(data[0]["condicion_"+x][x][4]);
                            }
                            $("#inp_condicion_"+a).val(data[0]["condicion_"+x][x][2]);
                        }
                    }
                    x++;
                }                
            };
            $("#campo_update").val(data[0]["select_variable"]);
            $(".op_condiciones").on("change", function () {
                var numero = $(this).attr('n_slc');
                $("#inp_condicion_"+numero).val("");
                if ($("#condicion_comp_"+numero).val() != 7) {
                    $("#op_and_"+numero).remove();
                    $("#div_inp_condiciones_2_"+numero).remove();
                }else{
                    $("#content_filtro_"+numero).append('<div class="col-lg-1" id="op_and_'+numero+'"><h4>AND</h4></div>');
                    $("#content_filtro_"+numero).append('<div class="col-lg-2" id="div_inp_condiciones_2_'+numero+'"><input type="text" class="form-control inp_condiciones_2" placeholder=".::Ingresar valor::." autocomplete="off" id="inp_condicion_2_'+numero+'"></div>');
                    $('#op_and_'+numero).css("text-align","center");
                }
            })

            $(".campos_select").on("change", function () {
                var numero = $(this).attr('n_slc');
                $("#inp_condicion_"+numero).val("");
                $("#condicion_comp_"+numero).val("");
                $("#op_and_"+numero).remove();
                $("#div_inp_condiciones_2_"+numero).remove();
            })

            $("#condicion_slc_0").on("change", function () {
                let base = $("#base_update").val();
                let tabla = $("#tabla_update").val();
                let columna = $("#condicion_slc_0").val();
                $.ajax({
                    type: "post",
                    url: base_url + "admin_sistemas/obtenerTipoDato",
                    data: {"base":base, "tabla":tabla, "columna":columna},
                    success: function (response) {
                        let resp = eval(response)
                        if (resp[0]["tipo_dato"] == "date" || resp[0]["tipo_dato"] == "datetime") {
                            $("#inp_condicion_0" ).datepicker({
                                dateFormat: 'yy-mm-dd'
                            });
                            $("#inp_condicion_2_0" ).datepicker({
                                dateFormat: 'yy-mm-dd'
                            });
                        }else{
                            $('#inp_condicion_0').removeClass('calendarclass');
                            $('#inp_condicion_0').removeClass('hasDatepicker');
                            $('#inp_condicion_0').unbind();

                            $('#inp_condicion_2_0').removeClass('calendarclass');
                            $('#inp_condicion_2_0').removeClass('hasDatepicker');
                            $('#inp_condicion_2_0').unbind();
                        }
                    }
                });
            });
            
            $("#condicion_slc_1").on("change", function () {
                let base = $("#base_update").val();
                let tabla = $("#tabla_update").val();
                let columna = $("#condicion_slc_1").val();
                $.ajax({
                    type: "post",
                    url: base_url + "admin_sistemas/obtenerTipoDato",
                    data: {"base":base, "tabla":tabla, "columna":columna},
                    success: function (response) {
                        let resp = eval(response)
                        if (resp[0]["tipo_dato"] == "date" || resp[0]["tipo_dato"] == "datetime") {
                            $("#inp_condicion_1" ).datepicker({
                                dateFormat: 'yy-mm-dd'
                            });
                            $("#inp_condicion_2_1" ).datepicker({
                                dateFormat: 'yy-mm-dd'
                            });
                        }else{
                            $('#inp_condicion_1').removeClass('calendarclass');
                            $('#inp_condicion_1').removeClass('hasDatepicker');
                            $('#inp_condicion_1').unbind();

                            $('#inp_condicion_2_1').removeClass('calendarclass');
                            $('#inp_condicion_2_1').removeClass('hasDatepicker');
                            $('#inp_condicion_2_1').unbind();
                        }
                    }
                });
            });
            
            $("#condicion_slc_2").on("change", function () {
                let base = $("#base_update").val();
                let tabla = $("#tabla_update").val();
                let columna = $("#condicion_slc_2").val();
                $.ajax({
                    type: "post",
                    url: base_url + "admin_sistemas/obtenerTipoDato",
                    data: {"base":base, "tabla":tabla, "columna":columna},
                    success: function (response) {
                        let resp = eval(response)
                        if (resp[0]["tipo_dato"] == "date" || resp[0]["tipo_dato"] == "datetime") {
                            $("#inp_condicion_2" ).datepicker({
                                dateFormat: 'yy-mm-dd'
                            });
                            $("#inp_condicion_2_2" ).datepicker({
                                dateFormat: 'yy-mm-dd'
                            });
                        }else{
                            $('#inp_condicion_2').removeClass('calendarclass');
                            $('#inp_condicion_2').removeClass('hasDatepicker');
                            $('#inp_condicion_2').unbind();

                            $('#inp_condicion_2_2').removeClass('calendarclass');
                            $('#inp_condicion_2_2').removeClass('hasDatepicker');
                            $('#inp_condicion_2_2').unbind();
                        }
                    }
                });
            });

            $("#modal_update_variables").modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });
            $("#modal_update_variables").show();
        }
    });

}

$("#base_update").on("change", function () {
    $(".op_condiciones").val("");
    $(".campos_select").empty();
    $(".campos_select").append("<option value='' selected>Selecione</option>");
    $(".inp_condiciones").val("");
    $("#condicion_slc").empty();
    $("#tabla_update").empty();
    $("#tabla_update").append("<option value='' selected>Selecione</option>");
    $(".op_and").remove();
    $(".div_inp_condiciones_2").remove();
    let base = $("#base_update").val();
    $.ajax({
        type: "post",
        url: base_url + "admin_sistemas/obtenerTablas",
        data: {"base":base},
        success: function (response) {
            let resp = eval(response)
            for (let i = 0; i < resp.length; i++) {
                $("#tabla_update").append("<option value='"+resp[i].nombre+"'>"+resp[i].nombre+"</option>");
            }
        }
    });
});

$("#tabla_update").on("change", function () {
    $(".campos_select").empty();
    $(".campos_select").append("<option value='' selected>Selecione</option>");
    $(".op_condiciones").val("");
    $(".inp_condiciones").val("");
    $(".op_and").remove();
    $(".div_inp_condiciones_2").remove();
    let base = $("#base_update").val();
    let tabla = $("#tabla_update").val();
    $.ajax({
        type: "post",
        url: base_url + "admin_sistemas/obtenerCampos",
        data: {"tabla":tabla, "base":base},
        success: function (response) {
            let resp = eval(response)
            for (let i = 0; i < resp.length; i++) {
                $(".campos_select").append("<option value='"+resp[i].nombre+"'>"+resp[i].nombre+"</option>");
            }
        }
    });
});

$("#btn_update").on("click", function () {
    let nombre = $("#denominacion_update").val();
    let base = $("#base_update").val();
    let tabla = $("#tabla_update").val();
    let campo = $("#campo_update").val();

    let condicion = $("#condicion_slc_0").val();
    let comparador = $("#condicion_comp_0").val();
    let inp_condicion = $("#inp_condicion_0").val();
    let inp_condicion_and = $("#inp_condicion_2_0").val();
    let condiciones_1 = [condicion, comparador, inp_condicion, inp_condicion_and];
    
    let condicion_1 = $("#condicion_slc_1").val();
    let comparador_1 = $("#condicion_comp_1").val();
    let inp_condicion_1 = $("#inp_condicion_1").val();
    let inp_condicion_and_1 = $("#inp_condicion_2_1").val();
    let condiciones_2 = [condicion_1, comparador_1, inp_condicion_1, inp_condicion_and_1];
    
    let condicion_2 = $("#condicion_slc_2").val();
    let comparador_2 = $("#condicion_comp_2").val();
    let inp_condicion_2 = $("#inp_condicion_2").val();
    let inp_condicion_and_2 = $("#inp_condicion_2_2").val();
    let condiciones_3 = [condicion_2, comparador_2, inp_condicion_2, inp_condicion_and_2];

    let formato = $('[name="dato_formato_m"]:checked').map(function(){
        return this.value;
    }).get();

    let valor = $('[name="dato_valor_m"]:checked').map(function(){
        return this.value;
    }).get();

    let filtro = $("#slc_filtro_modal").val();
    let estado = $("#slc_estado_modal").val();
    let tipo = $("#slc_tipo_modal").val();
    
    let id = $("#id_variable").val();

    let documento = $("#inp_documento_modal").val();
    
    if ($(".inp_condiciones").val() == "" ){
        Swal.fire({
            title: 'Error al guardar la variable!',
            text: "Debe agregar alguna condicion",
        });
    }else if($("#inp_documento_modal").val() == ""){
        Swal.fire({
            title: 'Error al guardar la variable!',
            text: "Debe agregar un documento",
        });
    }else if (nombre == "" ){
        Swal.fire({
            title: 'Error al guardar la variable!',
            text: "Debe agregar algun nombre para la variable",
        });
    }else if(tipo == ""){
        Swal.fire({
            title: 'Error al guardar la variable!',
            text: "Debe seleccionar un tipo",
        });
    }else if(formato == ""){
        Swal.fire({
            title: 'Error al guardar la variable!',
            text: "Debe seleccionar un formato",
        });
    }else if(valor == "" && tipo == "num"){
        Swal.fire({
            title: 'Error al guardar la variable!',
            text: "Debe seleccionar un valor",
        });
    }else{
        $.ajax({
            type: "post",
            url: base_url + "admin_sistemas/updateVariable",
            data: {
                "id":id,
                "nombre":nombre,
                "base":base,
                "tabla":tabla,
                "campo":campo,
                "condiciones_1":condiciones_1,
                "condiciones_2":condiciones_2,
                "condiciones_3":condiciones_3,
                "filtro":filtro,
                "estado":estado,
                "tipo":tipo,
                "formato":formato,
                "valor":valor,
                "documento":documento,
            },
            success: function (response) {
                let resp = JSON.parse(response);    
                if (resp.status == 200) {
                    $("#table_variables").dataTable().fnDestroy();
                    Swal.fire('Se ha realizado la actualizacion', resp.mensaje, 'success');
                    $("#data").empty();
                    crearTable();
                }else{
                    Swal.fire('Error en la actualizacion', resp.mensaje, 'error');
                }
                $("#btn_cerrar_modal").trigger('click');
            }
        });
    }
})

$("#slc_filtro_modal").change(function () {
    let base = $("#base_update").val();
    let tabla = $("#tabla_update").val();
    $("#div_condiciones").empty();
    $("#div_condiciones").append('<label> Condiciones:</label>');
    if ($("#slc_filtro_modal").val() == 0) {
        for (let i = 0; i < 3; i++) {
            $("#div_condiciones").append('<div class="col-lg-12" id="content_filtro_'+i+'" style="margin-left:-3%;"></div>');
            $("#content_filtro_"+i).append('<div class="col-lg-2"><select class="form-control campos_select" n_slc="'+i+'" name="Campo" id="condicion_slc_'+i+'"></select></div>');
            $("#content_filtro_"+i).append('<div class="col-lg-2"><select class="form-control op_condiciones" n_slc="'+i+'" id="condicion_comp_'+i+'"></select></div>');
            $("#condicion_comp_"+i).append('<option value="" selected>Seleccione</option>');
            $("#condicion_comp_"+i).append('<option value="1">IGUAL</option>');
            $("#condicion_comp_"+i).append('<option value="2">MAYOR A</option>');
            $("#condicion_comp_"+i).append('<option value="3">MAYOR IGUAL</option>');
            $("#condicion_comp_"+i).append('<option value="4">MENOR A</option>');
            $("#condicion_comp_"+i).append('<option value="5">MENOR IGUAL</option>');
            $("#condicion_comp_"+i).append('<option value="6">DISTINTO</option>');
            $("#condicion_comp_"+i).append('<option value="7">ENTRE</option>');
            $("#content_filtro_"+i).append('<div class="col-lg-2"><input type="text" class="form-control inp_condiciones" placeholder=".::Ingresar valor::." autocomplete="off" id="inp_condicion_'+i+'"></div>');
        
            if (i > 0) {
                $("#content_filtro_"+i).css("padding-top","1%");
            }
        }
        $(".campos_select").append('<option value="">Seleccione</option>');
        $(".campos_select").val("");
        obtener_tablas(base, tabla);
    }else{
        $("#div_condiciones").append('<div class="col-lg-12" id="div_documento" style="margin-left:-3%;"></div>');
        $("#div_documento").append('<div class="col-lg-2"><input type="text" class="form-control" autocomplete="off" disabled value="Documento"></div>');
        $("#div_documento").append('<div class="col-lg-3"><input type="text" class="form-control" autocomplete="off" id="inp_documento_modal" placeholder=".::Documento::."></div>');
    }

    $(".op_condiciones").on("change", function () {
        var numero = $(this).attr('n_slc');
        $("#inp_condicion_"+numero).val("");
        if ($("#condicion_comp_"+numero).val() != 7) {
            $("#op_and_"+numero).remove();
            $("#div_inp_condiciones_2_"+numero).remove();
        }else{
            $("#content_filtro_"+numero).append('<div class="col-lg-1" id="op_and_'+numero+'"><h4>AND</h4></div>');
            $("#content_filtro_"+numero).append('<div class="col-lg-2" id="div_inp_condiciones_2_'+numero+'"><input type="text" class="form-control inp_condiciones_2" placeholder=".::Ingresar valor::." autocomplete="off" id="inp_condicion_2_'+numero+'"></div>');
            $('#op_and_'+numero).css("text-align","center");
        }
    })
});


function obtener_tablas(base, tabla){
    $.ajax({
        type: "post",
        url: base_url + "admin_sistemas/obtenerCampos",
        data: {"tabla":tabla, "base":base},
        success: function (response) {
            let resp = eval(response)
            for (let i = 0; i < resp.length; i++) {
                $("#slc_campo").append("<option value='"+resp[i].nombre+"'>"+resp[i].nombre+"</option>");
                $("#slc_campo_estatico0").append("<option value='"+resp[i].nombre+"'>"+resp[i].nombre+"</option>");
                $("#slc_campo_estatico1").append("<option value='"+resp[i].nombre+"'>"+resp[i].nombre+"</option>");
                $("#slc_campo_estatico2").append("<option value='"+resp[i].nombre+"'>"+resp[i].nombre+"</option>");
                $(".campos_select").append("<option value='"+resp[i].nombre+"'>"+resp[i].nombre+"</option>");
            }
        }
    });
}

$("#slc_campo_estatico0").on("change", function () {
    let base = $("#slc_baseDatos").val();
    let tabla = $("#slc_tabla").val();
    let columna = $("#slc_campo_estatico0").val();
    $.ajax({
        type: "post",
        url: base_url + "admin_sistemas/obtenerTipoDato",
        data: {"base":base, "tabla":tabla, "columna":columna},
        success: function (response) {
            let resp = eval(response)
            if (resp[0]["tipo_dato"] == "date" || resp[0]["tipo_dato"] == "datetime") {
                $("#inp_estatico0" ).datepicker({
                    dateFormat: 'yy-mm-dd'
                });
            }else{
                $('#inp_estatico0').removeClass('calendarclass');
                $('#inp_estatico0').removeClass('hasDatepicker');
                $('#inp_estatico0').unbind();
            }
        }
    });
});

$("#slc_campo_estatico1").on("change", function () {
    let base = $("#slc_baseDatos").val();
    let tabla = $("#slc_tabla").val();
    let columna = $("#slc_campo_estatico1").val();
    $.ajax({
        type: "post",
        url: base_url + "admin_sistemas/obtenerTipoDato",
        data: {"base":base, "tabla":tabla, "columna":columna},
        success: function (response) {
            let resp = eval(response)
            if (resp[0]["tipo_dato"] == "date" || resp[0]["tipo_dato"] == "datetime") {
                $("#inp_estatico1" ).datepicker({
                    dateFormat: 'yy-mm-dd'
                });
            }else{
                $('#inp_estatico1').removeClass('calendarclass');
                $('#inp_estatico1').removeClass('hasDatepicker');
                $('#inp_estatico1').unbind();
            }
        }
    });
});

$("#slc_campo_estatico2").on("change", function () {
    let base = $("#slc_baseDatos").val();
    let tabla = $("#slc_tabla").val();
    let columna = $("#slc_campo_estatico2").val();
    $.ajax({
        type: "post",
        url: base_url + "admin_sistemas/obtenerTipoDato",
        data: {"base":base, "tabla":tabla, "columna":columna},
        success: function (response) {
            let resp = eval(response)
            if (resp[0]["tipo_dato"] == "date" || resp[0]["tipo_dato"] == "datetime") {
                $("#inp_estatico2").datepicker({
                    dateFormat: 'yy-mm-dd'
                });
            }else{
                $('#inp_estatico2').removeClass('calendarclass');
                $('#inp_estatico2').removeClass('hasDatepicker');
                $('#inp_estatico2').unbind();
            }
        }
    });
});

function valorFormato() {
    $("#chk_formato_modal").empty();
    $("#chk_valor_modal").empty();
    if($("#slc_tipo_modal").val() == "caracter"){
        $("#chk_formato_modal").append("<input class='form-check-input check_formato' type='radio' name='dato_formato_m' value='mayuscula'><label class='form-check-label' style='padding-left:2%;'>Todo Mayuscola</label><br>")
        $("#chk_formato_modal").append("<input class='form-check-input check_formato' type='radio' name='dato_formato_m' value='minuscula'><label class='form-check-label' style='padding-left:2%;'>Todo Minúscula</label><br>")
        $("#chk_formato_modal").append("<input class='form-check-input check_formato' type='radio' name='dato_formato_m' value='titulo'><label class='form-check-label' style='padding-left:2%;'>Tipo Titulo</label><br>")
        $("#chk_formato_modal").append("<input class='form-check-input check_formato' type='radio' name='dato_formato_m' value='oracion'><label class='form-check-label' style='padding-left:2%;'>Tipo oración</label>")
    }else if($("#slc_tipo_modal").val() == "num"){
        $("#chk_formato_modal").append("<input class='form-check-input check_formato' type='radio' name='dato_formato_m' value='sp_miles'><label class='form-check-label' style='padding-left:2%;'>Separador miles</label><br>")
        $("#chk_formato_modal").append("<input class='form-check-input check_formato' type='radio' name='dato_formato_m' value='sin_decimales'><label class='form-check-label' style='padding-left:2%;'>Sin decimales</label>")

        $("#chk_valor_modal").append("<input class='form-check-input check_valor' type='radio' name='dato_valor_m' value='redondeado'><label class='form-check-label' style='padding-left:2%;'>Redondeado</label><br>")
        $("#chk_valor_modal").append("<input class='form-check-input check_valor' type='radio' name='dato_valor_m' value='entero'><label class='form-check-label' style='padding-left:2%;'>Entero</label>")
    }else if ($("#slc_tipo_modal").val() == "fecha") {
        $("#chk_formato_modal").append("<input class='form-check-input check_formato' type='radio' name='dato_formato_m' value='fecha_corta'><label class='form-check-label' style='padding-left:2%;'>Fecha corta dd/mm/aaaa</label><br>")
        $("#chk_formato_modal").append("<input class='form-check-input check_formato' type='radio' name='dato_formato_m' value='fecha_larga'><label class='form-check-label' style='padding-left:2%;'>Fecha larga</label><br>")
        $("#chk_formato_modal").append("<input class='form-check-input check_formato' type='radio' name='dato_formato_m' value='nombre_dia'><label class='form-check-label' style='padding-left:2%;'>Nombre dia</label><br>")
        $("#chk_formato_modal").append("<input class='form-check-input check_formato' type='radio' name='dato_formato_m' value='nombre_mes'><label class='form-check-label' style='padding-left:2%;'>Nombre mes</label>")
    }
}

$("#slc_tipo_modal").on("change", function () {
    valorFormato();
});

$("#btn_obternerVal_modal").on("click", function () {
    let formato = $('[name="dato_formato_m"]:checked').map(function(){
        return this.value;
    }).get();

    let valor = $('[name="dato_valor_m"]:checked').map(function(){
        return this.value;
    }).get();

    let tipo = $("#slc_tipo_modal").val();

    if ($(".inp_condiciones").val() == "" ){
        Swal.fire({
            title: 'Error al obtener el valor!',
            text: "Debe agregar alguna condicion para la busqueda",
        });
    }else if($("#inp_documento_modal").val() == ""){
        Swal.fire({
            title: 'Error al obtener el valor!',
            text: "Debe agregar un documento para la busqueda",
        });
    }else if(tipo == ""){
        Swal.fire({
            title: 'Error al obtener el valor!',
            text: "Debe seleccionar un tipo",
        });
    }else if(formato == ""){
        Swal.fire({
            title: 'Error al obtener el valor!',
            text: "Debe seleccionar un formato",
        });
    }else if(valor == "" && tipo == "num"){
        Swal.fire({
            title: 'Error al obtener el valor!',
            text: "Debe seleccionar un valor",
        });
    }else{
        let accion = "obtener";

        let deno = $("#denominacion_update").val();
        let denominacion = deno.replace(/ /g, "");
        let base = $("#base_update").val();
        let tabla = $("#tabla_update").val();
        let campo = $("#campo_update").val();
        let estado = $("#slc_estado_modal").val();
        let data = [accion, denominacion, base, tabla, campo, estado, tipo, formato];

        data.push($("#slc_filtro_modal").val());

        if($("#slc_filtro_modal").val() == "0"){
                
            let condicionA = [$("#condicion_slc_0").val(), $("#condicion_slc_1").val(), $("#condicion_slc_2").val()]
            data.push(condicionA);
            
            let condicionB = [$("#condicion_comp_0").val(), $("#condicion_comp_1").val(), $("#condicion_comp_2").val()];
            data.push(condicionB);
            
            let condicionC = [$("#inp_condicion_0").val(), $("#inp_condicion_1").val(), $("#inp_condicion_2").val()];
            data.push(condicionC);

            if ($("#inp_condicion_2_0").val() != "" || $("#inp_condicion_2_1").val() != "" || $("#inp_condicion_2_2").val() != "")  {
                let condiciones_where = [$("#inp_condicion_2_0").val(), $("#inp_condicion_2_1").val(), $("#inp_condicion_2_2").val()];                    
                data.push(condiciones_where);
            }
        }else{
            let documento = $("#inp_documento_modal").val();
            data.push(documento);
        }

        if (valor.length != 0) {
            data.push(valor);
        }
        
        $.ajax({
            type: "post",
            url: base_url + "admin_sistemas/accionesGuardarProbar",
            data: {"data":data},
            success: function (response) {
                let resp = JSON.parse(response);
                if (resp.status == 200) {
                    $("#inp_valor_variable_modal").val(resp.mensaje);
                }else{
                    Swal.fire({
                        title: 'Error al probar variable!',
                        text: resp.mensaje,
                    });
                }
            }
        });
    }
});

$('#denominacion_update').keypress(function(e) {  
    if (e.keyCode == 32) {
        let deno =$.trim($('#denominacion_update').val());
        deno +="_";
        $("#denominacion_update").val(deno);
    }
    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
    if (!regex.test(key)) {
        event.preventDefault();
        return false;
    }
})
.on(function(e){
    e.preventDefault();
}); 

