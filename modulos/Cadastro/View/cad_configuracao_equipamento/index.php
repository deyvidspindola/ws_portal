<div class="bloco_titulo">Pesquisa</div>

<form action="" name="pesquisa_configuracao_equipamento" id="pesquisa_configuracao_equipamento" method="post" onsubmit="return false;">	
    <input type="hidden" name="acao" id="acao" value="pesquisar" />
    <input type="hidden" name="equipamento_selected" id="equipamento_selected" value="<?php echo $_POST['ceqpprdoid']?>" />
    <input type="hidden" name="clientes_selected" id="clientes_selected" value="<?php echo ($_POST['cliente'])?implode(',', $_POST['cliente']):''?>" />
    
	<div class="bloco_conteudo">
		<div class="conteudo">	
			
			<div class="campo" style="float:left;width:50%">
				<fieldset id="fieldsetEquipamento" style="min-height:95px">
				    <legend>Equipamento</legend>
				
					<div class="campo">
					    <div class="campo menor">
							<label for="ceqpoid">ID </label>	
							<input class="campo menor numerico" type="text" name="ceqpoid" id="ceqpoid" value="<?php $_POST['ceqpoid']?>" />
						</div>
				
					    <div class="campo menor">
					    	<fieldset style="margin-top:0px;">
			    				<legend>Segmento</legend>
						    	<div>
								    <input type="radio" name="tipo_equip" value="DCT" id="tipo_equipDCT" class="tipo_equip" <?php echo ($_POST['tipo_equip'] == 'DCT') ? 'checked' : '' ;?>><label for="tipo_equipDCT">DCT</label>
								    <input type="radio" name="tipo_equip" value="RTN" id="tipo_equipRTN" class="tipo_equip" <?php echo ($_POST['tipo_equip'] == 'RTN') ? 'checked' : '' ;?>><label for="tipo_equipRTN">RTN</label>  
								</div>
							</fieldset>
					    </div>
					    
						<div class="campo medio">
							<label for="tipo_equip">Material </label>
						    <select id="ceqpprdoid" name="ceqpprdoid" class="campo medio" >			    
							</select>
					    </div>
				    </div>
				    
				</fieldset>
			</div>

			<div class="campo" style="float:left;width:49%;margin:5px 0px">
				<fieldset id="fieldsetDisponibilidade" style="min-height:95px">
				    <legend>Disponibilidade Comercial</legend>
				    <div class="campo menor">
				    	<br/>
					    <input type="radio" name="ceqpdisp_comercial" <?php echo ($_POST['ceqpdisp_comercial'] == 'S') ? 'checked' : '' ;?> value="S" id="" class="ceqpdisp_comercial"><label for="ceqpdisp_comercialSim">Sim</label>
					    <input type="radio" name="ceqpdisp_comercial" <?php echo ($_POST['ceqpdisp_comercial'] == 'S') ? '' : 'checked' ;?> value="N" id="" class="ceqpdisp_comercial"><label for="ceqpdisp_comercialNao">Não</label>
					    <img src="images/help10.gif" onmouseover="document.body.style.cursor='pointer';" onmouseout="document.body.style.cursor='default';" onclick="mostrarHelpComment(this,'Indica se esta configuração é visivel para a plataforma de Vendas e CRM.','D' , '');">  
				    </div>
				</fieldset>
			</div>

			<div class="clear" ></div>

			<div class="campo" style="float:left;width:50%">
				<fieldset id="fieldsetConfigSoftware" style="min-height: 130px;">
				    <legend>Configuração de Software</legend>
				    
				    <div class="campo">
					    <div class="campo medio">
							<label for="ceqpveqpoid">Validade </label>
							<select id="ceqpveqpoid" name="ceqpveqpoid" class="campo">
								<option value="">Selecione a Validade</option>
								<?php foreach($this->combos['validade'] as $validade): 
									$veqpoid = $validade['veqpoid'];
									$veqpdescricao = $validade['veqpdescricao'];
									
									$sel = (isset($_POST['ceqpveqpoid']) && $_POST['ceqpveqpoid'] == $veqpoid) ? "selected = 'selected'" : "";
								?>
								<option value="<?php echo $veqpoid ?>" <?php echo $sel; ?>><?php echo $veqpdescricao; ?></option>
									                
				                <?php endforeach; ?> 
							</select>
						</div>		
						
						<div class="campo medio">
							<label for="nome_busca">Intervalo </label>
							<select id="ceqpiposeqpoid" name="ceqpiposeqpoid" class="campo">
								<option value="">Selecione o Intervalo</option>
								<?php foreach($this->combos['intervalo'] as $intervalo): 
									$iposeqpoid = $intervalo['iposeqpoid'];
									$iposeqpdescricao = $intervalo['iposeqpdescricao'];
									
									$sel = (isset($_POST['ceqpiposeqpoid']) && $_POST['ceqpiposeqpoid'] == $iposeqpoid) ? "selected = 'selected'" : "";
								?>
								<option value="<?php echo $iposeqpoid ?>" <?php echo $sel; ?>><?php echo $iposeqpdescricao; ?></option>
									                
				                <?php endforeach; ?>
							</select>
						</div>	
					
						<div class="clear" ></div>	
						
						<div class="campo medio">
							<label for="ceqpemeeqpoid">Emergência </label>
							<select id="ceqpemeeqpoid" name="ceqpemeeqpoid" class="campo">
								<option value="">Selecione a Emergência</option>
								<?php foreach($this->combos['emergencia'] as $emergencia): 
									$emeeqpoid = $emergencia['emeeqpoid'];
									$emeeqpdescricao = $emergencia['emeeqpdescricao'];
									
									$sel = (isset($_POST['ceqpemeeqpoid']) && $_POST['ceqpemeeqpoid'] == $emeeqpoid) ? "selected = 'selected'" : "";
								?>
								<option value="<?php echo $emeeqpoid ?>" <?php echo $sel; ?>><?php echo $emeeqpdescricao; ?></option>
									                
				                <?php endforeach; ?>
							</select>
						</div>	
						
						<div class="campo medio">
							<label for="ceqpciceqpoid">Ciclo </label>
							<select id="ceqpciceqpoid" name="ceqpciceqpoid" class="campo">
								<option value="">Selecione o Ciclo</option>
								<?php foreach($this->combos['ciclo'] as $ciclo): 
									$ciceqpoid = $ciclo['ciceqpoid'];
									$ciceqpdescricao = $ciclo['ciceqpdescricao'];
									
									$sel = (isset($_POST['ceqpciceqpoid']) && $_POST['ceqpciceqpoid'] == $ciceqpoid) ? "selected = 'selected'" : "";
								?>
								<option value="<?php echo $ciceqpoid ?>" <?php echo $sel; ?>><?php echo $ciceqpdescricao; ?></option>
									                
				                <?php endforeach; ?>
							</select>
						</div>	
					</div>
				</fieldset>
			</div>

			<div class="campo" style="float:left;width:49%;margin:5px 0px">
			 	<fieldset style=" min-height: 70px;height: 130px;">
			    	<legend>Apenas Venda Restrita por Cliente</legend>
			    	
				    <div class="campo menor" style="position: relative;top: 30%">
					    <input type="radio" name="venda_restrita" <?php echo ($_POST['venda_restrita'] == 'S') ? 'checked' : '' ;?> value="S" id="venda_restritaSim"><label for="venda_restritaSim">Sim</label>
					    <input type="radio" name="venda_restrita" <?php echo ($_POST['venda_restrita'] == 'S') ? '' : 'checked' ;?> value="N" id="venda_restritaNao"><label for="venda_restritaNao">Não</label>
				    </div>
					
					<div class="campo maior" style="" id="bloco_clientes">
						<label for="cliente">Selecione o(s) Cliente(s) - Cliente | (CPF/CNPJ) </label>
					    <select id="cliente" name="cliente[]" multiple="multiple">
						</select> 
					</div>
			    </fieldset>
			</div>

			<div class="clear" ></div>
		</div>
	</div>
		
	<div class="bloco_acoes">
		<button type="button" value="pesquisar" id="buttonPesquisar" name="buttonPesquisar">Pesquisar</button>
		<button type="button" value="novo" id="buttonNovo" name="buttonNovo">Novo</button>
	</div>
