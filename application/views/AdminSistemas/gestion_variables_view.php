<script type="text/javascript" src="<?php echo base_url('assets/function.js');?>"></script>
<style>
    #columnas{
        background-color: #9BC0EC;
    }

    #columnas th{
        text-align:center;
    }

    hr{
        height:1px;
        border-top: 3px solid #e1dcdc;
        display: block;
        clear: both;
    }
</style>
<div>
    <div>
        <label>Gestion de Variables</label>
        <div style="padding-bottom:1%;">
            <input type="button" class="btn btn-primary" id="btn_new_variable" value="Nueva Variable">
        </div>
        <div id="formulario_variable" style="display:none; padding-bottom:2%;"> 
            <div class="col-lg-12" style="border: 1px solid #e1dcdc;">
                <div class="col-lg-12" style="padding-top:1%;">           
                    <div class="col-lg-3">
                        <label class="form-label">Denominación</label>
                        <input type="text" class="form-control" id="inp_denominacion" autocomplete="off" placeholder=".::Denominación de variable::.">
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">Estado</label>
                        <select class="form-control" name="estados" id="slc_estados">
                            <option value="activo" selected>Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">Base de Datos</label>
                        <select class="form-control" name="base_datos" id="slc_baseDatos">
                            <option value="" selected>Selecione</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">Tabla</label>
                        <select class="form-control" name="tablas" id="slc_tabla">
                            <option value="" selected>Selecione</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-12" style="padding-top:1%;">
                    <div class="col-lg-3">
                        <label class="form-label">Campo</label>
                        <select class="form-control" name="Campo" id="slc_campo">
                            <option value="" selected>Selecione</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">Filtro</label>
                        <select class="form-control" name="Filtro" id="slc_filtro">
                            <option value="0">Estatico</option>
                            <option value="1" selected>Dinamico</option>
                        </select>
                    </div>

                    <div style="display: none;" id="content_filtros_estatico">
                        <div class="col-lg-12">
                            <div class="row col-lg-12">
                                <div class="col-lg-12 content_filtro0">
                                    <div class="col-lg-4">
                                        <label class="form-label">Campo</label>
                                        <select class="form-control condiciones_busqueda" name="Campo" id="slc_campo_estatico0">
                                            <option value="" selected>Selecione</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2" style="padding-top:3px;">
                                        <label class="form-label"></label>
                                        <select class="form-control operadores_comp" name="comparacion" id="op_comparacion0">
                                            <option value="" selected>Selecione</option>
                                            <option value='1'>IGUAL</option>
                                            <option value='2'>MAYOR A</option>
                                            <option value='3'>MAYOR IGUAL</option>
                                            <option value='4'>MENOR A</option>
                                            <option value='5'>MENOR IGUAL</option>
                                            <option value='6'>DISTINTO</option>
                                            <option value='7'>ENTRE</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-3" style="padding-top:3px;">
                                        <label class="form-label"></label>
                                        <input type="text" class="form-control comp_condicion" n_comd="0" placeholder=".::Ingresar valor::." autocomplete="off" id="inp_estatico0">
                                    </div>
                                </div>
                            </div>
                            <div class="row col-lg-12">
                                <div class="col-lg-12 content_filtro1">
                                    <div class="col-lg-4">
                                        <label class="form-label">Campo</label>
                                        <select class="form-control condiciones_busqueda" name="Campo" id="slc_campo_estatico1">
                                            <option value="" selected>Selecione</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2" style="padding-top:3px;">
                                        <label class="form-label"></label>
                                        <select class="form-control operadores_comp" name="Campo" id="op_comparacion1">
                                            <option value="" selected>Selecione</option>
                                            <option value='1'>IGUAL</option>
                                            <option value='2'>MAYOR A</option>
                                            <option value='3'>MAYOR IGUAL</option>
                                            <option value='4'>MENOR A</option>
                                            <option value='5'>MENOR IGUAL</option>
                                            <option value='6'>DISTINTO</option>
                                            <option value='7'>ENTRE</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-3" style="padding-top:3px;">
                                        <label class="form-label"></label>
                                        <input type="text" class="form-control comp_condicion" n_comd="1" placeholder=".::Ingresar valor::." autocomplete="off" id="inp_estatico1">
                                    </div>
                                </div>
                            </div>
                            <div class="row col-lg-12">
                                <div class="col-lg-12 content_filtro2">
                                    <div class="col-lg-4">
                                        <label class="form-label">Campo</label>
                                        <select class="form-control condiciones_busqueda" name="Campo" id="slc_campo_estatico2">
                                            <option value="" selected>Selecione</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2" style="padding-top:3px;">
                                        <label class="form-label"></label>
                                        <select class="form-control operadores_comp" name="Campo" id="op_comparacion2">
                                            <option value="" selected>Selecione</option>
                                            <option value='1'>IGUAL</option>
                                            <option value='2'>MAYOR A</option>
                                            <option value='3'>MAYOR IGUAL</option>
                                            <option value='4'>MENOR A</option>
                                            <option value='5'>MENOR IGUAL</option>
                                            <option value='6'>DISTINTO</option>
                                            <option value='7'>ENTRE</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-3" style="padding-top:3px;">
                                        <label class="form-label"></label>
                                        <input type="text" class="form-control comp_condicion" n_comd="2" placeholder=".::Ingresar valor::." autocomplete="off" id="inp_estatico2">
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                <!-- TIPO -->
                
            </div>
            <div class="col-lg-12 slc_tipo" style="padding-top:1%; padding-bottom:1%;">
                    <div class="col-lg-3">
                        <label class="form-label">Tipo</label>
                            <select class="form-control" name="Campo" id="slc_tipo">
                                <option value="" selected>Selecione</option>
                                <option value="caracter">CARACTER</option>
                                <option value="num">NUMERICO</option>
                                <option value="fecha">FECHA</option>
                            </select>
                    </div>
                    <div class="col-lg-2" style="margin-top:2%;">
                        <label class="form-label">FORMATO</label>
                        <div id="chk_formato" class="form-check form-switch">

                        </div>
                    </div>
                    <div class="col-lg-2" style="margin-top:2%;">
                        <label class="form-label">VALOR</label>
                        <div id="chk_valor" class="form-check form-switch">

                        </div>
                    </div>

                    <div class="col-lg-4" style="margin-top:1%; margin-left:-1%; padding-bottom:1%; border: 1px solid #e1dcdc;">
                        <label class="form-label" style="padding-top:1%;">Probar Variables</label>
                        <hr style="margin:auto;">
                        <div class="col-lg-12" style="padding-top:1%;">
                            <div class="col-lg-7">
                                <p>Ingrese documento:</p>
                                <input type="text" class="form-control" autocomplete="off" placeholder=".::Documento::." id="inp_documento">
                            </div>
                            <div class="col-lg-4" style="padding-top:30px;">
                                <input type="button" class="btn btn-primary btn_accion" boton="obtener" id="btn_obternerVal" value="Obtener Valor">
                            </div>
                        </div>
                        <div class="col-lg-12" style="padding-top:1%;">
                            <div class="col-lg-11">
                                <input type="text" class="form-control" disabled id="inp_valor_variable">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                
            <div class="col-lg-12" style="padding-bottom:1%; padding-top:1%;">
                <input type="button" class="btn btn-success btn_accion" id="btn_guardar"  boton="guardar" value="Guardar">
                <input type="button" class="btn btn-info" id="btn_cerrar" value="Cerrar">
            </div>
        </div>
    </div>

    <div id="tabla">
        <table id="table_variables" class="table table-striped table-bordered" style="width:100%">
            <thead id="columnas">
                <tr>
                    <th>Id</th>
                    <th>Nombre Variable</th>
                    <th>Estado</th>
                    <th>Operador</th>
                    <th>Fecha de Creacion</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="data">

            </tbody>
        </table>
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="modal_update_variables" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="width:155%; right:30%;">
            <div class="modal-header">
                <button class="close btn_cerrar" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Actualizacion de Variables</h4>
            </div>
            <div class="modal-body" id="data_modal">
                <div class="container">
                    <div class="col-lg-12" style="display:flex;">
                        <div class="col-lg-3" id="div_denominacion">
                            <label> Denominacion de Variable:</label>
                            <input type="text" class="form-control" name="denominacion" placeholder=".::Denominación de variable::." id="denominacion_update" autocomplete="off"></input>
                            </select>
                        </div>

                        <div class="col-lg-3" style="padding-left:3%;" id="div_campo">
                            <label> Base seleccionada:</label>
                            <select class="form-control base_mostrar" name="base" id="base_update">
                            </select>
                        </div>
                        
                        <div class="col-lg-3" id="div_base" style="padding-left:3%;">
                            <label> Tabla seleccionada:</label>
                            <select class="form-control" name="tabla" id="tabla_update">
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12" style="display:flex; padding-top:2%;">
                        <div class="col-lg-3" id="div_tabla">
                            <label> Campo seleccionado:</label>
                            <select class="form-control campos_select" name="campo" id="campo_update">
                            </select>
                        </div>
                        <div class="col-lg-3" style="padding-left:3%;">
                            <label class="form-label">Estado:</label>
                            <select class="form-control" name="Filtro" id="slc_estado_modal">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>

                        <div class="col-lg-3" style="padding-left:3%;">
                            <label class="form-label">Filtro:</label>
                            <select class="form-control" name="Filtro" id="slc_filtro_modal">
                                <option value="0">Estatico</option>
                                <option value="1">Dinamico</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12" style="padding-top:2%;">                      
                            <div class="col-lg-3">
                                <label class="form-label">Tipo</label>
                                    <select class="form-control" name="Campo" id="slc_tipo_modal">
                                        <option value="" selected>Selecione</option>
                                        <option value="caracter">CARACTER</option>
                                        <option value="num">NUMERICO</option>
                                        <option value="fecha">FECHA</option>
                                    </select>
                            </div>
                            <div class="col-lg-3" style="margin-top:2%;padding-left:3%;">
                                <label class="form-label">FORMATO</label>
                                <div id="chk_formato_modal" class="form-check form-switch">

                                </div>
                            </div>
                            <div class="col-lg-3" style="margin-top:2%;padding-left:3%;">
                                <label class="form-label">VALOR</label>
                                <div id="chk_valor_modal" class="form-check form-switch">

                                </div>
                            </div>

                        </div>
                        <div class="col-lg-12" id="div_condiciones" style="padding-top:2%;padding-left:3%;">
                                
                        </div>       
                        <div class="col-lg-4" style="padding-top:1%;margin-left:1.5%;">
                            <input type="text" class="form-control" disabled id="inp_valor_variable_modal" value="">
                            <input type="button" class="btn btn-primary" boton="obtener" id="btn_obternerVal_modal" style="margin-top:3%;" value="Obtener Valor">
                        </div>                 
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn_cerrar" id="btn_cerrar_modal" data-dismiss="modal">Cancelar</button>
                <input type="button" class="btn btn-success" id="btn_update" value="Guardar"></input>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url('assets/js/adminSistemas/GestionVariables.js');?>"></script>