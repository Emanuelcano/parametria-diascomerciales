<style>
table.modificable input[type=text], table.modificable select {
    border: 0px !important;
    border-bottom: 1px solid #ccc !important;
    height: 20px !important;
    padding-bottom: 0px !important;
    padding-top: 0px !important;
}
table.modificable .btn-group-sm>.btn, table.modificable .btn-sm {
    padding: 1px 10px !important;
}

a[data-title]:hover:after {
    opacity: 1;
    transition: all 0.1s ease 0.5s;
    visibility: visible;
}
a[data-title]:after {
    content: attr(data-title);
    background-color: #000000c9;
    color: #f4f4f4;
    position: absolute;
    padding: 7px;
    white-space: nowrap;
    box-shadow: 1px 1px 3px #222222;
    opacity: 0;
    z-index: 1;
    height: 30px;
    visibility: hidden;
    left: 20px;
    bottom: -6px
}
a[data-title] {
    position: relative;
    float: right;
}
.texto-success{
color:green;
}

.texto-warning{
color:red;
}
.texto-danger{
color:grey;
}

.accordion_3 {
  background-color: #afd879 ;
  color: #FFF;
  cursor: pointer;
  padding: 10px;
  width: 100%;
  border: none;
  text-align: left;
  outline: none;
  font-size: 1em;
  transition: 0.4s;
  letter-spacing: 0.2em;
}

.accordion_5 {
  background-color: #ACEAFA ;
  color: #FFF;
  cursor: pointer;
  padding: 10px;
  width: 100%;
  border: none;
  text-align: left;
  outline: none;
  font-size: 1em;
  transition: 0.4s;
  letter-spacing: 0.2em;
}
.accordion_7 {
  background-color: #c99ad5;
  color: #FFF;
  cursor: pointer;
  padding: 10px;
  width: 100%;
  border: none;
  text-align: left;
  outline: none;
  font-size: 1em;
  transition: 0.4s;
  letter-spacing: 0.2em;
}
.accordion_10{
  background-color: #d8d5f9;
  box-shadow: 0px 9px 10px -9px #888888; 
  z-index: 1;
  cursor: pointer;
  width: 100%;
  border: none;
  outline: none;
  transition: 0.4s;

}
.accordion_3 .active .accordion_3:hover {
  background-color: #668c31; 
}
.accordion_5 .active .accordion_5:hover {
  background-color: #668c31; 
}
.accordion_7 .active .accordion_7:hover {
  background-color: #668c31; 
}
.accordion_10:hover {
  background-color: #c8bef6 ; 
}
.accordion_10.active{
  background-color: #c8bef6;
}

.active.accordion_10:after {
  content: "\2B9E";
}
.panel_4 >.active:after {
  content: "\2B9E";
}
.panel_6 >.active:after {
  content: "\2B9E";
}
.panel_8 >.active:after {
  content: "\2B9E";
}


.accordion_3:after{
  content: "\2B9F";
  color: white;
  font-weight: bold;
  float: right;
  margin-left:5px;
}
.accordion_5:after{
  content: "\2B9F";
  color: white;
  font-weight: bold;
  float: right;
  margin-left:5px;
}
.accordion_7:after{
  content: "\2B9F";
  color: white;
  font-weight: bold;
  float: right;
  margin-left:5px;
}

.accordion_10:after{
  content: "\2B9F";
  color: black;
  font-weight: bold;
  float: right;
  margin-top: -2em;
}

.panel_3 {
  padding: 0px;
  display: none;
  background-color: white;
  overflow: hidden;
}
.panel_5 {
  padding: 0px;
  display: none;
  background-color: white;
  overflow: hidden;
}
.panel_7 {
  padding: 0px;
  display: none;
  background-color: white;
  overflow: hidden;
}
.panel_4 {
  padding: 0px;
  display: none;
  background-color: white;
  overflow: hidden;
  position: absolute;
  width: 50em;
  z-index:1000 !important;
  max-height: 60em;
  overflow: auto;
  right:-1em;
  margin-top: 2em;
}
.panel_6 {
  padding: 0px;
  display: none;
  background-color: white;
  overflow: hidden;
  position: absolute;
  width: 50em;
  z-index:1000 !important;
  max-height: 60em;
  overflow: auto;
  right:-1em;
  margin-top: 2em;
}
.panel_8 {
  padding: 0px;
  display: none;
  background-color: white;
  overflow: hidden;
  position: absolute;
  width: 50em;
  z-index:1000 !important;
  max-height: 60em;
  overflow: auto;
  right:-1em;
  margin-top: 2em;
}
.panel_10 {
  background-color: white;
}
.active_panel{
  display: block;
}
.btn_estado_servicio{
  cursor: pointer;
}
.no-drop{
 cursor: no-drop; 
}

audio::-webkit-media-controls-panel 
{
    background-color: #FFFEDE;
}

audio::-webkit-media-controls-timeline 
{
    background-color: #f7e6a4;
    border-radius: 25px;
    margin-left: 10px;
    margin-right: 10px;
}
</style>
<input id="tipo_operador" type="hidden" value="<?=$this->session->userdata("tipo_operador");?>">
<input id="tipo_canal" type="hidden" value="<?= $tipo_canal ?>">