</form>
<div class="separador"></div>
<?php if($_POST && $this->resultadoPesquisa){?>
<div class="resultado_pesquisa">
	<div class="bloco_titulo">Resultado da Pesquisa</div>
	<div class="bloco_conteudo">
	    <div class="listagem">
	        <table>
	            <thead>
	                <tr>	                	
	                    <th>ID</th>
	                    <th>Equipamento</th>
	                    <th>Disponibilidade Comercial</th>
	                    <th>Validade</th>
	                    <th>Intervalo</th>
	                    <th>Emergência</th>
	                    <th>Ciclo</th>
	                </tr>
	            </thead>
	            <tbody>
	            <?php 
                	$cor = 'par';
                	if(count($this->resultadoPesquisa) > 0){
		            	foreach($this->resultadoPesquisa as $linha){
							
							$cor = ($cor=="par") ? "" : "par";?>
							<tr <?php if ($cor != '') { ?> class="<?=$cor?>" <?php } ?>>
								<td class="direita">
									<a class="clickEditar" href="javascript:void(0);" id="<?php echo $linha['ceqpoid']; ?>">
										<?php echo $linha['ceqpoid']; ?>
									</a>
								</td>
								<td><?php echo $linha['prdproduto']; ?></td>
								<td><?php echo ($linha['ceqpdisp_comercial'] == 't') ? 'Sim' : 'Não'?></td>
								<td>
									<?php echo ($linha['veqpdescricao'])?$linha['veqpdescricao']:'-'; ?>
								</td>
								<td>
									<?php echo ($linha['iposeqpdescricao'])?$linha['iposeqpdescricao']:'-'; ?>
								</td>
								<td>
									<?php echo ($linha['emeeqpdescricao'])?$linha['emeeqpdescricao']:'-'; ?>
								</td>
								<td>
									<?php echo ($linha['ciceqpdescricao'])?$linha['ciceqpdescricao']:'-'; ?>
								</td>
							</tr>
					<?	}
					}
	            ?>	
	            </tbody>
	            <tfoot>
	                <tr>
	                    <td colspan="7"><?php echo $this->getMensagemTotalRegistros(count($this->resultadoPesquisa));?><!-- 1 registro encontrado. -->
	                    </td>
	                </tr>
	            </tfoot>
	        </table>
	    </div>
	</div>
    <!-- div class="bloco_acoes"><p>Nenhum Resultado Encontrado.</p></div-->
</div>
<?php }elseif($_POST && count($this->resultadoPesquisa) == 0){ ?>
	
<div class="resultado_pesquisa">
	<div class="bloco_titulo">Resultado da Pesquisa</div>
	<div class="bloco_conteudo">
	    <div class="listagem">
	        <table>	            
	            <tfoot>
	                <tr>
	                    <td colspan="7"><?php echo $this->getMensagemTotalRegistros(count($this->resultadoPesquisa));?><!-- 1 registro encontrado. -->
	                    </td>
	                </tr>
	            </tfoot>
	        </table>
	    </div>
	</div>
    <!-- div class="bloco_acoes"><p>Nenhum Resultado Encontrado.</p></div-->
</div>
<?php }?>