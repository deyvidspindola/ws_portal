<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">

		<div class="campo data periodo">
			<div class="inicial">
				<label for="dt_ini_busca">Período</label>
				<input id="dt_ini_busca" name="dt_ini_busca" maxlength="10" value="<?php echo $this->view->parametros->dt_ini_busca; ?>" class="campo" type="text" tabindex="1">
			</div>
			<div class="campo label-periodo">a</div>
			<div class="final">
				<label for="dt_fim_busca">&nbsp;</label>
				<input id="dt_fim_busca" name="dt_fim_busca" maxlength="10" value="<?php echo $this->view->parametros->dt_fim_busca; ?>" class="campo" type="text" tabindex="2">
			</div>
		</div>		
		<fieldset class="maior opcoes-display-block">
			<legend>Data Referência</legend>
			<input id="dt_ref_busca1" name="dt_ref_busca" value="0" type="radio" checked="checked" tabindex="3" <?php if ($this->view->parametros->dt_ref_busca == 0 || $this->view->parametros->dt_ref_busca == '') echo 'checked="checked"'; ?> >
			<label for="dt_ref_busca1">Agendamento O.S.</label>
			<input id="dt_ref_busca2" name="dt_ref_busca" value="1" type="radio" tabindex="4" <?php if ($this->view->parametros->dt_ref_busca == 1) echo 'checked="checked"'; ?> >
			<label for="dt_ref_busca2">Envio SMS</label>
		</fieldset>
		
		<div class="clear"></div>
		
		<div class="campo menor">
			<label for="hsecodigo_retorno_busca">Cód. Cancelamento</label>
			<input id="hsecodigo_retorno_busca" name="hsecodigo_retorno_busca" value="<?php echo $this->view->parametros->hsecodigo_retorno_busca; ?>" class="campo" type="text" maxlength="3" tabindex="5">
		</div>
		<div class="campo menor">
			<label for="ordoid_busca">Nº O.S.</label>
			<input id="ordoid_busca" name="ordoid_busca" value="<?php echo $this->view->parametros->ordoid_busca; ?>" class="campo" type="text" maxlength="10" tabindex="6">
		</div>
		<div class="campo maior">
			<label for="clinome_busca">Cliente</label>
			<input id="clinome_busca" name="clinome_busca" value="<?php echo $this->view->parametros->clinome_busca; ?>" class="campo" type="text" tabindex="7">
		</div>
		
		<div class="clear"></div>
		
		<div class="campo menor">
			<label for="endno_ddd_busca">Cód. DDD</label>
			<input id="endno_ddd_busca" name="endno_ddd_busca" value="<?php echo $this->view->parametros->endno_ddd_busca; ?>" class="campo" type="text" maxlength="2" tabindex="8">
		</div>
		<div class="campo menor">
			<label for="endno_cel_busca">Nº Celular</label>
			<input id="endno_cel_busca" name="endno_cel_busca" value="<?php echo $this->view->parametros->endno_cel_busca; ?>" class="campo" type="text" maxlength="9" tabindex="9">
		</div>		
		<div class="campo menor">
			<label for="veiplaca_busca">Placa</label>
			<input id="veiplaca_busca" name="veiplaca_busca" value="<?php echo $this->view->parametros->veiplaca_busca; ?>" class="campo" type="text" maxlength="9" tabindex="10">
		</div>
	
		<div class="clear"></div>
		
    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar" tabindex="11">Pesquisar</button>
    <button type="button" id="bt_csv" tabindex="12">Gerar CSV</button>
</div>