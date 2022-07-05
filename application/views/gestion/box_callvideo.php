<style>
	#box_callvideo {
		position: fixed;
		z-index: 9;
		background-color: #f1f1f1;
		text-align: center;
		border: 1px solid #d3d3d3;
		top: 160px;
		left: 120px;
		width: 33%;
		border-top-color: #00f;

	}


	#box_callvideo div.box-header button {
		padding: 0px;
	}

	#box_callvideo div.box-header button:hover {
		box-shadow: 0px 9px 10px -9px #333;
	}

	#box_callvideo div.box-body {
		background-color: #fff;
		height: 600px
	}

	.responsive-iframe {
		top: 0;
		left: 0;
		bottom: 0;
		right: 0;
		width: 100%;
		height: 100%;
	}

</style>
<div id="box_callvideo" class="box box-info hidden">
	<div class="box-header with-border" id="box_callvideo_header">
		<div class="row">
			<div class="col-md-12">
				<div class="col-md-6" style="text-align: left;">
					<span >Video llamadas</span>
				</div>
				<div id="callvideo_buttons" class="btn-group col-md-2 col-md-offset-4">
					<button id="btn-callvideo-minimize" class="col-md-12 btn btn-sm"><i class="fa fa-window-minimize"></i></button>
					<button id="btn-callvideo-restore" class="col-md-12 btn btn-sm hidden"><i class="fa fa-window-restore"></i></button>
				</div>
				
			</div>
		</div>
	</div>
	<div class="box-body">
		<div class="container-fluid">
			<div id="root-iframe" class="row"></div>
		</div>
	</div>
	<div class="box-footer">
		<!-- Botones?? -->
	</div>
</div>


<script type="text/javascript" src="<?php echo base_url('assets/gestion/box_callvideo.js');?>"></script>