<div id="box_agenda_telf_new" class="box box-info">
    <div class="box-header with-border" id="titulo">
    </div>
    <div class="box-body" style="font-size: 12px;">
        
        <div class="container-fluid">
            <div class="row">
                <button class ="col-sm-12 text-center accordion_10 active">
                    <h4 class="title_button_veragenda">DIRECTORIO TELEFÓNICO</h4>
                </button>
                <div class="panel_10" style="display:block;">
                    <div class="col-sm-12"  id="tabla_agenda2" style="padding-top: 1em;"></div>
                </div>
            </div>
            <div class="panel_10" style="display:block;">
                    <div class="col-sm-12"  id="audios_escuchar" style="padding-top: 1em;background-color: #F5F5F5;margin-top: 1%;padding-bottom: 1%;display:none;"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="new-create-agenda" tabindex="-1" role="dialog" aria-labelledby="agendaLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header text-center">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="agendaLabel"></label></h3>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-12 text-center" style="background-color: #d8d5f9; box-shadow: 0px 9px 10px -9px #888888; z-index: 1;"><h4 id="new-titulo"></h4></div>
            <div class="col-sm-12"><br></div>
            <div class="col-sm-12" id="form-tel">
                <div class="form-group col-sm-6">
                    <label for="new-numero-new" class="col-form-label">Número:</label>
                    <input type="number" class="form-control" id="new-numero-new" onkeypress='return validaNumericos(event)'>
                </div>
                
                <div class="form-group col-sm-6">
                    <label for="new-contacto" class="col-form-label">Contacto:</label>
                    <input type="text" class="form-control" id="new-contacto-new">
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-fuente" class="col-form-label">Fuente:</label>
                    <select class="form-control" id="new-fuente-new">
                        <option value="" disabled selected >Seleccione</option>
                        <option value="PERSONAL">Personal</option>
                        <option value="PERSONAL LLAMADA">Personal llamada</option>
                        <option value="PERSONAL WHATSAPP">Personal whatsapp</option>
                        <option value="REFERENCIA">Referencia</option>
                        <option value="LABORAL">Laboral</option>
                        <option value="BURO_CELULAR">Buro - Celular - D</option>
                        <option value="BURO_CELULAR_T">Buro - Celular - T</option>
                        <option value="BURO_LABORAL">Buro - Laboral - D</option>
                        <option value="BURO_REFERENCIA">Buro - Referencia - D</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-tipo" class="col-form-label">Tipo:</label>
                    <select class="form-control" id="new-tipo-new">
                        <option value="MOVIL">Movil</option>
                        <option value="FIJO">Fijo</option>
                    </select>
                </div>
                
                <div class="form-group col-sm-6" id="div-departamentos-new">
                    <label for="departamentos" class="col-form-label">Departamento:</label>
                    <select class="form-control" id="departamentos-new">
                        <option value="" disabled selected >Seleccione</option>
                    </select>
                </div>
                <div class="form-group col-sm-6" id="div-ciudad-new">
                    <label for="ciudad" class="col-form-label">Ciudad:</label>
                    <select class="form-control" id="ciudad-new" disabled >
                        <option value="" disabled selected>Seleccione</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-estado" class="col-form-label">Estado:</label>
                    <select class="form-control" id="new-estado-new">
                        <option value="1">Activo</option>
                        <option value="0">Fuera de servicio</option>
                    </select>
                </div>
                <div style="display:none;" class="form-group col-sm-6" id="div-parentesco-new">
                    <label class="col-form-label">Parentesco</label>
                    <select class="form-control" id="parentesco-new">
                        <option value="" disabled selected>Seleccione</option>
                        <option value="0">Sin parentesco</option>
                        <option value="1">Madre</option>
                        <option value="2">Padre</option>
                        <option value="3">Hijo/a</option>
                        <option value="4">Hermano/a</option>
                        <option value="5">Amigo/a</option>
                        <option value="6">Compañero</option>
                        <option value="7">Otro</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-estado" class="col-form-label">Verificacion de Llamada</label>
                    <select class="form-control" id="llamada-verificada-new">
                        <option value="1">Verificado</option>
                        <option value="0">No Verificado</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-estado" class="col-form-label">Verificacion de SMS</label>
                    <select class="form-control" id="sms-verificada-new">
                        <option value="1">Verificado</option>
                        <option value="0">No Verificado</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-estado" class="col-form-label">Verificacion de Whatsapp</label>
                    <select class="form-control" id="wts-verificada-new">
                        <option value="1">Verificado</option>
                        <option value="0">No Verificado</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-estado" class="col-form-label">Llamada</label>
                    <select class="form-control" id="llamada-new">
                        <option value="1">Si</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-estado" class="col-form-label">SMS</label>
                    <select class="form-control" id="sms-new">
                        <option value="1">Si</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-estado" class="col-form-label">Whatsapp</label>
                    <select class="form-control" id="wts-new">
                        <option value="1">Si</option>
                        <option value="0">No</option>
                    </select>
                </div>

            </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" data-id_solicitud='<?=$id_solicitud?>' id="agendar_tlf_new" onclick="agendarTelefonoSolicitante(<?= $documento ?>)"><i class="fa fa-plus"></i> AGREGAR</button>
        <button type="button" style="display:none;" class="btn btn-info" id="update_tlf_new"><i class="fa fa-refresh"></i> ACTUALIZAR</button>

    </div>
    </div>
  </div>
</div>

<div class="modal fade" id="new-localidad-modal" tabindex="-1" role="dialog" aria-labelledby="new-localidad-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="creditoLabel">Agregar departamento y ciudad</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label for="departamentos-modal" class="col-form-label">Departamento:</label>
                        <select class="form-control" id="departamentos-modal-new">
                            <option value="" disabled selected >Seleccione</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="ciudad-modal" class="col-form-label">Ciudad:</label>
                        <select class="form-control" id="ciudad-modal-new" disabled >
                            <option value="" disabled selected>Seleccione</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="new-estado-servicio" tabindex="-1" role="dialog" aria-labelledby="new-estado-servicio">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="newEstadoLabel">Cambio Estado de Servicio</h3>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>


<div class="modal fade" id="reportarAudioModal" tabindex="-1" role="dialog" >
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
       

        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>
