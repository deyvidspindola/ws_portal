<div class="">
	<ul class="bloco_opcoes">
		<li class="ativo">
			<a href="cad_parametrizacao_rs_calculo_repasse.php" title="Cálculo do Repasse">Cálculo do Repasse</a>
		</li>
        <li class="">
            <a href="cad_parametrizacao_rs_calculo_repasse.php?acao=historico" title="Histórico Cálculo do Repasse">Histórico Cálculo do Repasse</a>
        </li>
		<li class="">
            <a href="cad_retencao_impostos.php" title="Retenção de Impostos">Retenção de Impostos</a>
        </li>
		<li class="">
		    <a href="cad_retencao_impostos.php?acao=historico" title="Histórico Retenção de Impostos">Histórico Retenção de Impostos</a>
	    </li>
	</ul>
</div>
<div class="bloco_titulo">Dados Principais</div>
    <div class="bloco_conteudo">
    	<div class="formulario ui-sortable">
            <?php if($this->view->parametros->acao != 'editar'): ?>
    		<div class="campo medio">
                <label for="hrscrdt_cadastro">Vigente Desde</label>
                <input id="hrscrdt_cadastro" name="hrscrdt_cadastro" value="<?php echo !empty($this->view->parametros->dataUltimoHistorico) && $this->view->parametros->dataUltimoHistorico != '' ? date('d/m/Y H:i:s', strtotime($this->view->parametros->dataUltimoHistorico)) : '' ?>" class="campo" type="text" disabled="disabled">
            </div>
            <?php endif; ?>

            <div class="clear"></div>
            <div style="cursor: default; position: relative; opacity: 1; left: 0px; top: 0px;" class="campo medio">
                <label for="prscrfaixa_inicial">Faixa Inicial - Parque Médio (unid./ veíc.) *</label>
                <input <?php echo !empty($this->view->parametros->prscroid) ? 'readonly="readonly" class="campo desabilitado"' : 'class="campo"' ?> id="prscrfaixa_inicial" maxlength="11" name="prscrfaixa_inicial" value="<?php echo $this->view->parametros->prscrfaixa_inicial ?>" type="text">
            </div>
            <div class="campo medio">
            	<label for="prscrfaixa_final">Faixa Final - Parque Médio (unid./ veíc.) *</label>
            	<input <?php echo !empty($this->view->parametros->prscroid) ? 'readonly="readonly" class="campo desabilitado"' : 'class="campo"' ?> id="prscrfaixa_final" maxlength="11" name="prscrfaixa_final" value="<?php echo $this->view->parametros->prscrfaixa_final ?>" type="text">
            </div>
            <div class="clear"></div>
            <div class="clear"></div><div class="campo medio">
            	<label for="prscrrevenue_share_vivo">Revenue Share VIVO (%) *</label>
            	<input id="prscrrevenue_share_vivo" maxlength="6" name="prscrrevenue_share_vivo" value="<?php echo $this->view->parametros->prscrrevenue_share_vivo  ?>" class="campo" type="text">
            </div>
            <div style="cursor: default; position: relative; opacity: 1; left: 0px; top: 0px;" class="campo medio">
            	<label for="prscrrevenue_share_sascar">Revenue Share SASCAR (%) *</label>
            	<input id="prscrrevenue_share_sascar" maxlength="6" name="prscrrevenue_share_sascar" value="<?php echo $this->view->parametros->prscrrevenue_share_sascar ?>" class="campo" type="text">
            </div>
            <div class="clear"></div>
            <div class="campo menor">
            	<label for="prscrpreco_minimo">Preço Mínimo *</label>
            	<input id="prscrpreco_minimo" maxlength="10" name="prscrpreco_minimo" value="<?php echo $this->view->parametros->prscrpreco_minimo ?>" class="campo" type="text">
            </div>
            <div style="cursor: default; position: relative; opacity: 1; left: 0px; top: 0px;" class="campo menor">
            	<label for="prscrincremento_valor">Incremento Valor *</label>
            	<input id="prscrincremento_valor" maxlength="10" name="prscrincremento_valor" value="<?php echo $this->view->parametros->prscrincremento_valor ?>" class="campo" type="text">
            </div>
            <div class="clear"></div>

    	</div>
    </div>
    <div class="bloco_acoes">
        <?php if($this->view->parametros->acao == 'editar'): ?>
    	<button type="submit">Alterar</button>
        <?php else: ?>
        <button type="submit">Incluir</button>
        <?php endif; ?>
    </div>

<div class="separador"></div>