<head>
	<!-- CSS -->
	<link rel="stylesheet" href="includes/css/base_form.css" type="text/css">
	<style type="text/css">
		input  { width: 90px; }
		select { width: 350px; margin:5px 0 3px 0; }
		#tbResultadoPesquisa, #tbObrigacoesVinculadas, #trBotoesInclusao { display: none; }
		.processando { display:none; margin-bottom: 10px; }
		.tdTitulo { padding-left: 4px; }
		#div_msg ( width: 100%; )
	</style>

	<!-- JAVASCRIPT -->
	<script type="text/javascript" src="includes/js/auxiliares.js"></script>
	<script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
	<script type="text/javascript" src="modulos/web/js/cad_teste_obrigacao_fin.js"></script>
</head>
<div align="center"><br>
	<form name="formPesqTesteObrig" id="formPesqTesteObrig" method="POST">
		<table width="98%" class="tableMoldura">
			<tbody>
				<tr class="tableTitulo">
					<td>
						<h1>Cadastro de Teste X Obrigações Financeiras</h1>
					</td>
				</tr>
				<tr height="10" class="div_msg" >
					<td width="100%" colspan="4">
						<div id="div_msg" class="msg"></div>
				    </td>
				</tr>
				<tr>
					<td align="center">
						<br>
						<table width="98%" class="tableMoldura" align="center" id="tbPesquisa">
							<tbody>
								<tr class="tableSubTitulo">
									<td>
										<h2 id="hSubTitulo">Dados para Pesquisa</h2>
									</td>
								</tr>
								<tr>
									<td>
										<table width="100%" align="center" cellpadding="10" >
											<tbody>
												<tr>
													<td width="13%" nowrap="nowrap"><label for="cb_pesq_teste">Testes:</label></td>
													<td>
														<select id="cb_pesq_teste" name="cb_pesq_teste">
															<option value="">Escolha</option>
														</select>
														<img id="pesq_teste_progress" class="image-progress" src="images/progress.gif" />
													</td>
												</tr>
												<tr>
													<td nowrap="nowrap"><label for="cb_pesq_obrigacao">Obrigações Financeiras: </label></td>
													<td>
														<select id="cb_pesq_obrigacao" name="cb_pesq_obrigacao">
															<option value="">Escolha</option>
														</select>
														<img id="pesq_obrig_progress" class="image-progress" src="images/progress.gif" />
													</td>
												</tr>   
												<tr class="tableRodapeModelo1" id="trBotoesPesquisa">
													<td colspan="2" align="center">
														<input type="button" name="btn_pesquisar" id="btn_pesquisar" value="Pesquisar" class="botao" />
														<input type="button" name="btn_novo" id="btn_novo" value="Novo" class="botao" />
													</td>
												</tr>
												<tr class="tableRodapeModelo1" id="trBotoesInclusao">
													<td colspan="2" align="center">
														<input type="button" name="btn_salvar" id="btn_salvar" value="Salvar" class="botao" />
														<input type="button" name="btn_voltar" id="btn_voltar" value="Voltar" class="botao" />
													</td>
												</tr>
											</tbody>
										</table>			
									</td>
								</tr>
							</tbody>
						</table>        
					</td>
				</tr>
				<tr>
	    			<td align="center">
	    				
	    				<!-- RESULTADO PESQUISA -->
		    			<table class="tableMoldura" id="tbResultadoPesquisa">
		    				<tr class="tableSubTitulo">
								<td colspan="3">
									<h2>Resultado da Pesquisa</h2>
								</td>
							</tr>
							<tr class="tableTituloColunas">
								<td class="tdTitulo"><b>Teste</b></td>
								<td class="tdTitulo"><b>Obrigação Financeira</b></td>
								<td class="tdTitulo" align="center">&nbsp;</td>
							</tr>
		    			</table>
		    			
		    			<!-- ITENS CADASTRO -->
		    			<table class="tableMoldura" id="tbObrigacoesVinculadas">
		    				<tr class="tableSubTitulo">
								<td colspan="3">
									<h2>Obrigações Financeiras Vinculadas ao Teste</h2>
								</td>
							</tr>
							<tr class="tableTituloColunas">
								<td class="tdTitulo"><b>Obrigação Financeira</b></td>
								<td align="center">&nbsp;</td>
							</tr>
		    			</table>
	    			</td>
	   			</tr>
	   			<tr>
	   				<td align="center" colspan="4">
            			<img class="processando" src="images/loading.gif" alt="Carregando..." />
            		</td>
	   			</tr>
			</tbody>
		</table>
	</form>
</div>
<?php
	require_once 'lib/rodape.php'; 
?>