<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">
        <form action="" id="form_pesquisa" method="post">
            <input type="hidden" id="acao" name="acao" value="pesquisar" />

            <div class="campo data periodo">
                <div class="inicial">
                    <label for="data_inicial">Período</label>
                    <input id="data_inicial" name="data_inicial" maxlength="10" value="<?php echo $this->view->parametros->data_inicial;?>" class="campo" type="text" />
                </div>
                <div class="campo label-periodo">à</div>
                <div class="final">
                    <label for="data_final">&nbsp;</label>
                    <input id="data_final" name="data_final" maxlength="10" value="<?php echo $this->view->parametros->data_final;?>" class="campo" type="text" />
                </div>
            </div>
             <div class="campo menor">
                <label for="linnumero">Linha</label>
                <input id="linnumero" name="linnumero" value="<?php echo $this->view->parametros->linnumero;?>" class="campo numerico" type="text" />
            </div>
            <div class="clear"></div>

            <div class="campo medio">
                <label for="csloid">Status da linha</label>
                <select id="csloid" name="csloid">
                    <option value="">Escolha</option>
                    <?php foreach ($this->view->comboStatusLinha as $statusLinha): ?>
                        <option value="<?php echo $statusLinha->csloid ?>" <?php echo ($this->view->parametros->csloid == $statusLinha->csloid) ? 'selected="true"' : '' ; ?>>
                            <?php echo $statusLinha->cslstatus; ?>
                        </option>
                    <?php endForeach; ?>
                </select>
            </div>
            <div class="campo medio">
                <label for="csioid">Status do contrato</label>
                <select id="csioid" name="csioid">
                    <option value="">Escolha</option>
                    <?php foreach ($this->view->comboStatusContrato as $dados): ?>
                        <option value="<?php echo $dados->csioid; ?>" <?php echo ($this->view->parametros->csioid == $dados->csioid) ? 'selected="true"' : '' ; ?>>
                            <?php echo $dados->csidescricao; ?>
                        </option>
                    <?php endForeach; ?>
                </select>
            </div>
            <div class="clear"></div>

            <div class="campo medio">
                <label for="clinome_pesq">Cliente</label>
                <input id="clinome_pesq" name="clinome_pesq" value="<?php echo $this->view->parametros->clinome_pesq;?>" class="campo" type="text">
                <input id="clioid_pesq" name="clioid_pesq" value="<?php echo $this->view->parametros->clioid_pesq;?>" class="campo" type="hidden">
            </div>

            <div class="campo medio">
                <label for="texto">Contrato</label>
                <input id="connumero" name="connumero" value="<?php echo $this->view->parametros->connumero;?>" class="campo numerico" type="text">
            </div>

            <div class="campo medio">
                <label for="placa">Placa</label>
                <input id="placa" name="placa" value="<?php echo $this->view->parametros->placa;?>" class="campo placa uppercase" type="text" maxlength="15" />
            </div>
            <div class="clear"></div>

            <fieldset class="medio opcoes-inline">
                <legend>Tipo resultado</legend>
                <input id="resultado_tela" name="tipo_resultado" value="T" type="radio"
                        <?php echo ($this->view->parametros->tipo_resultado == 'T') ? 'checked="checked"' : (empty($this->view->parametros->resultado_tela) ? 'checked="checked"' :'') ; ?>
                        /><label for="resultado_tela">Tela</label>
                <input id="resultado_csv" name="tipo_resultado" value="A" type="radio"
                        <?php echo ($this->view->parametros->tipo_resultado == 'A') ? 'checked="checked""' : '' ; ?>
                        /><label for="resultado_csv">CSV</label>
            </fieldset>
            <div class="clear"></div>
        </form>
    </div>
</div>
<div class="bloco_acoes">
    <button type="button" id="btn_pesquisar">Pesquisar</button>
</div>
<div class="separador"></div>