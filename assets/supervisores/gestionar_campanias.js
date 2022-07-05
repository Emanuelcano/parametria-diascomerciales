/*
|--------------------------------------------------------------------------
| Controlador de segundo nivel proceso procesamiento campañias Ing. Esthiven Garcia
|--------------------------------------------------------------------------
*/

$( document ).ready(function() {
    /*
    |-------------------------------------------------------------------------------------------------------------------
    | Area de inicializacion de variables Ing. Esthiven Garcia
    |-------------------------------------------------------------------------------------------------------------------
    */
    
    let base_url= $("#base_url").val();
    
    
    /*
    |-------------------------------------------------------------------------------------------------------------------
    | Area de inicializacion de pluggins Ing. Esthiven Garcia
    | esta seccion consume las diferentes librerias a consumir en la vista.
    |-------------------------------------------------------------------------------------------------------------------
    */
    
    
     $('#sl_criterios').select2({
        placeholder: '.: Selecciona los criterios :.',
        multiple : true
     });
    
    
     $('#date_rangeA,#date_rangeB').datepicker(
            {
                autoUpdateInput: false,
                dateFormat: 'yy-mm-dd',
                "locale": 
                {
                    
                    "daysOfWeek": ["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
                    "monthNames": ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
                    "firstDay": 1
                },
                startDate: moment().subtract(29, 'days'),
                endDate  : moment(),
                timePicker: false,
            }
    
            
        );
    
    
    
        
            cargaCriterios();
    
    function cargaCriterios(){
      $.ajax({
            dataType: "json",
            url:   base_url+'ApiBuscaLogicas',
            type: 'POST',
            beforeSend: function(){
                //Lo que se hace antes de enviar el formulario
                },
            success: function(response){
                //lo que se si el destino devuelve algo
                    //console.log(response);
                     var regis = eval(response);
                     //console.log(response[14]['base_datos']);
    
                     html='<option value="0">- primero seleccion un criterio de busqueda -</option>';
                      for (var i = 0; i < regis.length; i++) {
                          html +='<option value="'+regis[i]['idrango']+'"  data-base_datos="'+regis[i]['base_datos']+'" data-tabla_primaria="'+regis[i]['tabla_primaria']+'" data-tabla="'+regis[i]['tabla']+'" data-campo="'+regis[i]['campo']+'" data-condicion="'+regis[i]['condicion']+'" >'+regis[i]['denominacion']+'</option>';
                          
                      }
    
                    $("#sl_criterios").html(html);
                    //$("#sl_campania").selectpicker('refresh');
            },
            error:  function(xhr,err){
                alert("readyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\n \n responseText: "+xhr.responseText);
            }
        });
    }
    /*
    |-------------------------------------------------------------------------------------------------------------------
    | Area de funciones para manejo de calendario Ing. Esthiven Garcia
    | esta seccion determina las diferentes funciones y comportamiento de la vista.
    |-------------------------------------------------------------------------------------------------------------------
    */
	
	$('#save_message').click(function(){
		check_mensaje_predet();
	});
	
	$('#mensaje').blur(function() {
		$("#textarea_mensaje_last_position").val($(this).caret());
	});
	
	$('#sl_criterios').on('select2:select', function (e) {
        //debugger;
        $(".enable-control").attr("disabled",false);
        var data = e.params.data;
        //var xyz  = data.select2().find(':selected').data('campo');
        var base_datos = ($(e.params.data.element).data("base_datos"));
        var tabla_primaria = ($(e.params.data.element).data("tabla_primaria"));
        var tabla = ($(e.params.data.element).data("tabla"));
        var campo = ($(e.params.data.element).data("campo"));
        var condicion = ($(e.params.data.element).data("condicion"));
        var termino = "FROM";
        let cadena = $("#query_contenido").val();
        var posicion = cadena.indexOf(termino);
    
        // console.log(data.text);
        var objetivos = $("#mensaje").val();
        let largoMensaje = objetivos.length;
        let textarea_mensaje_last_position = $("#textarea_mensaje_last_position").val();
        let parte1 = objetivos.substring(0,textarea_mensaje_last_position);
        let parte2 = objetivos.substring(textarea_mensaje_last_position, largoMensaje);
        let variable = "$" + data.text + " ";
        
				objetivos = parte1+variable+parte2;
        $("#mensaje").val(objetivos);
    
        if (posicion !== -1){
            let nueva_cadena = '';
            nueva_cadena = cadena.replace(termino, " , "+campo+ " FROM ");
    
            if(nueva_cadena.search('INNER JOIN ' +tabla+' '+condicion ) == -1){
                nueva_cadena+= ' INNER JOIN ' +tabla+' '+condicion
            } 
    
            $("#query_contenido").val("");
            $("#query_contenido").val(nueva_cadena);
            // $("#query_contenido").append(", "+campo+ " ");
    
        }else{
    
            var query_contenido = $("#query_contenido").val()
    
            if (!query_contenido.indexOf("INNER JOIN credito_detalle ON maestro.credito_detalle.id_credito = creditos.id") > -1) {
                $("#query_contenido").append("SELECT "+campo+" FROM "+base_datos+"."+tabla_primaria+" INNER JOIN "+tabla+" "+condicion);
            }else{
                $("#query_contenido").append("SELECT "+campo+" FROM "+base_datos+"."+tabla_primaria);
            }
        }
    
        console.log($("#query_contenido").val());
        //console.log(condicion);
    });
    
    
    /*
    |-------------------------------------------------------------------------------------------------------------------
    | Area de funciones para manejo de calendario Ing. Nicolaiev Brito
    | BOTON Y FUNCIONALIDAD API PARA CREAR CSV DE CAMPANIAS GENERALES
    |-------------------------------------------------------------------------------------------------------------------
    */
    $("#btn_csv_descarga").addClass('hide');
    $('#btn_csv_query').click(function (){
    
        var boton = $('#btn_csv_descarga');
        let textCSV = $('#sl_criterios').select2('data');
        let id_Logica = $('#id_logica').val();
        let arrayCSV = [];
    
        $.each(textCSV, function (j, valor) {
            arrayCSV.push(valor.text);
        });
        arrayCSV = JSON.stringify(arrayCSV, null, 2);
        console.log(arrayCSV);
        console.log(id_Logica);
    
        formData = new FormData();
        formData.append('idLogicaCSV',id_Logica);
        
        if (arrayCSV != "") {
    
            $.ajax({
                url:base_url+"api/ApiSupervisores/GenerarCsvCampaniasGenerales",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                crossDomain: true,
            })
                .done(function (response) {
                    Swal.fire('Bien','Se genero el CSV con exito','success');
                    $("#btn_csv_descarga").removeClass('hide');
                    $("#btn_csv_query").addClass('hide');
                    var nombreArchivo = "ReporteCampaniaCSV"+moment().format('YYYYMMDD')+"-"+id_Logica+".xlsx";
                    $("#btn_csv_descarga").val(nombreArchivo);
                    $('#btn_csv_descarga').attr('href', 'CampaniaCSV/downloads?file='+nombreArchivo);
                })
                .fail(function () {
                    swal.fire('Fail','Error','error');
                })
                .always(function () {
            });
    
        }else{
            Swal.fire('Error','El campo Objetivos a Buscar debe poseer algun valor','warning');
            $('#sl_criterios').focus();
        }
        
    });
    
    /*
    |-------------------------------------------------------------------------------------------------------------------
    | Area de funciones para manejo de calendario Ing. Nicolaiev Brito
    | BOTON Y FUNCIONALIDAD API PARA DESCARGAR CSV DE CAMPANIAS GENERALES
    |-------------------------------------------------------------------------------------------------------------------
    */
    $('#btn_csv_descarga').click(function (){
        var nombreArchivo = $("#btn_csv_descarga").val();
        console.log( $('#btn_csv_descarga').attr('href'));
        console.log(nombreArchivo);
        $("#btn_csv_descarga").addClass('hide');
        $("#btn_csv_query").removeClass('hide');
    });

    /*
    |-------------------------------------------------------------------------------------------------------------------
    | Area de funciones para manejo de calendario Ing. Nicolaiev Brito
    | DESHABILITAR BOTON CUANDO LA CAMPANIAS SE ESTE CREANDO
    |-------------------------------------------------------------------------------------------------------------------
    */
    // $('#btn_nuevalogica').click(function (){
    //     $("#btn_csv_query").hide();
    //     $("#btn_csv_descarga").hide();
    // });
    
    
    
    //$("#sl_criterios").change(function() {
    /*$('body').on('change','#sl_criterios',function(event){
    
     event.preventDefault();
    
            Idlogica = $(this).attr('value');
            base_datos = $(this).attr('base_datos');
            tabla_primaria = $(this).attr('tabla_primaria');
            tabla = $(this).attr('tabla');
            campo = $(this).attr('campo');
            condicion = $(this).attr('condicion');
    /*
      var criterios  = $('#sl_criterios').val();
      let primera = criterios.filter(number => number === "Nombre_Cliente");
      let segunda = criterios.filter(number => number === "Apellido_Cliente");
      let tercera = criterios.filter(number => number === "Tlf_Cliente");
      let cuarta = criterios.filter(number => number === "Email_Cliente");
      let quinto = criterios.filter(number => number === "Monto_Deuda");
      let sexto = criterios.filter(number => number === "Monto_Extra");
      let septimo = criterios.filter(number => number === "Dias_Mora");
      let octavo = criterios.filter(number => number === "Fecha_Vencimiento");*/
    
      //console.log(Idlogica,base_datos,tabla_primaria,tabla,campo,condicion);    
    
    
    
    //});
    
    $('#frm_modalAdd').submit(function (event){
    
        event.preventDefault();
    
        $.ajax({
            url:$('#frm_modalAdd').attr('action'),
            type:$('#frm_modalAdd').attr('method'),
            data:$('#frm_modalAdd').serialize(),
            success:function(respuesta){
                //alert(respuesta);
                //$('#compose-modal').modal('hide');
                
                calendar.refetchEvents();
                swal("Registrado!", respuesta, "success");
    
                //mostrarDatos('');
                // parent.location.reload();
            }
        });
    });
    
    });
    
    
         
    
    $('#ModalAddPro #frm_modalAddPro').submit(function (event){
    
        event.preventDefault();
    //console.log($('#frm_modalAddPro').attr('action'));
        $.ajax({
            url:$('#frm_modalAddPro').attr('action'),
            type:$('#frm_modalAddPro').attr('method'),
            data:$('#frm_modalAddPro').serialize(),
            success:function(respuesta){
                //alert(respuesta);
    
                swal("Registrado!", respuesta, "info");
                mostrarProveedores();
                $('#ModalAddPro').modal('hide');
                //mostrarDatos('');
                // parent.location.reload();
            }
        });
    });
    
    
    $('#ModalAdd #frm_modalAdd #btn_viewlogic').click(function (event){
    
        event.preventDefault();
        let id_logica = $('#id_log').val();
        // $('#btn_csv_query').addClass('show');
        //console.log($('#id_log').val());
    
        $("#btn_csv_query").removeClass('hide');
        $("#btn_guardar_log").addClass('hide');
    
        $.ajax({
            url:base_url+"api/ApiCampanias/consultaLogicasbyId",
            type:"POST",
            data:{id_logica:id_logica},
            success:function(respuesta){
               var registros = eval(respuesta);
                
                
    
                for (var i = 0; i < registros.length; i++) {
                    
                $("#txt_type_submit").val("update");
                $("#id_logica").val(registros[i]['id_logica']);
                $("#nombre_logica").val(registros[i]['nombre_logica']);
                $("#id_proveedor").val(registros[i]['id_proveedor']);
                $("#type_logic").val(registros[i]['type_logic']);
                $("#query_contenido").val(registros[i]['query_contenido']);
                $("#mensaje").val(registros[i]['mensaje']);
                $("#estado").val(registros[i]['id_proveedor']);
                console.log(registros[i]);
                $('#ModalAddLog').modal('show');
                    
                    
                }
                //swal("Registrado!", respuesta, "info");
                //mostrarProveedores();
                //$('#ModalAddPro').modal('hide');
                //mostrarDatos('');
                // parent.location.reload();
            }
        });
    });
    
    $('#ModalAdd #frm_modalAdd #btn_test_template').click(function (event){
    
        event.preventDefault();
        
    
        let id_logica = $('#id_log').val();
        console.log($('#id_log').val());
        url = base_url+'supervisores/Supervisores/render_mail_template'+"/"+id_logica;
        window.open(url, '_blank');
    
    });
    
    $('#ModalAddLog #frm_modalAddLog').submit(function (event){
    
        event.preventDefault();
        let type_submit= $("#txt_type_submit").val();
    
    //console.log($('#frm_modalAddPro').attr('action'));
        $.ajax({
            url:$('#frm_modalAddLog').attr('action'),
            type:$('#frm_modalAddLog').attr('method'),
            data:$('#frm_modalAddLog').serialize(),
            success:function(respuesta){
                //alert(respuesta);
    
                swal("Registrado!", respuesta, "info");
                mostrarLogicas();
                $('#ModalAddlog').modal('hide');
                
    
                //mostrarDatos('');
                // parent.location.reload();
            }
        });
    });
    
    
    
    
    
    $('body').on('click','#ModalAddLog button[id="btn_test_query"]',function(event){
            event.preventDefault();
            let id_proveedor = $("#id_proveedor").val();
            let query_contenido = $("#query_contenido").val();
            let mensaje = $("#mensaje").val();
            let estado = $("#estado").val();
    
            $("#btn_guardar_log").removeClass('hide');
            
                 
    var parametros = {
    
                  
                  "id_proveedor" : id_proveedor,
                  "query_contenido" : query_contenido,
                  "mensaje" : mensaje,
                  "estado" : estado,
                  
                };
    
        $.ajax({
            url:base_url+"api/ApiCampanias/consultaTesting",
            type:'POST',
            data:parametros,
            success:function(respuesta){
              $("#view_test_query").removeClass("hide");
              $("#view_test_query").html("<pre>"+respuesta+"</pre>");
              //swal("Verifique!", "No posee permisos para entrar en este sistema o ejecutar esta accion contacte a dpto de sistemas", "info");
    
              
    
            }
        });
    });
    
    
    
    function mostrarProveedores(valor=null){
        $.ajax({
            url:base_url+"api/ApiCampanias/get_all_proveedores",
            type:'POST',
            data:{buscar:valor},
            success:function(respuesta){
                //alert(respuesta);
                var registros = eval(respuesta);
                
                html ="";
    
                for (var i = 0; i < registros.length; i++) {
                    
                    html +='<option value="'+registros[i]['id_proveedor']+'" selected="selected" >'+registros[i]['nombre_proveedor']+'</option>';
                    
                    
                }
                
                $('#id_pro').html(html);
                
            }
        });
    }
    
    function nuevaLogica(){
        $('#id_logica #nombre_logica #id_proveedor #type_logic #query_contenido #mensaje #estado').val('');
        $('#txt_type_submit').val('insert');
        $('#ModalAddLog').modal('show');
        $('#btn_csv_query').addClass('hide');
        $('#btn_guardar_log').addClass('hide');
    }
    
    function mostrarLogicas(valor=null){
        $.ajax({
            url:base_url+"api/ApiCampanias/get_all_logicas",
            type:'POST',
            data:{buscar:valor},
            success:function(respuesta){
                //alert(respuesta);
                var registros = eval(respuesta);
                
                html ="";
    
                for (var i = 0; i < registros.length; i++) {
                    
                    html +='<option value="'+registros[i]['id_logica']+'" selected="selected" >'+registros[i]['nombre_logica']+'</option>';
                    
                    
                }
                
                $('#id_log').html(html);
                
            }
        });
    }
    
    $('body').on('click','#btn_test_mensaje',function(event){
    event.preventDefault();
    let constante_url= $("#constante_url").val();
    var logica = $("#id_log").val();
    
      $.ajax({
          type:'POST',
          data:{query:logica,test:1},
          url: constante_url,
          beforesend:function(){
            request.setRequestHeader("Access-Control-Allow-Origin", '*');
          },
          success:function(res)
          {
     
              $("#view_test_camp").html("<pre>"+res+"</pre>");       
               
                 
              
          }
        });
    
    });
    
    function validaciones(button){
        console.log(button);
    
        var sl_central  = $('#sl_central').val();
        var sl_campania  = $('#sl_campania').val();
        var criterios  = $('#sl_criterios').val();
        var sl_antiguedad  = $('#sl_antiguedad').val();
        var sl_logica  = $('#sl_logica').val();
        var currency_rangeA  = $('#currency_rangeA').val();
        var currency_rangeB  = $('#currency_rangeB').val();
        var date_rangeA  = $('#date_rangeA').val();
        var date_rangeB  = $('#date_rangeB').val();
        var dias_atrasoA  = $('#dias_atrasoA').val();
        var dias_atrasoB  = $('#dias_atrasoB').val();
        var sl_limite  = $('#sl_limite').val();
     
        let primera = criterios.filter(number => number === "conAcuerdo");
        let segunda = criterios.filter(number => number === "sinAcuerdo");
        //console.log(primera + segunda);
    
        if (primera!="" && segunda!="") {
            //console.log("tengo "+primera+" y "+segunda)
            swal("Atención","Los criterios sin acuerdo y con acuerdo no se pueden usar como criterios razonables para la busqueda elija solo uno de ellos","error");
            return false;
    
        }else if (sl_central==0 && sl_campania==0 && sl_antiguedad==0 && sl_logica==0) {
            
            swal("Atención","Los criterios de busqueda requieren los campos de: central,campañia,criterios,antiguedad, logica y almenos un rango uno de ellos","error");
            return false;
    
        }else{
    
            if (button=="btn_search") {
                buscarCampania();
            }else if(button=="btn_comunicacion"){
                migrarCampania();
            }else if(button=="btn_csv"){
                generaCsv();
            }else if(button=="btn_plantilla"){
                generaTabModal();
            }else if(button=="btn_clear_campania"){
                clearcampania();
            }else{
                return false;
            }
        }
    }
    
    /*
    |-------------------------------------------------------------------------------------------------------------------
    | Area de funciones para manejo de calendario Ing. Nicolaiev Brito
    | FUNCIONALIDAD PARA CONDICION DE QUERY
    |-------------------------------------------------------------------------------------------------------------------
    */
    $("#currency_rangeA, #currency_rangeB, #date_rangeA, #date_rangeB, #dias_atrasoA, #dias_atrasoB, #sl_limit, #sl_estado_mora, #sl_fuente, #sl_groupBy").change(function(event) {
        debugger;
        event.preventDefault();
        valor = $(this).val();
        var padre = $(this).prop("name");
        var query_contenido = $("#query_contenido").val();
        //console.log(padre);
    
        if (($("#sl_logica").val()=="BETWEEN") &&(padre=="date_rangeA")) {
            query_contenido += " '" + $("#date_rangeA").val() + "'";
            $("#query_contenido").val(query_contenido);
    
        }else if (padre=="date_rangeB") { 
            query_contenido += " AND '" + valor + "'";
            $("#query_contenido").val(query_contenido);
        
        }else if (padre=="dia_atrasosB") { 
            query_contenido += " AND " + valor;
            $("#query_contenido").val(query_contenido);
    
        }else if (padre=="sl_limit") {  
            query_contenido += " LIMIT " + valor;
            $("#query_contenido").val(query_contenido);
    
        }else if ((padre=="dias_atrasoA") && ($("#sl_logica").val()=="IN")){  
            query_contenido += valor +" ) " ;
            $("#query_contenido").val(query_contenido);
    
        }else{
            query_contenido += " " + valor;
            $("#query_contenido").val(query_contenido);
        }
    });
    
    
    /*
    |-------------------------------------------------------------------------------------------------------------------
    | Area de funciones para manejo de calendario Ing. Nicolaiev Brito
    | FUNCIONALIDAD INPUT RANGO DE MORA
    |-------------------------------------------------------------------------------------------------------------------
    */
    $("#sl_logica").change(function() {
        // debugger;
        var query_contenido = $("#query_contenido").val();
        var antiguedad = $("#sl_antiguedad").val();
        var logica = $("#sl_logica").val();
    
        if (antiguedad==="credito_detalle.fecha_vencimiento"){
    
            if (logica===">"){
                //console.log($(antiguedad+" "+logica);
                $("#date_rangeA").removeClass('hide');
                $("#currency_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');
                query_contenido += " " + logica;
                $("#query_contenido").val(query_contenido);
            }else if(logica==="="){
                $("#date_rangeA").removeClass('hide');
                $("#currency_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');
                query_contenido += " " + logica;
                $("#query_contenido").val(query_contenido);
            }else if(logica==="<"){
                $("#date_rangeA").removeClass('hide');
                $("#currency_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');
                query_contenido += " " + logica;
                $("#query_contenido").val(query_contenido);
            }else if(logica==="!="){
                $("#date_rangeA").removeClass('hide');
                $("#currency_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');
                query_contenido += " " + logica;
                $("#query_contenido").val(query_contenido);
            }else if(logica==="BETWEEN"){
                $("#date_rangeA, #date_rangeB").removeClass('hide');
                $("#currency_rangeA, #currency_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');
                query_contenido += " " + logica;
                $("#query_contenido").val(query_contenido);
            }else if(logica==="IN"){
                $("#date_rangeA").removeClass('hide');
                $("#currency_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');
                query_contenido += " IN ( ";
                $("#query_contenido").val(query_contenido);
            }
    
        }else if (antiguedad==="credito_detalle.dias_atraso"){
    
            if (logica===">"){
                $("#dias_atrasoA").removeClass('hide');
                $("#date_rangeA, #currency_rangeB, #date_rangeB, #currency_rangeA, #dias_atrasoB").addClass('hide');
                query_contenido += " " + logica;
                $("#query_contenido").val(query_contenido);
            }else if(logica==="="){
                $("#dias_atrasoA").removeClass('hide');
                $("#date_rangeA, #currency_rangeB, #date_rangeB, #currency_rangeA, #dias_atrasoB").addClass('hide');
                query_contenido += " " + logica;
                $("#query_contenido").val(query_contenido);
            }else if(logica==="<"){
                $("#dias_atrasoA").removeClass('hide');
                $("#date_rangeA, #currency_rangeB, #date_rangeB, #currency_rangeA, #dias_atrasoB").addClass('hide');
                query_contenido += " " + logica;
                $("#query_contenido").val(query_contenido);
            }else if(logica==="!="){
                $("#dias_atrasoA").removeClass('hide');
                $("#date_rangeA, #currency_rangeB, #date_rangeB, #currency_rangeA, #dias_atrasoB").addClass('hide');
                query_contenido += " " + logica;
                $("#query_contenido").val(query_contenido);
            }else if(logica==="BETWEEN"){
                $("#dias_atrasoA, #dias_atrasoB").removeClass('hide');
                $("#date_rangeA, #date_rangeB, #currency_rangeB, #currency_rangeA").addClass('hide');
                query_contenido += " " + logica;
                $("#query_contenido").val(query_contenido);
            }else if(logica=="IN"){
                $("#dias_atrasoA").removeClass('hide');
                $("#date_rangeA, #currency_rangeB, #date_rangeB, #currency_rangeA, #dias_atrasoB").addClass('hide');
                query_contenido += " IN ( ";
                $("#query_contenido").val(query_contenido); 
            }
        }
    });
    
    /*
    |-------------------------------------------------------------------------------------------------------------------
    | Area de funciones para manejo de calendario Ing. Nicolaiev Brito
    | BOTON PARA LIMPIAR QUERY
    |-------------------------------------------------------------------------------------------------------------------
    */
    $('body').on('click','button[id="btn_clean_query"]',function(){
        $("#query_contenido").text("");
        $("#query_contenido").val("");
        $("#mensaje").text("");
        $("#mensaje").val("");
        $('#dias_atrasoA').hide();
    });
    
    
    /*
    |-------------------------------------------------------------------------------------------------------------------
    | Area de funciones para manejo de calendario Ing. Nicolaiev Brito
    | FUNCIONALIDAD INPUT ANTIGUEDAD
    |-------------------------------------------------------------------------------------------------------------------
    */
    $("#sl_antiguedad").change(function() {
        // debugger;
        var query_contenido = $("#query_contenido").val();
        var antiguedad = $("#sl_antiguedad").val();
        var logica = $("#sl_logica").val();
    
        if (antiguedad=="credito_detalle.fecha_vencimiento"){
            console.log('credito_detalle.fecha_vencimiento');
    
            if (query_contenido.indexOf("INNER JOIN credito_detalle ON maestro.credito_detalle.id_credito = creditos.id") > -1) {
                query_contenido += " WHERE " + antiguedad;
                $("#query_contenido").val(query_contenido);
            }else{
                query_contenido += " INNER JOIN maestro.credito_detalle ON maestro.credito_detalle.id_credito = creditos.id WHERE " + antiguedad;
                $("#query_contenido").val(query_contenido);
            }
    
            if (logica==">" || logica=="=" || logica=="<" || logica=="!=" || logica=="IN"){
                $("#date_rangeA").removeClass('hide');
                $("#currency_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');
            }else if(logica=="BETWEEN"){
                $("#date_rangeA, #date_rangeB").removeClass('hide');
                $("#currency_rangeA, #currency_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');
            }
    
        }else if (antiguedad=="credito_detalle.dias_atraso"){
            console.log('credito_detalle.dias_atraso');
    
            if (query_contenido.indexOf("INNER JOIN credito_detalle ON maestro.credito_detalle.id_credito = creditos.id") > -1) {
                query_contenido += " WHERE " + antiguedad;
                $("#query_contenido").val(query_contenido);
            }else{
                query_contenido += " INNER JOIN maestro.credito_detalle ON maestro.credito_detalle.id_credito = creditos.id WHERE " + antiguedad;
                $("#query_contenido").val(query_contenido);
            }
            
            if (logica==">" || logica=="=" || logica=="<" || logica=="!=" || logica=="IN"){
                $("#dias_atrasoA").removeClass('hide');
                $("#date_rangeA, #currency_rangeB, #date_rangeB, #currency_rangeA, #dias_atrasoB").addClass('hide');
            }else if(logica=="BETWEEN"){
                $("#dias_atrasoA, #dias_atrasoB").removeClass('hide');
                $("#date_rangeA, #date_rangeB, #currency_rangeB, #currency_rangeA").addClass('hide');
            }
    
        }
    });
    
    //$('.entero').inputmask('9999',{placeholder:' '});
    $('.moneda').autoNumeric('init', {aSep: '.', aDec: ',', aSign: ''});
     $('#sl_criterios').select2({
        placeholder: '.: Selecciona los criterios :.',
        multiple : true
    });
    
    function ValidarNumeros(event){
        const reg = new RegExp(/^\d+$/, 'g');
        //const cadena = reg.test(event.key);
        if (!reg.test(event.key))
        {
            event.preventDefault();
        }
    }
    
    
    
    
    
