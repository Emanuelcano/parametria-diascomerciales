<style>
    #contenido{
        margin-left:10px;
    }

    hr{
        border: 0 none #e8e1e0;
        border-top: 1px solid #e8e1e0;
        height: 1px;
        margin: 5px 0;
        display: block;
        clear: both;
    }

    #mes select{
        width:200%;
    }

    #anio select{
        width:290%;
    }

    #monto input{
        width:110%;
    }
    
    #monto{
        width:13%;
    }

    #registro{
        margin-top:24px;
        width:7%;
    }

    #registro button{
        width:137%;
    }

    .modal-dialog {
        margin-top:9%;
        margin-left:40%;
        width:40%;
    }

    #cabeceras{
        background-color:#dad5ec;
    }


    div#monto{
        margin-right: 2%;
    }

    #tablaUsura{
        width: 100%;
    }

</style>

<form action="" method="POST" id="frm_registrar_usura" name="frm_registrar_usura">
    <div class="row" id="contenido">
        <h3>Nuevo Registro: </h3>
            <div class="col-lg-2 " id="mes">
                <div class="form-group">
                    <label>Mes: </label>
                    <div class="input-group">
                    <select class="form-control" id="slc_mes">
                        <option value="Enero" selected>Enero</option>
                        <option value="Febrero">Febrero</option>
                        <option value="Marzo">Marzo</option>
                        <option value="Abril">Abril</option>
                        <option value="Mayo">Mayo</option>
                        <option value="Junio">Junio</option>
                        <option value="Julio">Julio</option>
                        <option value="Agosto">Agosto</option>
                        <option value="Septiembre">Septiembre</option>
                        <option value="Octubre">Octubre</option>
                        <option value="Noviembre">Noviembre</option>
                        <option value="Diciembre">Diciembre</option>
                    </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-2" id="anio">
                <div class="form-group">
                    <label>Anio: </label>
                    <div class="input-group">
                    <select class="form-control" id="slc_anio">
                    </select>
                    </div>
                </div>
            </div>
            <div class="col-lg-2" id="monto">
                <div class="form-group">
                    <label>Monto: </label>
                    <div class="input-group" id="dato_monto">
                            <input type="input" autocomplete="off" class="form-control" name="txt_monto" id="txt_monto">
                    </div>
                </div>
            </div>
            <div class="col-lg-2" id="registro">
                <div class="form-group">
                        <div class="input-group">
                            <button type="button" class="btn btn-success" id="btn_registrar_usura"> Registrar</button>
                        </div>
                </div>
            </div>
        </div>
</form>
<hr>
<br>
<h3 id="titulo_tabla">Historico de tasas de la Superintendencia</h3>
<div class="col-lg-12 col-md-12" id="tableusura">
    <table id="tablaUsura" class="display" style="display:none">
        <thead id="cabeceras">
            <tr>
                <th>Fecha Creacion</th>
                <th>Creado por</th>
                <th>Fecha Actualizacion</th>
                <th>Actualizado por</th>
                <th>Anio</th>
                <th>Mes</th>
                <th>Monto</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="cuerpo">

        </tbody>
    </table>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <button class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title">Actualizar datos</h3>
        </div>
        <div class="modal-body">
            <div class="container-fluid">
                <div class="row">
                <div class="col-md-1 ms-auto" id="id_usura"></div>
                <div class="col-md-3 ms-auto" id="act_mes"></div>
                <div class="col-md-3 ms-auto" id="act_anio"></div>
                <div class="col-md-3 ms-auto" id="act_monto"></div>
            </div>
        </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-success" id="btn_actualizar" data-dismiss="modal">Guardar</button>
        </div>
        </div>
    </div>
</div>



<script type="text/javascript" src="<?php echo base_url('assets/legales/tasa_usura.js');?>" ></script>
