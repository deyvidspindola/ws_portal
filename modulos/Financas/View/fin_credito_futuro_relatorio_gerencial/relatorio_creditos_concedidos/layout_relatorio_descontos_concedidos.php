<?php

$sitedir = strpos($_SERVER['HTTP_HOST'], '10.20.12.') === false ? '' : _SITEDIR_.'/lib/php5-jpgraph/';

require_once $sitedir.'jpgraph.php';
require_once $sitedir.'jpgraph_bar.php';
 
$data1y=array(32.00,32.00);
$data2y=array(30.00,31.99);
$data3y=array(292.00,1.11);
 
// Create the graph. These two calls are always required
$graph = new Graph(800,400);    
$graph->SetScale("textlin");
 
$months = $gDateLocale->GetShortMonth();
$months = array('10/2013', '11/2013');


$graph->SetShadow('',0,0,false);
$graph->SetBackgroundGradient('white', 'white', 2, BGRAD_FRAME);
$graph->img->SetMargin(40,30,20,40);
$graph->xaxis->SetTickLabels($months);

// Create the bar plots
$b1plot = new BarPlot($data1y);
$b1plot->SetFillColor("#00aa00");
$b1plot->SetLegend("Vlr.Itens");
$b1plot->SetShadow("silver", 3, 3, true);

$b2plot = new BarPlot($data2y);
$b2plot->SetFillColor("red");
$b2plot->SetLegend("Vlr.Descto.");
$b2plot->SetShadow("silver", 3, 3, true);

$b3plot = new BarPlot($data3y);
$b3plot->SetFillColor("blue");
$b3plot->SetLegend("Vlr.NF");
$b3plot->SetShadow("silver", 3, 3, true);
 
// Create the grouped bar plot
$gbplot = new GroupBarPlot(array($b1plot,$b2plot,$b3plot));
$labels = array ('legenda 1', 'legenda 2', 'legenda 3');

$gbplot->SetFillColor('#E234A9'); 
// ...and add it to the graPH
$graph->Add($gbplot);
 
//$graph->title->Set("Sintético");
$graph->xaxis->title->Set("Período");
$graph->yaxis->title->Set("Valor");
 
$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
 



// Display the graph
unlink('grafico_prototipo.jpg');
$graph->Stroke('grafico_prototipo.jpg');
?>
<html>
	<head>
		<meta charset="ISO-8859-1">
		
		<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css" />

