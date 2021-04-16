<div class="bloco_conteudo">
	<div class="formulario">	
		<div class="campo maior" style="width:421px">		    
			<label for="nomeCliente"><input type="checkbox" <?php if ($this->view->parametros->obrigacaoFinPenApro) echo 'checked="checked"'; ?> id="obrigacaoFinPenApro"  name="obrigacaoFinPenApro" value="1"> Apresentar Pré-obrigações Financeiras Pendentes de Aprovação</label>
		</div>
		<div class="clear"></div>		
		<div class="campo medio">
            <label for="dataReferencia">Status: </label>
            <select id="status" name="status">
				<option value="">Escolha</option>
			<?php if (!empty($dadosStatus)){
					foreach ($dadosStatus as $value) { ?>
				 <option <?php if ($this->view->parametros->status == $value['rastsoid']) echo 'selected="selected"'; ?> value="<?php print $value['rastsoid'];?>"><?php print $value['rastsdescricao'];?></option>
			<?php } }?>
			</select>
        </div>
		<div class="clear"></div>		
		<div class="campo data periodo">
			<div class="inicial">
				<label for="data_1">Período: </label>
				<input id="dtInicio" name="dtInicio" value="<?php print $vl = $this->view->parametros->dtInicio !="" ? $this->view->parametros->dtInicio:""; ?>" <?php if ($this->view->parametros->obrigacaoFinPenApro) echo 'disabled="disabled"'; ?> class="campo desabilitavel" type="text">
			</div>
			<div class="campo label-periodo">a</div>
			<div class="final">
				<label for="data_2">&nbsp;</label>				
			<input id="dtFim" name="dtFim" value="<?php print $vl = $this->view->parametros->dtFim !="" ? $this->view->parametros->dtFim:""; ?>" <?php if ($this->view->parametros->obrigacaoFinPenApro) echo 'disabled="disabled"'; ?> class="campo desabilitavel" type="text">
			</div>
		</div>
		<div class="clear"></div>		
		
		<div class="campo maior">
			<label for="nomeCliente">Tag: </label>
			<input id="tag" name="tag" value="<?php print $vl = $this->view->parametros->tag !="" ? $this->view->parametros->tag:""; ?>" <?php if ($this->view->parametros->obrigacaoFinPenApro) echo 'disabled="disabled"'; ?> class="campo desabilitavel" type="text" >
		</div><div class="clear"></div>
		
		<div class="campo maior">
			<label for="nomeCliente">Descrição: </label>
			<input id="descricao" name="descricao" value="<?php print $vl = $this->view->parametros->descricao !="" ? $this->view->parametros->descricao:""; ?>" <?php if ($this->view->parametros->obrigacaoFinPenApro) echo 'disabled="disabled"'; ?> class="campo desabilitavel" type="text">
		</div><div class="clear"></div>
		
		<div class="campo medio">
			<label for="nomeCliente">Tipo: </label>
			<select id="tipo" name="tipo" <?php if ($this->view->parametros->obrigacaoFinPenApro) echo 'disabled="disabled"'; ?>>
				<option value="">Escolha</option>
				<option <?php if ($this->view->parametros->tipo == "F") echo 'selected="selected"'; ?> value="F">Funcionalidade</option>
				<option <?php if ($this->view->parametros->tipo == "P") echo 'selected="selected"'; ?> value="P">Pacote</option>
			</select>
		</div><div class="clear"></div>
		
	</div>
</div>
<div class="bloco_acoes">
	<button type="button" id="bt_gerarPrevisao" >Pesquisar</button>
</div>