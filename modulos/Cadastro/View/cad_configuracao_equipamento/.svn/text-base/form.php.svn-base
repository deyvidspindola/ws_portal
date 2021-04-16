<?php 
	// Limpa a Sessão de taxas
	unset($_SESSION['conf_equ_taxa']);

	if(is_null($_SESSION['funcao']['config_equip_ct_obrig_fin']) || $_SESSION['funcao']['config_equip_ct_obrig_fin'] != 1){
		$permissaoObrigFin = false;
	} else {
		$permissaoObrigFin = true;
	}

	if(is_null($_SESSION['funcao']['config_equip_ct_disp_comercial']) || $_SESSION['funcao']['config_equip_ct_disp_comercial'] != 1){
		$permissaoDispComercial = false;
	} else {
		$permissaoDispComercial = true;
	}
	
	$arrTipoCadastro = array('P'=>'Produto','C'=>'Serviço'); 
?>

<div class="bloco_titulo"><?php echo $this->tituloTela?></div>

<form action="" name="cadastro_configuracao_equipamento" id="cadastro_configuracao_equipamento" method="post" onsubmit="return false;">
    <input type="hidden" name="acao" id="acao" value="<?php echo $this->acaoTela?>" />   
    <input type="hidden" name="tipoproduto" id="tipoproduto" value="<?php echo $this->tipoproduto ?>" />
    <input type="hidden" id="config_equip_ct_obrig_fin" name="config_equip_ct_obrig_fin" value="<?php echo ($permissaoObrigFin) ? 1 : 0 ?>" />
    <input type="hidden" id="config_equip_ct_disp_comercial" name="config_equip_ct_disp_comercial" value="<?php echo ($permissaoDispComercial) ? 1 : 0 ?>" />
    <input type="hidden" name="equipamento_selected" id="equipamento_selected" value="<?php echo $this->ceqpprdoid?>" />
	
	<div class="bloco_conteudo">
		<div class="conteudo">

			<fieldset id="fieldsetEquipamentoForm" >
			    <legend>Equipamento</legend>
			    
				<div class="campo">
					<div class="campo menor">
						<label for="ceqpoid">ID </label>	
						<input class="campo menor desabilitado" type="text" name="ceqpoid" id="ceqpoid" value="<?php echo $this->ceqpoid ?>" readonly="readonly" />
					</div>

				    <div class="campo menor">
				    	<fieldset style="margin-top:0px;">
			    			<legend>Segmento</legend>
					    	<div>
							    <input type="radio" name="tipo_equip" value="DCT" id="tipo_equipDCT" class="tipo_equip" <?php echo ($this->tipoproduto == 'C') ? 'checked' : '';?>><label for="tipo_equipDCT">DCT</label>
							    <input type="radio" name="tipo_equip" value="RTN" id="tipo_equipRTN" class="tipo_equip" <?php echo ($this->tipoproduto == 'I') ? 'checked' : '';?>><label for="tipo_equipRTN">RTN</label>  
							</div>
						</fieldset>
				    </div>
				    
					<div class="campo maior">
						<label for="tipo_equip">Material </label>
					    <select id="ceqpprdoid" name="ceqpprdoid" class="maior obrigatorio">
						</select>
					</div>
				</div>
			</fieldset>

			<div class="clear" ></div>
			
			<div class="campo" style="float:left;width:50%">
				<fieldset style="min-height: 130px;">
				    <legend>Configuração de Software</legend>
				    
				    <div class="campo">
					    <div class="campo medio">
							<label for="ceqpveqpoid">Validade </label>
							<select id="ceqpveqpoid" name="ceqpveqpoid" class="campo obrigatorio">
								<option value="">Selecione a Validade</option>
								<?php foreach($this->combos['validade'] as $validade): 
									$veqpoid = $validade['veqpoid'];
									$veqpdescricao = $validade['veqpdescricao'];
									
									$sel = (isset($this->ceqpveqpoid) && $this->ceqpveqpoid == $veqpoid) ? "selected = 'selected'" : "";
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
									
									$sel = (isset($this->ceqpiposeqpoid) && $this->ceqpiposeqpoid == $iposeqpoid) ? "selected = 'selected'" : "";
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
									
									$sel = (isset($this->ceqpemeeqpoid) && $this->ceqpemeeqpoid == $emeeqpoid) ? "selected = 'selected'" : "";
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
									
									$sel = (isset($this->ceqpciceqpoid) && $this->ceqpciceqpoid == $ciceqpoid) ? "selected = 'selected'" : "";
								?>
								<option value="<?php echo $ciceqpoid ?>" <?php echo $sel; ?>><?php echo $ciceqpdescricao; ?></option>
									                
				                <?php endforeach; ?>
							</select>
						</div>	
					</div>
					
				</fieldset>
			</div>

			<div class="campo" style="float:left;width:49%;margin:5px 0px">
				<fieldset style="min-height: 130px;">
				    <legend>Referência ao Material</legend>
				    <p><label>Código: </label><a href="cad_material_novo.php?acao=editar&prdoid=<?php echo $this->ceqpprdoid?>" id="codigo-material" target="_blank"><?php echo $this->ceqpprdoid?></a><br></p>
				    <p><label>Tipo de Cadastro: </label><strong id="tipo-cadastro"><?php echo $arrTipoCadastro[$this->prdtp_cadastro];?></strong><br></p>
				    <p><label>Tipo de Produto: </label><strong id="tipo-produto"><?php echo $this->ptidescricao?></strong></p>
				    <p class="tipo-imobilizado" style="display:none"><label>Tipo de Imobilizado: </label><strong id="tipo-imobilizado"><?php echo $this->imotdescricao?></strong></p>
				    
				</fieldset>
			</div>
			<div class="clear" ></div>	

			<div class="campo" style="float:left;width:50%">
				<fieldset style="margin: 0; min-height: 100px;min-width:565px">
					<legend>Obrigação Financeira</legend>
					
					<div class="campo">
					
						<label>Valor de Referência</label><br>
						<select id="ceqpobroid" name="ceqpobroid" class="big obrigatorio" <?php if(!$permissaoObrigFin):?> disabled="disabled" <?php endif; ?>>
							<option value="">Selecione</option>						 
							<?php foreach($this->combos['obrigacaofinanceira'] as $obrigacaofinanceira): 
								$obroid = $obrigacaofinanceira['obroid'];
								$obrobrigacao = $obrigacaofinanceira['obrobrigacao'];  
								
								$sel = (isset($this->ceqpobroid) && $this->ceqpobroid == $obroid) ? "selected = 'selected'" : "";
							?>
							<option value="<?php echo $obroid ?>" <?php echo $sel?>><?php echo $obrobrigacao; ?></option>
								                
			                <?php endforeach; ?>
						</select> 
						<img align="absmiddle" onclick="mostrarHelpComment(this,'Esta obrigação financeira é usada apenas para dar um valor de referência para a plataforma de Vendas e CRM. O valor cadastrado nesta obrigação financeira e publicado na tabela de preços vigente, é o valor inicial que é apresentado para o consultor comercial na plataforma de Vendas e CRM. Esta obrigação financeira não é usada no faturamento. Seu preenchimento é reservado apenas ao departamento Financeiro.','D' , '');" onmouseout="document.body.style.cursor='default';" onmouseover="document.body.style.cursor='pointer';" src="images/help10.gif" />
					</div>
					<div class="clear" ></div>
					<label>Valor de Faturamento</label><br>
					<select id="ceqpobroid_valor_faturamento_sel" name="ceqpobroid_valor_faturamento_sel" class="big" disabled="disabled">
						<?php foreach($this->combos['valor_faturamento'] as $obrigacaofinanceira):
								$obroid = $obrigacaofinanceira['obroid'];
								$obrobrigacao = $obrigacaofinanceira['obrobrigacao'];
						?>
						<option value="<?php echo $obroid ?>"><?php echo $obrobrigacao; ?></option>
						<input type="hidden" value="<?php echo $obroid ?>" name="ceqpobroid_valor_faturamento" id="ceqpobroid_valor_faturamento" />
					    <?php endforeach; ?>
					</select>
				</fieldset>
			</div>

			<div class="campo" style="float:left;width:49%;margin:5px 0px">
				<fieldset id="fieldsetDisponibilidade" style="margin: 0; min-height: 100px;" <?php if(!$permissaoDispComercial || $this->ceqpobroid ==''):?> disabled="disabled" <?php endif; ?>>
				    <legend>Disponibilidade Comercial</legend>
				    <div class="campo menor">
				    	<br/>
					    <input type="radio" name="ceqpdisp_comercial" <?php echo ($this->ceqpdisp_comercial == 'T') ? 'checked' : '' ;?> value="S" id="ceqpdisp_comercialS" class="ceqpdisp_comercial"><label for="ceqpdisp_comercialSim">Sim</label>
					    <input type="radio" name="ceqpdisp_comercial" <?php echo ($this->ceqpdisp_comercial == 'F' || $this->ceqpdisp_comercial == '') ? 'checked' : '' ;?> value="N" id="ceqpdisp_comercialN" class="ceqpdisp_comercial"><label for="ceqpdisp_comercialNao">Não</label>  
					    <img onclick="mostrarHelpComment(this,'Indica se esta configuração é visivel para a plataforma de Vendas e CRM.','D' , '');" onmouseout="document.body.style.cursor='default';" onmouseover="document.body.style.cursor='pointer';" src="images/help10.gif" />
				    </div>
				</fieldset>
			</div>

			<?php if(!$permissaoObrigFin):?>
				<input type="hidden" id="ceqpobroid" name="ceqpobroid" value="<?php echo $this->ceqpobroid ?>" class="taxa_equip" />
			<?php endif; ?>	
			<div class="clear" ></div>
			
			<fieldset id="fieldsetTaxas" <?php if(!$permissaoObrigFin):?> disabled="disabled" <?php endif; ?>>
				<legend>Taxa(s)</legend>
			
					<div class="campo">
						<label for="ceqpobroid_taxa">Obrigação Financeira Taxa </label><br/>
						<select id="ceqpobroid_taxa" name="ceqpobroid_taxa" class="maior">
							<option value="">Selecione</option>
							<?php foreach($this->combos['obrigacaofinanceira'] as $obrigacaofinanceira): 
								$obroid = $obrigacaofinanceira['obroid'];
								$obrobrigacao = $obrigacaofinanceira['obrobrigacao'];
							?>
							<option value="<?php echo $obroid ?>"><?php echo $obrobrigacao; ?></option>
			                <?php endforeach; ?>
						</select>
						<img align="absmiddle" onclick="mostrarHelpComment(this,'Recomenda-se usar a taxa de adesão em todas as configurações de equipamentos retornáveis RTN, com incidência por contrato.','D' , '');" onmouseout="document.body.style.cursor='default';" onmouseover="document.body.style.cursor='pointer';" src="images/help10.gif" />
					</div>

					<div class="clear" ></div>
					
					<fieldset class="maior opcoes-display-block" id="incidencia_taxa">
					    <legend>Incidência de Taxa:</legend>
					    <input type="radio" name="ceqpincidencia_taxa" value="E" <?php echo (($this->ceqpincidencia_taxa == 'E' || !$this->ceqpincidencia_taxa) ? 'CHECKED' : '' ) ?> id="ceqpincidencia_taxa_equ"><label for="ceqpincidencia_taxa_equ">Cobrar a Cada Equipamento</label>
					    <input type="radio" name="ceqpincidencia_taxa" value="C" <?php echo ($this->ceqpincidencia_taxa == 'C' ? 'CHECKED' : '' ) ?> id="ceqpincidencia_taxa_con"><label for="ceqpincidencia_taxa_con">Cobrar a Cada Contrato</label>
					</fieldset>
					<div class="campo maior">
						<button type="button" value="salvar" id="buttonSalvarTaxa" name="buttonSalvarTaxa" class="salvar_taxa" >Inserir</button>
					</div>
					<div class="clear" ></div>
					<div class="separador"></div>	
					
					<div class="opcoes-display-block" id="table_taxa">
						<div class="bloco_titulo" style="margin: 0;">Obrigação Financeira Taxa</div>	
						<div class="bloco_conteudo" style="margin: 0;">
							<div class="listagem">
								<table id="lista-taxas-cadastradas">
									<thead>
										<tr>
											<th>Obrigação Financeira Taxa</th>
											<th class="maior">Incidência de Taxa</th>
											<th class="acao">Ação</th>
										</tr>
									</thead>
									<tbody>
									<?php 
										if(sizeof($this->taxas)>0){
											$cor = "par";		
											foreach($this->taxas as $taxa){
											$cor = ($cor=="par") ? "" : "par";
									?>
										<tr id="taxa_<?php echo $taxa['ceqptxoid']?>" class="<?php echo $cor;?>">
											<td><?php echo $taxa['obrobrigacao']?></td>
											<td><?php echo $taxa['incidencia']?></td>
											<td class="centro">
												<?php if($permissaoObrigFin): ?>
													<a href='javascript:void(0);' class='deletarTaxa' dir='<?php echo $taxa['ceqptxoid']?>'><img title='Excluir' src='images/icon_error.png' class='icone'></a>
												<?php endif; ?>
											</td>
										</tr>
									<?php 
											}
										}
									?>
									</tbody>
								</table>
							</div>
						</div>	
					</div>
			</fieldset>
			
			<fieldset id="fieldsetVendaRestritaForm">
				<legend>Venda Restrita</legend>				
				<input type="radio" id="venda_restritaSim" name="venda_restrita" value="S" <?php echo ($this->vendaRestrita == true) ? 'checked' : ''; ?> />
				<label for="venda_restritaSim">Sim</label>
				<input type="radio" id="venda_restritaNao" name="venda_restrita" value="N" <?php echo ($this->vendaRestrita == false) ? 'checked' : ''; ?>/>
				<label for="venda_restritaNao">Não</label>
			
				<div class="clear" ></div>		

				<div id="bloco_clientes" class="">
						        
					<div class="campo">
		                <label for="nomeCliente">Nome do Cliente</label><br>
		                <input type="text" id="nomeCliente" name="nomeCliente" maxlength="40" value="" class="campo medio" />		                	                
		                <button type="button" id="bt_pesquisar_cliente" name="bt_pesquisar_cliente">Pesquisar</button>	
					</div>
					
					<div class="clear"></div>
	
			        <div id="select_clientes" style="position: relative;">
				         <div id="resultados_busca_cliente" class="campo maior">
						 	<label for="clientes_resultado">Clientes Encontrados </label>
						 	<select id="clientes_resultado" multiple="multiple" >								
							</select>
						</div>
				        <button type="button" class="campo" id="bt_adicionar_lista" name="bt_adicionar_lista" style="position: absolute; top: 65px;">Adicionar na lista</button>
			        </div>
		        
			        <div class="clear"></div>
					<div class="separador"></div>
						<?php 
						if($this->vendaRestrita):
							foreach($this->clientes as $cliente):
							
								$lista[] = $cliente->clioid; 
							?>
						<?php 
							endforeach;

							$listaClientes = implode(',', $lista);
						endif;
						?>	
			        <input type="hidden" id="listaClientes" name="listaClientes" value="<?php echo $listaClientes;?>"/>
			        
			        <div id="bloco_lista_clientes" class="">
				        <div class="bloco_titulo" style="margin: 0;">Clientes Permitidos</div>	
				        <div class="bloco_conteudo" style="margin: 0;">
				        	<div class="listagem">
				        		<table id="lista_clientes">
									<thead>
										<tr>
											<th class="">Nome</th>
											<th>CPF / CNPJ</th>										
											<th class="acao">Ação</th>
										</tr>
									</thead>
									<tbody>	
										<?php 
										if($this->vendaRestrita):
											$cor = "par";		
											foreach($this->clientes as $cliente):
												$cor = ($cor=="par") ? "" : "par";

												echo '<tr class="'.$cor.'" id="tr_'.$cliente->clioid.'" dir="'.$cliente->clioid.'" >';
												echo '<td>'.$cliente->clinome.'</td>';
												echo '<td class="direita">'.$cliente->doc.'</td>';
												echo '<td class="centro">';	
											?>
												<img  onclick="excluirCliente(this)" id="excluir_cliente"  title="Excluir" src="images/icon_error.png" class="icone" /></td>																						
										<?php 
											endforeach;
										endif;
										?>				
									</tbody>
								</table>
							</div>
						</div>
					</div>	
		        </div>
			</fieldset>
		</div>
	</div>	
	<div class="bloco_acoes">
		<button type="button" value="salvar" id="buttonSalvar" name="buttonSalvar" class="salvar validacao">Salvar</button>
		<?php if($this->ceqpoid){?>
		<button type="button" value="excluir" id="buttonExcluir" name="buttonExcluir">Excluir</button>
		<?php }?>
		<button type="button" value="cancelar" id="buttonCancelar" name="buttonCancelar">Cancelar</button>
	</div>
</form>
<div class="separador"></div>