<div class="">
	<ul class="bloco_opcoes">
		<li class="">
			<a href="fin_previsao_arrecadacao_vivo.php" title="Gerar Previsão">Gerar Previsão</a>
		</li>
        <li class="ativo">
            <a href="fin_previsao_arrecadacao_vivo.php?acao=consulta" title="Consultar Previsão">Consultar Previsão</a>
        </li>
	</ul>
</div>
<div class="bloco_titulo">Consultar Previsão</div>
<div class="bloco_conteudo">
	<div class="formulario ui-sortable">
		<div class="campo mes_ano">
            <label for="dataReferencia_psq">Referência *</label>
            <input id="dataReferencia_psq" class="campo" type="text" value="<?php echo $this->view->parametros->dataReferencia_psq; ?>" name="dataReferencia_psq" <?php if ($this->view->processoExecutando) echo 'disabled="disabled"'; ?>>
        </div>
		<fieldset class="maior opcoes-inline">
			<legend>Opção *</legend>
			<input id="opcao_psq" name="opcao_psq" value="FALSE" type="radio" <?php if($this->view->parametros->opcao_psq == 'FALSE' || !isset($this->view->parametros->opcao_psq)) echo 'checked="checked"'; ?> <?php if ($this->view->processoExecutando) echo 'disabled="disabled"'; ?>>
				<label for="opcao_psq">Não Processados</label>
			<input id="opcao_psq" name="opcao_psq" value="TRUE" type="radio" <?php if($this->view->parametros->opcao_psq == 'TRUE') echo 'checked="checked"'; ?> <?php if ($this->view->processoExecutando) echo 'disabled="disabled"'; ?>>
				<label for="opcao_psq">Processados</label>
			<input id="opcao_psq" name="opcao_psq" value="NULL" type="radio" <?php if($this->view->parametros->opcao_psq == 'NULL') echo 'checked="checked"'; ?> <?php if ($this->view->processoExecutando) echo 'disabled="disabled"'; ?>>
				<label for="opcao_psq">Ambos</label>
		</fieldset>
		<div class="clear"></div>
		
		<div class="campo maior">
			<label for="nomeCliente_psq">Nome do Cliente</label>
			<input id="nomeCliente_psq" name="nomeCliente_psq" value="<?php echo $this->view->parametros->nomeCliente_psq; ?>" class="campo" type="text" <?php if ($this->view->processoExecutando) echo 'disabled="disabled"'; ?>>
		</div><div class="clear"></div>
		
	</div>
</div>
<div class="bloco_acoes">
	<button type="button" id="bt_consultar" <?php if ($this->view->processoExecutando) echo 'disabled="disabled"'; ?>>Consultar</button>
	<button type="button" id="bt_processar" <?php if ($this->view->processoExecutando || (!isset($this->view->dados))) echo 'disabled="disabled"'; ?>>Processar</button>
	<button type="button" id="bt_excluir" <?php if ($this->view->processoExecutando || (!isset($this->view->dados))) echo 'disabled="disabled"'; ?>>Excluir Não Processados</button>
</div> 