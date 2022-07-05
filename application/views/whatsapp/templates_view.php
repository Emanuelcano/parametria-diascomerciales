<script src="<?php echo base_url('assets/templates/templates.js');?>"></script>

<style>
    .collapse_editor{
        display: none;
    }
    .modal-height-fix-scroll{
        height: 800px;
        width: 100%;
        overflow-y: auto;
    }
</style>

<hr>
<!-- menu de listados -->
<div id="menu_container" style="display: block; background: #FFFFFF;">
<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="template_type" name="template_type" value="">
    <div class="col-md-12" align="center" id="" style="display: block; height: 100%;margin-top: 1%">
            <div class="col-lg-12" id="" style="display: block">

                <div class="box-body pull-left">
                    <a class="btn btn-app" onclick="templateList('WAPP');">
                        <i class="fa fa-whatsapp"></i> WhatsApp
                    </a>
                </div>

                <div class="box-body pull-left">
                    <a class="btn btn-app" onclick="templateList('sms');">
                        <i class="fa fa-send"></i> SMS
                    </a>
                </div>

                <div class="box-body pull-left">
                    <a class="btn btn-app" onclick="templateList('ivr');">
                        <i class="fa fa-phone" aria-hidden="true"></i> IVR
                    </a>
                </div>

                <div class="box-body pull-left">
                    <a class="btn btn-app" onclick="templateList('email');">
                        <i class="fa fa-envelope-open" aria-hidden="true"></i> Email
                    </a>
                </div>
            </div>
    </div>
</div>
<!-- menu de listados -->
</br>

<div class="row" style="padding-left: 50px;padding-right:50px;">
    <!-- nuevo template button -->
    <div class="col-md-5" id="button_create_template_container" style="display:none;">
        <h4 id="title_list"  style="color:grey; font-weight:bold;"></h4>
        <a href="#" class="btn btn-primary" id="modal_create_template"><i class="fa fa-plus" aria-hidden="true" ></i> Nuevo template </a>
    </div>
    <!-- nuevo template button -->

    <!-- inputs testing templates -->
    <div class="col-md-5 pull-right" id="test_inputs_container" style="display:none;">
        <table class="table">
            <thead>
                <tr>
                    <th class="col-md-5" style="color:grey;" >Prueba template</th>
                    <th class="col-md-5"></th>
                    <th class="col-md-2"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th class="col-ms-5">
                        <input type="text" class="form-control pull-right" placeholder="Numero de documento" name="test_documento" id="test_documento" value="">
                    </th>
                    <th class="col-md-5">
                        <input type="hidden" class="form-control pull-right" placeholder="Numero de teléfono" name="test_number"   style="margin-left:20px;" id="test_number" value="">
                        <input type="hidden" class="form-control pull-right" placeholder="Email" name="test_email" id="test_email" value="">
                    </th>
                    <th class="col-md-2"> 
                        <a href="#" class="btn btn-primary pull-right" id="test_template_with_number"><i class="fa fa-send" aria-hidden="true" ></i> Probar templates </a>
                    </th>
                </tr>
                <tr>
                    <th class="col-ms-5"></th>
                    <th class="col-md-5"><span id="ok_number" style="font-size: 8px; color:grey;"></span></th>
                    <th class="col-md-2"></th>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- inputs testing templates -->
</div>


<!-- Modal templates WAPP, SMS, IVR -->
<div class="modal fade" id="modalTemplate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" style="width: 65%;" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="myModalLabel">Template</h3>
            </div>
            
            <div class="modal-body">
            	<div class="container-fluid">
                    <input type="hidden" value="" name="template_id">

            		<div class="row">
                        <div class="form-group col-md-6">
                            <label for="canal">Canal: </label>
                            <select class="js-example-basic-multiple" name="canal[]" id="canal"
								multiple="multiple" style="width: 100%" placeholder="Canal">
                                <option value="1">Ventas</option>
                                <option value="2">Cobranzas</option>
						    </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tipo_template">Tipo: </label>
                            <select class="form-control" name="tipo_template" id="tipo_template" style="width: 100%" placeholder="Canal">
                                <option value="" disabled selected>Seleccione tipo</option>
                                <option value="WAPP">WAPP</option>
                                <option value="SMS">SMS</option>
                                <option value="IVR">IVR</option>
                                <option value="EMAIL">EMAIL</option>
						    </select>
                        </div>
                    </div>

                    <div class="row">
                        <div id="grupo_container" class="col-md-6"></div>
                        <div class="form-group  col-md-6" id="new_group_container" style="display: none;">
                        <label for="new_grupo">Nuevo grupo: </label>   
							<div class="col-md-12">
                                <input class="form-control col-md-9" style="margin-left: -10px;" name="new_grupo" id="new_grupo">
                                <a class="col-md-2 btn btn-info pull-right" href="#" id="return_select_group"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                           </div>
                        </div>
                        
                        <div id="proveedor_container" class="col-md-6"></div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="msg_string">Mensaje: </label>
                            <textarea class="form-control" name="msg_string" id="msg_string" cols="100" rows="3"></textarea>
                        </div>
                    </div>

                    <br>
                    <div id="test_by_documento" style="display:none;">
                        <div class="modal-header">
                            <h3 class="modal-title" id="myModalLabel">Probar Template</h3>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="col-md-12">
                                    <label for="campo">Ingrese documento: </label>
                                    <input type="text" id="documento" name="documento" class="form-control" value="">
                                </div>
                            </div>
                            <div class="col-md-6">
                                    <a href="#" style="margin-top:22px;" class="btn btn-primary" id="test_template_by_documento"><i class="fa fa-plus" aria-hidden="true" ></i> Test </a>
                            </div>
                            
                            <div class="col-md-12" style="display:none;">
                                <div class="card" style=" border:solid 1px #CCC; margin-top:10px;">
                                    <div class="card-body " id="test_render_template" style="padding:7px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-header">
                        <h3 class="modal-title" id="myModalLabel">Variables</h3>
                    </div>
                    <br>

                    <div class="row">
                        <div id="variables_select_container" class="col-md-6">
                            
                        </div>
                        <div class="form-group col-md-6" style="display:none;">
                            <label for="tipo_variable">Tipo: </label>
                            <select class="form-control" name="tipo_variable" id="tipo_variable" style="width: 100%" placeholder="tipo_variable">
                                <option value="" disabled selected>Seleccione tipo:</option>
                                <option value="1">Dinamica</option>
                                <option value="2">Estatica</option>
						    </select>
                        </div>
            		</div>

                    <div class="row">
                        <div class="col-md-4" style="display:none;">
                            <div class="col-md-12">
                                <label for="campo">Campo: </label>
                                <input type="text" id="campo" name="campo" class="form-control" value="">
                            </div>
                        </div>
                        <div class="col-md-4" style="display:none;">
                            <div class="col-md-12">
                                <label for="condicion">Condición: </label>
                                <input type="text" id="condicion" name="condicion" class="form-control" value="">
                            </div>
                        </div>
                        <div class="col-md-4" style="display:none;">
                            <div class="col-md-12">
                                <label for="formato">Formato: </label>
                                <input type="text" id="formato" name="formato" class="form-control" value="">
                            </div>
                        </div>
                    </div>

                    <br>
                    <div class="row">
                        <div id="container_new_variables"></div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6"> 
                            <a href="#" data-new_variable_lenght="0" data-variable_exists="0" class="btn btn-small btn-primary" id="create_variable"><i class="fa fa-plus" aria-hidden="true" ></i> Agregar Variable </a>
                        </div>
                    </div>            
            	</div>
            </div>

            <div class="modal-footer" style=" padding-bottom: 20px;">
                <div class="col-md-6 ">
                    <a class="btn btn-info" data-id="" id="guardar_template"><i class="fa fa-send"></i> guardar</a> &nbsp;
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal -->

