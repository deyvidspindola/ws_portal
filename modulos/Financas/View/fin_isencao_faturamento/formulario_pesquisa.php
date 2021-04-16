<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">
		
            <div class="campo medio">
				<label id="lbl_placa_busca" for="placa_busca">Placa</label>
				<input id="placa_busca" name="placa_busca" value="<?php echo $this->view->parametros->placa_busca;?>" class="campo" type="text" maxlength="9">
			</div>
			<div class="campo menor">
				&nbsp;
			</div>
			<div class="campo medio">
	            <label id="lbl_conoid_busca" for="conoid_busca">Contrato</label>
	            <input id="conoid_busca" class="campo" type="text" value="<?php echo $this->view->parametros->conoid_busca;?>" name="conoid_busca" maxlength="12">
			</div>
			<div class="clear"></div>
			
			<div class="campo maior">
            <label id="lbl_cliente_busca" for="cliente_busca">Cliente</label>
            <input id="cliente_busca" class="campo" type="text" value="<?php echo $this->view->parametros->cliente_busca;?>" name="cliente_busca" maxlength="60">
			</div>
			<div class="campo medio">
				<label id="lbl_docto_busca" for="docto_busca">CPF/ CNPJ</label>
				<input id="docto_busca" name="docto_busca" value="<?php echo $this->view->parametros->docto_busca;?>" class="campo" type="text" maxlength="18" onkeydown="javascript:return aplica_mascara_cpfcnpj(this,18,event)" onkeyup="javascript:return aplica_mascara_cpfcnpj(this,18,event)">
			</div>
			<div class="clear"></div>
			
    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
    <button type="button" id="bt_limpar">Limpar</button>
</div>







