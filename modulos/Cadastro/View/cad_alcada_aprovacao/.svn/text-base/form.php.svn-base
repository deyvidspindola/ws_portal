<?php 
	// Limpa a Sessão de taxas
	unset($_SESSION['conf_equ_taxa']);
?>
<div class="bloco_titulo"><?php echo $this->tituloTela?></div>

<form action="" name="cadastro_configuracao_equipamento" id="cadastro_configuracao_equipamento" method="post">
    <input type="hidden" name="acao" id="acao" value="<?php echo $this->acaoTela?>" />
    <input type="hidden" name="ceqpoid" id="ceqpoid" value="<?php echo $this->ceqpoid ?>" />
    <input type="hidden" name="tipoproduto" id="tipoproduto" value="<?php echo $this->tipoproduto ?>" />
	<div class="bloco_conteudo">
		<div class="conteudo">
		
			<div class="campo pesquisaMaior">
				<label for="equipamento_busca">Equipamento </label>
				<input type="hidden" id="ceqpprdoid" name="ceqpprdoid" value="<?=$this->ceqpprdoid?>"/>
				<input type="text" id="equipamento_busca" name="equipamento_busca" value="<?=$this->equipamento_busca?>" class="campo obrigatorio"/>
				<button type="button" value="pesquisarEquipamento" id="buttonPesquisarEquipamento" name="buttonPesquisarEquipamento">Pesquisar</button>
			</div>

			<div class="clear" ></div>

			 <div class="campo maior" style="display: none">
			 	<label for="equipamentosEncontrados">Equipamento Encontrados </label>
			 	<select id="equipamentosEncontrados" multiple="multiple" style="min-height: 100px; max-heigth: 150px;">
					
				</select>
			</div>
			<div class="clear" ></div>
			
			<div id="descCaracteristica">
				<div class="campo menor"><label for="textCaracteristica">Característica </label></div>
				<div class="campo maior" id="textCaracteristica">
				    <?php 
				    if($this->tipoproduto == 'I'){
        				echo 'Rastreador Retornável (RTN)';	
        			}else if($this->tipoproduto == 'C'){
        				echo 'Rastreador Descartável (DCT)';
        			}else{
        				echo 'Outros';	
        			}?>
        			</div> 
        			<div class="clear" >
				</div>
			</div>
			
			<div class="campo medio">
				<label for="ceqpveqpoid">Validade </label>
				<select id="ceqpveqpoid" name="ceqpveqpoid" value="" class="obrigatorio">
					<option value="">Selecione</option>
					
					<?php foreach($this->combos['validade'] as $validade): 
						$veqpoid = $validade['veqpoid'];
						$veqpdescricao = $validade['veqpdescricao'];
						
						$sel = (isset($this->ceqpveqpoid) && $this->ceqpveqpoid == $veqpoid) ? "selected = 'selected'" : "";
					?>
					<option value="<?php echo $veqpoid ?>" <?php echo $sel?>><?php echo $veqpdescricao; ?></option>
						                
	                <?php endforeach; ?> 
				</select>
			</div>		
			<div class="campo medio">
				<label for="ceqpiposeqpoid">Intervalo </label>
				<select id="ceqpiposeqpoid" name="ceqpiposeqpoid">
					<option value="">Selecione</option>
					
					<?php foreach($this->combos['intervalo'] as $intervalo): 
						$iposeqpoid = $intervalo['iposeqpoid'];
						$iposeqpdescricao = $intervalo['iposeqpdescricao'];
						
						$sel = (isset($this->ceqpiposeqpoid) && $this->ceqpiposeqpoid == $iposeqpoid) ? "selected = 'selected'" : "";
					?>
					<option value="<?php echo $iposeqpoid ?>" <?php echo $sel?>><?php echo $iposeqpdescricao; ?></option>
						                
	                <?php endforeach; ?> 
				</select>
			</div>		
			<div class="clear" ></div>
			<div class="campo medio">
				<label for="ceqpemeeqpoid">Emergência </label>
				<select id="ceqpemeeqpoid" name="ceqpemeeqpoid">
					<option value="">Selecione</option>
					
					<?php foreach($this->combos['emergencia'] as $emergencia): 
						$emeeqpoid = $emergencia['emeeqpoid'];
						$emeeqpdescricao = $emergencia['emeeqpdescricao'];
						
						$sel = (isset($this->ceqpemeeqpoid) && $this->ceqpemeeqpoid == $emeeqpoid) ? "selected = 'selected'" : "";
					?>
					<option value="<?php echo $emeeqpoid ?>" <?php echo $sel?>><?php echo $emeeqpdescricao; ?></option>
						                
	                <?php endforeach; ?> 
				</select>
			</div>		
			<div class="campo medio">
				<label for="ceqpciceqpoid">Ciclo </label>
				<select id="ceqpciceqpoid" name="ceqpciceqpoid">
					<option value="">Selecione</option>
					
					<?php foreach($this->combos['ciclo'] as $ciclo): 
						$ciceqpoid = $ciclo['ciceqpoid'];
						$ciceqpdescricao = $ciclo['ciceqpdescricao'];
						
						$sel = (isset($this->ceqpciceqpoid) && $this->ceqpciceqpoid == $ciceqpoid) ? "selected = 'selected'" : "";
					?>
					<option value="<?php echo $ciceqpoid ?>" <?php echo $sel?>><?php echo $ciceqpdescricao; ?></option>
						                
	                <?php endforeach; ?> 
				</select>
			</div>
			<div class="clear" ></div>			
				
			<div class="campo maior">
				<label for="ceqpobroid">Obrigação Financeira</label>
				<select id="ceqpobroid" name="ceqpobroid" class="campo obrigatorio">
					<option value="">Selecione</option>
					
					<?php foreach($this->combos['obrigacaofinanceira'] as $obrigacaofinanceira): 
						$obroid = $obrigacaofinanceira['obroid'];
						$obrobrigacao = $obrigacaofinanceira['obrobrigacao'];  
						
						$sel = (isset($this->ceqpobroid) && $this->ceqpobroid == $obroid) ? "selected = 'selected'" : "";
					?>
					<option value="<?php echo $obroid ?>" <?php echo $sel?>><?php echo $obrobrigacao; ?></option>
						                
	                <?php endforeach; ?> 
				</select>
			</div>
			<div class="clear" ></div>	
						
			<div class="bloco_titulo" style="margin: 0;">Taxa(s):</div>
			<div class="bloco_conteudo" style="margin: 0;">
				<div class="conteudo">
					<div class="campo maior">
						<label for="ceqpobroid_taxa">Obrigação Financeira Taxa </label>
						<select id="ceqpobroid_taxa" name="ceqpobroid_taxa" class="campo">
							<option value="">Selecione</option>
							
							<?php foreach($this->combos['obrigacaofinanceira'] as $obrigacaofinanceira): 
								$obroid = $obrigacaofinanceira['obroid'];
								$obrobrigacao = $obrigacaofinanceira['obrobrigacao'];
								
