<style>
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

	.texto-success {
		color: green;
	}

	.texto-warning {
		color: red;
	}

	.texto-danger {
		color: grey;
	}

	.accordion_gest_st_ila,
	.accordion_gest_st_ilt,
	.accordion_info_endeudamiento {
		background-color: #d8d5f9;
		box-shadow: 0px 9px 10px -9px #888888;
		z-index: 1;
		cursor: pointer;
		width: 100%;
		border: none;
		outline: none;
		transition: 0.4s;
	}

	.accordion_info_endeudamiento:hover {
		background-color: #c8bef6;
	}

	.accordion_info_endeudamiento.active {
		background-color: #c8bef6;
	}
	.active.accordion_gest_st_ila:after,
	.active.accordion_gest_st_ilt:after,
	.active.accordion_info_endeudamiento:after {
		content: "\2B9E";
	}
	.accordion_gest_st_ila:after,
	.accordion_gest_st_ilt:after,
	.accordion_info_endeudamiento:after {
		content: "\2B9F";
		color: black;
		font-weight: bold;
		float: right;
		margin-top: -2em;
	}

	.panel_10 {
		background-color: white;
	}
	.active_panel {
		display: block;
	}
	.gs_laboral {
		background-color: #e0dff5;
	}
	
	.gs_real_al_dia {
		background-color: #efe4b0;
	}
	
	.gs_real_extinguido {
		background-color: #efe4b0;
	}
	
	#box_gestion_laboral th {
		font-weight: 400;
		text-align : center
	}
	#box_gestion_laboral td {
		font-weight: 700;
	}	
	#box_gestion_laboral td.st_numero {
		text-align: center;
	}
	
	#box_gestion_laboral td.st_monto {
		text-align: right;
	}

	#box_finaciero_al_dia {
		margin-top: 50px;
	}

	.table_info_endeudamiento td{
		background-color: #f2f2f2;
		border: 2px solid white !important;
	}
	.table_info_endeudamiento th{
		background-color: #ddebf7;
		border: 2px solid #bfbfbf !important;
		text-align: center;
		vertical-align: middle !important;
	}
	.cellComportamientos{
		background-color: #ddebf7 !important;
		text-align: center;
		vertical-align: middle;
	}
	
	
</style>

<div id="box_info_endeudamiento" class="box box-info">
	<div class="box-header with-border" id="titulo"></div>
	<div class="box-body" style="font-size: 12px;">
		<div class="container-fluid">
			<div class="row">		
				<button class="col-sm-12 text-center accordion_info_endeudamiento">
					<h4 class="title_button_info_endeudamiento">VER INFORMACI??N ENDEUDAMIENTO</h4>
				</button>
				<div class="panel_10" style="display:none;">
					<div class="container-fluid" id="info-endeudamiento">
						<div id="info4">
							<div>
								<div id="infoLoading1">
									<div class="loader" id="loader-6">
										<span></span>
										<span></span>
										<span></span>
										<span></span>
									</div>
								</div>
								<div id="financiero_al_dia" style="display: none"></div>
							</div>
							<div>
								<div id="infoLoading2">
									<div class="loader" id="loader-6">
										<span></span>
										<span></span>
										<span></span>
										<span></span>
									</div>
								</div>
								<div id="financiero_en_mora" style="display: none"></div>
							</div>
							<div>
								<div id="infoLoading5">
									<div class="loader" id="loader-6">
										<span></span>
										<span></span>
										<span></span>
										<span></span>
									</div>
								</div>
								<div id="financiero_extinguido" style="display: none"></div>
							</div>
							<div>
								<div id="infoLoading3">
									<div class="loader" id="loader-6">
										<span></span>
										<span></span>
										<span></span>
										<span></span>
									</div>
								</div>
								<div id="real_al_dia" style="display: none"></div>
							</div>
							<div>
								<div id="infoLoading4">
									<div class="loader" id="loader-6">
										<span></span>
										<span></span>
										<span></span>
										<span></span>
									</div>
								</div>
								<div id="real_en_mora" style="display: none"></div>
							</div>
							<div>
								<div id="infoLoading6">
									<div class="loader" id="loader-6">
										<span></span>
										<span></span>
										<span></span>
										<span></span>
									</div>
								</div>
								<div id="real_exitinguido" style="display: none"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
