<script language="Javascript" type="text/javascript">

function linkNovaGerenciadora(){
	 $('input').val('');
     window.location= "<?php echo $this->listCadastroGerenciadora ?>";
}

function resetarSenhaIntegradora(geroid, gernome){
	var executar = confirm('Tem certeza que quer resetar a senha da integração da gerenciadora \n ' + gernome + " ?");
	if (executar==true){		
		$("#geroid").val(geroid);
		$("#acao").val('resetarSenhaIntegradora');
		$('#acoesIntegracao').submit();
	}
}

function gerarAcessoIntegradora(geroid, gernome){
	var executar = confirm('Tem certeza criar acesso de integração para a gerenciadora \n ' + gernome + " ?");
	if (executar==true){
		$("#geroid").val(geroid);
		$("#acao").val('gerarAcessoIntegradora');
		$('#acoesIntegracao').submit();
	}
}
</script>

<!-- Mensagens Erro -->
<div id="mensagem_erro" class="mensagem erro <?php if (empty($this->mensagemErro)): ?>invisivel<?php endif;?>">
	<?php echo $this->mensagemErro; ?>
</div>

<!-- Mensagens Sucesso -->
<div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->mensagemSucesso)): ?>invisivel<?php endif;?>">
	<?php echo $this->mensagemSucesso; ?>
</div>

<div class="bloco_titulo">Dados para Pesquisa</div>
<form action="" name="acoesIntegracao" id="acoesIntegracao" method="post">
    <input type="hidden" name="acao" id="acao" value="pesquisar"/>
    <input type="hidden" name="geroid" id="geroid" value="0"/>
	<div class="bloco_conteudo">
		<div class="conteudo">
			<div class="campo">
				<div class="campo maior">
					<label for="ger_nome">Nome: </label>
					<input type="text" id="ger_nome" name="ger_nome" value="<?php echo $this->ger_nome; ?>" class="campo texto" maxlength="40"/>
				</div>
				<div class="campo maior">
					<label for="serie">Software: </label>
					<select name="busca_gersoftware" id="busca_gersoftware">
						<?php foreach($this->software as $software){?>
							<option value="<?php echo $software['id_software']; ?>" 
								<? if($this->busca_gersoftware == $software['id_software']){?> 
									selected="selected" <?php 
								}?>>
								<?php echo $software['nome_software']; ?>
							</option>
						<?php }?>
					</select>
				</div>
				<div class="clear" ></div>
				<div class="campo maior">
					<label for="serie">Tipo: </label>
					<select name="busca_tipo" id="busca_tipo">
						<?php foreach($this->tipo as $tipo){?>
							<option value="<?php echo $tipo['tipoSoftware']; ?>" 
								<?php if($this->busca_tipo == $tipo['tipoSoftware']){?> 
									selected="selected" 
								<?php }?>>
								<?php echo $tipo['softwareNome']; ?>
							</option>
						<?php }?>
					</select>
				</div>
				
				<div class="campo maior">
					<label for="serie">Possui Integração: </label>
					<select name="possuiIntegracao" id="possuiIntegracao">
						<?php foreach($this->integracao as $tipo){?>
							<option value="<?php echo $tipo['buscaIntegracao']; ?>" 
								<?php if($this->possuiIntegracao == $tipo['buscaIntegracao']){?> 
									selected="selected" 
								<?php }?>>
								<?php echo $tipo['descricao']; ?>
							</option>
						<?php }?>
					</select>
				</div>
				
			</div>
			<div class="clear" ></div>
		</div>
	</div>		
	<div class="bloco_acoes">
		
		<?php if(count($this->resultadoPesquisa) < 1 && $this->permissaoAcessoLinkNovaGerenciadora){ ?>
			<input type="button" class="button" id="buttonNovaGerenciadora" value="Cadastrar Nova Gerenciadora" onclick="linkNovaGerenciadora()"/>
		<?php } ?>
		<button type="button" value="pesquisar" id="buttonPesquisar" name="buttonPesquisar" class="validacao">Pesquisar</button>
	</div>
</form>

<div class="separador"></div>

<?php
	if($this->acao == 'PESQUISAR'):
		$numResultado = count($this->resultadoPesquisa);
	    
	    if($numResultado > 1){
            $textoResult = $numResultado ." registros encontrados.";
        }elseif($numResultado == 1){
            $textoResult = "1 registro encontrado.";
        }elseif($numResultado == 0){
            $textoResult = "Nenhum Resultado Encontrado";
        }
?>

<div class="resultado_pesquisa">
	<div class="bloco_titulo">Resultado da Pesquisa</div>
		<div class="bloco_conteudo">
		    <div class="listagem">
		        <table>
					<?php if($numResultado > 0) {?>
			            <thead>
			                <tr>
			                	<td align="center"><h3>ID</h3></td>  	
			                    <td align="center"><h3>Nome</h3></td>
								<td align="center"><h3>Tipo</h3></td>
								<td align="center"><h3>CNPJ</h3></td>
								<td align="center"><h3>Fone</h3></td>
								<td align="center"><h3>Ações</h3></td>
								<?php if($this->permissaoAcessoLog) {?>
								<td align="center"><h3>logs</h3></td>
								<?php }?>
			                </tr>
			            </thead>
			            <tbody>
			            <?php 
	                	$cor = 'par';
		            	foreach($this->resultadoPesquisa as $linha){
							$cor = ($cor=="par") ? "" : "par";?>
							<tr <?php if ($cor != '') { ?> class="<?=$cor?>" <?php } ?>>
								<td><?php echo $linha->geroid ?></td>
								<td><?php echo $linha->gernome ?></td>
								<td align="right"><?php echo $linha->gertdescricao ?></td>
								<td><?php echo $linha->gercnpj ?></td>
								<td><?php echo $linha->gerfone ?></td>
								<td align="center">
								<?php if($linha->possuiIntegracao == FALSE) {?>
									<input type="button" class="button" id="gerar_acesso_integracao" onclick="gerarAcessoIntegradora('<?php echo $linha->geroid ?>', '<?php echo $linha->gernome ?>')" value="Gerar Acesso Integração"/>
								<?php }?>
								<?php if($linha->possuiIntegracao == TRUE) {?>
									<input type="button" class="button" id="resetar_senha" onclick="resetarSenhaIntegradora('<?php echo $linha->geroid ?>', '<?php echo $linha->gernome ?>' )" value="Resetar Senha"/>
								<?php }?>
								</td>
								<?php if($this->permissaoAcessoLog) {?>
								<td align="center">
									<a class="arquivo_anexo"
										style="width: 10px; height: 10px;" 
										href="<?php echo $linha->arquivoLog ?>">
										<?php if($linha->arquivoLog == TRUE){ ?>
											<img src="images/download.png" alt="download" />
										<?php } ?>
									</a>
								</td>
								<?php } ?>
							</tr>
							<?php
						}
			            ?>			
			            </tbody>
					<?php } ?>
		            <tfoot>
		                <tr>
		                    <td colspan="8">
		                        <?php echo $textoResult ?>
		                    </td>
		                </tr>
		            </tfoot>
		        </table>
		    </div>
		</div>
</div>

<?php endif; ?>