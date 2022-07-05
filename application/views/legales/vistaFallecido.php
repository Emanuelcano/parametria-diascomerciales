<style type="text/css">
    .loader {
    border: 16px solid #f3f3f3; /* Light grey */
    border-top: 16px solid #3498db; /* Blue */
    border-radius: 50%;
    width: 100px;
    height: 100px;
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
.botones{
    padding-top:24px;
}

#nombre, #nombre1{
    text-align:left;
}

#btn_buscar{
    width:200%;
}

.inputs{
    margin-left:15%;
}
.boton_reset{
    position: relative;
    left: -5%;
}

.boton_buscar{
    width:22%;
}

#boton_lim{
    width:200%;
    position: relative;
}


#datos_cliente{
    width:400px;
}

#frm_dar_fallecido {
    position: relative;
    left: 1%;
    border: 2px solid #f0eeee;
}

.tabla{
    padding-top:2%;
    width:60%;
}

#clientes{
    width:100%;
}
.cuerpo{
    background-color:#faf8f8;
}

.cuerpo th{
    text-align:center;
    height:50px;
    
}

#nombre, #nombre1{
    text-align:left;
}

.btn_b.col-lg-2 {
    width: 20%;
}

.boton_reset.col-lg-3 {
    position: relative;
    left: -3%;
}

.cabeza tr{
    background-color:#c0e7fa;
    height:30px;
    border-bottom: 1px solid #1f1f1f;
}

.cabeza tr td{
    text-align:center;
    font-family: Arial;
}

hr{
    width:102%;
    height:1px;
    border-top: 3px solid #46c8f5;
    margin: 5px 0;
    display: block;
    clear: both;
}

.modalAdjuntarFallecidos{
    top:25%;
}


.tabla{
    width:100%;
}
.modal-dialog{
    width: 1000px;
}
</style>
<h2 id="titulo" align="center">Fallecidos</h2>
<br><br>
<div class="container">
    <form action="<?php base_url();?>legales/Legales/get_datos_baja" method="POST" id="frm_datos_baja" name="frm_datos_baja">
        <div class="row">
            <div class="inputs">
                <div class="txt_datos col-lg-5">
                    <label>Buscar Cliente:</label>
                    <div class="input-group mb-3">
                        <input type="text" id="datos_cliente" name="datos_cliente" class="form-control" placeholder="Ingresar Datos" aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                </div>
                <div class="boton_buscar col-lg-3">
                    <div class="botones input-group">
                        <button type="button" id="btn_buscar" class="btn btn-info"><i class="fa fa-search"></i> Buscar</button>
                    </div>              
                </div>           
                <div class="boton_reset col-lg-3">
                    <div class="botones input-group">
                        <a href="javascript:document.getElementById('frm_datos_baja').reset();" id="boton_lim" class="btn btn-danger">
                            <i class="fa fa-times"></i> Limpiar
                        </a>                  
                    </div>              
                </div>
            </div>           
        </div>
    </form> 
</div>
<div class="row card tabla" style="display:none">
    <div class="tabla col-lg-12 col-md-12 col-sm-12">
        <hr class="pt">
        <br>
        <form action="<?php base_url();?>legales/Legales/dar_baja" method="post" id="frm_dar_fallecido" name="frm_dar_fallecido">
            <table id="clientes" class="table-striped">
                <thead class="cabeza">
                    <tr>
                    <td><i class="fa fa-id-card-o" aria-hidden="true"></i><strong> Documento</strong></td>
                    <td scope="col" id="nombre"><i class="fa fa-user" aria-hidden="true"></i><strong> Nombre Cliente</strong></td>
                    <td scope="col" id="nombre1"><i class="fa fa-user" aria-hidden="true"></i><strong> Apellido Cliente</strong></td>
                    <td scope="col" id="adjuntar_fallecidos" style="display:none"><i class="fa fa-file-archive-o " aria-hidden="true"></i><strong> Comprobante</strong></td>
                    <td scope="col"><i class="fa fa-wrench" aria-hidden="true"></i><strong> Opciones</strong></td>
                    </tr>
                </thead>
                <tbody class="cuerpo">
                    
                </tbody>
            </table>
        </form>
    </div>
</div>
<div class="modal fade" id="compose-modal-wait" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-time"></i> POR FAVOR ESPERE MIENTRAS SE DAN BAJA LOS DATOS </h4>
                <div class="col-md-12 hide" id="succes">
                    <!-- Primary box -->
                    <div class="box box-solid box-primary">
                        <div class="box-header">
                            <h3 class="box-title">BAJA FALLECIDOS</h3>
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

<div class="modal fade" id="myModalAdjuntarFallecido" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modalAdjuntarFallecidos modal-dialog">
        <div class="modal-content">
            <div class="modalCabeceraFallecidos modal-header">
                <button class="btn_cerrar close" data-dismiss="modal">&times;</button>
                
            </div>
            <div class="cuerpoModal modal-body">
                <div class="contenido_modal container-fluid">
                    <form action="<?php base_url();?>legales/Legales/adjuntar_archivos" enctype="multipart/form-data" id="formArchivo" method="post">
                    <div class="div_archivo">
                                <div>
                                    <td><label class="form-label">Seleccione uno o mas archivos:</label></td>
                                    <td><input class="inp_adjuntarFallecidos form-control" type="file" multiple name="file[]" value="" id="file"></td>
                                </div>
                            </div>
                        </form>
                        </div>
                <div class="pieModalFallecidos modal-footer">
                    <button type="button" class="btn_cerrar btn btn-danger"  data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" id="btn_agregar_comprobante">Guardar</button>
                </div>
            </div>
        </div>
</div>

<script type="text/javascript" src="<?php echo base_url('assets/legales/fallecidos.js');?>" ></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/fileinput.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/fileinput_locale_es.js');?>"></script>


