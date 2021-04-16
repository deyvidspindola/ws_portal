<?php 
 if($retorno['dados'] != "apagar"){
	$valor = $retorno['dados'];
	$dados = $resultadoPesquisa['parametros_debito'];
}
//print_r($banco);


if($dados[0]->pdadt_envio_arquivo){
	$disabled = '';
}elseif(!empty($_POST['parametro_select_dataenvio'])){
   $disabled = '';	
}else{
	 $disabled = 'disabled="disabled"';	
}

?>
<form name="form_parametros" id="form_parametros" onsubmit="javascript: return valida()" class="form_parametros" method="post" action="" >
	<!-- Tabela do Formulário Gerar Arquivo de Remessa --> 
	 <?php if ($dados[0]->pdaoid >0) {?>
	 	 <input type="hidden" name="segundo" value="1">	
	    	 <input type="hidden" name="atualizar_pri" value="<?php print $dados[0]->pdaoid ;?>">	
	    <?php }?>
	<table width="98%" class="tableMoldura">
		<tr>
			<td class="tableSubTitulo" colspan="5">
				<h2>Gerar Arquivo de Remessa</h2>
			</td>
		</tr>
		<tr>
		  <td>
			 <table>
	             <thead>
	                <tr>
	                    <th class="meno">Data do 1° envio</th>                                    
	                    <th class="meno">Período de Faturamento</th>
	                    <th class="meno">Mês de Referência</th>		                	
	                    <th id="meno"   class="meno">Ação</th>                                              
	                </tr>
	             </thead>
	            <tbody>
	                <tr class="impar">
	                    <td class="meno">
	                        <select id="parametro_select_dataenvio" class="<?php print $retorno['parametro_select_dataenvio']; ?>" name="parametro_select_dataenvio">
	                        	 <option value="">Selecione</option>
	                            <?php 
	                            for($dia=1;$dia<=31;$dia++){                            	
										                            	
	                            	if(!isset($_POST['parametro_select_dataenvio'])){	                            		 
	                            		if($dia == $dados[0]->pdadt_envio_arquivo){	                            			
	                            		 $sel2 = "selected = 'selected'";
	                            		}else{
	                            			$sel2 = "";
	                            		}
	                            	}									
	                            	$sel = (isset($_POST['parametro_select_dataenvio']) && $_POST['parametro_select_dataenvio'] == $dia) ? "selected = 'selected'" : "";?>
									<option value="<?=$dia?>" <?php echo $sel; ?> <?php echo  $sel2;?>><?=$dia?></option>
								<?php	}?>
	                        </select>
	                    </td>
	                    <td class="meno centro">
	                        <select id="parametro_select_datainicial" <?php print $disabled; ?> class="<?php print $retorno['parametro_select']; ?> <?php print $retorno['parametro_select_inicial']; ?>" name="parametro_select_datainicial">
	                            <option value="">Selecione</option>
	                            <?php for($dia1=1;$dia1<=31;$dia1++){

	                            	if(empty($_POST['parametro_select_datainicial'])){

	                            		if($dia1 == $dados[0]->pdadt_inicio_faturamento){	                            			
	                            		 	$sel2 = "selected = 'selected'";
	                            		}else{
	                            			$sel2 = "";
	                            		}
	                            	}

	                            	$sel = (isset($_POST['parametro_select_datainicial']) && $_POST['parametro_select_datainicial'] == $dia1) ? "selected = 'selected'" : "";?>
									<option value="<?=$dia1?>" <?php echo $sel; ?> <?php echo  $sel2;?>><?=$dia1?></option>
								<?php	}?>
	                        </select>
	                        &nbsp; a &nbsp;
	                        <select id="parametro_select_datafinal" <?php print $disabled; ?> class="<?php print $retorno['parametro_select']; ?> <?php print $retorno['parametro_select_final']; ?>" name="parametro_select_datafinal">
	                             <option value="">Selecione</option>
	                            <?php for($dia=1;$dia<=31;$dia++){
	                            	if(empty($_POST['parametro_select_datafinal'])){	                            		 
	                            		if($dia == $dados[0]->pdadt_fim_faturamento){	                            			
	                            		 	$sel2 = "selected = 'selected'";
	                            		}else{
	                            			$sel2 = "";
	                            		}
	                            	}	
	                            	$sel = (isset($_POST['parametro_select_datafinal']) && $_POST['parametro_select_datafinal'] == $dia) ? "selected = 'selected'" : "";?>
									<option value="<?=$dia?>" <?php echo $sel; ?> <?php echo  $sel2;?>><?=$dia?></option>
								<?php	}?>
	                        </select>
	                    </td>
	                    <td class="meno centro">
	                    	
	                        <select id="parametro_select_mes" <?php print $disabled; ?> class="<?php print $retorno['parametro_select_mes']; ?>" name="parametro_select_mes">
	                             <option value="">Selecione</option>
	                        	<option value="A" <? print $Val = ($dados[0]->pdames_referencia == "A") ? 'selected' : '' ; ?> <?php print $retVal = ($_POST['parametro_select_mes'] == "A") ? 'selected' : '' ; ?>>Mês Atual</option>
	                        	<option value="S" <? print $Val = ($dados[0]->pdames_referencia == "S") ? 'selected' : '' ; ?> <?php print $retVal = ($_POST['parametro_select_mes'] == "S") ? 'selected' : '' ; ?>>Mês Seguinte</option>
	                        </select>
	                    </td>
	                    <td class="meno centro"   style=" <?php print $retVal = ($_POST['parametro_select_dataenvio_'.$ativo]) ? 'display: none;' : '' ;?> "> 	                    		
	                    		<button type="button" id="limpa1" value="Excluir" />	Limpar</button>	
	                    </td>
	                   
	                </tr>
	            </tbody>
	        </table>	
	    <?php $ativo = 1;

       $banco = $valor['banco'];

       if($dados[1]->pdadt_envio_arquivo){
			$disabled = '';
		}elseif(!empty($_POST['parametro_select_dataenvio_1'])){
		   $disabled = '';			
		}else{
			 $disabled = 'disabled="disabled"';	
		}
	     ?>	    	   	    	 
	    		
			<table>
	             <thead>
	                <tr>
	                    <th class="meno">Data do 2° envio</th>                                    
	                    <th class="meno">Período de Faturamento</th>
	                    <th class="meno">Mês de Referência</th>
	                    
	                </tr>
	             </thead>
	            <tbody>
	                <tr class="impar">
	                    <td class="meno">
	                        <select id="parametro_select_dataenvio_<?php print $ativo;?>"  class="<?php print $retorno['parametro_select_dataenvio_1']; ?>" name="parametro_select_dataenvio_<?php print $ativo;?>">		                                          
	                             <option value="">Selecione</option>
	                            <?php 
	                            for($dia=1;$dia<=31;$dia++){	                            	
	                            	if(!isset($_POST['parametro_select_dataenvio_'.$ativo])){	                            		 
	                            		if($dia == $dados[$ativo]->pdadt_envio_arquivo){	                            			
	                            			$sel3 = "selected = 'selected'";
	                            		}else{
	                            			$sel3 = "";
	                            		}
	                            	}									
	                            	$sel4 = (isset($_POST['parametro_select_dataenvio_'.$ativo]) && $_POST['parametro_select_dataenvio_'.$ativo] == $dia) ? "selected = 'selected'" : "";?>
									<option value="<?=$dia?>" <?php echo $sel4; ?> <?php echo  $sel3;?>><?=$dia?></option>
								<?php	}?>
	                        </select>
	                    </td>
	                    <td class="meno centro">
	                        <select id="parametro_select_diainicial_<?php print $ativo;?>" <?php print $disabled;?> class="<?php print $retorno['parametro_select_1'];?> <?php print $retorno['parametro_select_diainicial_1'];?>" name="parametro_select_diainicial_<?php print $ativo;?>">
	                            <option value="">Selecione</option>
	                            <?php 
	                            for($dia=1;$dia<=31;$dia++){	                            	
	                            	if(empty($_POST['parametro_select_diainicial_'.$ativo])){	                            		 
	                            		if($dia == $dados[$ativo]->pdadt_inicio_faturamento){	                            			
	                            		 $sel3 = "selected = 'selected'";
	                            		}else{
	                            			$sel3 = "";
	                            		}
	                            	}									
	                            	$sel4 = (isset($_POST['parametro_select_diainicial_'.$ativo]) && $_POST['parametro_select_diainicial_'.$ativo] == $dia) ? "selected = 'selected'" : "";?>
									<option value="<?=$dia?>" <?php echo $sel4; ?> <?php echo  $sel3;?>><?=$dia?></option>
								<?php	}?>
	                        </select>
	                        &nbsp; a &nbsp;
	                        <select id="parametro_select_diaFinal_<?php print $ativo;?>" <?php print $disabled;?>  class="<?php print $retorno['parametro_select_1'];?> <?php print $retorno['parametro_select_diaFinal_1'];?>" name="parametro_select_diaFinal_<?php print $ativo;?>">
	                            <option value="">Selecione</option>
	                            <?php 
	                            for($dia=1;$dia<=31;$dia++){	                            	
	                            	if(empty($_POST['parametro_select_diaFinal_'.$ativo])){	                            		 
	                            		if($dia == $dados[$ativo]->pdadt_fim_faturamento){	                            			
	                            		 $sel3 = "selected = 'selected'";
	                            		}else{
	                            			$sel3 = "";
	                            		}
	                            	}									
	                            	$sel4 = (isset($_POST['parametro_select_diaFinal_'.$ativo]) && $_POST['parametro_select_diaFinal_'.$ativo] == $dia) ? "selected = 'selected'" : "";?>
									<option value="<?=$dia?>" <?php echo $sel3; ?> <?php echo  $sel4;?>><?=$dia?></option>
								<?php	}?>
	                        </select>
	                    </td>
	                    <td class="meno centro">
	                        <select id="parametro_select_mes_<?php print $ativo;?>" <?php print $disabled;?> class="<?php print $retorno['parametro_select_mes_1'];?>" name="parametro_select_mes_<?php print $ativo;?>">
	                        	<option value="">Selecione</option>
	                        	<option value="A" <? print $Val = ($dados[$ativo]->pdames_referencia == "A") ? 'selected' : '' ; ?> <?php print $retVal = ($_POST['parametro_select_mes_'.$ativo] == "A") ? 'selected' : '' ; ?>>Mês Atual</option>
	                        	<option value="S" <? print $Val = ($dados[$ativo]->pdames_referencia == "S") ? 'selected' : '' ; ?> <?php print $retVal = ($_POST['parametro_select_mes_'.$ativo] == "S") ? 'selected' : '' ; ?>>Mês Seguinte</option>	                               
	                        </select>
	                    </td>	
	                    <td class="meno centro" > 	                    		
	                    		<button type="button" id="limpa2" value="Excluir" />Limpar</button>	
	                    </td>                    
	                </tr>
	            </tbody>
	        </table>	
	        	<div class="separador"></div>			
		</td>
	  </tr>												
	</table>
	<!-- The end Gerar Arquivo de Remessa -->
	<div class="separador"></div>
	<!-- Tabela do Formulário Banco a Enviar o arquivo de remessa --> 
		
		<table width="98%" class="tableMoldura">
			<tr>
				<td class="tableSubTitulo" colspan="2">
					<h2>Banco a Enviar o arquivo de remessa</h2>
				</td>
			</tr>							
			<tr class="par"> 							   
				 <?php 
				 $i = '';
				 $a = 0;
				 $checked = '';
				 
				 foreach ($resultadoPesquisa['comboClassesFormaCobranca'] as $values){ 
				 	$var = '';
					if(!empty($_POST['banco'])){					
					 	foreach ($_POST['banco'] as $key) {
						  if($key == $values['cfbbanco']){						
						       $var = 1;
						     }
						}
					}
					if(!$_POST){
						if($values['cfcarquivo_remessa'] == 't'){
							$checked = 'checked="checked"';
						}else if($values['cfcarquivo_remessa'] == 'f'){
							$checked = '';
						}
					}
					
					 ?>
				 	
				 	<?php if($i == ""){ print '<td class="opcoes-display-block">'; }elseif($i == 3){ print '<td class="opcoes-display-block2">';} ?>
				 		<input type="checkbox" <?php print $checked; ?> <?php print $retVal = ($var == 1) ? 'checked="checked"' : b ; ?> name="banco[]" value="<?php print $values['cfbbanco'];?>" > <label for="opcao_4_1" class="<?php print $retorno['banco'];?>"> <?php print $values['forcnome'];?></label></br>	                            	                             
	              <?php $i = 1 + $i;  $a++; }?> 
	            </td>                           
	         </tr>													
		</table>
		<!-- the end Banco a Enviar o arquivo de remessa -->
	<div class="separador"></div>
	<!-- Tabela do Formulário Aviso do arquivo de remessa Gerado --> 								
		<table width="98%" class="tableMoldura">
			<tr>
				<td class="tableSubTitulo" colspan="2">
					<h2>Aviso do arquivo de remessa Gerado</h2>
				</td>
			</tr>							
			<tr>
				<td width="8%" class="bloco_conteudo" >
					<div class="campo maior">
						<label for="campo_3">Enviar aviso para o email</label>
						<?php if(!empty($_POST['email']))
						         	$dados = $_POST['email'];							
								else
								 	$dados = $dados[0]->pdadt_email_aviso
						  ?>
						<input id="email" class="campo <?php print $retorno['email']; ?>"  type="text"  name='email' placeHolder="Seu e-mail" value="<?php print trim($dados);?>"><p>&nbsp; Separar com ";" para adicionar mais emails.</p>
					</div>									
				</td>
			</tr>							
		</table>		
	<!-- the end Aviso do arquivo de remessa Gerado -->
	</td>
	</tr>			
	<tr class="tableRodapeModelo1">
	<td align="center" colspan="2">	
	   <button type="submit" id="form_enviar" class="botao">Salvar</button>				   
	</td>
</form>
			