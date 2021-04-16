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
		input {
            width: 90px;
            margin:5px 0 3px 0;
        }
		.campos_editar input {
            width: 170px;
            margin:5px 0 3px 0;
        }
		#btn_excluir {
            display:none;
        }
		select, #cmp_descricao_teste {
            width: 370px;
            margin:5px 0 3px 0;
        }
		.mensagem {
            width: 800px;
            margin:5px 0 3px 0;
        }
		#tbResultadoPesquisa {
            display: block;
        }
		.tdTitulo {
            padding-left: 4px;
            white-space: nowrap;
        }
		.linha_resultado {
            height: 22px;
        }
		#div_msg {
            width: 100%;
        }
        fieldset {
            background: none !important;
            float: left;
            margin: 5px 10px 5px 0;
            padding: 5px 10px 10px 10px;
            border: 1px solid #999;
            width: auto !important;
            width: 350px !important;
        }

        fieldset label {
            display: inline !important;;
            float: left;
            clear: right;
            margin: 3px 0 6px 0;
        }

        fieldset input {
            float: left;
            clear: left;
            padding:0px;
            margin:0px;
            margin-right: 3px;
            width:10px;
        }
	</style>

	<!-- JAVASCRIPT -->
	<script type="text/javascript" src="includes/js/auxiliares.js"></script>
	<script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
	<script type="text/javascript" src="modulos/web/js/cad_testes_ativacao.js"></script>
