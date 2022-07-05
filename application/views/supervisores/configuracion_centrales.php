<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="id_operador" name="id_operador" value="<?php echo $this->session->userdata('idoperador'); ?>">
<div class="contenedor">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#agenteCentrales" data-toggle="tab">Agente</a></li>
    <li class=""><a href="#skillCentrales"data-toggle="tab">Skill</a></li>
    <li><a href="#campañaCentrales" data-toggle="tab">Campaña</a></li>
    
  </ul>

  <div class="tab-content">
    <div class="col-lg-12 tab-pane fade in active" id="agenteCentrales">
      <div class="alert alert-warning" id="SaveDB">
        <b>Crear Agente solo en Base de Datos</b>
      </div>
      <div class="alert alert-info" id="SaveCentrales">
        <b>Crear Agente en central y en Base de Datos</b>
      </div>
      <div class="col-xs-12">
        <div class="row">
          <div class="col-xs-12">
              <br>
              <h5><b>REGISTRAR AGENTE</b></h5>	
          </div>
          <div class="form-group col-sm-2" id="form_datos">
            <div class="form-check form-check" style="padding-right: inherit;" id="div_check_SDB">
              <input class="form-check-input" id ="check_SDB" name="check_SDB" type="checkbox" value="1">
              <span class="form-check-label">Guardar solo en base de Datos</span>
            </div>
          </div>
          <div class="form-group col-sm-2">
          
            <select class="form-control" name="sl_central" id="sl_central">
                <option value="" selected="selected">Seleccione Central</option>
                <option value="wolkvox"> WOLKVOX </option>
                <option value="isabel"> ISABEL </option>
                <option value="neotell"> NEOTEL </option>
            </select>
          </div>
          <div class="form-group col-sm-3" id="div_slc-operadores">
            <select id="slc-operadores" class="form-control js-example-basic" data-live-search="true" onChange="">
                <option selected="true" disabled="disabled" value="">Seleccione Operador</option>
                  <?php foreach ($lista_operadores as $lista_operadore): ?>
                    <option value="<?= $lista_operadore["idoperador"]?>"><?= $lista_operadore["nombre_apellido"]?></option>
                  <?php endforeach; ?>			
            </select>
          </div>
          <div class="form-group col-sm-1">
            <input type="number" onkeypress="return solo_numeros(event)" class="form-control" id="input_id_agente" placeholder="ID AGENTE"></input>
          </div>
          <div class="form-group col-sm-2">
            <select id="input_skill" class="form-control" onChange="" placeholder="SKILL">
                <option selected="true" disabled="disabled" value="">Seleccione Skill</option>
                  <?php foreach ($lista_skills as $lista_skill): ?>
                    <option value="<?= $lista_skill["id_skill"]?>"><?= $lista_skill["descripcion"]?></option>
                  <?php endforeach; ?>			
            </select>
            <!-- <input type="text" class="form-control" id="input_skill" placeholder="SKILL"></input> -->

          </div>
          <div class='form-group col-sm-1' id="cancelAgente" style="display:none;float: left;">
            <a class="btn btn-danger"  title="Cancelar Editar Agente" onclick="cancelEditAgente();"><i class="fa fa-ban"></i></a>
          </div>
          <div class="form-group col-sm-2">
              <button  type="button" class="btn btn-success" title="registrar" style="font-size: 12px; width: 100%;" id="registrar-agente" ><i class="fa fa-check"></i> REGISTRAR AGENTE</button>
              <button  type="button" class="btn btn-success" title="actualizar" style="font-size: 12px; width: 100%; display:none" id="actualizar-agente" ><i class="fa fa-check"></i> ACTUALIZAR AGENTE</button>
          </div>

        </div>
      </div>
      <div id="table_agente_central">
        <?= $this->load->view('supervisores/table_agentes', null, true); ?>  
      </div>

    </div>

    <div class="col-lg-12 tab-pane fade" id="campañaCentrales">
      <div class="alert alert-warning" id="SaveDB2">
        <b>Crear Campaña solo en Base de Datos</b>
      </div>
      <div class="alert alert-info" id="SaveCentrales2">
        <b>Crear Campaña en central y en Base de Datos</b>
      </div>
      <div class="box-header with-border" id="titulo">
        <h6 class="box-title"><small><strong>Registro Campaña</strong></small></h6>
      </div>
      <div class="col-xs-12">
        <div class="row">
          <div class="col-xs-12">
              <br>
              <h5><b>REGISTRAR CAMPAÑA</b></h5>	
          </div>
          
          <div class="form-group col-sm-2" id="form_datos2">
            <div class="form-check form-check" style="padding-right: inherit;" id="div_check_SDB2">
              <input class="form-check-input" id ="check_SDB2" name="check_SDB" type="checkbox" value="1">
              <span class="form-check-label">Guardar solo en base de Datos</span>
            </div>
          </div>
          <div class="form-group col-sm-2" id="div_id_campania">
            <input type="text" class="form-control" id="id_campania" placeholder="ID CAMPAÑA"></input>
          </div>
          <div class="form-group col-sm-2">
              <select class="form-control" name="sl_central_campania" id="sl_central_campania">
                  <option value="" selected="selected" disabled="disabled">Selecciona Central</option>
                <option value="wolkvox"> WOLKVOX </option>
                <option value="isabel"> ISABEL </option>
                <option value="neotel"> NEOTEL </option>
              </select>
          </div>
          <div class="form-group col-sm-2">
              <select class="form-control" name="sl_preview_campana" id="sl_preview_campana">
                  <option value="" selected="selected" disabled="disabled">Selecciona Tipo Campaña</option>
                <option value="yes"> PREVIEW </option>
                <option value="no"> PREDICTIVO </option>
              </select>
          </div>
          <div class="form-group col-sm-2">
            <input type="text" class="form-control" id="input_name_campania" placeholder="NOMBRE CAMPAÑA"></input>
          </div>
          <div class="form-group col-sm-2">
            <input type="text" class="form-control" id="input_description_campania" placeholder="DESCRIPCION CAMPAÑA"></input>
          </div>
          <div class="form-group col-sm-2">
            <select id="sl_skill_campania" class="form-control" onChange="">
                <option selected="true" disabled="disabled" value="">Seleccione Skill</option>
                  <?php foreach ($lista_skills as $lista_skill): ?>
                    <option value="<?= $lista_skill["id_skill"]?>"><?= $lista_skill["descripcion"]?></option>
                  <?php endforeach; ?>			
            </select>
          </div>
          <div class='form-group col-sm-1' id="cancelarEditCampania" style="display:none;float: left;">
              <a class="btn btn-danger"  title="Cancelar Editar Campania" onclick="cancelEditCampania();"><i class="fa fa-ban"></i></a>
          </div>
          <div class="form-group col-sm-1" id="div_hora_ini_campania">
            Hora Inicio
            <span class='input-group timepicker' >
              <input type='text' class="form-control" id='hora_ini_campania' placeholder="HORA INICIO"/>
              <span class="input-group-addon">
                <span class="fa fa-clock-o"></span>
              </span>
						</span>
            <!-- <input type="text" maxlength="4" class="form-control" id="hora_ini_campania" placeholder="HORA INICIO" onkeypress="return solo_numeros(event)"></input> -->
          </div>
          <div class="form-group col-sm-1" id="div_hora_fin_campania">
            Hora Final
            <div class='input-group timepicker' >
              <input type='text' class="form-control" id='hora_fin_campania' placeholder="HORA FINAL"/>
              <span class="input-group-addon">
                <span class="fa fa-clock-o"></span>
              </span>
						</div>
            <!-- <input type="text" maxlength="4" class="form-control" id="hora_fin_campania" placeholder="HORA FINAL" onkeypress="return solo_numeros(event)"></input> -->
          </div>
          <div class="form-group col-sm-2" id="div_opt1_campania">
            <input type="text" class="form-control" id="opt1_campania" placeholder="OPCION 1"></input>
          </div>
          <div class="form-group col-sm-2" id="div_opt2_campania">
            <input type="text" class="form-control" id="opt2_campania" placeholder="OPCION 2"></input>
          </div>
          <div class="form-group col-sm-2" id="div_opt3_campania">
            <input type="text" class="form-control" id="opt3_campania" placeholder="OPCION 3"></input>
          </div>
          <div class="form-group col-sm-2" id="div_opt4_campania">
            <input type="text" class="form-control" id="opt4_campania" placeholder="OPCION 4"></input>
          </div>
          <div class="form-group col-sm-2" id="div_opt5_campania">
            <input type="text" class="form-control" id="opt5_campania" placeholder="OPCION 5"></input>
          </div>
          <div class="form-group col-sm-2" id="div_opt6_campania">
            <input type="text" class="form-control" id="opt6_campania" placeholder="OPCION 6"></input>
          </div>
          <div class="form-group col-sm-2" id="div_opt7_campania">
            <input type="text" class="form-control" id="opt7_campania" placeholder="OPCION 7"></input>
          </div>

          <div class="form-group col-sm-2" style="float: right">
              <button  type="button" class="btn btn-success" title="registrar" style="font-size: 12px; width: 100%;" id="registrar-campania" ><i class="fa fa-check"></i> REGISTRAR CAMPAÑA</button>
              <button  type="button" class="btn btn-success" title="actualizar" style="font-size: 12px; width: 100%; display:none" id="actualizar-campania" ><i class="fa fa-check"></i> ACTUALIZAR CAMPAÑA</button>
          </div>

        </div>
      </div>
      <div id="table_crear_campanis">
        <?= $this->load->view('supervisores/table_create_campania', null, true); ?>  
      </div>

    </div>

