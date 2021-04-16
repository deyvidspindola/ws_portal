
<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo maior">
            <label for="lbl_tipo" id="lbl_tipo">Tipo *</label>
            <select id="tipo" name="tipo">
                <option value="1"<?php echo(isset($this->view->parametros->tipo) && !empty($this->view->parametros->tipo) != '' && $this->view->parametros->tipo == "1") ? 'selected="selected"' : '' ?>>Análise Ordem de Serviço</option>
                <option value="2"<?php echo(isset($this->view->parametros->tipo) && !empty($this->view->parametros->tipo) != '' && $this->view->parametros->tipo == "2") ? 'selected="selected"' : '' ?>>Analítico</option>
            </select>
        </div>

        <div class="clear"></div>

        <div class="campo data periodo" style="width:290px !important;">
            <div class="inicial">
                <label for="data_inicial" id="lbl_periodo_inicial">Período *</label>
                <input id="data_inicial" type="text" maxlength="10" name="data_inicial" value="<?php echo $this->view->parametros->data_inicial; ?>" class="campo" />
            </div>
            <div class="campo label-periodo">a</div>
            <div class="final">
                <label for="data_final">&nbsp;</label>
                <input id="data_final" type="text" maxlength="10" name="data_final" value="<?php echo $this->view->parametros->data_final; ?>" class="campo" />
                <img src="images/help10.gif"class="help" align="absmiddle" title="Data Posição Atual" id="help-data-analise" />
            </div>
        </div>

        <div class="clear"></div>

        <div class="campo maior">
            <label for="aoamoid_acao" id="lbl_acao">Ação</label>
            <select id="aoamoid_acao" name="aoamoid_acao">
                <option value="">Escolha</option>
                <?php if (count($this->view->parametros->acoes) > 0) : ?>
                    <?php foreach ($this->view->parametros->acoes as $acao): ?>
                        <option <?php echo ($this->view->parametros->aoamoid_acao == $acao->aoamoid) ? 'selected="selected"' : '' ?> value="<?php echo $acao->aoamoid; ?> "><?php echo $acao->aoamdescricao; ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="campo maior">
            <label for="aoamoid_motivo" id="lbl_motivo">Motivo</label>
            <select id="aoamoid_motivo" name="aoamoid_motivo">
                <option value="">Escolha</option>
                <?php if (count($this->view->parametros->motivos) > 0) : ?>
                    <?php foreach ($this->view->parametros->motivos as $motivo): ?>
                        <option <?php echo (intval($this->view->parametros->aoamoid_motivo) == $motivo->aoamoid) ? 'selected="selected"' : '' ?> value="<?php echo $motivo->aoamoid; ?>"><?php echo $motivo->aoamdescricao; ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <img src="modulos/web/images/ajax-loader-circle.gif" style="display: none;" class="carregando" />
        </div>
<?php //echo "kkkk";//print_r($this->view->parametros->atendentes); ?>
        <div class="campo medio analitico invisivel">
            <label for="lbl_atendente" id="lbl_atendente">Atendente</label>
            <select id="cd_usuario" name="cd_usuario">
                <option value="">Escolha</option>
                <?php foreach ($this->view->parametros->atendentes as $atendentes): ?>
                    <option <?php echo (intval($this->view->parametros->cd_usuario) == $atendentes->cd_usuario) ? 'selected="selected"' : '' ?> value="<?php echo $atendentes->cd_usuario; ?>"><?php echo $atendentes->ds_login; ?></option>
                <?php endforeach; ?>
            </select>
            <img src="modulos/web/images/ajax-loader-circle.gif" style="display: none;" class="carregando" />
        </div>

        <div class="clear"></div>

        <div class="campo medio analitico invisivel">
            <label for="lbl_clinome" id="lbl_clinome" style="cursor: default;">Cliente</label>
            <input id="clinome" class="campo" type="text" value="<?php echo $this->view->parametros->clinome ?>" name="clinome" maxlength="50">
        </div>

        <div class="campo medio analitico invisivel">
            <label for="lbl_tipocontrato" id="lbl_tipocontrato">Tipo Contrato</label>
            <select id="tpcoid" name="tpcoid">
                <option value="">Escolha</option>
                <?php foreach ($this->view->parametros->tipocontrato as $tipocontrato): ?>
                    <option <?php echo ($this->view->parametros->tpcoid == $tipocontrato->tpcoid) ? 'selected="selected"' : '' ?> value="<?php echo $tipocontrato->tpcoid; ?>"><?php echo $tipocontrato->tpcdescricao; ?></option>
                <?php endforeach; ?>
            </select>
            <img src="modulos/web/images/ajax-loader-circle.gif" style="display: none;" class="carregando" />
        </div>
    </div>
    <div class="clear"></div>
    <div class="separador"></div>
</div>
<div class="bloco_acoes">
    <button type="button" id="bt_pesquisar">Pesquisar</button>
    <button type="button" id="bt_gerar_csv">Gerar CSV</button>
</div>







