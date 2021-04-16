<?php 
cabecalho();
include("calendar/calendar.js");
include("lib/funcoes.js");
?>
	<link rel="stylesheet" href="calendar/calendar.css" type="text/css"  />    
    <link type="text/css" rel="stylesheet" href="includes/css/base_form.css">
    <link rel="stylesheet" href="modulos/web/css/rel_acompanhamento_os.css" type="text/css"  />
    <link rel="stylesheet" href="modulos/web/css/lib/loading.css" type="text/css"  />    
    <script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>       
    <script type="text/javascript" src="modulos/web/js/lib/loading.js"></script>       
    <script type="text/javascript" src="js/jquery.validate.js"></script>   
    <script type="text/javascript" src="includes/js/mascaras.js"></script>
    <script type="text/javascript" src="includes/js/validacoes.js"></script> 
    <script type="text/javascript" src="includes/js/auxiliares.js"></script>
    <script type="text/javascript" src="modulos/web/js/rel_acompanhamento_os.js"></script>
 
	<br />
		<form name="filtro">
		<!--  Controlador de Ações -->
		<input type="hidden" id="acao" name="acao" />
		<div align="center">
			<table class="tableMoldura" width="98%">
				<thead>
					<tr class="tableTitulo">
						<td>
							<h1>Acompanhamento de Ordens de Serviço</h1>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="center">
							<br /> 
							<table class="tableMoldura" width="98%">
								<thead>
									<tr class="tableSubTitulo">
										<td colspan="8">
											<h2>Dados para pesquisa</h2>
										</td>
									</tr>
								</thead>
								<tbody class="corpo_filtro">
									<!-- FILTROS -->
								
									<!-- Filtros linha 1 -->
									<tr>
										<!-- Campo Período -->
										<td class="campo_filtro1" nowrap="nowrap">
											<label for="dt_ini" >Período: *</label>
										</td>
										<td class="campo_filtro_input1" nowrap="nowrap">
											<input type="text" class="form_field" value="" maxlength="10" size="10" id="dt_ini" name="dt_ini"> 
											<img align="middle" border="0" alt="Calendário..." src="images/calendar_cal.gif" id="img_dt_ini">
											à <input type="text" class="form_field" value="" maxlength="10" size="10" id="dt_fim" name="dt_fim"> 
											<img align="middle" border="0" alt="Calendário..." src="images/calendar_cal.gif" id="img_dt_fim">
										</td>
										
										<!-- Campo Tipo Relatório -->
										<td class="campo_filtro" nowrap="nowrap">
											<label for="tipo_relatorio">Tipo de Relatório:</label>
										</td>
										<td class="campo_filtro_input" nowrap="nowrap" align="left">
											<select name="tipo_relatorio">
											<?php foreach ($tiposRelatorio as $id=>$descricao): ?>
												<option <?php echo $id == $_POST['tipo_relatorio'] ? 'selected="selected"' : '' ?> <?php (isset($_POST['tipo_relatorio']) && $_POST['tipo_relatorio'] == $id) ? 'selected' : ''; ?>  value="<?php echo $id; ?>">
													<?php echo $descricao; ?>
												</option>
											<?php endforeach;?>
											</select>
										</td>
									</tr>
									
									<!-- Filtros linha 2 -->
									<tr>
									
										<!-- Campo Status -->
										<td class="campo_filtro1" nowrap="nowrap">
											<label for="status">Status:</label>
										</td>
										<td class="campo_filtro_input1" nowrap="nowrap">
											<select name="status">
												<option value="">Todos</option>
												<?php foreach ($statusList as $id=>$descricao):?>
													<option <?php echo $id == $_POST['tipo'] ? 'selected="selected"' : '' ?> value="<?php echo $id?>"><?php echo $descricao?></option>
												<?php endforeach;?>
											</select>
										</td>
										
										<!-- Campo Modelo Equipamento -->
										<td class="campo_filtro" nowrap="nowrap">
											<label for="modelo_equipamento">Modelo de Equipamento:</label>
										</td>
										<td class="campo_filtro_input" nowrap="nowrap">
											<select name="modelo_equipamento">
												<option value="">Todos</option>
												<?php foreach ($modeloEquipamentoList as $id=>$descricao): ?>
													<option <?php echo $id == $_POST['modelo_equipamento'] ? 'selected="selected"' : '' ?> value="<?php echo $id?>"><?php echo $descricao?></option>
												<?php endforeach;?>
											</select>
										</td>
									</tr>
									
									<!-- Filtros linha 3 -->
									<tr>
									
										<!-- Campo Tipo Solicitação -->
										<td class="campo_filtro1" nowrap="nowrap">
											<label for="tipo_solicitacao">Tipo Solicitação:</label>
										</td>
										<td class="campo_filtro_input1" nowrap="nowrap">
											<select name="tipo_solicitacao">
												<option value="">Todos</option>
												<?php foreach ($tipoSolicitacaoList as $id=>$descricao):?>
													<option <?php echo $id == $_POST['tipo'] ? 'selected="selected"' : '' ?> value="<?php echo $id;?>"><?php echo $descricao?></option>
												<?php endforeach;?>
											</select>
										</td>
										
										<!-- Campo Versão do Equipamento -->
										<td class="campo_com_load" nowrap="nowrap">
											<label for="versao_equipamento">Versão de Equipamento:</label>
											<img class="img_progress" id="versao_equipamento_progress" alt="Carregando..." src="images/progress4.gif"  />
										</td>
										<td class="campo_filtro_input" nowrap="nowrap">
											<select name="versao_equipamento">
												<option value="">Todos</option>
											</select>
										</td>
									</tr>
									
									<!-- Filtros linha 4 -->
									<tr>
									
										<!-- Campo Tipo Contrato -->
										<td class="campo_filtro1" nowrap="nowrap">
											<label for="tipo_contrato">Tipo Contrato:</label>
										</td>
										<td class="campo_filtro_input1" nowrap="nowrap">
											<select name="tipo_contrato[]" id="tipo_contrato" multiple>
												<option value="">Todos</option>
												<?php foreach ($tipoContratoList as $id => $descricao):?>
													<option <?php echo ($id == $_POST['tipo_contrato'] && isset($_POST['tipo_contrato'])) ? 'selected="selected"' : '' ?> value="<?php echo $id;?>"><?php echo $descricao?></option>
												<?php endforeach;?>
											</select>
										</td>
										
										<!-- Campo Defeito Alegado -->
										<td class="campo_filtro1" nowrap="nowrap">
											<label for="defeito_alegado">Defeito Alegado:</label>
										</td>
										<td class="campo_filtro_input2" nowrap="nowrap">
											<select name="defeito_alegado">
												<option value="">Todos</option>
												<?php foreach ($defeitosList as $id=>$descricao): ?>
													<option <?php echo $id == $_POST['defeito_alegado'] ? 'selected="selected"' : '' ?> value="<?php echo $id;?>"><?php echo $descricao?></option>
												<?php endforeach;?>
											</select>
										</td>
									</tr>
									
									<!-- Filtros linha 5 -->
									<tr>
									
										<!-- Campo Classe Contrato -->
										<td class="campo_filtro1" nowrap="nowrap">
											<label for="classe_contrato">Classe Contrato:</label>
										</td>
										<td class="campo_filtro_input1" nowrap="nowrap">
											<select name="classe_contrato">
												<option value="">Todos</option>
												<?php foreach ($equipamentoClasseList as $id=>$descricao): ?>
													<option <?php echo $id == $_POST['classe_contrato'] ? 'selected="selected"' : '' ?> value="<?php echo $id;?>"><?php echo $descricao?></option>
												<?php endforeach;?>
											</select>
										</td>
										
										<!-- Campo Defeito Constatado -->
										<td class="campo_filtro" nowrap="nowrap">
											<label for="defeito_constatado">Defeito Constatado:</label>
										</td>
										<td class="campo_filtro_input2" nowrap="nowrap">
											<select name="defeito_constatado">
												<option value="">Todos</option>
												<?php foreach ($defeitosList as $id=>$descricao): ?>
													<option <?php echo $id == $_POST['defeito_constatado'] ? 'selected="selected"' : '' ?> value="<?php echo $id;?>"><?php echo $descricao?></option>
												<?php endforeach;?>
											</select>
										</td>
									</tr>
									
									<!-- Filtros linha 6 -->
									<tr>
									
										<!-- Campo Cliente -->
										<td class="campo_filtro1" nowrap="nowrap">
											<label for="cliente">Cliente:</label>
										</td>
										<td class="campo_filtro_input" nowrap="nowrap">
											<input class="campo_cliente" type="text" name="cliente" /> 
										</td>
										
										<!-- Campo Responsável Abertura -->
										<td class="campo_filtro" nowrap="nowrap">
											<label for="responsavel_abertura">Responsável Abertura:</label>
										</td>
										<td class="campo_filtro_input2" nowrap="nowrap">
											<select name="responsavel_abertura">
												<option value="">Todos</option>
												<?php foreach ($usuariosList as $id=>$nome): ?>
													<option <?php echo $id == $_POST['responsavel_abertura'] ? 'selected="selected"' : '' ?> value="<?php echo $id;?>"><?php echo $nome?></option>
												<?php endforeach;?>
											</select>
										</td>
									</tr>
									
									<!-- Filtros linha 7 -->
									<tr>
									
										<!-- Campo Placa -->
										<td class="campo_filtro1" nowrap="nowrap">
											<label for="placa">Placa:</label>
										</td>
										<td class="campo_filtro_input" nowrap="nowrap">
											<input type="text" name="placa" size="7" maxlength="7" />
										</td>
										
										<!-- Campo Responsável Autorização -->
										<td class="campo_filtro" nowrap="nowrap">
											<label for="responsavel_autorizacao">Responsável Autorização:</label>
										</td>
										<td class="campo_filtro_input2" nowrap="nowrap">
											<select name="responsavel_autorizacao">
												<option value="">Todos</option>
												<?php foreach ($usuariosList as $id=>$nome): ?>
													<option <?php echo $id == $_POST['responsavel_autorizacao'] ? 'selected="selected"' : '' ?> value="<?php echo $id;?>"><?php echo $nome?></option>
												<?php endforeach;?>
											</select>
										</td>
									</tr>
									
									<!-- Filtros linha 8 -->
									<tr>
									
										<!-- Campo item -->
										<td class="campo_filtro1" nowrap="nowrap">
											<label for="item">Item:</label>
										</td>
										<td class="campo_filtro_input1" nowrap="nowrap">
											<select name="item">
												<option value="">Todos</option>
												<?php foreach ($itemList as $val=>$descricao): ?>
													<option <?php echo $val == $_POST['item'] ? 'selected="selected"' : '' ?> value="<?php echo $val;?>"><?php echo $descricao?></option>
												<?php endforeach;?>
											</select>
										</td>
										
										<!-- Campo Responsável Cancelamento -->
										<td class="campo_filtro" nowrap="nowrap">
											<label for="responsavel_cancelamento">Responsável Cancelamento:</label>
										</td>
										<td class="campo_filtro_input2" nowrap="nowrap">
											<select name="responsavel_cancelamento">
												<option value="">Todos</option>
												<?php foreach ($usuariosList as $id=>$nome): ?>
													<option <?php echo $id == $_POST['responsavel_cancelamento'] ? 'selected="selected"' : '' ?> value="<?php echo $id;?>"><?php echo $nome?></option>
												<?php endforeach;?>
											</select>
										</td>
									</tr>
									
									<!-- Filtros linha 9 -->
									<tr>
									
										<!-- Campo Tipo -->
										<td class="campo_filtro1" nowrap="nowrap">
											<label for="tipo">Tipo:</label>
										</td>
										<td class="campo_filtro_input1" nowrap="nowrap">
											<select name="tipo">
												<option value="">Todos</option>
												<?php foreach ($tipoOsList as $id=>$descricao): ?>
													<option <?php echo $id == $_POST['tipo'] ? 'selected="selected"' : '' ?> value="<?php echo $id;?>"><?php echo $descricao?></option>
												<?php endforeach;?>
											</select>
										</td>
										
										<!-- Campo Responsável Conclusão -->
										<td class="campo_filtro" nowrap="nowrap">
											<label for="responsavel_conclusao">Responsável Conclusão:</label>
										</td>
										<td class="campo_filtro_input2" nowrap="nowrap">
											<select name="responsavel_conclusao">
												<option value="">Todos</option>
												<?php foreach ($usuariosList as $id=>$nome): ?>
													<option <?php echo $id == $_POST['responsavel_conclusão'] ? 'selected="selected"' : '' ?> value="<?php echo $id;?>"><?php echo $nome?></option>
												<?php endforeach;?>
											</select>
										</td>
									</tr>
									
									<!-- Filtros linha 10 -->
									<tr>
									
										<!-- Campo Motivo -->
										<td class="campo_filtro1 campo_com_load"  nowrap="nowrap">
											<label for="motivo">Motivo:</label>
											<img class="img_progress" id="motivo_progress" alt="Carregando..." src="images/progress4.gif"  />
										</td>
										<td class="campo_filtro_input1" nowrap="nowrap">
											<select name="motivo">
												<option value="">Todos</option>
											</select>
										</td>
									
									</tr>
								</tbody>
								
								<tfoot>
									<tr class="tableRodapeModelo1">
										<td align="center" colspan="8">
											<input type="button" class="botao" name="pesquisar" value="Pesquisar" />
											<input type="button" class="botao" name="gerar_xls" value="Gerar XLS" />
										</td>
									</tr>
								</tfoot>
							</table>
							
							<div id="resultado_progress" align="center" style="display:none">
								<img src="modulos/web/images/loading.gif" alt="Carregando..." />
							</div>
							
							<div id="resultado_relatorio">
								
							</div>
							
						</td>
					</tr>
				</tbody>
			</table>
			</div>
		</form>
<?php 
include ("lib/rodape.php");