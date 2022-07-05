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
.accordion_listas_restrictivas{
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
.accordion_listas_restrictivas:hover {
  background-color: #c8bef6 ; 
}
.accordion_listas_restrictivas.active{
  background-color: #c8bef6;
}

.active.accordion_listas_restrictivas:after {
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

.accordion_listas_restrictivas:after{
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

.table_listas_restrictivas thead tr {
	background-color: #ddebf7;
}

.table_listas_restrictivas thead tr th{
	text-align: center;
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
                <button class ="col-sm-12 text-center accordion_listas_restrictivas">
                    <h4 class="title_button_verlistasr">LISTAS RESTRICTIVAS</h4>
                </button>
                <div class="panel_10" style="display:none;">
                    <div class="col-sm-12"  id="lista_restrictiva" style="padding-top: 1em;">
						<div id="infoLoadinglistas">
							<div class="loader" id="loader-6">
								<span></span>
								<span></span>
								<span></span>
								<span></span>
							</div>
						</div>
						<div id="listas_restrictivas_content" style="display: none"></div>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>




