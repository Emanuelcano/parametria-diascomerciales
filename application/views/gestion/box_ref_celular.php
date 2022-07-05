<div class="box box-info">
    <div class="box-header with-border" id="titulo">
        <h6 class="box-title"><small><strong>Celulares</strong></small></h6>
    </div>
    <table class="table table-striped table=hover display" width="100%">
        <tbody>
            <?php for ($i = 0; $i < count($celulares_cliente); $i++) {?>
            <tr>
                <td class="analisis_col_izq" width="30%" style="text-align: right;">#<?php echo $i + 1?></td>
                <td class="analisis_col_der">
                    <strong><?php echo isset($celulares_cliente[$i]['celular'])?$celulares_cliente[$i]['celular']:'' ?></strong>
                </td>
            </tr>
            <?php 
            }
            ?>
        </tbody>
    </table>
</div>