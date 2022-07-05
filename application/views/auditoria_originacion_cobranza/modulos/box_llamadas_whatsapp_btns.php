<input id="canal" type="text" class="" style="display:none">
<div class="row" id="btnsAuditoria" style="margin-top:1%">
    <div class="col"  style="margin-left:1.4%">
        <button 
            id="auditoriaLlamadas" 
            class="col-xs-12 btn btn-xs bg-light" 
            title="Auditoria llamadas" 
            style="padding:0!important;width:190px;height:30px;background-color: #dfdfdf;"
            <?php if (isset($data)) { ?>
                disabled
            <?php } ?>
            onclick="cargarTable()">
                <i class="fa fa-phone"></i>    
            Auditoria llamadas
        </button>
    </div>

    <div class="col" style="margin-left:12.4%">
        <button 
            id="auditoriaWhatApp" 
            class="col-xs-12 btn btn-xs bg-light" 
            title="Auditoria llamadas" 
            <?php if (isset($data)) { ?>
                disabled
            <?php } ?>
            style="padding:0!important;width:190px;height:30px;background-color: #dfdfdf;">
                <i class="fa fa-whatsapp"></i>    
            Auditoria WhatApp
        </button>
    </div>

</div>