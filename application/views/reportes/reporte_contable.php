    <div id="pestanas_contable" class="btn-group row col-md-12">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#contable_gastos" data-toggle="tab">Gastos</a></li>
            <li><a href="#contable_cobros" data-toggle="tab">Cobros</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="contable_gastos">
                <?php $this->load->view('reportes/reporte_gastos'); ?>
            </div>

            <div class="tab-pane" id="contable_cobros">
                <?php $this->load->view('reportes/reporte_cobros'); ?>
            </div>
        </div>
    </div>