<!-- JAVASCRIPT -->
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>

		<script type="text/javascript">
			jQuery(document).ready(function() {

				/**
				* @input(type=select, id=tipo_relatorio)
				* @event OnChange
				*/
				jQuery("#tipo_relatorio").change(function(e) {

					var tipo = jQuery(this).val();

					if (tipo === "A") {

						jQuery(".sintetico").fadeOut('fast', function() {
							jQuery(".analitico").fadeIn('slow');	
						});
					} else {

						jQuery(".analitico").fadeOut('fast', function() {
							jQuery(".sintetico").fadeIn('slow');	
						});
					}
				});

				/**
				* @button(id=btn-analitico-enviar-email)
				* @event OnClick
				*/
				jQuery("#btn-analitico-enviar-email").click(function(){

					jQuery("#dialog-email-analitico").dialog({
			            autoOpen: false,
			            minHeight: 300 ,
			            maxHeight: 700 ,
			            width: 550,
			            modal: true,
			            buttons: {
			                "Enviar": function() {
			                	alert("Envia o e-mail");
			                },
			                "Cancelar": function() {
			                   jQuery("#dialog-email-analitico").dialog('close');
			                }
			            }
			        }).dialog('open');
				});

				/**
				* @button(id=btn-sintetico-enviar-email)
				* @event OnClick
				*/
				jQuery(".btn-sintetico-enviar-email").click(function(){

					jQuery("#dialog-email-sintetico").dialog({
			            autoOpen: false,
			            minHeight: 300 ,
			            maxHeight: 700 ,
			            width: 550,
			            modal: true,
			            buttons: {
			                "Enviar": function() {
			                	alert("Envia o e-mail");
			                },
			                "Cancelar": function() {
			                   jQuery("#dialog-email-sintetico").dialog('close');
			                }
			            }
			        }).dialog('open');
				});
			});
		</script>
	</head>
	<body>
		<div class="modulo_titulo">Relatório Gerencial de Descontos Concedidos</div>
		<div class="modulo_conteudo">

			<div id="info_principal" class="mensagem info">Campos com * são obrigatórios.</div>
			<div class="bloco_titulo">Dados para pesquisa</div>
			<div class="bloco_conteudo">

				<div class="formulario">

					<!-- TIPO DE RELATÓRIO -->

					<div class="campo medio">
						<label>
							Tipo
						</label>
						<select id="tipo_relatorio">
							<option value="A">Analítico</option>
							<option value="S">Sintético</option>
						</select>
					</div>

					<!-- MOTIVO DE CRÉDITO -->
					<div class="campo maior">
						<label>
							Motivo do Crédito
						</label>
						<select>
							<option>Todos</option>
							<option>Contestação</option>
						</select>
					</div>

					<div class="clear"></div>

					<!-- PERÍODO DE INCLUSÃO -->
					<div class="campo data periodo">
		                <div class="inicial">
		                    <label for="periodo_inclusao_ini">Período *</label>
		                    <input type="text" id="periodo_inclusao_ini" value="" class="campo" />
		                </div>							
		                <div class="campo label-periodo">a</div>
		                <div class="final">
		                    <label for="periodo_inclusao_fim">&nbsp;</label>
		                    <input type="text" id="periodo_inclusao_fim" value="" class="campo" />
		                </div>                            
					</div>

					<!-- TIPO CAMPANHA PROMOCIONAL -->
					<div class="campo maior">
						<label>
							Tipo de Campanha Promocional
						</label>
						<select>
							<option>Todos</option>
							<option>Indicação de Amigo</option>
						</select>
					</div>
											
					<div class="clear"></div>

					<!-- INCLUSÃO -->	
					<fieldset class="medio">
						<legend>Inclusão</legend>
						<input type="radio"  />
						<label>Manual</label>
						<input type="radio"/>
						<label>Automática</label> 
						<input type="radio" checked="checked"/>
						<label>Todas</label> 
					</fieldset>

					<div class="clear"></div>
					<div class="analitico">

						<div class="clear"></div>
						<div class="separador"></div>
						
						<!-- PESQUISA CLIENTE INDICADOR -->				
						<div class="campo medio" style="position: relative;">
							<label>Nome do Cliente Indicador</label>
							<input type="text" class="campo "  maxlength="50" value=""  />
						</div>

						<div class="clear"></div>

						<fieldset class="medio">
							<legend>Tipo Pessoa</legend>
							<input type="radio" value="F" class="componente_tipo_pessoa"  />
							<label>Física</label>
							<input type="radio" value="J" class="componente_tipo_pessoa" checked="checked"/>
							<label>Jurídica</label> 
						</fieldset>

						<div class="clear"></div>

						<div class="campo medio" >
							<label>CNPJ</label>
							<input type="text" value="" class="campo mask_cnpj "  />
						</div> 

						<div class="clear"></div>

						<!-- NOTA FISCAL -->
						<div class="campo medio">
							<label>Número NF</label>
							<input type="text" class="campo"  maxlength="10"  />
						</div>
						<div class="campo menor">
							<label>
								Série NF
							</label>
							<select>
								<option>Todas</option>
								<option>A</option>
							</select>
						</div>
					</div>

					<div class="sintetico" style="display: none">
						<!-- RESULTADO -->	
						<fieldset class="medio">
							<legend>Resultado</legend>
							<input type="radio" checked  />
							<label>Diário</label>
							<input type="radio"/>
							<label>Mensal</label> 
						</fieldset>
					</div>
            		<div class="clear"></div>
				</div>
			</div>
			<!-- BARRA DE AÇÕES -->
			<div class="bloco_acoes">
				<button type="button">Pesquisar</button>
				<button type="button">Retornar</button>
			</div>

			<div class="separador"></div>
			
			<!-- RESULTADO ANALITICO -->
			<div class="analitico">
				<div class="bloco_titulo">Resultado da pesquisa</div>
				<div class="bloco_conteudo">
					<div class="listagem">
						<table>
							<thead>
								<tr>
									<th class="menor">Dt. Emissão</th>
									<th class="maior">NF/Série</th>
									<th class="menor">Cliente</th>
									<th class="maior">CNPJ/CPF</th>
									<th class="maior">Cód. Identif. CF</th>
									<th class="maior">Motivo do Crédito</th>
									<th class="menor">Inclusão</th>
									<th class="menor">Protocolo</th>
									<th class="maior">Campanha Promocional</th>
									<th class="maior">Vlr.Itens</th>
									<th class="maior">Vlr.Descto.</th>
									<th class="maior">Vlr.NF</th>
								</tr>
							</thead>
							<tbody>
								<tr class="impar">
									<td align="center">10/10/2013</td>
									<td align="center">987987/A</td>
									<td align="left">FULANO O OBLITERADOR</td>
									<td align="center">325.965.544-96</td>
									<td align="center">21</td>
									<td align="left">Contestação de Fatura</td>
									<td align="center">Automática</td>
									<td align="right">9587</td>
									<td align="left"></td>
									<td align="right">R$ 250,00</td>
									<td align="right">R$ 15,00</td>
									<td align="right">R$ 235,00</td>
								</tr>
								<tr class="par">
									<td align="center">12/10/2013</td>
									<td align="center">658547/A</td>
									<td align="left">BELTRANO O MAGO</td>
									<td align="center">325.535.987-91</td>
									<td align="center">35</td>
									<td align="left">Indicação de Amigo</td>
									<td align="center">Automática</td>
									<td align="center"></td>
									<td align="left">Amigo da onça</td>
									<td align="right">R$ 72,00</td>
									<td align="right">R$ 15,00</td>
									<td align="right">R$ 57,00</td>
								</tr>
								<tr class="impar">
									<td align="center">02/11/2013</td>
									<td align="center">98547/A</td>
									<td align="left">SICLANO O GUERREIRO</td>
									<td align="center">125.325.964-56</td>
									<td align="center">35</td>
									<td align="left">Isenção de Monitoramento</td>
									<td align="center">Manual</td>
									<td align="center"></td>
									<td align="left"></td>
									<td align="right">R$ 32,00</td>
									<td align="right">R$ 31,99</td>
									<td align="right">R$ 0,01</td>
								</tr>
								<tr class="par">
									<td align="right" colspan="9"><b>TOTAL</b></td>
									<td align="right"><b>R$ 354,00</b></td>
									<td align="right"><b>R$ 61,99</b></td>
									<td align="right"><b>R$ 292,01</b></td>
								</tr>
							</tbody>
							<tfoot>
								
							</tfoot>
						</table>
					</div>
				</div> 
				<div class="bloco_acoes">
					<p>3 registros encontrados.</p>
				</div> 
				<!-- BARRA DE AÇÕES -->
				<div class="bloco_acoes">
					<button type="button">Gerar XLS</button>
					<button type="button" id="btn-analitico-enviar-email">Enviar E-mail</button>
				</div>
				
				<!-- DOWNLOAD ANALÍTICO -->
				<div class="clear separador"></div>
				<div class="bloco_titulo">Download</div>
				<div class="bloco_conteudo">
			        <div class="conteudo centro">
			            <a target="_blank" href="#">
			                <img src="../../../images/icones/t3/caixa2.jpg">
			                <br>Relatório Gerencial de Descontos Concedidos-dd-mm-yyyy</a>
			        </div>
			    </div>
			</div>

			<!-- RESULTADO SINTETICO -->
			<div class="sintetico" style="display:none">
				<!-- MENSAL -->
				<div class="bloco_titulo">Resultado da pesquisa</div>
				<div class="bloco_conteudo">
					<div class="listagem">
						<table>
							<thead>
								<tr>
									<th class="menor">Mês/Ano</th>
									<th class="maior">Vlr.Itens</th>
									<th class="maior">Vlr.Descto.</th>
									<th class="maior">Vlr.NF</th>
									<th class="maior">% Vlr.Descto. Sobre Vlr.Itens</th>
								</tr>
							</thead>
							<tbody>
								<tr class="impar">
									<td class="agrupamento" align="center" rowspan="2">10/2013</td>
									<td align="right">R$ 250,00</td>
									<td align="right">R$ 15,00</td>
									<td align="right">R$ 235,00</td>
									<td align="right">12,00 %</td>
								</tr>
								<tr class="par">
									<td align="right">R$ 72,00</td>
									<td align="right">R$ 15,00</td>
									<td align="right">R$ 57,00</td>
									<td align="right">57,00 %</td>
								</tr>
								<tr class="impar">
									<td class="agrupamento" align="center">11/2013</td>
									<td align="right">R$ 32,00</td>
									<td align="right">R$ 31,99</td>
									<td align="right">R$ 0,01</td>
									<td align="right">100,00 %</td>
								</tr>
								<tr class="par">
									<td class="agrupamento" align="right"><b>TOTAL</b></td>
									<td align="right"><b>R$ 354,00</b></td>
									<td align="right"><b>R$ 61,99</b></td>
									<td align="right"><b>R$ 292,01</b></td>
									<td align="right"><b>90,00 %</b></td>
								</tr>
							</tbody>
						
						</table>
					</div>
				</div> 
				<!-- BARRA DE AÇÕES -->
				<div class="bloco_acoes">
					<button type="button">Gerar XLS</button>
					<button type="button" class="btn-sintetico-enviar-email">Enviar E-mail</button>
				</div>

				<div class="clear separador"></div>
				<div class="bloco_titulo">Gráfico</div>
				<div class="bloco_conteudo">
					<img src="grafico_prototipo.jpg">
				</div>


				<!-- MENSAL -->
				<div class="clear separador"></div>
				<div class="bloco_titulo">Resultado da pesquisa</div>
				<div class="bloco_conteudo">
					<div class="listagem">
						<table>
							<thead>
								<tr>
									<th class="menor">Dt.Emissão</th>
									<th class="maior">Motivo do Crédito</th>
									<th class="maior">Vlr.Itens</th>
									<th class="maior">Vlr.Descto.</th>
									<th class="maior">Vlr.NF</th>
									<th class="maior">% Vlr.Descto. Sobre Vlr.Itens</th>
								</tr>
							</thead>
							<tbody>
								<tr class="impar">
									<td align="center">05/10/2013</td>
									<td align="center">Contestação</td>
									<td align="right">R$ 250,00</td>
									<td align="right">R$ 15,00</td>
									<td align="right">R$ 235,00</td>
									<td align="right">12,00 %</td>
								</tr>
								<tr class="par">
									<td align="center">08/10/2013</td>
									<td align="center">Indicação de Amigo</td>
									<td align="right">R$ 72,00</td>
									<td align="right">R$ 15,00</td>
									<td align="right">R$ 57,00</td>
									<td align="right">57,00 %</td>
								</tr>
								<tr class="impar">
									<td align="center">11/10/2013</td>
									<td align="center">Isenção de Monitoramento</td>
									<td align="right">R$ 32,00</td>
									<td align="right">R$ 31,99</td>
									<td align="right">R$ 0,01</td>
									<td align="right">100,00 %</td>
								</tr>
								<tr class="par">
									<td align="right" colspan="2"><b>TOTAL</b></td>
									<td align="right"><b>R$ 354,00</b></td>
									<td align="right"><b>R$ 61,99</b></td>
									<td align="right"><b>R$ 292,01</b></td>
									<td align="right"><b>90,00 %</b></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div> 
				<div class="bloco_acoes">
					<p>3 registros encontrados.</p>
				</div> 

				<!-- BARRA DE AÇÕES -->
				<div class="bloco_acoes">
					<button type="button">Gerar XLS</button>
					<button type="button" class="btn-sintetico-enviar-email">Enviar E-mail</button>
				</div>

				<!-- DOWNLOAD ANALÍTICO -->
				<div class="clear separador"></div>
				<div class="bloco_titulo">Download</div>
				<div class="bloco_conteudo">
			        <div class="conteudo centro">
			            <a target="_blank" href="#">
			                <img src="../../../images/icones/t3/caixa2.jpg">
			                <br>Relatório Gerencial de Descontos Concedidos-Sintético-dd-mm-yyyy</a>
			        </div>
			    </div>

				<div id="dialog-email-analitico" title="Enviar o relatório por e-mail" class="invisivel">
	            <div class="formulario">
		            <div class="campo menor">
		               <label>Para: *</label>
		            </div>
		            <div class="campo">
		                <input type="text" style="width: 500px;" value="fulano@sascar.com.br" /> 
		            </div>

	           		<div class="clear"></div>

	           		<div class="campo menor">
		            	<label>CC:</label>
		          	</div>
			        <div class="campo" style="text-align: right">
			            <input type="text" style="width: 500px;" disabled="disabled" value="opoderosochefao@sascar.com.br" /> 
			        </div>

	           		<div class="clear"></div>

		            <div class="campo menor">
		            	<label>Assunto:</label>
		          	</div>
			        <div class="campo" style="text-align: right">
			            <input type="text" style="width: 500px;" disabled="disabled" value="Relatório Gerencial de Descontos a Concedidos - Analítico – 99/99/9999 a 99/99/9999." /> 
			        </div>

	          		<div class="clear"></div>

			        <div class="campo menor">
			            <label>Corpo: *</label>
			        </div>

	          		<div class="campo">
	              		<textarea cols="100" rows="8" style="width: 500px;">
