<div class="modulo_titulo">Cadastro Alçada de Aprovação</div>
    <div class="modulo_conteudo">        
        <div id="mensagem" class="mensagem <?php echo ($this->retorno != '') ? $this->retorno['status'] : '' ?>"><?php echo ($this->retorno != '') ? $this->retorno['mensagem'] : '' ?></div>

        <div class="mensagem info">(*) Campos de preenchimento obrigatório.</div>
        
<div class="bloco_titulo">Cadastrar</div>

<form action="" name="pesquisa_configuracao_equipamento" id="pesquisa_configuracao_equipamento" method="post">
    <input type="hidden" name="acao" id="acao" value="cadastrarAlcada" />
	<div class="bloco_conteudo">
		<div class="conteudo">	
		
			<div class="campo medio">
				<label for="alcousuoid">Usuário aprovador *</label>			
				<select id="alcousuoid" name="alcousuoid" class="alcousuoid">
					<option value="">Selecione</option>
					<?php foreach($this->usuarioAprovador as $usuario):?>
					<option value="<?php echo $usuario['cd_usuario']?>"><?php echo $usuario['nm_usuario']?></option>
					<?php endforeach;?>
				</select>				
			</div>
			
			<div class="clear" ></div>

			<div class="campo medio">
				<label for="alcovlr_inicio">Valor inicial da aprovação *</label>			
				R$&nbsp;<input type="text" value="" class="valorZerado alcovlr_inicio numerico" name="alcovlr_inicio" id="alcovlr_inicio" maxlength="13" /> 			
			</div>

			<div class="campo medio">
				<label for="alcovlr_fim">Valor final da aprovação *</label>			
				R$&nbsp;<input type="text" value="" class="valor alcovlr_fim numerico" name="alcovlr_fim" id="alcovlr_fim" maxlength="13"/> 			
			</div>
		
			<div class="clear" ></div>

			<div class="campo medio">
				<br/>
				<input type="checkbox" value="1" name="alcodupla_check" id="alcodupla_check" class="alcodupla_check" style="vertical-align: middle" />
				<label for="alcodupla_check" style="display: inline">Checagem dupla</label>
			</div>
			
			<div class="clear" ></div>

			<div class="campo medio">
				<label for="alcousuoid_dupla_check">Segundo usuário aprovador </label>			
				<select id="alcousuoid_dupla_check" name="alcousuoid_dupla_check" class="alcousuoid_dupla_check" disabled="true">
					<option value="">Selecione</option>
					<?php foreach($this->usuarioAprovador as $usuario):?>
					<option value="<?php echo $usuario['cd_usuario']?>"><?php echo $usuario['nm_usuario']?></option>
					<?php endforeach;?>
				</select>				
			</div>

			<div class="clear" ></div>

			<div class="campo medio">
				<label for="alcovlr_inicio_dupla_check">Valor inicial da aprovação</label>			
				R$&nbsp;<input type="text" value="" name="alcovlr_inicio_dupla_check" id="alcovlr_inicio_dupla_check" class="valor alcovlr_inicio_dupla_check numerico" disabled="true" maxlength="13"/> 			
			</div>

			<div class="campo medio">
				<label for="alcovlr_fim_dupla_check">Valor final da aprovação</label>			
				R$&nbsp;<input type="text" value="" name="alcovlr_fim_dupla_check" id="alcovlr_fim_dupla_check" class="valor alcovlr_fim_dupla_check numerico" disabled="true" maxlength="13"/> 						
			</div>

			<div class="clear" ></div>
		</div>
	</div>
		
	<div class="bloco_acoes">
		<button type="button" value="cadastrarAlcada" id="buttonGravar" name="buttonGravar">Salvar</button>
		<button type="button" value="Voltar" id="buttonVoltar" name="buttonVoltar" onclick="window.location.href='<?php echo str_replace('/', '', strrchr($_SERVER['SCRIPT_NAME'], '/'));?>'">Cancelar</button>
		<button type="button" value="limpar" id="buttonLimpar" name="buttonLimpar">Limpar</button>
	</div>
</form>