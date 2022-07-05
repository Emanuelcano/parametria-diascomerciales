<body onload="vistaBeneficiarios()">
    <span class="hidden-xs">
        <?php
        $usuario = $this->session->userdata("username");
        $tipoUsuario = $this->session->userdata("tipo");
        ?>
    </span>

    <input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
    <input type="hidden" id="usuario_session" value="<?php echo $usuario ?>">
    <input type="hidden" id="tipo" value="<?php echo $tipoUsuario; ?>" >

    <div id="mostrarModal" class="modalAjuste" style="width:0%; height: 0%; margin-left: -15%; margin-top: 2%;">
        <div id="modalInfo"></div>
    </div>

    <div id="fadeMostrarModal" class="overlayAjuste" onclick = "limpiarAnalista();
            document.getElementById('mostrarModal').style.display = 'none';
            document.getElementById('fadeMostrarModal').style.display = 'none';
            $('#modalInfo').html('');
            $('input#gestionando').val('no');"></div>

    <div id="dashboard_principal" style="display: block; background: #FFFFFF;">
        <div class="col-md-12" align="center" id="divLiquidar" style="display: block; height: 100%;margin-top: 0%">
            <div class="box-header with-border" class="col-lg-12">
                <div class="col-lg-12" id="cuerpoCreditosBuscar" style="display: block">
                    <div class="box-body pull-left">
                        <a class="btn btn-app" onclick="vistaBeneficiarios();">
                            <span class="badge bg-purple" id="cantidad_beneficiario"><?php echo $data['cant_beneficiarios']; ?></span>
                            <i class="fa fa-users"></i> Beneficiarios
                        </a>
                    </div>
                    <div class="box-body pull-left">
                        <a class="btn btn-app" onclick="vistaGastos();">
                            <span class="badge bg-teal" id="cantidad_gasto"><?php echo $data['cant_gastos']; ?></span>
                            <i class="fa fa-users"></i> Gastos
                        </a>
                    </div>
                    <div class="box-body pull-left"> 
                        <a class="btn btn-app" onclick="administrarGastos();">
                            <span class="badge bg-teal" id="cantidad_gastos_pendientes"><?php echo $data['cantidad_gastos_pendientes']; ?></span>
                            <i class="fa fa-users"></i> Aprobar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="dashboard_principal">
        <section class="content">

            <div class="col-lg-12" id="main" style="display: block">

            </div>

        </section>
    </div>
    <style>
        .vencido{
            background-color:#F7DBDB!important;
        }
        .vencido:hover {
            background-color:#F4C4C4!important;
        }
        .content{
            padding-top: 0px!important;
        }
        .box{
            margin-bottom: 7px!important;
        }
        .login-box-body, .register-box-body {
            font-size: 14px!important;
            line-height: 0.428571;
            color: #333;
        }
        .table>tbody>tr>td{
            padding-top: 0px!important;
        }
        table.dataTable tbody td {
            padding: 4px 10px;
        }
        
    </style>
</body>
<script src="<?php echo base_url('assets/moment/moment.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/operaciones/gastos.js'); ?>"></script>
<script src="<?php echo base_url('assets/operaciones/beneficiarios.js'); ?>"></script>
<script src="<?php echo base_url('assets/function.js'); ?>"></script>
<script src="<?php echo base_url('assets/operaciones/operaciones.js'); ?>"></script>
<script src="<?php echo base_url('assets/administracion/administracion.js'); ?>"></script>


