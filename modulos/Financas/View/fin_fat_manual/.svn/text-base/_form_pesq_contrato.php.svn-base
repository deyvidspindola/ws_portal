
<style>
.componentePesquisa{
left: 0px; 

top: 1px; 
height: 200px !important; 
border-width: 1px; 
border-style: solid; 
border-color: rgb(255, 255, 255) rgb(204, 204, 204) rgb(204, 204, 204); 
font-family: Arial; font-size: 12px; overflow-y: auto; 
overflow-x: hidden; 
width: 300px; 
box-shadow: rgb(136, 136, 136) 2px 2px 2px; 
z-index: 999999; 
display: none; 
background-color: rgb(255, 255, 255);
}

.cpx_div_link2{
margin: 0px; 
padding: 1px; 
cursor: pointer; 
border-bottom-width: 1px; 
border-bottom-style: solid; 
border-bottom-color: rgb(239, 239, 239);
}
</style>

<div class="bloco_titulo">Inclusão de novo item</div>
<div class="bloco_conteudo">
			<div class="separador"></div>
			
	<div id="frm_pesquisa_contato">
			
			<div class="bloco_titulo">Pesquisar contrato</div>
			<div class="bloco_conteudo">		
				<div class="formulario">
					<div class="campo medio">					
							<label for="placa">Cliente</label>
							<input type="text" id="cliente" name="cliente" value="" class="campo" maxlength="15"/>
							<input type="hidden" id="id_cliente" name="id_cliente" />
						</div>
							<div class="campo medio">	
							<p>				
							<input type="button" id="limparCliente" name="limparCliente" value="Limpar"/>
						</div>
						<div class="clear"></div>
						
							<div class="campo medio">					
					<div class="componentePesquisa" id="cpx_div_result_cliente_nome_2"></div>
					<div class="clear"></div>
						<div class="campo medio">					
							<label for="contrato_item">Contrato</label>
							<input type="text" id="contrato_item" name="contrato_item" value="" class="campo" onKeyup="formatar(this, '@');" onBlur="revalidar(this, '@', '');"  maxlength="10" />
						</div> 
				<!-- 	<div class="campo medio">					
							<label for="contratos">Status do Termo</label>
							<select id="contratos" name="contratos">
                        		<option value="">Escolha</option>
                        		<?php foreach ($this->dao->getSituacaoContrato() as $classe): ?>
									<option value="<?=$classe['csioid']?>"><?=$classe['csidescricao']?></option>
								<?php endforeach;?>
							</select> 
						</div>-->					
						<div class="clear"></div>
						
						<div class="campo medio">					
							<label for="placa">Placa</label>
							<input type="text" id="placa" name="placa" value="" class="campo" maxlength="15"/>
						</div> 
							
					
					
						</div>
					
						
			<!-- 			<div class="campo medio">					
							<label for="classe_equipamento">Classe</label>
							<select id="classe_equipamento" name="classe_equipamento">
                        		<option value="">Escolha</option>
								<?php foreach ($this->dao->getClassesEquipamento() as $classe): ?>
									<option value="<?=$classe['eqcoid']?>"><?=$classe['eqcdescricao']?></option>
								<?php endforeach;?>
							</select>
						</div>-->
						
						<div class="clear"></div>
						
					<!-- 	<div class="campo medio">					
							<label for="equipamento">Equipamento</label>
							<input type="text" id="equipamento" name="equipamento" value="" class="campo"  onKeyup="formatar(this, '@');" onBlur="revalidar(this, '@', '');"  maxlength="10"  />
						</div> -->
					<!-- 	<div class="campo medio">					
							<label for="tipo_contrato">Tipo</label>
							<select id="tipo_contrato" name="tipo_contrato">
                        		<option value="">Escolha</option>
								<?php foreach ($this->dao->getTiposContrato() as $tipo): ?>
									<option value="<?=$tipo['tpcoid']?>"><?=$tipo['tpcdescricao']?></option>
								<?php endforeach;?>
							</select>
						</div>	-->			
						<div class="clear"></div>						
						
							
				  </div>
			</div>		
			<div class="bloco_acoes">
				<button type="button" id="bt_pesquisa_contrato">Pesquisar</button>
				<button type="button" id="bt_inclui_sem_contrato">Item sem Contrato</button>
			</div>			
			<div class="separador"></div>
			
	</div>
			
	<div class="mensagem alerta"  id="msgalerta4"   style="display:none;" ></div>
	<div class="mensagem sucesso" id="msgsucesso4"  style="display:none;" ></div>
	<div class="mensagem erro"    id="msgerro4"     style="display:none;" ></div>
			
	<!-- LISTA DE CONTRATOS PESQUISADOS - CARREGA VIA AJAX -->
	<div id="frame02"></div>		
	
	<div class="mensagem alerta"  id="msgalerta5"   style="display:none;" ></div>
	<div class="mensagem sucesso" id="msgsucesso5"  style="display:none;" ></div>
	<div class="mensagem erro"    id="msgerro5"     style="display:none;" ></div>
			
	<!-- FORMULÁRIO DE INCLUSAO DE ITEM - CARREGA VIA AJAX -->
	<div id="frame03"></div>		
	
</div>		
<div class="bloco_acoes">
	<button type="button" id="bt_retorna_contrato">Cancelar inclusão do item</button>
</div>	
<div class="separador"></div>