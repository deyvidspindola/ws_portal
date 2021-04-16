<?php 
/**
 * @autor Gabriel Luiz Pereira
 * @since 13/12/2012
 * @param string $mensagemInformativa 	Caso exista essa variável, irá aparecer uma mensagem no topo da tela.
 * @param string $resultadoRelatorio  	Caso exista essa variável, irá aparecer no corpo da página seu conteúdo.
 */
cabecalho();
include("calendar/calendar.js");
include("lib/funcoes.js");
?>
	<link rel="stylesheet" href="calendar/calendar.css" type="text/css"  />    
    <link type="text/css" rel="stylesheet" href="includes/css/base_form.css">
    <link rel="stylesheet" href="modulos/web/css/rel_linhas.css" type="text/css"  />
    <link rel="stylesheet" href="modulos/web/css/lib/loading.css" type="text/css"  />    
    <script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>       
    <script type="text/javascript" src="modulos/web/js/lib/loading.js"></script>       
    <script type="text/javascript" src="js/jquery.validate.js"></script>   
    <script type="text/javascript" src="includes/js/mascaras.js"></script>
    <script type="text/javascript" src="includes/js/validacoes.js"></script> 
    <script type="text/javascript" src="includes/js/auxiliares.js"></script>
    <script type="text/javascript" src="modulos/web/js/rel_linhas.js"></script>

	<br />
		<iframe name="postiframe" id="postiframe" width="0" height="0"></iframe>
		
		<form name="filtro" enctype="multipart/form-data" target="postiframe" method="post" action="rel_linhas.php">
		
		<!--  Controlador de Ações -->
		<input type="hidden" id="acao" name="acao" />
		<input type="hidden" id="registros_arquivo_importado" name="registros_arquivo_importado" value="<?php echo $_POST['registros_arquivo_importado']?>" />
		<input type="hidden" id="arquivo_importado" name="arquivo_importado" value="<?php echo $_POST['arquivo_importado']?>" />
		<input type="hidden" id="listagem" name="listagem" value="<?php echo $_POST['listagem']; ?>" />
		
		<div align="center">
			<table class="tableMoldura" width="98%">
				<thead>
					<tr class="tableTitulo">
						<td>
							<h1>Relatório de Linhas</h1>
						</td>
					</tr>
					<?php if ($mensagemInformativa != ''): ?>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td class="msg">
							<?php echo $mensagemInformativa; ?>
						</td>
					</tr>
					<?php endif;?>
				</thead>
				<tbody>
					<tr>
						<td align="center">
							<br /> 
							<table class="tableMoldura" width="98%">
								<thead>
									<tr class="tableSubTitulo">
										<td colspan="8">
											<h2>Importação de Arquivo</h2>
										</td>
									</tr>
								</thead>
								<tbody class="corpo_filtro">
									<!-- FILTROS -->
								
									<!-- Filtros linha 1 -->
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<!-- Campo Arquivo -->
										<td nowrap="nowrap" width="110px">
											<label for="arquivo" >Arquivo: </label>
										</td>
										<td nowrap="nowrap">
											<input type="file" id="arquivo" name="arquivo" />
										</td>
									</tr>
									
									<tr>
										<td colspan="2">
											<br/>
											<div style="margin-left: 10px; margin-top: 10px;font-weight:bold;">
												<label class="blinking" style="margin: 0px;font-weight: inherit; color:#ff0000;">ATENÇÃO! Certifique-se de que o arquivo atenda as seguintes regras:</label>
												<br /><br />

												<label style="font-weight: inherit;margin: 0px;">
												A primeira linha do arquivo deve ser o cabeçalho, (Linha ou CCID ou Antena); <br />
												Não tenha nenhuma linha em branco no início ou no final do arquivo;<br />
												Não tenha nenhum espaço em branco entre as informações, apenas a quebra de linha.
												</label>
												</strong>
												<br /><br />
												<label style="margin: 0px;">
												Ex:<br />
												Linha<br />
												11951001024
												<br /><br />
												ou
												<br /><br />
												cid<br />
												89550440000396297240<br /><br />
												ou
												<br /><br />
												antena<br />
												01038928SKY71CD<br />
												</label>
											</div>
										</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
								</tbody>
								
								<tfoot>
									<tr class="tableRodapeModelo1">
										<td align="center" colspan="8">
											<input id="gerar_relatorio" type="button" class="botao" name="gerar_relatorio" value="Gerar Relatório" />
										</td>
									</tr>
								</tfoot>
							</table>
							
							<div id="iframe">
							</div>
							
							<div id="resultado_progress" align="center" style="display:none">
								<img src="modulos/web/images/loading.gif" alt="Carregando..." />
							</div>
							
							<div id="resultado_relatorio">
								<?php if (!empty($resultadoRelatorio)):?>
								<?php include "modulos/Relatorio/View/rel_linhas/resultado_relatorio.php"; ?>
								<?php endif;?>
							</div>
							
						</td>
					</tr>
				</tbody>
			</table>
			</div>
		</form>
<?php 
include ("lib/rodape.php");