Prezado(s), 
Segue anexo, o Relatório Gerencial de Descontos Concedidos referente o período de 99/99/9999 a 99/99/9999. 

Att.
[nome_usuario]
	            		</textarea>
	        		</div>
	    		</div>
			</div>



			<div id="dialog-email-sintetico" title="Enviar o relatório por e-mail" class="invisivel">
	            <div class="formulario">
		            <div class="campo menor">
		               <label>Para: *</label>
		            </div>
		            <div class="campo">
		                <input type="text" style="width: 500px;" value="fulano@sascar.com.br" /> 
		            </div>

	           		<div class="clear"></div>

	           		<div class="campo menor">
		            	<label>CC:</label>
		          	</div>
			        <div class="campo" style="text-align: right">
			            <input type="text" style="width: 500px;" disabled="disabled" value="opoderosochefao@sascar.com.br" /> 
			        </div>

	           		<div class="clear"></div>

		            <div class="campo menor">
		            	<label>Assunto:</label>
		          	</div>
			        <div class="campo" style="text-align: right">
			            <input type="text" style="width: 500px;" disabled="disabled" value="Relatório Gerencial de Descontos a Concedidos - Sintético – 99/99/9999 a 99/99/9999." /> 
			        </div>

	          		<div class="clear"></div>

			        <div class="campo menor">
			            <label>Corpo: *</label>
			        </div>

	          		<div class="campo">
	              		<textarea cols="100" rows="8" style="width: 500px;">
Prezado(s), 
Segue anexo, o Relatório Gerencial de Descontos Concedidos referente o período de 99/99/9999 a 99/99/9999. 

Att.
[nome_usuario]
	            		</textarea>
	        		</div>
	    		</div>
			</div>
		</div>
	</body>
</html>