</head>
<div align="center"><br>
	<form name="formCadTesteAtivacao" id="formCadTesteAtivacao" method="POST" action="">
		<input name="acao" id="acao" value="cadastraNovoTeste" type="hidden"/>
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
										<h2 id="hSubTitulo">Dados do Teste:</h2>
									</td>
								</tr>
								<tr>
									<td>
										<table width="100%" align="center" cellpadding="10" >
											<tbody>
												<tr>
													<td width="14%" nowrap="nowrap"><label for="eptpoid">ID do Teste:</label></td>
													<td>
														<input type="text" id="eptpoid" name="eptpoid" value="<?=isset($_POST['eptpoid']) ? $_POST['eptpoid'] : '';?>" readonly="readonly" size="10px" style="background-color: #D3D3D3;"/>
														<span id="msgCampoAutomatico"> <label style="color: red">Gerado Automaticamente</label></span>

														<input type="hidden" id="epttoid" name="epttoid"/>

													</td>

													<td width="14%" nowrap="nowrap" class="campos_editar"><label for="cmp_data_cadastro">Data Cadastro : </label></td>
													<td class="campos_editar">
														<input type="text" id="cmp_data_cadastro" name="cmp_data_cadastro" />
													</td>
												</tr>
												<tr>
													<td width="14%" nowrap="nowrap"><label for="cb_grupo_testes">Grupo Testes:</label></td>
													<td>
														<select id="cb_grupo_testes" name="cb_grupo_testes">
															<option value="">Escolha</option>
														</select>
														<img id="pesq_grupo_progress" class="image-progress" src="images/progress.gif" />
													</td>

													<td width="14%" nowrap="nowrap" class="campos_editar"><label for="cmp_usu_cadastro">Usuário Cadastro: </label></td>
													<td class="campos_editar">
														<input type="text" id="cmp_usu_cadastro" name="cmp_usu_cadastro" />
													</td>
												</tr>
												<tr>
													<td width="14%" nowrap="nowrap"><label for="cmp_descricao_teste">Descrição do Teste: </label></td>
													<td>
														<input type="text" id="cmp_descricao_teste" name="cmp_descricao_teste" class="descricao_mensagem"/>
													</td>

													<td width="14%" nowrap="nowrap" class="campos_editar"><label for="cmp_ultima_alteracao">Última Alteração: </label></td>
													<td class="campos_editar">
														<input type="text" id="cmp_ultima_alteracao" name="cmp_ultima_alteracao" />
													</td>
												</tr>
												<tr>
													<td width="14%" nowrap="nowrap"><label for="cmp_sigla_teste">Sigla Teste: </label></td>
													<td>
														<input type="text" id="cmp_sigla_teste" name="cmp_sigla_teste" />
													</td>

													<td width="14%" nowrap="nowrap" class="campos_editar"><label for="cmp_usu_alteracao">Usuário Alteração: </label></td>
													<td class="campos_editar">
														<input type="text" id="cmp_usu_alteracao" name="cmp_usu_alteracao" />
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>

								<tr class="tableSubTitulo">
									<td>
										<h2 id="hSubTitulo">Configuração do Teste:</h2>
									</td>
								</tr>
								<tr>
									<td>
										<table width="100%" align="center" cellpadding="10" >
											<tbody>
												<tr>
													<td width="14%" nowrap="nowrap"><label for="cb_acao_teste">Ação do Teste: </label></td>
													<td>
														<select id="cb_acao_teste" name="cb_acao_teste">
															<option value="">Escolha</option>
															<option value="true">Ativação</option>
															<option value="false">Desativação</option>
														</select>
													</td>

													<td width="14%" nowrap="nowrap"><label for="cb_indica_telemetria">Indica Telemetria: </label></td>
													<td>
														<select id="cb_indica_telemetria" name="cb_indica_telemetria">
															<option value="">Escolha</option>
															<option value="true">Sim</option>
															<option value="false">Não</option>
														</select>
													</td>
												</tr>
												<tr>
													<td width="14%" nowrap="nowrap"><label for="cb_teste_satelital">Teste Satelital: </label></td>
													<td>
														<select id="cb_teste_satelital" name="cb_teste_satelital">
															<option value="">Escolha</option>
															<option value="true">Sim</option>
															<option value="false">Não</option>
														</select>
													</td>

													<td width="14%" nowrap="nowrap"><label for="cb_verifica_porta">Valida Posição: </label></td>
													<td>
														<select id="cb_valida_posicao" name="cb_valida_posicao">
															<option value="">Escolha</option>
															<option value="true">Sim</option>
															<option value="false">Não</option>
														</select>
													</td>
												</tr>
												<tr>
													<td width="14%" nowrap="nowrap"><label for="cb_envia_configuracao">Envia Configuração: </label></td>
													<td>
														<select id="cb_envia_configuracao" name="cb_envia_configuracao">
															<option value="">Escolha</option>
															<option value="true">Sim</option>
															<option value="false">Não</option>
														</select>
													</td>

													<td width="14%" nowrap="nowrap"><label for="cb_verifica_porta">Verifica Porta: </label></td>
													<td>
														<select id="cb_verifica_porta" name="cb_verifica_porta">
															<option value="">Escolha</option>
															<option value="true">Sim</option>
															<option value="false">Não</option>
														</select>
													</td>
												</tr>
												<tr>
													<td width="14%" nowrap="nowrap"><label for="cb_numero_ws_teste">Número WS Teste: </label></td>
													<td>
														<select id="cb_numero_ws_teste" name="cb_numero_ws_teste">
															<option value="">Escolha</option>
														</select>
													</td>
													<td width="14%" nowrap="nowrap"><label for="cb_exige_verificacao">Exige Tela de Verificação: </label></td>
													<td>
														<select id="cb_exige_verificacao" name="cb_exige_verificacao">
															<option value="true">Sim</option>
															<option value="false" selected="selected">Não</option>
														</select>
													</td>
												</tr>

											</tbody>
										</table>
									</td>
								</tr>


								<tr class="tableSubTitulo">
									<td>
										<h2 id="hSubTitulo">Dados da Instrução:</h2>
									</td>
								</tr>
								<tr>
									<td>
										<table width="100%" align="center" cellpadding="10" >
											<tbody>
												<tr>
													<td width="14%" nowrap="nowrap"><label for="cmp_instrucao_teste">Instrução do Teste: </label></td>
													<td>
														<input type="text" id="cmp_instrucao_teste" name="cmp_instrucao_teste" class="mensagem" />
													</td>
												</tr>
												<tr>
													<td width="14%" nowrap="nowrap"><label for="cmp_teste_sucesso">Mensagem Teste Sucesso: </label></td>
													<td>
														<input type="text" id="cmp_teste_sucesso" name="cmp_teste_sucesso" class="mensagem" />
													</td>
												</tr>
												<tr>
													<td width="14%" nowrap="nowrap"><label for="cmp_teste">Mensagem Teste em Andamento: </label></td>
													<td>
														<input type="text" id="cmp_teste" name="cmp_teste" class="mensagem" />
													</td>
												</tr>
												<tr>
													<td width="14%" nowrap="nowrap"><label for="cmp_teste_insucesso">Mensagem Teste Insucesso: </label></td>
													<td>
														<input type="text" id="cmp_teste_insucesso" name="cmp_teste_insucesso" class="mensagem" />
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>

                                <tr class="tableSubTitulo">
                                    <td>
                                        <h2 id="hSubTitulo">Obrigatoriedade do Teste:</h2>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table width="100%" align="center" cellpadding="10" >
                                            <tbody>
                                                <tr>
                                                    <td width="14%" nowrap="nowrap"><label for="cb_teste_obrigatorio">Teste Obrigatório: </label></td>
                                                    <td>
                                                        <select id="cb_teste_obrigatorio" name="cb_teste_obrigatorio">
                                                            <option value="">Escolha</option>
                                                            <option value="true">Sim</option>
                                                            <option value="false">Não</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                 <tr>
                                                     <td width="14%" nowrap="nowrap"><label>Tipo de Ordem de Serviço: </label></td>
                                                    <td>
                                                       <fieldset id="tipo_os">
                                                        <?php foreach ($this->view->tipo_os as $key => $value) :?>
                                                            <input id="eptpostoid_teste_obrigatorio_<?php echo $value->ostoid; ?>"
                                                                    name="eptpostoid_teste_obrigatorio[]"
                                                                    value="<?php echo $value->ostoid; ?>" type="checkbox" disabled="true"
                                                                    <?php  echo (!isset($this->view->teste->tipo_os) && $value->ostoid == 4) ? 'checked="true"' : '' ;?>/>
                                                                    <label for="resultado_tela"><?php echo $value->ostdescricao; ?></label><br/>
                                                        <?php endforeach; ?>
                                                        </fieldset>
                                                    </td>
                                                </tr>
                                        </tbody>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Botão de ações -->
                                <tr class="tableRodapeModelo1" id="trBotoesPesquisa">
                                    <td colspan="2" align="center">
                                        <input type="button" name="btn_salvar" id="btn_salvar" value="Salvar" class="botao" onclick="salvarTesteAtivao()" />
                                        <input type="button" name="btn_excluir" id="btn_excluir" value="Excluir" class="botao" onclick="excluirTesteAtivacao()" />
                                        <input type="button" name="btn_voltar" id="btn_voltar" value="Voltar" class="botao" onclick="window.location.href='cad_testes_ativacao.php';" />
                                    </td>
                                </tr>

							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
			 <tr>
                <td align="center"><span id="loading" class="msg"></span></td>
            </tr>
		</table>
	</form>
</div>

<?php
	require_once 'lib/rodape.php';
?>