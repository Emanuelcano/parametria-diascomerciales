<style>
.panel_11 {
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
  margin-top: 0.5em;
}

#preview_mail_html .modal-body{
    height: 45em;
    width: 100%;
    overflow-y: auto;
}
.accordion_13:hover {
  background-color: #c8bef6 ; 
}
.accordion_13.active{
  background-color: #c8bef6;
}

.active.accordion_13:after {
  content: "\2B9E";
}
.accordion_13:after{
  content: "\2B9F";
  color: black;
  font-weight: bold;
  float: right;
  margin-top: -2em;
}
.accordion_13{
  background-color: #d8d5f9;
  box-shadow: 0px 9px 10px -9px #888888; 
  z-index: 1;
  cursor: pointer;
  width: 100%;
  border: none;
  outline: none;
  transition: 0.4s;

}
.active_panel_2{
  display: block;
}
</style>
<div id="box_agenda_mail_new" class="box box-info">
    <div class="box-header with-border" id="titulo">
    </div>
    <div class="box-body" style="font-size: 12px;">
      
        <div class="container-fluid">
            <div class="row">
                <button class="col-sm-12 text-center accordion_13 active">
                    <h4 class="title_button_vermail">DIRECTORIO MAIL</h4>
                 </button>
                <div class="panel_13" style="display:block;">
                    <div class="col-sm-12"  id="tabla_agenda_3" style="padding-top: 1em;"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="add_mail_new" tabindex="-1" role="dialog" aria-labelledby="agendaLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header text-center">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="agendaLabel"></label></h3>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-12 text-center" style="background-color: #d8d5f9; box-shadow: 0px 9px 10px -9px #888888; z-index: 1;"><h4 >AGENDAR CORREO</h4></div>
            <div class="col-sm-12"><br></div>
            <div class="col-sm-12" id="form-tel">
                <div class="form-group col-sm-12">
                    <label for="new-cuenta" class="col-form-label">Cuenta:</label>
                    <input type="email" class="form-control" placeholder="example@dominio.com" id="new-cuenta-mail"  onkeypress="return validateEmail(event)">
                </div>
                
                <div class="form-group col-sm-12">
                    <label for="new-contacto" class="col-form-label">Contacto:</label>
                    <input type="text" class="form-control" id="new-contacto-mail">
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-fuente-mail" class="col-form-label">Fuente:</label>
                    <select class="form-control" id="new-fuente-mail">
                        <option value="PERSONAL">Personal</option>
                        <option value="REFERENCIA">Referencia</option>
                        <option value="LABORAL">Laboral</option>
                        <option value="BURO_CELULAR">Buro - Celular - D</option>
                        <option value="BURO_CELULAR_T">Buro - Celular - T</option>
                        <option value="BURO_LABORAL">Buro - Laboral - D</option>
                        <option value="BURO_REFERENCIA">Buro - Referencia - D</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-estado-mail" class="col-form-label">Estado:</label>
                    <select class="form-control" id="new-estado">
                        <option value="1">Activo</option>
                        <option value="0">Fuera de servicio</option>
                    </select>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="agendar_mail" onclick="agendarMail(<?= $documento ?>)" disabled><i class="fa fa-plus"></i> AGREGAR</button>
    </div>
    </div>
  </div>
</div>
<div class="modal fade" id="preview_mail_html" tabindex="-1" role="dialog" aria-labelledby="preview_mail_html">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <div class="col-md-12 text-center" style="background-color: #d8d5f9; box-shadow: 0px 9px 10px -9px #888888; z-index: 1;"><h4>PRE-VISUALIZACION MAIL </h4></div>            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success sendTemplateMail" data-id_solicitud='<?=$id_solicitud?>' data-documento='<?= $documento?>'><i class='fa fa-send'></i></button>

            </div>
        </div>
    </div>
</div>
