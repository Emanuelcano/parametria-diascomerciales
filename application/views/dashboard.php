<div class="box-header with-border" class="col-lg-12">
        <div class="col-md-12">
            &nbsp;
        </div>
</div>
<section class="content">
    <div class="card mt-3">
        <br>
        <div class="card-body">
            <div class="row">
                <?php
                foreach ($modulos as $modulo):
                    $link = $modulo->url ? base_url() . $modulo->url : 'javascript:void(0)';
                    ?>
                    <div class="col-12 col-sm-3 col-md-2">
                        <div class="small-box bg-blue">
                            <div class="inner">
                                <h3>&nbsp;</h3>
                                <p></p><h4><?php echo $modulo->nombre; ?></h4><p></p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-ios-paper-outline"></i>
                            </div>
                            <div class="small-box-footer">
                                <a href="<?php echo $link; ?>" target="_self" class="small-box-link">Ir <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
</section>