<!-- Modal Template Email -->
<div class="modal fade" id="modalEmailTemplate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" style="width: 65%;" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="myModalLabel">Template</h3>
            </div>
            
            <div class="modal-body">
                <input type="hidden" value="" name="template_id_email">
                <div class="row">
                        <div class="form-group col-md-12">
                            <label for="canal">Canal: </label>
                            <select class="js-example-basic-multiple" name="canal_email[]" id="canal_email"
								multiple="multiple" style="width: 100%" placeholder="Canal">
                                <option value="1">Ventas</option>
                                <option value="2">Cobranzas</option>
						    </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="campo">Asunto: </label>
                            <input type="text" id="nombre_logica" name="nombre_logica" class="form-control" value="">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="campo">Nombre: </label>
                            <input type="text" id="nombre_template" name="nombre_template" class="form-control" value="">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="campo">Arreglo de variables: </label>
                            <textarea class="form-control" name="arreglo_variables_rplc" id="arreglo_variables_rplc" cols="120" rows="2"></textarea>
                        </div>
                </div>

                <div class="modal-header">
                         <a class="btn btn-primary pull-right" data-toggle="collapse" href="#collapseQuery" role="button" aria-expanded="false" aria-controls="collapseExample">
                            <i class="fa fa-arrow-down" aria-hidden="true"></i>
                        </a>
                        <h3 class="modal-title" id="myModalAreaQueryLabel">Query Content:</h3>
                </div>  

                <div class="collapse" id="collapseQuery">
                    <div class="row">
            			<div class="col-md-12">
                            <div class="col-md-12">
                                <textarea class="form-control" name="query_contenido" id="query_contenido" cols="120" rows="8"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-header">
                    <a class="btn btn-primary pull-right" id="collapseEditor">
                        <i class="fa fa-arrow-down" aria-hidden="true"></i>
                    </a>
                    <h3 class="modal-title" id="myModalAreaHtmlLabel">HTML Content:</h3>
                </div>  

                <div id="editor_container" class="collapse_editor">
                    <!--editor html--> 
                    <textarea cols="80" id="editor_html" name="editor_html" rows="10" data-sample-short>Escriba HTML</textarea>
                    <!--editor html-->
                </div> 
            </div>

            <div class="modal-footer" style=" padding-bottom: 20px;">
                <div class="col-md-6 ">
                    <a class="btn btn-info" data-id="" id="guardar_email_template"><i class="fa fa-send"></i> guardar</a> &nbsp;
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal -->

<!-- Modal Template Email -->
<div class="modal fade" id="modalRenderEmailTemplate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" style="width: 65%;" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="myModalLabel">Template</h3>
            </div>
            
            <div class="modal-body  modal-height-fix-scroll" id="render_template_container">

            </div>

            <div class="modal-footer" >
                <div class="col-md-6 ">
                    <a class="btn btn-info" data-id="" id="guardar_email_template"><i class="fa fa-send"></i> guardar</a> &nbsp;
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal -->

<div id="template_list_container" style="padding-left: 50px; padding-right: 50px;"></div>
<script src="https://cdn.ckeditor.com/4.17.1/standard-all/ckeditor.js"></script>
<script>
    // We need to turn off the automatic editor creation first.
    CKEDITOR.disableAutoInline = true;

    CKEDITOR.replace('editor_html', {
                        extraPlugins: 'sourcedialog',
                        removePlugins: 'sourcearea',
                        removeButtons: 'PasteFromWord'
                    });

</script>


