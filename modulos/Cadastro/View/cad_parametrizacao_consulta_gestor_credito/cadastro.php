<?php 
cabecalho();
$displayAlerta 	= 	($this->msg['tipo'] == 'alerta') 	? 'block' : 'none';
$displaySucesso = 	($this->msg['tipo'] == 'sucesso') 	? 'block' : 'none';
$displayErro 	= 	($this->msg['tipo'] == 'erro') 		? 'block' : 'none';
?>

<!-- LINKS PARA CSS E JS -->
<?php require _MODULEDIR_ . 'Cadastro/View/cad_parametrizacao_consulta_gestor_credito/head.php' ?>
	<div class="modulo_titulo">Parametrização de Consulta ao Gestor de Crédito</div>
	<div class="modulo_conteudo">
		<div class="mensagem info invisivel"    id="msginfo" style="display:block;">Os campos com * são obrigatórios</div>
		<div class="mensagem alerta invisivel"  id="msgalerta" style="display: <?php echo $displayAlerta?>;"><?php echo $this->msg['mensagem'] ?></div>
		<div class="mensagem sucesso invisivel" id="msgsucesso" style="display: <?php echo $displaySucesso?>;"><?php echo $this->msg['mensagem'] ?></div>
		<div class="mensagem erro invisivel"    id="msgerro" style="display: <?php echo $displayErro?>;"><?php echo $this->msg['mensagem'] ?></div>
		<form method="post" id="frm_cadastro_gestor_credito" action="">
			<input type="hidden" name="acao" value="cadastrar" />
			<input type="hidden" name="id" value="<?php echo $id; ?>" />
			<input type="hidden" id="tipo_proposta_texto" name="tipo_proposta_texto" value="<?php echo $propostaAux; ?>" />
			<input type="hidden" value="<?php echo $persistirDados; ?>" id="persistirDados" name="persistirDados" />
			<div class="resultado">			
				<div class="bloco_titulo">Dados para Cadastro</div>				
				<div class="bloco_conteudo">
					<div class="formulario">
						<div class="campo medio">
							<fieldset class="medio" id="tipo_pessoa">
								<legend>Tipo Pessoa *</legend>
								<input id="tipo_pessoa_fisica" type="radio" name="tipo" value="F" 
								<?php echo $this->parametrizacao->gcptipopessoa == 'F' ? "checked" : ""; ?>>
								<label for="tipo_pessoa_fisica">Física</label>
								<input id="tipo_pessoa_juridica" type="radio" name="tipo" value="J"
								<?php echo $this->parametrizacao->gcptipopessoa == 'J' ? "checked" : ""; ?>>
								<label for="tipo_pessoa_juridica">Jurídica</label>
							</fieldset>
						</div>
							
						<div class="clear"></div>
							
						<div class="campo medio">
							<label for="tipo_proposta">Tipo Proposta *</label>
							<select name="tipo_proposta" id="tipo_proposta">
								<option value="">Escolha</option>
								<?php echo $tipoProposta; ?>
							</select>
						</div>
							
						<div class="campo medio"
		                	<?php if(!$subtipoProposta) : ?>
		                		style="display: none;"
		                	<?php endif; ?>
						>
							<label for="subtipo_proposta">Subtipo Proposta *</label>
							<select name="subtipo_proposta" id="subtipo_proposta">
								<option value="">Escolha</option>
								<?php echo $subtipoProposta; ?>
							</select>
						</div>
						
						<div class="clear"></div>
							
						<div class="campo medio">
							<label for="tipo_contrato">Tipo Contrato *</label>
							<select name="tipo_contrato" id="tipo_contrato">
								<option value="">Escolha</option>
								<?php echo $tipoContrato; ?>
							</select>
						</div>
						
						<div class="clear"></div>
						
						<div class="campo medio">
							<fieldset class="medio" id="vaigestor">
			                    <legend>Vai ao Gestor *</legend>
								<input id="vaigestor_sim" type="radio" name="vaigestor" value="t" 
								<?php echo $this->parametrizacao->gcpindica_gestor == 't' ? "checked" : ""; ?>>
								<label for="vaigestor_sim">Sim</label>
								<input id="vaigestor_nao" type="radio" name="vaigestor" value="f"
								<?php echo $this->parametrizacao->gcpindica_gestor == 'f' ? "checked" : ""; ?>>
								<label for="vaigestor_nao">Não</label>
		                    </fieldset>
						</div>		                
		                <div class="campo pequeno"
		                	<?php if(($this->parametrizacao->gcpindica_gestor == 't')||(!$id)) : ?>
		                		style="display: none;"
		                	<?php endif; ?>
		                >
		                    <label for="limite_contrato">Limite de Contratos *</label>
		                    <input type="text" id="limite_contrato" name="limite_contrato" value="<?php echo $limite; ?>" class="campo" />
		                </div>   
		                		                
		                <div class="clear"></div>
						
					</div>
				</div>
				
				<div class="bloco_acoes">
					<button type="button" id="btn_confirmar_cadastro" name="btn_confirmar_cadastro">Confirmar</button>
					<button type="button" id="btn_retornar_cadastro" name="btn_retornar_cadastro">Retornar</button>
				</div>
			
			</div>
		</form>	
	</div>
    <div class="separador"></div>
	<?php include('lib/rodape.php'); ?>