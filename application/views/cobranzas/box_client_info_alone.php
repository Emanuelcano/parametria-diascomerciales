<style>
.cont {
    max-height: 300px;
    overflow-y: auto;
    overflow-x: hidden;
}
.cont::-webkit-scrollbar {
    width:5px;
}
.cont::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
    border-radius:10px;  
}
.cont::-webkit-scrollbar-thumb {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.5);
    border-radius:10px;
}
div.box-body {
    font-size: 14px!important;
}
input[type="date" i]::-webkit-calendar-picker-indicator {
    margin-inline-start: 0px
}
.input-xs {
  height: 22px;
  padding: 2px 5px;
  font-size: 12px;
  line-height: 1.5; 
  border-radius: 3px;
}
@media (max-width: 1000px) {
  table {
      font-size: 10px;
  }
}
</style>
<?php 
    /*var_dump('<pre>');
    var_dump($creditos);
    var_dump('</pre>');*/
    $credito_valido =FALSE;
    foreach ($creditos as $key => $value) {
        if($value['estado_credito'] != 'anulado' && $value['estado_credito'] != 'cancelado')
            $credito_valido =TRUE;
    }
?>

<div id="box_client_data" class="box box-info">

        <input id="client" type="hidden"
                data-id_cliente="<?php echo $solicitude["id_cliente"] ?>"
    			data-name= "<?php echo $solicitude['nombres']. " " . $solicitude['apellidos']; ?>"
    			data-email = "<?php echo $solicitude['email']; ?>"
    			data-mobilephone = "<?php echo $solicitude['telefono']; ?>"
    			data-number_doc = "<?php echo $solicitude['documento']; ?>"
        >
        <?php if($this->session->userdata['tipo_operador'] != ID_OPERADOR_EXTERNO ) { ?>
            <input id="epayco" type="hidden"
                    data-test= "<?php echo TEST_E_PAYCO;?>"
                    data-confirmation = "<?php echo URL_CONFIRMATION;?>"
                    data-token = "<?php echo TOKEN_E_PAYCO_CASH;?>"
                    data-response = "<?php echo base_url('atencion_cobranzas/cobranzas');?>"
            >
        <?php } ?>
        <input id="cuotaAntigua" type="hidden"
    			data-id_cuota= "<?php echo $cuota_mas_antigua[0]['id']?>"
        >
        
    <div class="box-header with-border" id="titulo">
        <?php if($proximo_monto > 0){ ?>  
                <p>DISPONIBLE PROXIMA RENOVACION: $<b><?= number_format($proximo_monto, '2', ',', '.') ?></b>
                    <span class='hd_monto box-tools pull-right'>
                        <?php 
                                echo '<a class="btn btn-xs btn-warning" id="update-niveles"  style ="float:left" data-id-cliente="'. $solicitude["id_cliente"].'"> <i class="fa fa-refresh"></i></a>';
                            
                        ?>
                        <?="$ ".number_format($proximo_monto, 0, ',', '.')?>
                    </span>
                </p>   
        <?php } ?>
    </div><!-- end box-header -->
    <div class="box-body" style="font-size: 12px;">
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 text-center" style="background-color: #d8d5f9;box-shadow: 0px 9px 10px -9px #888888; z-index: 1;">
                    <h5> <b>DEUDA AL DIA <strong class="text-red"><?php echo isset($mora_al_dia[0])? '$'.number_format($mora_al_dia[0]['deuda'], 2, ',', '.'):'$0' ; ?></strong>
                    <?php if (isset($mora_al_dia[0]) && $credito_valido && $this->session->userdata['tipo_operador'] != ID_OPERADOR_EXTERNO){?>
                            <a href="#" data-action="cash" class="pay_it" data-credit="Deuda Total" data-quota="Pago "
                                data-amount="<?php echo isset($mora_al_dia[0])? $mora_al_dia[0]['deuda']:0 ?>" data-id_quota="<?php echo 'T-'.$solicitude["id_cliente"]; ?>" data-key="<?php echo TOKEN_E_PAYCO_CASH; ?>" data-test="<?php echo TEST_E_PAYCO;?>" >
                                <img src="<?php echo base_url('assets/images/money.png')?>" style="width: 30px;float: right;">
                            </a>
                    <?php } ?>
                    </b></h5>
                    
                </div>
                <div class="col-md-12">
                    <table id="tabla-datos-mora" class="table text-center table-bordered">
                        <thead>
                            <th>DIAS</th>
                            <th>CUOTAS</th>
                            <th>CREDITOS</th>
                            <?php if($this->session->userdata['tipo_operador'] == 13 || $this->session->userdata['tipo_operador'] == 9){
                                foreach ($creditos as $key => $credito): 
                                    if (($credito['estado']!='cancelado' &&  $credito['estado']!='pagado') && ($credito['estado_credito']=='vigente' || $credito['estado_credito']=='mora') && $this->session->userdata['tipo_operador'] != ID_OPERADOR_EXTERNO){ ?>
                            <th>CERTIFICADO DEUDA</th>
                            <?php break; } endforeach; }?>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-red"><strong><?php echo isset($mora_al_dia[0])? $mora_al_dia[0]['dias_atraso']:'0' ; ?></strong></td>
                                <td class="text-red"><strong><?php echo isset($mora_al_dia[0])? $mora_al_dia[0]['cuotas']:'0' ; ?></strong></td>
                                <td class="text-red"><strong><?php echo isset($mora_al_dia[0])? $mora_al_dia[0]['creditos']:'0' ; ?></strong></td>
                                <td>
                                    <?php if($this->session->userdata['tipo_operador'] == 13 || $this->session->userdata['tipo_operador'] == 9){
                                        foreach ($creditos as $key => $credito): ?>
                                        <?php if (($credito['estado']!='cancelado' &&  $credito['estado']!='pagado') && ($credito['estado_credito']=='vigente' || $credito['estado_credito']=='mora') && $this->session->userdata['tipo_operador'] != ID_OPERADOR_EXTERNO){?>
                                        <button type="button" onclick="enviarCertificado(<?php echo  $credito['id_cliente']; ?>, <?php echo $tipo=1 ?>)" class="certificado btn btn-primary btn-sm"><i class="fa fa-envelope" aria-hidden="true"></i></button>
                                        <button type="button" onclick="certificadoDeuda(<?php echo  $credito['id_cliente']; ?>, <?php echo $tipo=2 ?>)" class="certificado btn btn-warning btn-sm"><i class="fa fa-download" aria-hidden="true"></i></button>
                                    <?php break; } endforeach; }?>
                                </td>
                                
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
            <div class="col-md-12" style="background-color: #d8d5f9; box-shadow: 5px 9px 10px -9px #888888; z-index: 1;">
                    <div class="col-md-4 text-center" >
                        <h5> <b>CREDITOS</b>: <span style="font-size:14px"> <?=isset($cantidad_creditos[0]['COUNT(id)'])?$cantidad_creditos[0]['COUNT(id)']:''; ?></span></h5>
                    </div>
                    <div class="col-md-8 text-center" >
                        <h5> <b>ATRASOS: </b>
                           <span style="font-size:14px"> <?php if(isset($atrasos)):
                                                for ($i = 0; $i < count($atrasos); $i++) {
                                                    if ($i != 0) echo "-";
                                                    echo ($atrasos[$i]['dias_atraso']);

                                                
                                                }
                                            endif;
                                            ?></span>
                                            </h5>
                    </div>
                </div>
                <div class="col-md-12 cont">
                    <table id="tabla-datos-mora" class="table text-center table-bordered">
                        <thead>
                            <th></th>
                            <th>CREDITO</th>
                            <th>OTORGADO</th>
                            <th>VENCIMIENTO</th>
                            <th>A PAGAR</th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </thead>
                        <tbody>
                            <?php 
                            $total_creditos = count($creditos);
                            foreach ($creditos as $key => $credito):  ?>
                                
                                <tr style=" <?php echo ($credito['estado']=='pagado')? 'background:#e0e3e8':'' ?>">
                                    <td class="">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" data-id_credito="<?php echo $credito['id_credito']?>" data-id_cuota="<?php  echo $credito['id']?>" name ="ch-creditos" <?php echo ($credito['estado']=='pagado')? 'disabled':'checked' ?> onChange = "calcularMora()">
                                            <label class="form-check-label" for="ch-cred-<?php echo $credito['id_credito']?>"></label>
                                        </div>
                                    </td>
                                    <td ><?php echo $credito['id_credito'].'-'.$credito['numero_cuota']?></td>
                                    <td ><?php echo date_format(date_create($credito['fecha_otorgamiento']),"d-m-Y")?></td>
                                    <td >
                                        <?php 
                                            echo date_format(date_create($credito['fecha_vencimiento']),"d-m-Y");
                                            $fecha_vencimiento = new DateTime($credito['fecha_vencimiento']);
                                            $fecha_nueva = new DateTime($credito['fecha_vencimiento']);
                                            if (count($creditos_cambios) == 0) {
                                                if($credito['plazo'] == 1 && $credito['estado'] != 'pagado'){
                                                    $operador = $this->session->userdata['tipo_operador'];
                                                    if ($operador == 2 || $operador == 4 || $operador == 9 || $operador == 13) {
                                                        if ($credito['dias_atraso'] < 15) {
                                                            if ($operador == 2 || $operador == 4) {
                                                                if ($total_creditos <= 2 )
                                                                    $fecha_nueva->add(new DateInterval('P15D')); 
                                                                if ($total_creditos == 3 )
                                                                    $fecha_nueva->add(new DateInterval('P20D')); 
                                                                if ($total_creditos == 4 )
                                                                    $fecha_nueva->add(new DateInterval('P25D')); 
                                                                if ($total_creditos >= 5 )
                                                                    $fecha_nueva->add(new DateInterval('P30D'));
                                                            }
                                                            if ($operador == 9 || $operador == 13)
                                                                $fecha_nueva->add(new DateInterval('P30D'));
                                                            
                                                            echo '<div class="col-md-12" style="padding: 0px 0px">';
                                                            echo '<div class="col-md-8" style="padding: 0px 0px"><input class="input-xs" type="date" name="fecha_v" id="inpfecha_v" value="'.$fecha_nueva->format('Y-m-d').'" min="'.$fecha_vencimiento->format('Y-m-d').'" max="'.$fecha_nueva->format('Y-m-d').'"></div>'; 
                                                            echo '<div class="col-md-4" style="padding: 0px 0px"><button class="btn btn-success btn-xs" type="button" name="fecha_v" data-fecha_A="'.$fecha_vencimiento->format('Y-m-d').'" onclick="actualizar_fecha_vencimiento(\''.$credito['id_credito'].'\', this)" id="btnfecha_v">Actualizar</button></div>'; 
                                                            echo '</div>';
                                                        }
                                                    }
                                                }
                                            }
                                            ?>
                                    </td>
                                    <td ><?php echo ($credito['estado']=='pagado')? '$0<br><label style="color: #00ce7c;">PAGADO</label>':'$'.number_format($credito['monto_cobrar'], 2, ',', '.') ?> <?php echo ($credito['estado']=='mora')? '<br><label style="color: red;">MORA</label>':'' ?> <?php echo ($credito['estado']!='mora' && $credito['estado']!='pagado')? '<br><label style="color: orange;">PENDIENTE</label>':'' ?></td>
                                    <td class=""><a onclick="envioConsultarCredito(<?php echo $credito['id_credito']?>)"><i class="fa fa-eye text-blue"></i></a></td>
                                    <td class=""> 
                                        <?php if (($credito['estado']!='cancelado' &&  $credito['estado']!='pagado') && ($credito['estado_credito']=='vigente' || $credito['estado_credito']=='mora') && $this->session->userdata['tipo_operador'] != ID_OPERADOR_EXTERNO){?>
                                            <a href="#" data-action="cash" class="pay_it" data-credit="Crédito <?php echo $credito['id_credito']; ?>" data-quota="Cuota <?php echo $credito['numero_cuota']; ?>"
                                                data-amount="<?php echo $credito['monto_cobrar'] ?>" data-id_quota="<?php echo 'C-'.$credito['id']; ?>" data-key="<?php echo TOKEN_E_PAYCO_CASH; ?>" data-test="<?php echo TEST_E_PAYCO;?>" >
                                                <img src="<?php echo base_url('assets/images/money.png')?>" style="width:35%;">
                                            </a>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if($credito['estado_credito']=='cancelado'){?>
                                            <button id="enviar_salvo" class="btn_viejos btn btn-success btn-sm" style="color:white;" onclick="enviar_salvo(<?=$credito['id_credito'];?>)"><i class="fa fa-send"></i></button>
                                            <button id="imprimir_salvo" class="btn_viejos btn btn-info btn-sm" style="color:white;" onclick="descargar_salvo(<?=$credito['id_credito'];?>)"><i class="fa fa-download"></i></button>
                                        <?php } else {
                                            ?>
                                            <script> $(function() { $('input#credito').data("status", "<?= $credito['estado_credito'] ?>");})</script>
                                            <?php
                                        }?>

                                        <!-- envio acuerdo de pagos -->
                                        <?php  if( ($credito['estado_credito']=='vigente' 
                                                    || $credito['estado_credito']=='mora')  
                                                    && ($status_chat != NULL) ){?>
                                            <button id="" class="btn btn-success btn-sm linkPago" style="color:white;"
                                            data-id_cliente="<?php echo $solicitude["id_cliente"] ?>"
                                            data-mobilephone = "<?php echo $solicitude['telefono']; ?>"
                                            data-medio-pago="efectivo"
                                            data-canal-chat="<?php echo $canal_chat?>" 
                                            onclick="envioLinkDePago(this)"><i class="fa fa-money"></i></button>

                                            <button id="" class="btn btn-info btn-sm" style="color:white;" 
                                            data-id_cliente="<?php echo $solicitude["id_cliente"] ?>"
                                            data-mobilephone = "<?php echo $solicitude['telefono']; ?>"
                                            data-medio-pago="PSE" 
                                            data-canal-chat="<?php echo $canal_chat?>"
                                            onclick="envioLinkDePago(this)"><i class="fa fa-university"></i></button>
                                        <?php }?> 
                                    </td>
                                </tr>
                            <?php endforeach;?>
                            
                        </tbody>
                    </table>
                </div>
               
            </div>

            <div class="row">
                <div class="col-md-12 text-center" style="background-color: #d8d5f9; box-shadow: 0px 9px 10px -9px #888888; z-index: 1;">
                    <h5><b>ACUERDOS DE PAGO</b></h5>
                </div>
                <div class="col-md-12 cont" style=" max-height: 200px;overflow: auto;">

                    <table id="tabla-promesa" class="table text-center table-bordered" style="margin:0px;">
                        <thead>
                            <th>PAGA EL</th>
                            <th>MONTO</th>
                            <th>MEDIO</th>
                            <th>CREADO</th>
                            <th>ESTADO</th>
                            <th></th>
                            <th></th>
                        </thead>
                        <tbody>
                            <?php 
                                $cumplidos = 0;
                                $incumplidos = 0;
                                foreach ($acuerdos_pago as $key => $acuerdo): 
                            ?>
                                <tr>
                                    <td class=""><?php echo date_format(date_create($acuerdo['fecha']),"d-m-Y")?></td>
                                    <td class=""><?php echo '$'.number_format($acuerdo['monto'], 2, ',', '.')?></td>
                                    <td class=""><?php echo $acuerdo['medio']?></td>
                                    <td class=""><?php echo date_format(date_create($acuerdo['fecha_hora']),"d-m-Y")?></td>
                                    <td class="">
                                        <?php switch ($acuerdo['estado']) {
                                            case 'pendiente':
                                                echo '<i class="fa fa-spinner text-orange"></i>';
                                                break;

                                            case 'cumplido':
                                                $cumplidos ++;
                                                echo '<i class="fa fa-thumbs-up text-green"></i>';
                                                break;

                                            case 'incumplido':
                                                $incumplidos ++;
                                                echo '<i class="fa fa-thumbs-down text-red"></i>';
                                                break;

                                            case 'anulado':
                                                echo '<i class="fa fa-ban text-red"></i>';
                                                break;

                                            default:
                                                
                                                break;
                                        }?>
                                    </td>
                                    <td class="">
                                        <a onclick="envioConsultarPromesasDetalle(<?php echo $acuerdo['id']?>)"><i class="fa fa-eye text-blue"></i></a>
                                        <?php if ($this->session->userdata['tipo_operador'] != ID_OPERADOR_EXTERNO && $acuerdo['editable']){?>
                                            <a style="margin-left: 5px;" onclick="ajustarPlanDescuento(<?php echo $acuerdo['id']?>)"><i class="fa fa-edit text-blue"></i></a>
                                        <?php } ?>
                                    </td>
                                    <td class="">
                                        <?php if ($acuerdo['estado']=='pendiente' && $this->session->userdata['tipo_operador'] != ID_OPERADOR_EXTERNO){?>
                                            <a href="#" data-action="cash" class="pay_it" data-credit="Acuerdo <?php echo $acuerdo['id']; ?>" data-quota="Pago"
                                                data-amount="<?php echo $acuerdo['monto'] ?>" data-id_quota="<?php echo 'A-'.$acuerdo['id']; ?>" data-key="<?php echo TOKEN_E_PAYCO_CASH; ?>" data-test="<?php echo TEST_E_PAYCO;?>" >
                                                <img src="<?php echo base_url('assets/images/money.png')?>" style="width:35%;">
                                            </a>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php  if( ($acuerdo['estado']=='pendiente')  
                                                    && ($status_chat != NULL) ){?>
                                            <button id="" class="btn btn-success btn-sm linkPago" style="color:white;" 
                                            data-id_cliente="<?php echo $solicitude["id_cliente"] ?>"
                                            data-mobilephone = "<?php echo $solicitude['telefono']; ?>"
                                            data-medio-pago="efectivo"
                                            data-id-acuerdo="<?php echo $acuerdo['id']; ?>" 
                                            data-tipo-acuerdo="acuerdo" 
                                            data-canal-chat="<?php echo $canal_chat?>"
                                            onclick="envioLinkDePago(this)">
                                            <i class="fa fa-money"></i></button>

                                            <button id="" class="btn btn-info btn-sm linkPago" style="color:white;"
                                            data-id_cliente="<?php echo $solicitude["id_cliente"] ?>" 
                                            data-mobilephone = "<?php echo $solicitude['telefono']; ?>"
                                            data-medio-pago="PSE" 
                                            data-id-acuerdo="<?php echo $acuerdo['id']; ?>" 
                                            data-tipo-acuerdo="acuerdo"
                                            data-canal-chat="<?php echo $canal_chat?>"
                                            onclick="envioLinkDePago(this)">
                                            <i class="fa fa-university"></i></button>
                                        <?php }?> 
                                    </td>
                                </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>

                </div>
                
                <div class="col-md-6"><br><h5><i class="fa fa-thumbs-up text-green"></i> Cumplidos: <strong id="cumplidos"><?php echo $cumplidos?></strong></h5></div>
                <div class="col-md-6"><br><h5><i class="fa fa-thumbs-down text-red"></i> Incumplidos: <strong id="incumplidos"><?php echo $incumplidos?></strong></h5></div>

                <div class="col-md-12 descuento-campania"></div>
                
                <?php if($credito_valido) {?>
                    <br>
                    <?php if( count($planes_descuentos) > 0 && isset($mora_al_dia[0])){ ?>
                            <div class="col-md-12" style="margin-bottom:5px;">
                                
                                <div class="col-md-6">
                                    <label for="plan_descuento">Planes de descuento: </label>
                                    <select name="plan_descuento" class="form-control" id="plan_descuento" onchange="aplicarDescuento()">
                                        <option value="">Sin descuento</option>
                                        <?php foreach ($planes_descuentos as $key => $value) {  ?>
                                            <option value="<?= $value["id"] ?>" data-porcentaje="<?= $value["porcentaje"]?>" data-campos="<?= $value["aplica_sobre"] ?>"><?= $value["descripcion"] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="monto">Monto del descuento: </label>
                                    <input type="text" class="form-control" id="monto_descuento" readOnly value="0">
                                </div>
                                <div class="col-md-12" style=" padding-top: 5px; color: darkorange;">
                                    <span id="old_monto" style="display: none;">Monto del acuerdo antes del descuento:$ <strong></strong></span>
                                </div>
                            </div>
                       
                    <?php } ?>

                    <div class="col-md-12">
                    
                        <div class="col-md-6">
                            <label for="fechaAcuerdo">Fecha: </label>
                            <input type="text" class="form-control" id="fechaAcuerdo">
                        </div>
                        <div class="col-md-6">
                            <label for="monto">Monto del acuerdo: </label>
                            <input type="text" onClick="this.select();" min="0" value="0" class="form-control" id="monto" data-monto_original="" data-creditos="">
                        </div>
                        <div class="col-md-6">
                            <label for="medios">Medio: </label>
                            <select name="medios" id="medios" class="form-control">
                                <option value="efecty">Efecty</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="deposito">Depósito</option>
                                <option value="pse">PSE</option>
                                <option value="ePayco">ePayco</option>
                                <option value="baloto">Baloto</option>
                                <option value="corresponsal">Corresponsal Bancario</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="planes_pago">planes de pago: </label>
                            <select name="planes_pago" id="planes_pago" data-id_cliente="<?php echo $credito['id_cliente'];?>" class="form-control" onchange="cargarPlan(this)">
                                <option value="">Sin plan</option>
                                <?php foreach ($planes_pago as $key => $value) {  ?>
                                    <option value="<?= $value["id"] ?>"><?= $value["descripcion"] ?></option>
                                <?php } ?>
                                
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12 detalle-plan">
                        
                    </div>
                    
                    <div class="col-md-6 col-md-offset-3">
                            <a class="btn btn-primary" id="promesa-pago" style="margin-top: 25px; width: 100%" onclick="generarPromesa2(<?php echo $credito['id_cliente'];?>)"><i class="fa fa-edit"></i> ACORDAR PAGO</a>
                    </div>
                <?php }?>
                
            </div>
        </div>
    </div> <!-- end box-body -->

</div>

<!-- Modal detalle
<div class="modal fade" id="credito-detalle" tabindex="-1" role="dialog" aria-labelledby="creditoLabel">
  <div class="modal-dialog" role="document" style="width:100%">
    <div class="modal-content">
      <div class="modal-header text-center">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="creditoLabel"></label></h3>
      </div>
      <div class="modal-body">
        <div class="row" style="margin:0px;">
            <div class="col-md-12 text-center" style="background-color: #d8d5f9; box-shadow: 0px 9px 10px -9px #888888; z-index: 1;"><h4 id="titulo-1"></h4></div>
                <div class="col-sm-12">
                    <table class="table text-center" id="detalle-credito-modal">
                        <thead>
                        
                            
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="col-md-12 text-center" style="background-color: #d8d5f9; box-shadow: 0px 9px 10px -9px #888888; z-index: 1;"><h4 id="titulo-2"></h4></div>
                <div class="col-sm-12 ajuste-descuento" style="display: block;">
                                    <br>
                                    
                                    <div class="col-md-4">
                                        <label for="plan_descuent_ajusteo">Planes de descuentox: </label>
                                        <select name="plan_descuento_ajuste" class="form-control" id="plan_descuento_ajuste">
                                            
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="monto_descuento_ajuste">Monto del descuento: </label>
                                        <input type="text" class="form-control" id="monto_descuento_ajuste" readOnly value="0">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="monto_descuento_ajuste">Monto del acuerdo: </label>
                                        <input type="text" class="form-control" id="monto_acuerdo_ajuste" readOnly value="0">
                                    </div>
                                    <div class="col-md-12" style=" padding-top: 5px; color: darkorange;">
                                        <span id="old_monto_ajuste" style="display: none;">Monto del acuerdo antes del descuento:$ <strong></strong></span>
                                    </div>
                                    <div class="col-md-12 text-center" >
                                        <a class="btn btn-primary btn-lg">Guardar Cambios</a>
                                    </div>


                </div>
                <div class="col-sm-12">
                    <table class="table text-center" id="detalle-cuota-modal">
                        <thead>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div> -->
<script type="text/javascript" src="https://checkout.epayco.co/checkout.js"></script>
<script type="text/javascript">

    $(function () {
       
        let startdate = moment().format('YYYY-MM-DD');
        let day = parseInt(moment().format('DD'));

        //se suman 4 dias de cualquier fecha
        let maxdate = moment(startdate, "YYYY-MM-DD").add(3, 'days');
        $('#fechaAcuerdo').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            locale: {"format": "YYYY-MM-DD"},
            minYear: parseInt(moment().format('YYYY'),10),
            maxYear: parseInt(moment().format('YYYY'),10),
            minDate: moment().format('YYYY-MM-DD'),
            maxDate: maxdate 
        });
        calcularMora();
        campaniaDescuento();
        $('#fechaAcuerdo').on('apply.daterangepicker', function(ev, picker) {
            calcularMora();
            campaniaDescuento();
        });

        $(".pay_it").on("click", function(event)
        {
            event.preventDefault();
            let test = $(this).data("test");
            let key = $(this).data("key");
            let client = $("#box_client_data #client");
            let epayco = $("#box_client_data #epayco");
            var handler = ePayco.checkout.configure({
                key: key,
                test: test
            });
            /**
             * caso cuota: C-<id cuota>-<random> o <id cuota>-<random>
             * caso acuerdo: A-<id cliente>-<random> | FIXME
             * caso plan de pago: P-<id cliente>-<random> | TODO
             */
            var data={
                //Parametros compra (obligatorio)
                name: $(this).data("quota") +" - " +$(this).data("credit"),
                description: $(this).data("quota")+" - "+ $(this).data("credit"),
                invoice: $(this).data("id_quota")+"-"+ new Date().getTime(),
                currency: "cop",
                amount: Math.ceil($(this).data("amount")),
                tax_base: "0",
                tax: "0",
                country: "co",
                lang: "es",
                //Onpage="false" - Standard="true"
                external: "false",
                //Atributos opcionales
                extra1: "extra1",
                extra2: "extra2",
                extra3: "extra3",
                confirmation: epayco.data("confirmation"),
                response: epayco.data("response"),
                //Atributos cliente
                name_billing: client.data("name"),
                type_doc_billing: "cc",
                mobilephone_billing: client.data("mobilephone"),
                number_doc_billing: client.data("number_doc"),
                email_billing: client.data("email"),
                }
                handler.open(data);
        });


        $("#monto").on({
            "focus": function(event) {
                $(event.target).select();
            },
            "keyup": function(event) {
                $(event.target).val(function(index, value) {
                return value.replace(/\D/g, "")
                    .replace(/([0-9])([0-9]{2})$/, '$1,$2')
                    .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
                });
            }
            
        });

    });

    function enviarCertificado(id_cliente, tipo) {
        Swal.fire({
        title: 'CERTIFICADO DE DEUDA',
        text: "¿Desea enviar el certificado?",
        type: 'warning',
        showCancelButton: true,
        showCancelButton: true,
        confirmButtonText: `Si`,
        cancelButtonText: `Cancelar`,
        }).then((result) => {
            if (result.value) {
                certificadoDeuda(id_cliente, tipo);
            } else {
                Swal.fire('Se ha cancelado el envio', '', 'info');
            }
        })
    }
    
    function certificadoDeuda(id_cliente, tipo) {
        $.ajax({
            type: "post",
            url: base_url + "atencion_cobranzas/Cobranzas/generar_certificado",
            data: {id_cliente, tipo},
            success: function (response) {
                let respuesta = JSON.parse(response);
                if (respuesta.opcion == 1) {
                    if (respuesta.status == 1) {
                        Swal.fire(respuesta.response, '', 'success');
                    }else{
                        Swal.fire(respuesta.response, '', 'warning');
                    }
                }else{ 
                    url = base_url + respuesta.ruta;
                    var a = document.createElement('a');
                    a.download = respuesta.nombre;
                    a.target = '_blank';
                    a.href= url;
                    a.click();
                }
            }
        });
    }

    

    function envioConsultarCredito(param)
    {
        var obj = {function:'consultarCredito', data:param};
        window.parent.postMessage(JSON.stringify(obj), '*');
    }
    function envioConsultarPromesasDetalle(param)
    {
        var obj2 = {function:'consultarPromesasDetalle', data:param};
        window.parent.postMessage(JSON.stringify(obj2), '*');
    }
   function generarPromesa2(id_cliente) {
    const formData = new FormData();
    var cuotas = [];
    var detalle_plan = [];

    formData.append("medio", $("#medios").val());
    formData.append("id_cliente", id_cliente);

    //si el acuerdo viene por plan de pago
    if ($("#planes_pago").val() != "" && $("#planes_pago").val() != null) {
        $('.detalle-plan .table tbody tr').each(function () {
            var cuota_plan = $(this).data('id_detalle_plan');
            $(this).find("td input").each(function () {


                if (($(this).hasClass("hidden") && $(this).data("campo") == 'monto') || $(this).data("campo") != 'monto') {
                    cuota_plan += '&' + this.value;
                }
            });
            detalle_plan.push(cuota_plan);

        });
        formData.append("tipo", 'plan');
        formData.append("plan_detalle", detalle_plan);
        formData.append("fecha_calculo", $(".detalle-plan input[data-campo=fecha]").last().val());


        Swal.fire({
            title: '¡Atención!',
            text: 'Se generará el acuerdo de pago por $' + $("#monto").val() + '. Valido hasta el ' + $(".detalle-plan input[data-campo=fecha]").last().val(),
            icon: 'warning',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            showCancelButton: 'true'
        }).then((result) => {
            if (result.value) {

                $.ajax({
                    url: base_url + 'api/credito/crear_promesa',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                }).done(function (response) {
                    Swal.fire({
                        title: "¡Perfecto!",
                        text: response.message,
                        icon: 'success'
                    });
                    consultarPromesas(id_cliente);

                    let id_operador = $("#id_operador").val();
                    let type_contact = 10;
                    let comment = "<b>[ACUERDO DE PAGO]</b>" +
                        "<br> <b>Plan :" + $(".detalle-plan h5").data("plan") + "</b>" +
                        "<br> Medio: " + $("#medios").val();
                    "<br> Monto proyectoado: " + $("#monto").val();
                    detalle = response.data;
                    detalle.forEach(element => {
                        comment += "<br> <b>Cuota " + element.cuota + " para el " + moment(element.fecha).format('DD-MM-YYYY') + "</b>" +
                            "<br> Porcentaje: " + element.porcentaje +
                            "  Monto acuerdo: $" + formatNumber(element.monto);
                    });
                    saveTrack2(comment, type_contact, id_solicitud, id_operador, ()=>{get_box_track_stand_alone(id_solicitud,id_credito)});
                })
                    .fail(function (response) {
                        //console.log("error");
                    })
                    .always(function (response) {
                        //console.log("complete");
                    });


            }
        });



    } else {

        $.each($("input[name='ch-creditos']:checked"), function () {
            cuotas.push($(this).data('id_cuota'));
        });

        if (cuotas.length > 0) {
            let monto = parseFloat($("#monto").val().split('.').join("").replace(',', '.'));
            if ($("#fechaAcuerdo").val() != "" && monto > 0 && $("#medio").val() != "") {
                let descuento = 0;
                let id_descuento = 0;
                if (typeof ($("#monto_descuento").val()) != 'undefined' && $("#plan_descuento").val() != '') {
                    descuento = $("#monto_descuento").val().split('.').join("").replace(',', '.');
                    id_descuento = $("#plan_descuento").val();
                }

                formData.append("fecha", $("#fechaAcuerdo").val());
                formData.append("cuotas", cuotas);
                formData.append("tipo", 'simple');
                formData.append("monto", monto);
                formData.append("monto_descuento", descuento);
                formData.append("id_plan", id_descuento);

                let comment = "<b>[ACUERDO DE PAGO]</b><br>Fecha: " + moment($("#fechaAcuerdo").val()).format('DD-MM-YYYY') + "<br> Medio: " + $("#medios").val() + "<br> Monto acuerdo: $" + formatNumber(monto);
                comment += (parseFloat(descuento) > 0) ? ("<br>Descuento: " + $("#plan_descuento option:selected").text()) : '';
                comment += (parseFloat(descuento) > 0) ? ("<br>Monto descuento: $" + formatNumber(descuento)) : '';
                comment += "<br>Monto proyectado: $" + formatNumber($("#monto").data('monto_original'));
                comment += "<br>";
                if ($("#ch-descuento-campania").is(':checked')) {
                    comment += "<b>Descuento de campaña aplicado</b>";
                    comment += "<br>";
                    comment += "Deuda Al Dia: $" + formatNumber($("#ch-descuento-campania").data('deuda'));
                    comment += "<br>";
                    comment += "Monto Descuento:$" + formatNumber($("#ch-descuento-campania").data('descuento'));
                    comment += "<br>";
                }

                Swal.fire({
                    title: '¡Atención!',
                    text: 'Se generará el acuerdo de pago por $' + $("#monto").val() + '. Valido hasta el ' + moment($("#fechaAcuerdo").val()).format('DD-MM-YYYY'),
                    icon: 'warning',
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar',
                    showCancelButton: 'true'
                }).then((result) => {
                    if (result.value) {

                        $.ajax({
                            url: base_url + 'api/credito/crear_promesa',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                        }).done(function (response) {
                            Swal.fire({
                                title: "¡Perfecto!",
                                text: response.message,
                                icon: 'success'
                            });
                            consultarPromesas(id_cliente);

                            let id_operador = $("#id_operador").val();
                            let type_contact = 10;
                            saveTrack2(comment, type_contact, id_solicitud, id_operador, ()=>{get_box_track_stand_alone(id_solicitud,id_credito)});
                            
                        })
                            .fail(function (response) {
                                //console.log("error");
                            })
                            .always(function (response) {
                                //console.log("complete");
                            });

                    }
                });

            } else {
                alert("los campos Fecha, Medio y Monto son obligatorios")
            }
        } else {
            alert("Debes seleccionar las cuotas para las cuales se generara la nueva promesa");
        }
    }
}

$("#update-niveles").on('click', function(event){
                let base_url = $("#base_url").val();

                $.ajax({
                    type: "GET",
                    url: base_url + 'api/solicitud/update_niveles/'+ $('#update-niveles').data('id-cliente'),
                })
                .done(function(response) {
                    if (response.ok) {
                        Swal.fire("", response.message, "success");
                    }else {
                        Swal.fire("", response.message, "error");
                    }
                })
                .fail(function(xhr) {
                    Swal.fire("", "El regisgtro no pudo ser actualizado", "error");
                    
                });
            
        });  
</script>