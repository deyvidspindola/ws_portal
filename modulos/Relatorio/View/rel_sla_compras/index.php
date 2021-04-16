<?php cabecalho(); ?>

<script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
<link type="text/css" rel="stylesheet" href="lib/css/style.css" />

<script type="text/javascript" src="lib/js/bootstrap.js"></script>
<script type="text/javascript" src="lib/js/jquery-ui-1.10.0.custom.min.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>

<!-- jQuery UI -->
<link type="text/css" rel="stylesheet" href="lib/css/cupertino/jquery-ui-1.10.0.custom.min.css" />       
<script type="text/javascript" src="modulos/web/js/rel_sla_compras.js"></script>

<form id="frm_sla_compras" method="POST" name="frm_sla_compras">
	<input type="hidden" name="acao" id="acao" value="" />
	<div class="modulo_titulo">SLA Compras</div>
	<div class="modulo_conteudo">
	    <div class="mensagem info">Os campos com * são obrigatórios.</div>
	    <div id="msg_alerta" class="mensagem alerta invisivel"></div>	
        <?php if ($this->arquivoGerado === true) : ?>
	    <div id="msg_sucesso" class="mensagem sucesso">Arquivo gerado com sucesso.</div>
        <?php else: ?>
	    <div id="msg_sucesso" class="mensagem sucesso invisivel"></div>
        <?php endif; ?>
	    <?php if ($this->mensagem != "") : ?>
	    	<div id="msg_erro" class="mensagem erro"><?php echo $this->mensagem; ?></div>
	    <?php endif; ?>
		<div class="bloco_titulo">Dados para Pesquisa</div>
		<div class="bloco_conteudo">
			<div class="formulario">
			
				<div class="campo medio">
					<label for="cotacao">Cotação</label>
					<input type="text" id="cotacao" value="<?php echo ($this->frmPesquisa->cotacao ? $this->frmPesquisa->cotacao : "")?>" name="cotacao" class="campo" maxLength="10" />
				</div>
				<div class="clear"></div>
				
			    <div class="campo medio">
					<label for="rms">RMS</label>
					<input type="text" id="rms" value="<?php echo ($this->frmPesquisa->rms ? $this->frmPesquisa->rms : "")?>"  name="rms" class="campo" maxLength="10"  />
				</div>   
	            <div class="clear"></div>
	        
	        	<?php
	        		echo $htmlPeriodo;
	        	?>
	        	<div class="clear"></div>
	        	
	        	<div class="campo medio">
					<label for="visualizacao">Visualização * </label>
					<select name="visualizacao">
						<option value="T" <?php echo ($this->frmPesquisa->visualizacao=="T" ? "selected='selected'" : "")?> >Tela</option>
						<option value="C" <?php echo ($this->frmPesquisa->visualizacao=="C" ? "selected='selected'" : "")?> >Arquivo CSV</option>
					</select>
				</div>   
	            <div class="clear"></div>
			</div>
		</div>
		<div class="bloco_acoes">
			<button type="submit" id="pesquisar" name="pesquisar">Pesquisar</button>  
		</div>
		<div class="separador"></div>
	   <?php
		$rows = $this->dados;
		if($this->dados !== false) {
		if ($this->visualizacao == "T") {		
			if($rows){
		?>
		<div class="resultado" id="content_log">
			<div class="bloco_titulo">Resultado da Pesquisa</div>
			<div class="bloco_conteudo">
					<div class="conteudo">
						<fieldset>
							<legend>Legenda</legend>
							<ul>
								<li><img src="images/apr_bom.gif" alt=" " /> No Prazo</li>
								<li><img src="images/apr_ruim.gif" alt=" " /> Em Atraso</li>
							</ul>
						</fieldset>
					</div>
					<div class="bloco_conteudo">
				<div class="listagem">
				
					<table>
						<thead>
							<tr >
								<th class="centro">Cotação</th>
								<th class="centro">RMS</th>
								<th class="centro">Data de Abertura RMS</th>
								<th class="centro">Data de Autorização RMS</th>
								<th class="centro">Data Cotação</th>
								<th class="centro">Dias</th>
								<th >Comprador</th>
								<th class="centro">Situação</th>								
							</tr>
						</thead>
						<tbody>	
						<?php
						$class="";
						if($rows):
							
							$tr="";
							$ultimoCliente = 0;
							
							foreach ($this->dados  as $buscar ):

								
								
								$contador ++;
								
								$cliente = $buscar->cotoid;
								
								if ($cliente != $ultimoCliente) {
									$class = ($class=="" ? "class=\"par\"" : "");
									$tr = str_replace("[".$ultimoCliente."]", $contador, $tr);
									$contador = 0;
								}
								$tr.="<tr ".$class.">";
									if ($contador == 0) {
										$tr.="<td align=\"center\" rowspan=\"[".$cliente."]\"><a href=\"cad_cotacao_material.php?acao=editar&cotoid=".$buscar->cotoid."\" target=\"_blank\">".$buscar->cotoid."</a></td>";
									}
									$tr.="<td align=\"center\"><a href=\"cad_requisicao_material_novo.php?reqmoid=".$buscar->reqmoid."&acao=editar&tela=detalhamento\" target=\"_blank\">".$buscar->reqmoid."</a></td>";
									$tr.="<td align=\"center\">".$buscar->reqmcadastro."</td>";
									$tr.="<td align=\"center\">".$buscar->rmapdt_aprovacao."</td>";
									if ($contador == 0) {
										$tr.="<td align=\"center\" rowspan=\"[".$cliente."]\">".$buscar->cotcadastro."</td>";
										$tr.="<td align=\"center\" rowspan=\"[".$cliente."]\">".round($buscar->media_por_cotacao)."</td>";
										$tr.="<td rowspan=\"[".$cliente."]\">".$buscar->nm_usuario."</td>";
										$tr.="<td align=\"center\" rowspan=\"[".$cliente."]\">";
	                                        if ( $buscar->media_por_cotacao > 9) {
	                                            $tr.="<img src='images/apr_ruim.gif' />";
	                                        } else {
	                                            $tr.="<img src='images/apr_bom.gif'>";
	                                        }
	                                }
									$tr.="</td>
								</tr>";
									                                        
								$ultimoCliente = $cliente;
								
							endforeach;

							$contador++;
							$tr = str_replace("[".$ultimoCliente."]", $contador, $tr);
							
							echo $tr;
							
						endif;
						?>
						</tbody>
					</table>
					
				</div>
			</div>
			<div class="separador"></div>					
			</div>
		</div>
	     
			  <?php } 
			  		else 
				    { ?>
				   <div class="mensagem alerta"  id="msg_alerta1">Nenhum resultado encontrado.</div>    
	   		 <?php  }
   		  } 	
   		  else 
   		  { ?>
   		  		 
				<?php
					$rows = $this->dados;		
					if($rows){
				?>
				<div class="resultado">		    
				<div class="separador"></div>
					<div class="bloco_titulo resultado">Resultado da Pesquisa</div>
					<div class="bloco_conteudo">
						 <div class="conteudo centro">
						      <a href="download.php?arquivo=<?php echo $this->caminhoArquivo; ?>" target="_blank">
						      		<div>
								<img alt="Download" src="images/icones/t3/caixa2.jpg"><br/>
								Download do arquivo CSV
							</div>
						          
						     </a>
						 </div>
					</div>
				</div>
				<div class="separador"></div>
			   </div>
				<? } else{ ?>					
				 <div class="mensagem alerta" id="msg_alerta1">Nenhum resultado encontrado.</div>    
				<?php } ?>	
	
	
	 <?php 		}
   		  }?>
			

	</div>	
</form>
	<div class="separador"></div>	

<?php include "lib/rodape.php"; ?>