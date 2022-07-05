<?php if ($data['direccion']=='formEgresos') {?>
   

            
              <table data-page-length='15' align="center" id="tp_Indicadores" class="table table-striped table=hover display" width="100%">
                <thead>
                	<tr class="info">
		              	<th style="height: 5px; padding: 0px; width: 15%" rowspan="2">
                            <h5 align="center"><small><strong>Documento</strong></small></h5>
                        </th>
                        <th style="height: 5px; padding: 0px; width: 15%" rowspan="2">
		                	<h5 align="center"><small><strong>Cliente</strong></small></h5>
		              	</th>
		              	<!--th style="height: 5px; padding: 0px;" colspan="5">
		                	<h5 align="center"><small><strong>MES ANTERIOR</strong></small></h5>
		              	</th-->
		              	<th style="height: 5px; padding: 0px; background: #C4D4E7;" colspan="10">
		                	<h5 align="center"><small><strong>SITUACION CREDITICIA</strong></small></h5>
		              	</th>
		              	
		            </tr>
                  	<tr class="info">
	                    
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Monto</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Fecha</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Banco</small></th>
	                    
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;text-align: center;"><small>Ruta Archivo</small></th>
	                    
	                    
	                    

	                    
                  	</tr>
                </thead>
                <tbody>

                <?php foreach ($data['indicadores'] as $credito): 
                    if ($credito->documento!='') { ?>
                        
                        <tr>
                        <td style="padding: 0px; font-size: 16px; vertical-align: left; height: 35px;"><?= $credito->tipo_doc. " " .$credito->documento ?></td>
                        <td style="padding: 0px; font-size: 16px; vertical-align: middle; height: 35px;"> <?= $credito->nombres." ".$credito->apellidos ?></td>
                        
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #F9E79F;"><?= number_format($credito->MONTO, 2, ',', '.')  ?></td>

                         
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #F9E79F;"><?= $credito->fecha_carga ?></td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #EAEDED;"><?= $credito->Nombre_Banco ?></td>

                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #F9E79F;"><?= $credito->ruta_txt ?></td>
                        

                        
                    </tr>


                    <?php }else{ ?>

                    <tr>
                        <td  colspan="5" style="padding: 0px; font-size: 16px; vertical-align: left; height: 35px;"> No se encontraron resultados en las fechas indicadas...</td>
                    </tr>

                    
                    
                    <?php } ?>
                <?php endforeach; ?>
   
                </tbody>
              </table>

<?php }else{ ?>

            <table data-page-length='15' align="center" id="tp_Indicadores" class="table table-striped table=hover display" width="100%">
                <thead>
                    <tr class="info">
                        <th style="height: 5px; padding: 0px; width: 15%" rowspan="2">
                            <h5 align="center"><small><strong>Documento</strong></small></h5>
                        </th>
                        <th style="height: 5px; padding: 0px; width: 15%" rowspan="2">
                            <h5 align="center"><small><strong>Cliente</strong></small></h5>
                        </th>
                        <!--th style="height: 5px; padding: 0px;" colspan="5">
                            <h5 align="center"><small><strong>MES ANTERIOR</strong></small></h5>
                        </th-->
                        <th style="height: 5px; padding: 0px; background: #C4D4E7;" colspan="10">
                            <h5 align="center"><small><strong>INGRESO</strong></small></h5>
                        </th>
                        
                    </tr>
                    <tr class="info">
                        
                        <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Monto</small></th>
                        <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Fecha</small></th>
                        
                        
                        <th style="width: 4%; padding: 0px; padding-left: 10px;text-align: center;"><small>Estado</small></th>
                        
                        
                        

                        
                    </tr>
                </thead>
                <tbody>

                <?php foreach ($data['indicadores'] as $ingreso): 

                    if ($ingreso->documento!='') { ?>

                        <tr>
                        <td style="padding: 0px; font-size: 16px; vertical-align: left; height: 35px;"> <?= $ingreso->tipo_doc. " " .$ingreso->documento ?></td>
                        <td style="padding: 0px; font-size: 16px; vertical-align: middle; height: 35px;"> <?= $ingreso->nombres." ".$ingreso->apellidos ?></td>
                        
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #F9E79F;"><?= number_format($ingreso->monto_cobrado, 2, ',', '.')  ?></td>

                         
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #F9E79F;"><?= $ingreso->fecha_cobro ?></td>
                        

                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #F9E79F;"><?= $ingreso->estado ?></td>
                        

                        
                    </tr>


                        

                    <?php }else{ ?>

                        <tr>
                        <td colspan="5" style="padding: 0px; font-size: 16px; vertical-align: left; height: 35px;"> No se encontraron resultados en las fechas indicadas...</td>
                        </tr>
                    
                    <?php } ?>
                <?php endforeach; ?>
   
                </tbody>
              </table>
<?php } ?>