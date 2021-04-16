<div class="">
	<ul class="bloco_opcoes">
		<li class="ativo">
			<a href="fin_previsao_arrecadacao_vivo.php" title="Gerar Previsão">Gerar Previsão</a>
		</li>
        <li class="">
        <?php 	
        if (!$this->view->processoExecutando) {
        	echo '<a href="fin_previsao_arrecadacao_vivo.php?acao=consulta" title="Consultar Previsão">Consultar Previsão</a>';
        }
        else {
			echo '&nbsp; Consultar Previsão &nbsp;';
		}
        ?>
        </li>
	</ul>
</div>
<div class="bloco_titulo">Gerar Previsão</div>
<div class="bloco_conteudo">
	<div class="formulario ui-sortable">		
		<div class="campo <?php if (!$this->view->processoExecutando) echo 'mes_ano'; ?>">
            <label for="dataReferencia">Referência *</label>
            <input id="dataReferencia" name="dataReferencia" value="" class="campo" type="text" <?php if ($this->view->processoExecutando) echo 'disabled="disabled"'; ?>>
        </div>
		<div class="clear"></div>
		
		<div class="campo maior">
			<label for="nomeCliente">Nome do Cliente</label>
			<input id="nomeCliente" name="nomeCliente" value="" class="campo" type="text" <?php if ($this->view->processoExecutando) echo 'disabled="disabled"'; ?>>
		</div><div class="clear"></div>
		
	</div>
</div>
<div class="bloco_acoes">
	<button type="button" id="bt_gerarPrevisao" <?php if ($this->view->processoExecutando) echo 'disabled="disabled"'; ?>>Gerar Previsão</button>
</div>