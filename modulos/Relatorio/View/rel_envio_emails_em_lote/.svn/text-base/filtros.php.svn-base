<?php 
cabecalho();
include("calendar/calendar.js");
include("lib/funcoes.js");
?>
	<link rel="stylesheet" href="calendar/calendar.css" type="text/css"  />    
    <link type="text/css" rel="stylesheet" href="includes/css/base_form.css">
    <link rel="stylesheet" href="modulos/web/css/rel_envio_emails_em_lote.css" type="text/css"  />
    <script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>       
    <!--<script type="text/javascript" src="js/jquery.validate.js"></script>-->   
    <script type="text/javascript" src="includes/js/mascaras.js"></script>
    <script type="text/javascript" src="includes/js/validacoes.js"></script> 
    <script type="text/javascript" src="includes/js/auxiliares.js"></script>
    <script type="text/javascript" src="modulos/web/js/rel_envio_emails_em_lote.js"></script>
 	<!--<script type="text/javascript" src="modulos/web/js/lib/alerta.js"></script>-->
 	
	<br />
		<form name="filtro" method="post">
		<!--  Controlador de Ações -->
		<input type="hidden" id="acao" name="acao" />
		<div align="center">
			<table class="tableMoldura" width="98%">
				<thead>
					<tr class="tableTitulo">
						<td>
							<h1>Envio de Emails em Lote</h1>
						</td>
					</tr>
				</thead>
				<tbody>
                    <?php if ($mensagemInformativa != ''): ?>
                    <tr>
                        <td>&nbsp</td>
                    </tr>
                    <tr>
                        <td class="msg">
                            <?php echo $mensagemInformativa; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp</td>
                    </tr>
                    <?php endif;?>
					<tr>
						<td align="center">
							<br /> 
							<table class="tableMoldura" width="98%">
								<thead>
									<tr class="tableSubTitulo">
										<td colspan="2">
											<h2>Dados para pesquisa</h2>
										</td>
									</tr>
								</thead>
								<tbody class="corpo_filtro">
                                    <tr>
                                        <td colspan="2">
                                            &nbsp;
                                        </td>
                                    </tr>
									<!-- FILTROS -->
								
									<!-- Filtros linha 1 -->
									<tr>
										<!-- Campo Período -->
										<td width="15%" nowrap="nowrap">
											<label for="dt_ini" >Período: *</label>
										</td>
										<td nowrap="nowrap">
											<input type="text" class="float-left" value="<?php echo date('d/m/Y') ?>" maxlength="10" size="10" id="dt_ini" name="dt_ini"> 
											<img id="img_dt_ini" class="float-left calendar-margin" align="middle" border="0" alt="Calendário..." src="images/calendar_cal.gif">
                                            <span class="float-left calendar-label-margin">a</span> 
                                            <input type="text" class="float-left" value="<?php echo date('d/m/Y') ?>" maxlength="10" size="10" id="dt_fim" name="dt_fim"> 
											<img id="img_dt_fim" class="float-left calendar-margin" align="middle" border="0" alt="Calendário..." src="images/calendar_cal.gif">
										</td>
									</tr>
									
									<!-- Filtros linha 2 -->
									<tr>
									
										<!-- Campo Placa -->
										<td width="15%" nowrap="nowrap">
											<label for="placa">Placa:</label>
										</td>
										<td nowrap="nowrap">
											<input type="text" maxlength="7" class="small" id="placa" name="placa" />
										</td>
									</tr>
									
									<!-- Filtros linha 3 -->
									<tr>
									
										<!-- Campo Ver Insucessos -->
										<td width="15%" nowrap="nowrap">
											<label for="chassi">Chassi:</label>
										</td>
										<td nowrap="nowrap">
											<input type="text" maxlength="50" class="small" id="chassi" name="chassi" />
										</td>
									</tr>																			
									
									<!-- Filtros linha 4 -->	
									<tr>
										<!-- Campo Nome do Cliente -->
										<td width="15%" nowrap="nowrap">
											<label for="nome_cliente">Nome do Cliente:</label>
										</td>
										<td nowrap="nowrap">
											<input type="text" maxlength="50" class="medium" id="nome_cliente" name="nome_cliente" />
										</td>
									</tr>
									
									<!-- Filtros linha 5 -->	
									<tr>
										<!-- Combo sucesso de envio -->
										<td width="15%" nowrap="nowrap">
											<label for="sucesso_envio">Ver sucesso de envio:</label>
										</td>
										<td nowrap="nowrap">
											<select id="sucesso_envio" class="small" name="sucesso_envio">
                                                <option selected="selected" value="">Todos</option>
                                                <option value="0">Não</option>
                                                <option value="1">Sim</option>
                                            </select>
										</td>
									</tr>
                                    <tr>
                                        <td colspan="2">
                                            &nbsp;
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <span style="margin-left: 8px;">(*) Campos de preenchimento obrigatório.</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            &nbsp;
                                        </td>
                                    </tr>
								</tbody>
								
								<tfoot>
									<tr class="tableRodapeModelo1">
										<td align="center" colspan="2">
                                            <button type="button" name="pesquisar" class="btn_pesquisar">Pesquisar</button>											
										</td>
									</tr>
								</tfoot>
							</table>
							
							<div id="resultado_progress" align="center" style="display:none">
								<img src="modulos/web/images/loading.gif" alt="Carregando..." />
							</div>
							
							<div id="resultado_relatorio" style="display:none">
								<table  class="tableMoldura" width="98%">
									<thead>
										<tr class="tableSubTitulo">
											<td>
												<h2>Download</h2> 
											</td>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td align="center">
												<div id="resultado_relatorio_container" align="center"></div>
											</td>
										</tr>
									</tbody>
								</table>
							</div>							
						</td>
					</tr>
				</tbody>
			</table>
			</div>
		</form>
<?php 
include ("lib/rodape.php");