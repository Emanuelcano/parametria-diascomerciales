    <button type="button" class="btn btn-primary" style="width:100%; display: block;" onclick="showModal()">
        <i class="fa fa-cogs"> Configurar Tablero </i>
    </button>
    <div id="openModal" class="modalDialog">
        <div>
        <div class="modal-header">
            <h4 class="modal-title mb-4">Configurar tablero</h4>
        </div>
            <div class="container mb-4">
                <div class="row">
                    <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            <label for="">Descripción</label>
                            <div class="input-group">
                                <input name="descripcion" id="descripcion" type="text" required class="form-control" >
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            <label for="">Objetivo porcentaje</label>
                            <div class="input-group">
                                <input name="objetivo_porcentaje" id="objetivo_porcentaje" type="number" required class="form-control" >
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            <label for="">Estado</label>
                            <div class="input-group">
                                <select name="estado" id="estado" required class="form-control">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            <label for="">Tablero</label>
                            <div class="input-group">
                                <input name="tablero" id="tablero" type="number" required class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            <label for="">Condicion</label>
                            <div class="input-group">
                                <input name="condicion" id="condicion" type="number" required class="form-control" >
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            <label for="">Objetivos dependientes</label>
                            <div class="input-group">
                                <input name="objetivos_dependientes" id="objetivos_dependientes" type="number" required class="form-control" >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            <label for="">Objetivos independientes</label>
                            <div class="input-group">
                                <input name="objetivos_independientes" id="objetivos_independientes" type="number" required class="form-control" >
                            </div>
                        </div>
                    </div> 
                    <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            <label for="">Mora dependientes</label>
                            <div class="input-group">
                                <input name="mora_dependientes" id="mora_dependientes" type="number" required class="form-control" >
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            <label for="">Mora independientes</label>
                            <div class="input-group">
                                <input name="mora_independientes" id="mora_independientes" type="number" required class="form-control" >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            <label for="">Objetivos mora</label>
                            <div class="input-group">
                                <input name="objetivos_mora" id="objetivos_mora" type="number" required class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-3">
                        <div class="form-group">
                            <label for="">Fecha mora mostrar</label>
                            <div class="input-group">
                                <input name="fecha_mora_mostrar" id="fecha_mora_mostrar" type="date" required class="form-control" placeholder="0000-00-00" maxlength="10"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-3 mb-4">
                        <div class="form-group">
                            <label for="">Proximo Vencimiento</label>
                            <div class="input-group">
                                <input name="proximo_vencimiento" id="proximo_vencimiento" type="date" required class="form-control" placeholder="0000-00-00" maxlength="10"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-primary" id="btn_actualizar" onclick="actualizar()">Configurar</button>
            <button type="button" class="btn btn-danger" onclick="CloseModal()">Cerrar</button>

        </div>
    </div>
    <style>
    .modalDialog {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: rgba(0,0,0,0.8);
        z-index: 99999;
        display:none;
        pointer-events: auto;
    }
    .modalDialog > div {
        width: 869px;
        border-radius: 8px;
        position: relative;
        margin: 10% auto;
        padding: 5px 20px 13px 20px;
        background: #fff; 
    }
    .btn{
        font-size: 11px;
    }
    </style>