<div class="col-lg-12 tab-pane fade" id="skillCentrales">
      <div class="box-header with-border" id="titulo">
        <h6 class="box-title"><small><strong>Registro Skill</strong></small></h6>
      </div>
      <div class="col-xs-12">
        <div class="row">
          <div class="col-xs-12">
              <br>
              <h5><b>REGISTRAR SKILL</b></h5>	
          </div>
          <div class="form-group col-sm-2">
            <input type="text" class="form-control" id="id_skill_create_skill" placeholder="ID SKILL"></input>

          </div>
          <div class="form-group col-sm-2">
            <input type="text" class="form-control" id="id_grupo_operadores_create_skill" placeholder="ID GRUPO OPERADORES"></input>

          </div>
          <div class="form-group col-sm-2">
            <input type="text" class="form-control" id="descripcion_create_skill" placeholder="DESCRIPCION SKILL"></input>
          </div>
          <div class="form-group col-sm-2">
            <select class="form-control" name="sl_central_create_skill" id="sl_central_create_skill">
                <option value="" selected="selected" disabled="disabled">Seleccione Central</option>
                <option value="wolkvox"> WOLKVOX </option>
                <option value="isabel"> ISABEL </option>
                <option value="neotel"> NEOTEL </option>
            </select>
          </div>
          <div class='form-group col-sm-1' id="cancelarEditSkill" style="display:none;float: left;">
            <a class="btn btn-danger"  title="Cancelar Editar Skill" onclick="cancelEditSkill();"><i class="fa fa-ban"></i></a>
          </div>
          <div class="form-group col-sm-2">
              <button  type="button" class="btn btn-success" title="registrar" style="font-size: 12px; width: 100%;" id="registrar-skill" ><i class="fa fa-check"></i> REGISTRAR SKILL</button>
              <button  type="button" class="btn btn-success" title="actualizar" style="font-size: 12px; width: 100%; display:none" id="actualizar-skill" ><i class="fa fa-check"></i> ACTUALIZAR SKILL</button>
          </div>

        </div>
      </div>
      <div id="table_crear_skill">
      </div>
        <?= $this->load->view('supervisores/table_skill', null, true); ?>  
    </div>
  </div>
</div>

<!-- Modal asignar Skill -->
<div class="modal fade" id="modalA_asignarSkill" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" style="width: 80%;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel">Asignar Skill</h3>
      </div>
      <div class="modal-body">
      <div class="box-body">
        <div id="asignacion_skill">
          <div class="col-sm-12">
            <div id="dualSelectExample" style="width:100%; height:200px;"></div><br> 								
          </div>
          <div class="row text-right">
            <div class="col-sm-2">
                <button  type="button" class="btn btn-success" title="asignar-skill" style="font-size: 12px; width: 100%;" id="asignar-skill" ><i class="fa fa-check"></i> ASIGNAR SKILL </button>
            </div>
          </div>
        </div>
      </div>
            
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript" src="<?php echo base_url('assets/supervisores/configuracion_centrales.js'); ?>"></script>


