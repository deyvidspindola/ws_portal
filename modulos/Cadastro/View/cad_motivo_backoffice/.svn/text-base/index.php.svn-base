 <div class="modulo_titulo">Motivos de Solicitações BackOffice</div>
	<div class="modulo_conteudo div_acao_pesquisar">
		<div class="nenhum_registro_encontrado mensagem alerta" style="display: none;">Nenhum registro encontrado.</div>
		<div class="registro_excluido mensagem sucesso" style="display: none;">Registro excluído com sucesso.</div>
		<div class="msg_erro mensagem erro" style="display: none;"></div>
		
				
		<div class="bloco_titulo">Dados para Pesquisa</div>
		<div class="bloco_conteudo">
			<div class="formulario">
				<form name="form" id="form" method="POST" action="cad_motivo_backoffice.php">
					<div class="campo maior">
						<label for="motivo_pesquisa">Motivo</label>
                        <select id="motivo_pesquisa" name="motivo_pesquisa" class="comboMotivo">
								<option value="">Escolha</option>
								<?php 
									if (count($this->view->motivos) > 0) :
										foreach($this->view->motivos as $motivo) : ?>
											<option value="<?php echo $motivo['id']?>"><?php echo utf8_decode($motivo['descricao']); ?></option>
								<?php 	endforeach;
									endif; ?>
							</select>
                        <img src="modulos/web/images/ajax-loader-circle.gif" class="carregando" id="carregar_combo" style="display: none;">
                   </div>
					<div class="separador"></div>
				</form>
			</div>
			<div class="separador"></div>
		</div>
		<div class="bloco_acoes">
			<button type="button" name="bt_pesquisar" id="bt_pesquisar" value="Pesquisar" class="botao">Pesquisar</button>
			<button type="button" name="bt_novo" id="bt_novo" value="Novo" class="botao">Novo</button>
		</div>
		<div class="processando_pesquisa" style="display: none; text-align: center; margin: 0 auto;"><img src="images/loading.gif" alt="" /></div>
		<div class="separador"></div>
		<div class="resultado_pesquisa"></div>
		<div class="processando_excluir" style="display: none; text-align: center; margin: 0 auto;"><img src="images/loading.gif" alt="" /></div>
	</div>
	
	
	<div class="modulo_conteudo div_acao_cadastrar" style="display: none;">
		<div class="mensagem info" style="display: block;">Os campos com * são obrigatórios.</div>
		<div class="campos_obrigatorios mensagem alerta" style="display: none;">Existem campos obrigatórios não preenchidos.</div>
		<div class="registro_incluido mensagem sucesso" style="display: none;">Registro incluído com sucesso.</div>
		<div class="registro_alterado mensagem sucesso" style="display: none;">Registro alterado com sucesso.</div>
		<div class="msg_erro mensagem erro" style="display: none;"></div>
		<div class="msg_alerta mensagem alerta" style="display: none;"></div>
		
		
		<div class="bloco_titulo">Dados Principais</div>
		<div class="bloco_conteudo">
			<div class="formulario">
				<form name="form" id="form" method="POST" action="cad_motivo_backoffice.php">
					<div class="campo maior">
						<input type="hidden" name="id_motivo" id="id_motivo" value="" />
						<label for="motivo_cadastro">Motivo *</label>
						<input type="text" id="motivo_cadastro" name="motivo_cadastro" maxlength="50" class="campo" />
					</div>
					<div class="separador"></div>
				</form>
			</div>
			<div class="separador"></div>
		</div>
		<div class="bloco_acoes">
			<button type="button" name="bt_cadastrar" id="bt_cadastrar" value="Cadastrar" class="botao" >Confirmar</button>
			<button type="button" name="bt_voltar" id="bt_voltar" value="Retornar" class="botao">Voltar</button>
		</div>
		<div class="processando_cadastro" style="display: none; text-align: center; margin: 0 auto;"><img src="images/loading.gif" alt="" /></div>
	</div>
</div>
