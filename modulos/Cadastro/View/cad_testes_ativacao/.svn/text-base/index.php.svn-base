<?php
/**
 * @author	Leandro Alves Ivanaga
 * @email	leandroivanaga@brq.com
 * @since	30/01/2013
 */
?>

<head>
	<!-- CSS -->
	<link rel="stylesheet" href="includes/css/base_form.css" type="text/css">
	<style type="text/css">
		input { width: 90px; }
		select, #cmp_descricao_teste { width: 370px; margin:5px 0 3px 0; }
		#tbResultadoPesquisa { display: none; }
		.processando { display:none; margin-bottom: 10px; }
		.tdTitulo { padding-left: 4px; white-space: nowrap; }
		.linha_resultado { height: 22px;}
		#div_msg ( width: 100%; )
	</style>

	<!-- JAVASCRIPT -->
	<script type="text/javascript" src="includes/js/auxiliares.js"></script>
	<script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
	<script type="text/javascript" src="modulos/web/js/cad_testes_ativacao.js"></script>
</head>
<div align="center"><br>
	<form name="formPesqTesteAtivacao" id="formPesqTesteAtivacao" method="POST" action="">
		<input name="acao" id="acao" value="pesquisar" type="hidden" />
		<input name="eptpoid" id="eptpoid" value="" type="hidden" />
		<table width="98%" class="tableMoldura">
			<tbody>
				<tr class="tableTitulo">
					<td>
						<h1>Cadastro de Testes de Ativação</h1>
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
													<td width="13%" nowrap="nowrap"><label for="cb_grupo_testes">Grupo Testes:</label></td>
													<td>
														<select id="cb_grupo_testes" name="cb_grupo_testes">
															<option value="">Escolha</option>
														</select>
														<img id="pesq_grupo_progress" class="image-progress" src="images/progress.gif" />
													</td>
												</tr>
												<tr>
													<td nowrap="nowrap"><label for="cmp_descricao_teste">Descrição do Teste: </label></td>
													<td>
														<input type="text" id="cmp_descricao_teste" name="cmp_descricao_teste" />
													</td>
												</tr>
												<tr>
													<td nowrap="nowrap"><label for="cmp_sigla_teste">Sigla Teste: </label></td>
													<td>
														<input type="text" id="cmp_sigla_teste" name="cmp_sigla_teste" />
													</td>
												</tr>
												<tr class="tableRodapeModelo1" id="trBotoesPesquisa">
													<td colspan="2" align="center">
														<input type="button" name="btn_pesquisar" id="btn_pesquisar" value="Pesquisar" class="botao" />
														<input type="button" name="btn_novo" id="btn_novo" value="Novo" class="botao" onclick="exibeCadastro()" />
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
								<td colspan="12">
									<h2>Resultado da Pesquisa</h2>
								</td>
							</tr>
							<tr class="tableTituloColunas">
								<td class="tdTitulo" width="5%"><b>ID</b></td>
								<td class="tdTitulo" width="12%"><b>Grupo Teste</b></td>
								<td class="tdTitulo" width="25%"><b>Descrição Teste</b></td>
								<td class="tdTitulo" width="7%"><b>Sigla Teste</b></td>
								<td class="tdTitulo" width="5%"><b>Ação</b></td>
								<td class="tdTitulo"><b>Verifica Porta</b></td>
								<td class="tdTitulo"><b>Envia Configuração</b></td>
								<td class="tdTitulo"><b>Telemetria</b></td>
								<td class="tdTitulo"><b>Satelital</b></td>
								<td class="tdTitulo"><b>Valida Posição</b></td>
								<td class="tdTitulo" align="center" width="3%">&nbsp;</td>
								<td class="tdTitulo" align="center" width="3%">&nbsp;</td>
							</tr>
		    			</table>
	    			</td>
	   			</tr>
	   			<tr>
	   				<td align="center" colspan="12">
            			<img class="processando" src="images/loading.gif" alt="Carregando..." />
            			<span id="loading" class="msg"></span>
            		</td>
	   			</tr>
			</tbody>
		</table>
	</form>
</div>
<?php
	require_once 'lib/rodape.php';
?>