// 								$sel = (isset($this->ceqpobroid_taxa) && $this->ceqpobroid_taxa == $obroid) ? "selected = 'selected'" : "";
							?>
							<option value="<?php echo $obroid ?>" <?php // echo $sel?>><?php echo $obrobrigacao; ?></option>
		
			                <?php endforeach; ?> 
						</select>
					</div>
					<div class="clear" ></div>
					
					<fieldset class="maior opcoes-display-block" id="incidencia_taxa">
					    <legend>Incidência de Taxa:</legend>
					    <input type="radio" name="ceqpincidencia_taxa" value="E" <?php echo (($this->ceqpincidencia_taxa == 'E' || !$this->ceqpincidencia_taxa) ? 'CHECKED' : '' ) ?> id="ceqpincidencia_taxa_equ"><label for="ceqpincidencia_taxa_equ">Cobrar a Cada Equipamento</label>
					    <input type="radio" name="ceqpincidencia_taxa" value="C" <?php echo ($this->ceqpincidencia_taxa == 'C' ? 'CHECKED' : '' ) ?> id="ceqpincidencia_taxa_con"><label for="ceqpincidencia_taxa_con">Cobrar a Cada Contrato</label>
					</fieldset>
					<div class="campo maior">
						<button type="button" value="salvar" id="buttonSalvarTaxa" name="buttonSalvarTaxa" class="salvar_taxa desabilitado" style="margin: 0;" disabled="disabled" >Inserir</button>
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
												<td class="centro"><a href='javascript:void(0);' class='deletarTaxa' dir='<?php echo $taxa['ceqptxoid']?>'><img title='Excluir' src='images/icon_error.png' class='icone'></a></td>
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
				</div>
				<div class="separador"></div>	
			</div>
				<div class="separador"></div>	
		</div>
	</div>	
	<div class="bloco_acoes">
		<button type="button" value="salvar" id="buttonSalvar" name="buttonSalvar" class="salvar validacao">Salvar</button>
		<?php if($this->tituloTela == 'Editar'){ ?>
		<button type="button" value="excluir" id="buttonExcluir" name="buttonExcluir">Excluir</button>
		<?php } ?>
		<button type="button" value="cancelar" id="buttonCancelar" name="buttonCancelar">Cancelar</button>
	</div>
</form>
<div class="separador"></div>