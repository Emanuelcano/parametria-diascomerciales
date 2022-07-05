<style>
    .table>tbody>tr.info>td, .table>tbody>tr.info>th, .table>tbody>tr>td.info, .table>tbody>tr>th.info, .table>tfoot>tr.info>td, .table>tfoot>tr.info>th, .table>tfoot>tr>td.info, .table>tfoot>tr>th.info, .table>thead>tr.info>td, .table>thead>tr.info>th, .table>thead>tr>td.info, .table>thead>tr>th.info.cobrador{
        width : 100px !important;
    }
    .dt-buttons.dt-button.btn-general {
        background-color: green !important;
    }
    /**
     * temporal mientras busco otra forma de reducir la columna de cobrador
     */
</style>
<div id="dashboard_principal" style="display: block;">
    <?php 
        /**
        * Return view in anthoer view with all data
        */
        echo $this->load->view('tablero_cobranza/tbl_cobranza_general', ['data'=>$data], TRUE);
        echo $this->load->view('tablero_cobranza/tbl_cobranza_actual', ['data'=>$data], TRUE);
        echo $this->load->view('tablero_cobranza/tbl_cobranza_anterior', ['data'=>$data], TRUE);
    ?>
</div>
