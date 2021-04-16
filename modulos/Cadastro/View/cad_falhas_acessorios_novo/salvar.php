        <div class="separador"></div>        
        
        <div id="msg_erro2" class="mensagem erro invisivel"></div>
        <div id="msg_sucesso2" class="mensagem sucesso invisivel"></div>        
        <div id="msg_alerta2" class="mensagem alerta invisivel"></div>  
        
         <div id="cadastro_novo">
            <div class="bloco_titulo">Dados de Campo</div>
            <div class="bloco_conteudo">					
                <div class="listagem">
                    <table>
                        <thead>
                            <tr>                            	
                                <th>Serial</th>
                                <th>Defeito Constatado</th>                                 
                                <th>Causa</th>
                                <th>Ocorrência</th>
                                <th>Solução.</th>
                                <th>Componente</th> 
                                <th>Motivo</th>                                
                            </tr>
                        </thead>
                        <tbody id="conteudo_listagem">
                        	<?php 
                        	if($this->ordemServico !== false) : ?>	
							<tr <?php echo $class?>>   
								<td ><?php echo $this->ordemServico->imobserial; ?></td>
								<td> <?php echo $this->ordemServico->osdfdescricao; ?></td>
								<td><?php  echo $this->ordemServico->otcdescricao; ?></td>
								<td><?php  echo $this->ordemServico->otodescricao; ?></td>
								<td><?php  echo $this->ordemServico->otsdescricao; ?></td>
								<td><?php  echo $this->ordemServico->otadescricao; ?></td>
								<td><?php  echo $this->ordemServico->otidescricao; ?></td>									             
							</tr>
							<?php else : ?>
							<tr <?php echo $class?>>   
								<td colspan='6'>Nenhum registro encontrado.</td>									             
							</tr>
							<?php endif; ?>
                       </tbody>
                    </table>
                </div>
            </div> 
                 
            <div class="separador"></div>
	        <div id="msg_erro3" class="mensagem erro invisivel"></div>
	        <div id="msg_sucesso3" class="mensagem sucesso invisivel"></div>        
	        <div id="msg_alerta3" class="mensagem alerta invisivel"></div>  
            
            <div class="bloco_titulo">Dados Lab.</div>
            <div class="bloco_conteudo">					
                <div class="listagem">
                    <table>
                        <thead>
                            <tr>                            	
                                <th>Serial</th>
                                <th>Produto</th>                                 
                                <th>Data Entrada Lab.</th>
                                <th>Defeito Lab.</th>
                                <th>Ação Lab.</th>
                                <th>Componente Afetado Lab.</th> 
                                                          
                            </tr>
                        </thead>
                        <tbody id="conteudo_listagem2">	
                		<?php 
                			if(count($this->registrosComuns) > 0) {
								$class='class="par"';
                				foreach ($this->registrosComuns as $registro){
									$class = $class == '' ? 'class="par"' : '';
                		 ?>                		 
		                        <tr <?php echo $class;?>>                          
		                            <td><?php echo $registro->imobserial; ?></td>
		                            <td><?php echo $registro->prdproduto; ?></td>                                 
		                            <td><?php echo $registro->cfadt_entrada; ?></td>
		                            <td><?php echo wordwrap($registro->ifddescricao, 25, "<br />", true); ?></td>		                    		  
		                            <td><?php echo wordwrap($registro->ifadescricao, 25, "<br />", true); ?></td>
		                            <td><?php echo wordwrap($registro->ifcdescricao, 25, "<br />", true); ?></td>                              
                       			</tr>               		 
                		 <?php 
                		 		}
							}
                		 ?>
                        </tbody>
                        <tbody id="conteudo_listagem_insert">	
                        <tr>                          
                            <td><?php echo $this->parametros->serial; ?></td>
                            <td><?php echo $this->registro->prdproduto; ?></td>                                 
                            <td><?php echo ($this->parametros->editar ? $this->registro->cfadt_entrada : $this->registro->dataEntradaLab)?></td>
                            <td>
                            	<select class="campo medio" id="defeito_lab" name="defeito_lab" >
	                       			<option value="">Escolha</option>
	                           	<?php 
	                           	$editavel = $this->parametros->editar;
	                           		foreach($this->combos->defeitos as $defeitos): 
	                           			$select = (($editavel && $defeitos->ifdoid == $this->registro->ifdoid) ? "selected=\"selected\"" : "");
	                           	?>
	                            	<option value="<?php echo $defeitos->ifdoid; ?>" <?php echo $select;?>><?php echo $defeitos->ifddescricao; ?></option>  
	                            <?php 
	                            	endforeach;
	                            ?>   
	                    		</select> 
	                    	</td>
                    		  
                            <td>
                            	 <select  class="campo medio" id="acao_lab" name="acao_lab">
			                        <option value="">Escolha</option>
	                           	<?php foreach($this->combos->acoes as $acoes):  
	                           				$select = (($editavel && $acoes->ifaoid == $this->registro->ifaoid) ? "selected=\"selected\"" : "");
	                           	?>
	                           			<option value="<?php echo $acoes->ifaoid ?>" <?php echo $select;?>><?php echo $acoes->ifadescricao ?></option>  
	                            <?php endforeach; ?>   
	                    		</select> 						
                            </td>
                            <td>                            
                              	 <select class="campo medio" id="componente_lab" name="componente_lab">
			                        <option value="">Escolha</option>  
			                       	<?php foreach($this->combos->componentes as $componentes):  
	                           					$select = (($editavel && $componentes->ifcoid == $this->registro->ifcoid) ? "selected=\"selected\"" : "");
			                       	?>
			                       		<option value="<?php echo $componentes->ifcoid; ?>" <?php echo $select;?>><?php echo $componentes->ifcdescricao; ?></option>  
	                           		 <?php endforeach; ?>   							
			                     </select>			                 
			                     			                     
						 </td>                              
                       </tr>
                       </tbody>
                    </table>
                </div>
            </div>
            <div class="bloco_acoes">
            	<img src="modulos/web/images/ajax-loader-circle.gif" class="invisivel" id="loading_gravar"/>
            	<button id="btn_gravar" type="button">Gravar</button>
        	</div>
        </div>     
        
