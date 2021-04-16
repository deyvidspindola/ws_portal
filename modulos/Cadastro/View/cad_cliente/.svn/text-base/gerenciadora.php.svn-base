<div class="bloco_titulo">Gerenciadora</div>

<form action="" name="cad_gerenciadora" id="cad_gerenciadora" method="post">
	
	<input type="hidden" name="acao" id="acao" value="setGerenciadora" />
	<input type="hidden" name="clioid" id="clioid" value="<?php echo $this->clioid; ?>" />
		
	<div class="bloco_conteudo">
		<div class="conteudo">
			
			<div class="campo ">
				<label for="cligeroid">Gerenciadora: </label>
				<select id="cligeroid" name="cligeroid" class="campo maior obrigatorio_todos">
					<option value="">Selecione</option>
					
					<?php foreach($this->gerenciadoras as $gerenciadora):?>
					
						<option value="<?php echo $gerenciadora['geroid'];?>" <?=(isset($_POST['cligeroid']) && $_POST['cligeroid'] == $gerenciadora['geroid']) ? "selected = 'selected'" : ''?>><?php echo $gerenciadora['gernome'];?></option>
					
					<?php endforeach;?>
					
				</select>
			</div>
			
			<div class="clear" ></div>
			
			<div class="campo menor">
				<label for="clirede_ip">IP: </label>
				<input type="text" id="clirede_ip" name="clirede_ip" value="<?=(isset($_POST['clirede_ip']) && $_POST['clirede_ip'] != "")?$_POST['clirede_ip']:''?>" class="campo obrigatorio_todos" />
			</div>
			
			<div class="clear" ></div>
			
			<div class="campo menor">
				<label for="clirede_porta">Porta: </label>
				<input type="text" id="clirede_porta" name="clirede_porta" value="<?=(isset($_POST['clirede_porta']) && $_POST['clirede_porta'] != "")?$_POST['clirede_porta']:''?>" class="campo obrigatorio_todos camponum" maxlength="10"/>
			</div>
			
			<div class="clear" ></div>
			
			<div class="campo medio">
				<label for="clitpo_integr">Tipo Integração: </label>
				<select id="clitpo_integr" name="clitpo_integr" class="campo obrigatorio_todos	">
					<option value="">Selecione</option>
					<option value="S" <? if ($_POST['clitpo_integr']=="S") echo " SELECTED"; ?>>Acesso Sascar</option>
					<option value="A" <? if ($_POST['clitpo_integr']=="A") echo " SELECTED"; ?>>Autom&aacute;tico</option>
					<option value="L" <? if ($_POST['clitpo_integr']=="L") echo " SELECTED"; ?>>Servidor Local</option>
				</select>
			</div>
			
			<!-- 
			<div class="clear" ></div>
			<fieldset class="medio">
				<legend>Chat </legend>
				<input type="checkbox" id="clivisualizacao_sasgc" name="clivisualizacao_sasgc" value="t" <?php if($_POST['clivisualizacao_sasgc'] == 't') echo "checked";?> />
				<label style="display: inline" for="clivisualizacao_sasgc">SASGC</label>
			</fieldset>
			 -->
			
			<div class="clear" ></div> 
		</div>
	</div>

	<div class="bloco_acoes">
		<button type="submit" value="Confirmar" class="validacao" id="buttonConfirmarGerenciadora" name="buttonConfirmarGerenciadora" >Confirmar</button>
		<button type="button" value="Voltar" id="buttonVoltar" name="buttonVoltar" onclick="window.location.href='<?php echo str_replace('/', '', strrchr($_SERVER['SCRIPT_NAME'], '/'));?>'">Voltar</button>
	    <? if($this->retCliente!=""){ ?>
    		<button type="button" value="Voltar" id="buttonVoltar" name="buttonVoltar" onclick="window.location.href='<?=trata_retorno($this->retCliente,$clioid)?>'">Retornar ao Contrato</button>
        <? } ?>
	</div>
</form>

