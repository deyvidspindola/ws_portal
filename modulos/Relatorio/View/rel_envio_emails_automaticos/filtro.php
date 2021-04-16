<?php 
cabecalho();
include("calendar/calendar.js");
include("lib/funcoes.js");
?>
	<link rel="stylesheet" href="calendar/calendar.css" type="text/css"  />    
    <link type="text/css" rel="stylesheet" href="includes/css/base_form.css">
    <link rel="stylesheet" href="modulos/web/css/rel_envio_emails_automaticos.css" type="text/css"  />
    <script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>       
    <script type="text/javascript" src="js/jquery.validate.js"></script>   
    <script type="text/javascript" src="includes/js/mascaras.js"></script>
    <script type="text/javascript" src="includes/js/validacoes.js"></script> 
    <script type="text/javascript" src="includes/js/auxiliares.js"></script>
    <script type="text/javascript" src="modulos/web/js/rel_envio_emails_automaticos.js"></script>
 
	<br />
		<form name="filtro" method="post">
		<!--  Controlador de Ações -->
		<input type="hidden" id="acao" name="acao" />
		<div align="center">
			<table class="tableMoldura" width="98%">
				<thead>
					<tr class="tableTitulo">
						<td>
							<h1>Relatório Envio de E-mails Automáticos</h1>
						</td>
					</tr>
				</thead>
				<tbody>
				<?php if ($mensagemInformativa != ''): ?>
					<tr>
						<td></td>
					</tr>
					<tr>
						<td class="msg">
							<?php echo $mensagemInformativa; ?>
						</td>
					</tr>
					<?php endif;?>
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
										<td class="field_label" nowrap="nowrap">
											<label for="dt_ini" >Período: *</label>
										</td>
										<td nowrap="nowrap">
											<input type="text" class="form_field" value="<?php echo $_POST['dt_ini']?>" maxlength="10" size="10" id="dt_ini" name="dt_ini"> 
											<img id="img_dt_ini" align="middle" border="0" alt="Calendário..." src="images/calendar_cal.gif">
											à <input type="text"  class="form_field" value="<?php echo $_POST['dt_fim']?>" maxlength="10" size="10" id="dt_fim" name="dt_fim"> 
											<img id="img_dt_fim" align="middle" border="0" alt="Calendário..." src="images/calendar_cal.gif">
										</td>
									</tr>
									
									<!-- Filtros linha 2 -->
									<tr>
									
										<!-- Campo Tipo OS -->
										<td class="field_label" nowrap="nowrap">
											<label for="tipo_os">Tipo O.S.:</label>
										</td>
										<td nowrap="nowrap">
											<select name="tipo_os" style="width: 260px">
												<option value="">Escolha</option>
												<?php foreach ($comboTipoOS as $id=>$descricao): ?>
													<option <?php echo $id==$_POST['tipo_os'] ? 'selected="selected"' : ''  ?> value="<?php echo $id; ?>"><?php echo $descricao; ?></option>
												<?php endforeach; ?>
											</select>
										</td>
									</tr>
									
									<!-- Filtros linha 3 -->
									<tr>
									
										<!-- Campo Ver Insucessos -->
										<td class="field_label" nowrap="nowrap">
											<label for="ver_insucessos">Ver insucessos de envio:</label>
										</td>
										<td nowrap="nowrap">
											<input <?php echo ($_POST['ver_insucessos']==1 || !isset($_POST['ver_insucessos'])) ? 'checked' : ''  ?> type="radio" name="ver_insucessos" value="1"> Sim
											<input <?php echo $_POST['ver_insucessos']===0 ? 'checked' : ''  ?> type="radio" name="ver_insucessos" value="0"> Não
										</td>
									</tr>
									
									
									<!-- Filtros linha 4 -->
									<tr>
										<!-- Campo Quantidade de e-mails enviados -->
										<td class="field_label" nowrap="nowrap">
											<label for="quantidade_emails">Quantidade de e-mails enviados:</label>
										</td>
										<td nowrap="nowrap">
											<select name="quantidade_emails">
												<?php foreach ($comboQuantidadeEmails as $id=>$descricao): ?>
													<option value="<?php echo $id; ?>"><?php echo $descricao; ?></option>
												<?php endforeach; ?>
											</select>
										</td>
									</tr>
									
									<!-- Filtros linha 5 -->
									<tr>
										<!-- Campo Placa -->
										<td class="field_label" nowrap="nowrap">
											<label for="placa">Placa:</label>
										</td>
										<td nowrap="nowrap">
											<input type="text" name="placa" maxlength="7" size="8" />
										</td>
									</tr>
									
									<!-- Filtros linha 6 -->
									<tr>
										<!-- Campo Numero OS -->
										<td class="field_label" nowrap="nowrap">
											<label for="numero_os">Nº da O.S.:</label>
										</td>
										<td nowrap="nowrap">
											<input type="text" name="numero_os" size="8" />
										</td>
									</tr>
									
									<!-- Filtros linha 7 -->
									<tr>
										<!-- Campo Status -->
										<td class="field_label" nowrap="nowrap">
											<label for="nome_cliente">Nome do Cliente:</label>
										</td>
										<td nowrap="nowrap">
											<input type="text" name="nome_cliente" style="width: 260px"/>
										</td>
									</tr>
					                <tr>
					                    <td class="field_label" nowrap="nowrap"><label for="comboPropostas">Tipo de proposta:</label></td>
					                    <td nowrap="nowrap">
					                    	<select name="comboPropostas" id="comboPropostas">
					                    		<option value="">Selecione</option>
				                                <?php foreach($this->comboPropostas as $proposta): ?>
				                                <option value="<?php echo $proposta['tppoid'] ?>"><?php echo utf8_decode($proposta['tppdescricao']) ?></option>
				                                <?php endforeach; ?> 
					                    	</select>
					                    </td>
					                </tr>
					                <tr>

					                    <td class="field_label" nowrap="nowrap"><label for="comboSubpropostas" style="display:none" class="subtipo">Subtipo de proposta:</label></td>
					                    <td nowrap="nowrap">
					                    	<img src="images/progress4.gif" id="loading_tipo" style="display:none" />
					                    	<select name="comboSubpropostas" id="comboSubpropostas" class="subtipo" style="display:none">
					                    		<option value="">Selecione</option>                                
					                    	</select>
					                    </td>
					                </tr>
					                <tr>
					                    <td class="field_label" nowrap="nowrap"><label for="comboContratos">Tipo de contrato:</label></td>
					                    <td nowrap="nowrap">
					                    	<select name="comboContratos" id="comboContratos">
					                    		<option value="">Selecione</option>
				                                <?php foreach($this->comboContratos as $contrato): ?>
				                                <option value="<?php echo $contrato['tpcoid'] ?>"><?php echo utf8_decode($contrato['tpcdescricao']) ?></option>  
				                                <?php endforeach; ?> 
					                    	</select>
					                    </td>
					                </tr>									
								</tbody>
								
								<tfoot>
									<tr class="tableRodapeModelo1">
										<td align="center" colspan="8">
											<input type="button" class="botao" name="pesquisar" value="Pesquisar" />
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
											<td colspan="19">
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