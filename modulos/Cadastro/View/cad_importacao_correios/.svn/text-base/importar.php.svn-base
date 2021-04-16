<?php 
include_once '_header.php'; ?>

<form name="frm_importar" id="frm_importar" method="POST" action="cad_importacao_correios.php?acao=importar" enctype="multipart/form-data">
	<input type="hidden" name="acao" id="acao" />
	<div class="modulo_titulo">Atualização da Base dos Correios</div>
	<div class="modulo_conteudo">
	
		<?php include_once '_msgPrincipal.php';?>
		<div class="mensagem info" >Os campos com * são obrigatórios.</div>
		
		<div class="bloco_titulo">Importação</div>
		<div class="bloco_conteudo">		
            <div class="formulario"> 
                            
                <div class="campo maior">					
                    <label for="arquivo_zip">Arquivo *</label>
                    <input type="file" id="arquivo_zip" name="arquivo_zip" />
                </div>					
                <div class="clear"></div>				
                        
            </div>					
		</div>        
		<div class="bloco_acoes">
			<button type="button" id="importar">Importar</button>
		</div>		
		
		
		<div class="separador"></div>		
		<div id="loading" style="display:none;"><center><img src="images/loading.gif" alt="" /></center></div>	
		
		<?php
			$rows = $this->dao->getHistorico(1);		
			if($rows){
		?>
		<div class="resultado" id="content_log">
			<div class="bloco_titulo">Log de Importação</div>
			<div class="bloco_conteudo">
				<div class="listagem">
				
					<table>
						<thead>
							<tr>
								<th>Data</th>
								<th>Usuário</th>
								<th>Status</th>
								<th>Observação</th>
							</tr>
						</thead>
						<tbody>	
						<?php
						$class="";
						if($rows):
							foreach ($this->dao->getHistorico(1) as $historico): 
								$class = ($class=="" ? "class=\"par\"" : "") 
								?>
								<tr <?=$class?>>
									<td class="direita" width="25px"><?=$historico['hacdt_atualizacao']?></td>
									<td><?=$historico['nm_usuario']?></td>
									<td>
									<?php 
										$hacstatus=$historico['hacstatus'];
										if($hacstatus==CadImportacaoCorreios::EM_PROCESSAMENTO)
											echo "Em processamento";
										elseif ($hacstatus==CadImportacaoCorreios::FALHA_IMPORTACAO)
											echo "Falha na importação";
										elseif ($hacstatus==CadImportacaoCorreios::IMPORTADO_COM_SUCESSO)
											echo "Importado com sucesso";
									?>
									</td>
									<td><?=$historico['hacobservacao']?></td>										             
								</tr>
								<?php 
							endforeach; 
						endif;
						?>
						</tbody>
					</table>
				</div>

			</div>
	   </div>
	   <?php } ?>
	   <br />
		<div class="resultado" id="content_log">
			<div class="bloco_titulo">Arquivos</div>
			<div class="bloco_conteudo">
					<div class="conteudo">
						<fieldset>
							<legend>Legenda</legend>
							<ul>
								<li><img src="images/apr_bom.gif" alt="Exemplo 1" /> Arquivo Existente</li>
								<li><img src="images/apr_ruim.gif" alt="Exemplo 3" /> Arquivo Inexistente</li>
							</ul>
						</fieldset>
					</div>
				<div class="listagem">
					<table>
						<tbody>
							<?php 
							echo $this->gerarTabelaArquivos(); ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	
</form>
