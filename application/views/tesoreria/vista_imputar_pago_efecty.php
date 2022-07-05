<style>
.loader {
    border: 16px solid #f3f3f3; /* Light grey */
    border-top: 16px solid #3498db; /* Blue */
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
    display: none;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
    table input{
        border-top: 0px !important;
        border-left: 0px !important;
        border-right: 0px !important;
        background: transparent !important;
    }

#imputacionesRow{
    padding-left:25px;
}

#adjuntarImputacionEfecty{
    padding-left:40px;
}
</style>
<div class="box box-info" id="box-imputacion-pago">
    <div class="row" id="imputacionesRow">
        <div class="col-lg-3">
        <div class="box-header with-border">
                <h6 class="box-title"><small><strong>Imputación de archivo</strong></small>&nbsp;</h6>
                
        </div>

            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-file-excel-o"></i></span>
                    <select class="form-control" name="sl_tipo_imputacion" id="sl_tipo_imputacion">
                        <option value="0" selected="selected"> ---SELECCIONAR TIPO IMPUTACION--- </option>
                        <option value="1"> EFECTY </option>
                        <option value="2"> EPAYCO </option>
                    </select>
            </div>
            </div>
            <div class="imputaciones box-body col-lg-3" hidden id="adjuntarImputacionEfecty">
                <div id ="respuestaBanco" >
                    <div class="contenido form-group">
                        <input type="file" id="fileImputacionEfecty" required="true" name="fileImputacionEfecty">
                        <p class="help-block">Formatos permitidos: .xlsx</p>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-success btn-xs" id="procesar-respuesta">Cargar Archivo</button>
                    </div>
                </div>
            </div>
    </div>

    <div class="modal fade" id="compose-modal-wait" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-time"></i> POR FAVOR ESPERE MIENTRAS SE REALIZA LA IMPUTACION </h4>
                <div class="col-md-12 hide" id="succes">
                    <!-- Primary box -->
                    <div class="box box-solid box-primary">
                        <div class="box-header">
                            <h3 class="box-title">IMPUTACION DE ARCHIVO</h3>
                            <div class="box-tools pull-right">
                                <button class="btn btn-primary btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                <button class="btn btn-primary btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <span id="respuesta"></span>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div><!-- /.col -->
            </div>
        <div class="modal-body">
            <div class="data"></div>
            <div class="loader"></div> 
        </div>
            <div class="modal-footer clearfix">
            </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

        <!-- <div class="col-sm-6">
            <h3 class="box-title"><small><strong>Solicitudes de Devolución</strong></small>&nbsp;</h3>
        </div>
        <div class="col-sm-6 text-right">
            <br>
            <a class="btn btn-sm btn-success" id="generar-archivo">Generar archivo</a>
            <a class="btn btn-sm btn-warning" data-estado = "0" id="estado-consulta">Procesando</a>
        </div>
        <table id="tbl_solicitud_devolucion_all" class="table table-bordered table-hover dataTable" >
            <thead>
                <tr class="" style="background-color: #D8D5F9;">
                    <th style="">Fecha</th>
                    <th style="">Hora</th>
                    <th style="">Solicitado Por</th>
                    <th style="">Documento</th>
                    <th style="">Nombre y Apellido</th>
                    <th style="">Banco</th>
                    <th style="">Cuenta</th>
                    <th style="">Monto Devolver</th>
                    <th style="">Fecha Proceso</th>
                    <th style="">Resultado</th>
                    <th style="">Monto Devuelto</th>
                    <th style="">Estado Solicitud</th>
                    <th style="">Acciones</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table> -->
    </div>
</div>



<script>
    $(document).ready(function() {
       // initTableSolicitudDevolucion();

        $('#sl_tipo_imputacion').change(function (event) { 
            let imputacion = $('#sl_tipo_imputacion').val()
            switch (imputacion) {            
                case '1':
                    $('#adjuntarImputacionEfecty').show()

                    $('#titulo').remove()
                    $('.contenido').prepend('<label id="titulo" for="fileImputacionEfecty">Imputacion EFECTY</label>')
                    $('#fileImputacionEfecty').val('')
                    break;
                case '2':                
                    $('#adjuntarImputacionEfecty').show()
                    $('#titulo').remove()
                    $('.contenido').prepend('<label id="titulo" for="fileImputacionEfecty">Imputacion EPAYCO</label>')
                    $('#fileImputacionEfecty').val('')
                    break;
                default:
                    $('.imputaciones').hide()
                    break;
            }
        })

        $("#procesar-respuesta").click('on', function (){ 
            procesarImputacionEfecty();
        }); 
    });    

        
    
    function procesarImputacionEfecty(){
        if($("#fileImputacionEfecty").val() !== ""){
            let selectImput = $('#sl_tipo_imputacion').val()
            
            let rutaImputacion = ''
            if (selectImput == '1') {
                rutaImputacion = 'tesoreria/tesoreria/procesarImputacionEfecty'
            }else{
                rutaImputacion = 'tesoreria/tesoreria/procesarImputacionPSE'
            }
            Swal.fire({
                    title: 'IMPUTACION EFECTY',
                    text: "Seguro que desea efectuar la imputacion del archivo : "+ $("#fileImputacionEfecty").val().replace(/C:\\fakepath\\/i, ''),
                    icon: 'warning',
                    allowOutsideClick: false,
                    showCancelButton: true,
                    confirmButtonColor: '#00a65a',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: 'Confirmar'
                }).then((result) => {
                    // console.log(result);
                    if (result.value) {
                        let $loader = $('.loader');
                        let file = document.getElementById('fileImputacionEfecty');
                        let form = new FormData();
                        
                        form.append("file", file.files[0], file.value);
                        form.append("fileName", $("#fileImputacionEfecty").val().replace(/C:\\fakepath\\/i, ''));
                        data = form;
                        
                        let base_url = $("#base_url").val() + rutaImputacion;
                        let settings = {
                            "url": base_url,
                            "method": "POST",
                            "timeout": 0,
                            "processData": false,
                            "mimeType": "multipart/form-data",
                            "contentType": false,
                            "data": data,
                            beforeSend: function() {
                            // aquí puedes poner el código paraque te muestre el gif
                                $("#compose-modal-wait").modal({
                                    show: true,
                                    backdrop: 'static',
                                    keyboard: false
                                });
                                $loader.show();
                            },
                        };
                
                        $.ajax(settings).done(function (response) {
                            setTimeout(function(){
                                $loader.hide();
                                $("#compose-modal-wait").modal('hide');
                            }, 1000)
                            
                            response = JSON.parse(response);
                            if(response.status.ok){
                                Swal.fire('',response.message,'success');   
                            } else{
                                Swal.fire('',response.message,'error');   
                            }
                        
                        }).fail(function(xhr) {
                            setTimeout(function(){
                                $loader.hide();
                                $("#compose-modal-wait").modal('hide');
                            }, 1000)
                          Swal.fire('','Disculpe, estamos presentando problemas con el conexion','error');   

                        });
                    
                    }
                });
        }

    }
</script>
