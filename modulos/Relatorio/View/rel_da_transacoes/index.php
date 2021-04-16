<form id="form_exemplo" name="form_exemplo" method="post" >
   		<div class="separador"></div>	
   		<!-- msg retorno -->	
   		<?php if ($retorno['msg']) { ?>
		<div <?php print $dados; ?> class="mensagem <?php print $retorno['tipo']; ?>" id="composicao"><?php print ($retorno['msg']); ?></div>
		<?php } ?>
		<!-- End msg retorno -->	
     <div class="bloco_titulo">Dados Para Pesquisa</div>
     	<div class="bloco_conteudo">     		
			<div class="formulario">				
				<div class="campo medio">
                    <label for="campo_2">Nome</label>
                    <input type="text" id="clinome" name="clinome" value="<?php print $clinome = ($_POST['clinome']) ? $_POST['clinome'] : '' ; ?>" class="campo">
               </div>              
			<div class="clear"></div>
			<fieldset class="maior">
                <legend>Tipo de Pessoa:</legend>
                
                <?php 		                           
               		$clitipof = ($_POST['clitipo'] == "F") ? 'checked="checked"' : '' ;
					
			   		$clitipoj = ($_POST['clitipo'] == "J") ? 'checked="checked"' : '' ;
					
			   		$clitipotodos = ($_POST['clitipo'] == "todos") ? 'checked="checked"' : '' ;
					
					if(!$_POST['clitipo']){
						$clitipotodos = 'checked="checked"';
					}
               ?>                    		 	
    		 	<input type="radio"  value="F" <?php print $clitipof;?>  name="clitipo" id="opcao_1_1">  
    		 	  <label for="opcao_1_1">Física</label> 
    		 	<input type="radio" value="J" <?php print $clitipoj;?> name="clitipo" id="opcao_2_1">  
    		 	  <label for="opcao_2_1">Jurídica</label>
    		 	<input type="radio"  <?php print $clitipotodos;?> value="todos" name="clitipo" id="opcao_3_1">  
    		 	  <label for="opcao_3_1">Todos</label>    		 	
             </fieldset>
             
			<div class="clear"></div>
			
                <div class="campo data periodo">
                	<label for="campo_11" style="text-align: center;">Período (Data do Vencimento)</label>
                    <div class="inicial">                       
                        <input type="text" name="dataInicial_v" id="dataInicial_v" value="<?php print $dataInicial_v = ($_POST['dataInicial_v']!="") ? $_POST['dataInicial_v'] : '' ;?>" placeholder="dd/mm/aaaa"  class="<?php print $retorno['dataInicial_v']; print $retorno['data_v_maior'];?> campo" />
                    </div>
                    <div class="campo label-periodo">a</div>
                    <div class="final">                       
                        <input type="text" name="dataFinal_v" id="dataFinal_v" value="<?php print $dataInicial_v = ($_POST['dataFinal_v']!="") ? $_POST['dataFinal_v'] : '' ;?>" placeholder="dd/mm/aaaa" class="<?php print $retorno['dataFinal_v']; print $retorno['data_v_maior'];?> campo" />
                    </div>
                </div>
                
                <div class="clear"></div>  
                
                <div class="campo data periodo">
                	<label for="campo_11" style="text-align: center;">Período (Data de Pagamento)</label>
                    <div class="inicial">                       
                        <input type="text" name="dataInicial_p" id="dataInicial_p" placeholder="dd/mm/aaaa" value="<?php print $dataInicial_v = ($_POST['dataInicial_p']!="") ? $_POST['dataInicial_p'] : '' ;?>" class="<?php print $retorno['dataInicial_p']; print $retorno['data_p_maior'];?> campo" />
                    </div>
                    <div class="campo label-periodo">a</div>
                    <div class="final">                       
                        <input type="text" name="dataFinal_p" id="dataFinal_p" value="<?php print $dataInicial_v = ($_POST['dataFinal_p']!="") ? $_POST['dataFinal_p'] : '' ;?>" placeholder="dd/mm/aaaa" class="<?php print $retorno['dataFinal_p']; print $retorno['data_p_maior'];?> campo" />
                    </div>
                </div>
                
                <div class="clear"></div>                
                
                <div class="campo data periodo">
                	<label for="campo_12" style="text-align: center;">Período (Data de Cadastro D.A)</label>
                    <div class="inicial">                       
                        <input type="text" placeholder="dd/mm/aaaa" name="dataInicial_c" id="dataInicial_c" value="<?php print $dataInicial_c = ($_POST['dataInicial_c']!="") ? $_POST['dataInicial_c'] : '' ;?>" class="<?php print $retorno['dataInicial_c']; print $retorno['data_c_maior'];?> campo" />
                    </div>
                    <div class="campo label-periodo">a</div>
                    <div class="final">
                        <input type="text" placeholder="dd/mm/aaaa" name="dataFinal_c" id="dataFinal_c" value="<?php print $dataFinal_c = ($_POST['dataFinal_c']!="") ? $_POST['dataFinal_c'] : '' ;?>" class="<?php print $retorno['dataFinal_c']; print $retorno['data_c_maior'];?> campo" />
                    </div>
                </div>
                
                <div class="clear"></div>
                
                <div class="campo data periodo" style="width: 300px;">
                	<label for="campo_12" style="text-align: center;">Período (Data de importação do arquivo de retorno)</label>
                    <div class="inicial">                       
                        <input type="text" placeholder="dd/mm/aaaa" name="dataInicial_ret" id="dataInicial_ret" value="<?php print $dataInicial_c = ($_POST['dataInicial_ret']!="") ? $_POST['dataInicial_ret'] : '' ;?>" class="<?php print $retorno['dataInicial_ret']; print $retorno['data_ret_maior'];?> campo" />
                    </div>
                    <div class="campo label-periodo">a</div>
                    <div class="final">
                        <input type="text" placeholder="dd/mm/aaaa" name="dataFinal_ret" id="dataFinal_ret" value="<?php print $dataFinal_c = ($_POST['dataFinal_ret']!="") ? $_POST['dataFinal_ret'] : '' ;?>" class="<?php print $retorno['dataFinal_ret']; print $retorno['data_ret_maior'];?> campo" />
                    </div>
                </div>
                
                <div class="clear"></div>
                
                <div class="campo medio">
                    <label for="feedback_2">Banco</label>
                    <select id="banco" name="banco">
				                    <option <?php print $bancoTodos = ($_POST['banco'] == 'todos') ? 'selected="seelected"' : '' ; ?>value="todos">Todos</option>
				                <? 
				                if($resultadoPesquisa['parametros_banco'] !=""){
				                    foreach ($resultadoPesquisa['parametros_banco'] as $value) {				                    	 
				                    	$bancoPara = ($_POST['banco'] == $value['forcoid']) ? 'selected="seelected"' : '' ;
				                    	?>										
				                    <option <?php print $bancoPara; ?> value="<?php print $value['forcoid']; ?>"><?php print $value['forcnome']; ?></option>
				               <?php }}?>				                  
				      </select>                   
                </div>
                
                 <div class="clear"></div>
                
                <div class="campo medio">
                    <label for="feedback_2">Status</label>
                    <select id="status" name="status">
                            		<?php
                            		$todos = ($_POST['status'] == "todos") ? 'selected="seelected"' : '';
									$efetuado = ($_POST['status'] == "1") ? 'selected="seelected"' : '';
									$naoefetuado = ($_POST['status'] == "2") ? 'selected="seelected"' : '';
									$aguardando = ($_POST['status'] == "3") ? 'selected="seelected"' : '';
                            		 ?>
				                    <option <?php print $todos;?> value="todos">Todos</option>
				                    <option <?php print $efetuado;?> value="1">Débito Efetuado</option>
				                    <option <?php print $naoefetuado;?> value="2">Débito não efetuado</option>
				                    <option <?php print $aguardando;?> value="3">Aguardando Débito</option>				                  
				                </select>                  
                </div>
                <!--			
				<table>
                                        
                    
                    <tbody>
                    	<tr class="campo data periodo" style="width: 564px;">
                    		 <td class=""> </td>
                    		 <td class="" for="dataInicial_v"> 
                    		 	<div class="inicial">				                   
				                    </div>
				                <div class="campo label-periodo" style="left: 298px; top: 2px;"> até </div>
				                <div class="final">				                 
				                   
				                </div>
				            </td>
                    	</tr>
                    </tbody>
                     <tbody>
                    	<tr class="campo data periodo" style="width: 564px;">
                    		 <td class=""> : &nbsp;</td>
                    		 <td class=""> 
                    		 	<div class="inicial">				                   
				                  </div>
				                <div class="campo label-periodo" style=" left: 298px; top: 2px;">até</div>
				                <div class="final">				                 
				                     </div>
				            </td>
                    	</tr>
                    </tbody>
                    <tbody>
                    	<tr class="campo data periodo" style="width: 564px;">
                    		 <td class="">: &nbsp;</td>
                    		 <td class=""> 
                    		 	<div class="inicial">				                   
				                      </div>
				                <div class="campo label-periodo" style=" left: 298px; top: 2px;">até</div>
				                <div class="final">				                 
				                       </div>
				            </td>
                    	</tr>
                    </tbody>
                     <tbody>
                    	 <tr class="campo maior">
                            <th>Banco: </th>
                            <th class="menor">
                            	<?php // print_r($resultadoPesquisa['parametros_banco']); ?>
                            	<select id="banco" name="banco">
				                    <option <?php print $bancoTodos = ($_POST['banco'] == 'todos') ? 'selected="seelected"' : '' ; ?>value="todos">Todos</option>
				                <? 
				                if($resultadoPesquisa['parametros_banco'] !=""){
				                    foreach ($resultadoPesquisa['parametros_banco'] as $value) {				                    	 
				                    	$bancoPara = ($_POST['banco'] == $value['cfbbanco']) ? 'selected="seelected"' : '' ;
				                    	?>										
				                    <option <?php print $bancoPara; ?> value="<?php print $value['cfbbanco']; ?>"><?php print $value['cfbnome']; ?></option>
				               <?php }}?>				                  
				                </select>
                            </th>                                                        
                        </tr>
                    </tbody>
                     <tbody>
                    	 <tr class="campo maior">
                            <th>Status:</th>
                            <th class="menor">
                            	<select id="status" name="status">
                            		<?php
                            		$todos = ($_POST['status'] == "todos") ? 'selected="seelected"' : '';
									$efetuado = ($_POST['status'] == "1") ? 'selected="seelected"' : '';
									$naoefetuado = ($_POST['status'] == "2") ? 'selected="seelected"' : '';
									$aguardando = ($_POST['status'] == "3") ? 'selected="seelected"' : '';
                            		 ?>
				                    <option <?php print $todos;?> value="todos">Todos</option>
				                    <option <?php print $efetuado;?> value="1">Débito Efetuado</option>
				                    <option <?php print $naoefetuado;?> value="2">Débito não efetuado</option>
				                    <option <?php print $aguardando;?> value="3">Aguardando Débito</option>				                  
				                </select>
                            </th>                                                        
                        </tr>
                    </tbody>
               </table>  -->             
	        
            <div class="clear"></div>
		  </div>
		</div>     
	   <div class="bloco_acoes">
		  <button type="submit" id="pesquisar">Pesquisar</button>
	  </div>
</form>
<div class="separador"></div>
<?php //print_r($retorno); ?>

<div style="display: none" class="ordenacao">
	 <div id="loader_1"  class="carregando"></div> 
</div>
<div id="resposta"></div>

			