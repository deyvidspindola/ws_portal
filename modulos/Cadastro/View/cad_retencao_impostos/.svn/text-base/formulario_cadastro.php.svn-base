<style type="text/css">
.menor input.campo, .menor select {
    width: 120px !important;
}
</style>

<div class="">
	<ul class="bloco_opcoes">
		<li class="">
			<a href="cad_parametrizacao_rs_calculo_repasse.php" title="Cálculo do Repasse">Cálculo do Repasse</a>
		</li>
        <li class="">
            <a href="cad_parametrizacao_rs_calculo_repasse.php?acao=historico" title="Histórico Cálculo do Repasse">Histórico Cálculo do Repasse</a>
        </li>
		<li class="ativo">
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
    		<div class="campo medio">
                <label for="hrsridt_cadastro">Vigente Desde</label>
                <input id="hrsridt_cadastro" name="hrsridt_cadastro" value="<?php echo !empty($this->view->parametros->dataUltimoHistorico) && $this->view->parametros->dataUltimoHistorico != '' ? date('d/m/Y H:i:s', strtotime($this->view->parametros->dataUltimoHistorico)) : '' ?>" class="campo" type="text" disabled="disabled">
            </div>
            <div class="clear"></div>

            <fieldset class="medio">
                <legend>Imposto / Alíquota *</legend>
                <div class="campo menor">
                    <label for="prsriiss">ISS *</label>
                    <input id="prsriiss" maxlength="6" name="prsriiss" value="<?php echo isset($this->view->parametros->prsriiss) && $this->view->parametros->prsriiss != '' ? number_format($this->view->parametros->prsriiss, 2, ',', '.') : '' ?>" class="campo" type="text">
                </div>

                <div class="clear"></div>

                <div class="campo menor">
                    <label for="prsripis">PIS *</label>
                    <input id="prsripis" maxlength="6" name="prsripis" value="<?php echo isset($this->view->parametros->prsripis) && $this->view->parametros->prsripis != '' ? number_format($this->view->parametros->prsripis, 2, ',', '.') : '' ?>" class="campo" type="text">
                </div>
                <div class="clear"></div>

                <div class="campo menor">
                    <label for="prsricofins">COFINS *</label>
                    <input id="prsricofins" maxlength="6" name="prsricofins" value="<?php echo isset($this->view->parametros->prsricofins) && $this->view->parametros->prsricofins != '' ? number_format($this->view->parametros->prsricofins, 2, ',', '.') : '' ?>" class="campo" type="text">
                </div>
                <div class="clear"></div>

            </fieldset>

            <fieldset class="medio">
                <legend>Outros Descontos *</legend>
                <div class="campo menor">
                    <label for="prsrivalor_chip">Valor Chip *</label>
                    <input id="prsrivalor_chip" maxlength="12" name="prsrivalor_chip" value="<?php echo isset($this->view->parametros->prsrivalor_chip) && $this->view->parametros->prsrivalor_chip != '' ? number_format($this->view->parametros->prsrivalor_chip, 2, ',', '.') : '' ?>" class="campo" type="text">
                </div>

                <div class="clear"></div>

            </fieldset>

            <div class="clear"></div>

    	</div>
    </div>
    <div class="bloco_acoes">
        <button type="submit">Salvar</button>
    </div>

<div class="separador"></div>