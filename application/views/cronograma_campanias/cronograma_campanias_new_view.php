
<style>
	body, #modulo-content, .content {
		background-color: #ecf0f5 !important;
	}

	/* custom inclusion of right, left and below tabs */

	.tabs-below > .nav-tabs,
	.tabs-right > .nav-tabs,
	.tabs-left > .nav-tabs {
		border-bottom: 0;
	}

	.tab-content > .tab-pane,
	.pill-content > .pill-pane {
		display: none;
	}

	.tab-content > .active,
	.pill-content > .active {
		display: block;
	}

	.tabs-below > .nav-tabs {
		border-top: 1px solid #ddd;
	}

	.tabs-below > .nav-tabs > li {
		margin-top: -1px;
		margin-bottom: 0;
	}

	.tabs-below > .nav-tabs > li > a {
		-webkit-border-radius: 0 0 4px 4px;
		-moz-border-radius: 0 0 4px 4px;
		border-radius: 0 0 4px 4px;
	}

	.tabs-below > .nav-tabs > li > a:hover,
	.tabs-below > .nav-tabs > li > a:focus {
		border-top-color: #ddd;
		border-bottom-color: transparent;
	}

	.tabs-below > .nav-tabs > .active > a,
	.tabs-below > .nav-tabs > .active > a:hover,
	.tabs-below > .nav-tabs > .active > a:focus {
		border-color: transparent #ddd #ddd #ddd;
	}

	.tabs-left > .nav-tabs > li,
	.tabs-right > .nav-tabs > li {
		float: none;
	}

	.tabs-left > .nav-tabs > li > a,
	.tabs-right > .nav-tabs > li > a {
		min-width: 74px;
		margin-right: 0;
		margin-bottom: 3px;
	}

	.tabs-left > .nav-tabs {
		float: left;
		margin-right: 19px;
		border-right: 1px solid #ddd;
	}

	.tabs-left > .nav-tabs > li > a {
		margin-right: -1px;
		-webkit-border-radius: 4px 0 0 4px;
		-moz-border-radius: 4px 0 0 4px;
		border-radius: 4px 0 0 4px;
	}

	.tabs-left > .nav-tabs > li > a:hover,
	.tabs-left > .nav-tabs > li > a:focus {
		border-color: #eeeeee #dddddd #eeeeee #eeeeee;
	}

	.tabs-left > .nav-tabs .active > a,
	.tabs-left > .nav-tabs .active > a:hover,
	.tabs-left > .nav-tabs .active > a:focus {
		border-color: #ddd transparent #ddd #ddd;
		*border-right-color: #ffffff;
	}

	.tabs-right > .nav-tabs {
		float: right;
		margin-left: 19px;
		border-left: 1px solid #ddd;
	}

	.tabs-right > .nav-tabs > li > a {
		margin-left: -1px;
		-webkit-border-radius: 0 4px 4px 0;
		-moz-border-radius: 0 4px 4px 0;
		border-radius: 0 4px 4px 0;
	}

	.tabs-right > .nav-tabs > li > a:hover,
	.tabs-right > .nav-tabs > li > a:focus {
		border-color: #eeeeee #eeeeee #eeeeee #dddddd;
	}

	.tabs-right > .nav-tabs .active > a,
	.tabs-right > .nav-tabs .active > a:hover,
	.tabs-right > .nav-tabs .active > a:focus {
		border-color: #ddd #ddd #ddd transparent;
		*border-left-color: #ffffff;
	}


</style>
<?php $this->load->view('supervisores/menu/menu_supervisores'); ?>
<div id="contenedorCampania">
	<div class="row">
		<div class="col-lg-12" id="view-new_campain">
			<div class="content">
				<div class="card">
					<div class="content">
						<div class="col-md-12">
							<?php $this->load->view('cronograma_campanias/partials/campaignDetails', ['proveedores' => $proveedores]); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="<?php echo base_url('assets/clock-timepicker/jquery-clock-timepicker.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/ddslick/jquery.ddslick.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/taginput/bootstrap-tagsinput.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/caret/jquery.caret.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo asset('assets/cronograma_campanias/new.js'); ?>"></script>

