<?php
try {
	if ($acao=="editar") {
		
		$mProposta_segurado = $objPropostaSeguradora->propostaSegurado($id);
		
		$mProposta_segurado['prpsscnpj_cpf'] = str_replace('.','',$mProposta_segurado['prpsscnpj_cpf']);
		$mProposta_segurado['prpsscnpj_cpf'] = str_replace('-','',$mProposta_segurado['prpsscnpj_cpf']);
		$mProposta_segurado['prpsscnpj_cpf'] = str_replace('/','',$mProposta_segurado['prpsscnpj_cpf']);

		if($mProposta_segurado['prpstipo_pessoa'] == 'F')
		{		
			$conta_carac =  strlen($mProposta_segurado['prpsscnpj_cpf']);
			if($conta_carac < '11')
			{
				$mProposta_segurado['prpsscnpj_cpf'] = STR_PAD($mProposta_segurado['prpsscnpj_cpf'],"11","0",STR_PAD_LEFT);
			}		
		}

		if($mProposta_segurado['prpstipo_pessoa'] == 'J')
		{
			$conta_carac =  strlen($mProposta_segurado['prpsscnpj_cpf']);
			if ($conta_carac < '14')
			{
				$mProposta_segurado['prpsscnpj_cpf'] = STR_PAD($mProposta_segurado['prpsscnpj_cpf'],"14","0",STR_PAD_LEFT);
			}					
		}
	} else {
		$mProposta_segurado = array();
	}
	
?>
	
<center>
<style media="print">

	#filtro_pesquisa{
    	display:none;
    }
    #botoes_geradores{
    	display:none;
    }
    #cabecalho{
    	display:none;
    }
    #bt_pesquisar{
    	display:none;
    }
    
</style>
    
    
<center>
<table class="tableMoldura" >
	<tr class="tableTitulo">
    	<td><h1>Proposta Seguradora</h1></td>
	</tr>
	<?
    if($mensagem !="") {
        ?>
        <tr>
            <td class="msg"><b><?php echo $mensagem?><b></td>
        </tr>
        <?
    }
    ?>	
	<?php abas();?>
    <tr>
		<td align="center" valign="top">

    		<table class="tableMoldura" id="filtro_pesquisa">
        		<tr class="tableSubTitulo">
            		<td colspan="4"><h2>Dados Principais</h2></td>
        		</tr>
        		<?php if ($acao!="editar") { ?>
        		<tr>
                	<td width="10%">
                   		<label>
                   			Tipo de Contrato:
                   		</label>
                   	</td>
	            	<td>
	                	<SELECT name="prpstpcoid" id="prpstpcoid" onchange="verificaProposta($('#prpsproposta').val(),$('#prpstpcoid').val())">
							<option value=''>--Escolha--</option>
							<?php $objPropostaSeguradora->getTiposContrato(); ?>
                        </SELECT>
	                </td>	
				</tr>
        		<?php } ?>
        		<tr>
                	<td width="10%">
                   		<label>
                   			Proposta:
                   		</label>
                   	</td>
	            	<td>
	                	<input type="text" name="prpsproposta" id="prpsproposta" value="<?php echo $mProposta_segurado['prpsproposta']?>" maxlength="12"  onKeyUp="onlyNumbers(this)" onblur="verificaProposta($(this).val(),$('#prpstpcoid').val())" <?php if ($acao=="editar") echo 'readonly="readonly" style="background-color:#F0F0F0;" '; ?>/>
	                	<div id="loading1" style="display: inline; width:10px; height: 10px;"></div>
	                	<input type="hidden"  name="prpsoid" id='prpsoid' value="<?php echo $mProposta_segurado['prpsoid']?>"   />
	                	<input type="hidden"  name="prpsaoid" id='prpsaoid' value="<?php echo $mProposta_segurado['prpsaoid']?>"   />
	                	
	                </td>	
				</tr>
				<tr>
					<td>
						<label>Solicitante:</label>
					</td>
					<td>
						<input type="text"  name="prpssolicitante" id='prpssolicitante' value="<?php echo $mProposta_segurado['prpssolicitante']?>" size="50"  maxlength="50"   />
					</td>
				</tr>
				<tr>
					<td>
						<label>Data Solicitação:</label>
					</td>
					<td>
						<input  onblur="ValidaDataPC(prpsdt_solicitacao);" onkeyup="formata_dt(this);" maxlength="10" size="10" type="text"  name="prpsdt_solicitacao" id='prpsdt_solicitacao' value="<?php echo $mProposta_segurado['prpsdt_solicitacao']?>"   />
						<img src="images/calendar_cal.gif" align="absmiddle" border="0" alt="Calendário..." onclick="displayCalendar(document.forms[0].prpsdt_solicitacao,'dd/mm/yyyy',this)">
					</td>
				</tr>
				<?php if ($acao=="editar") { ?>
				<tr>
					<td>
						<label>Combinação:</label>
					</td>
					<td>
						<?php if ($mProposta_segurado['prpscombinacao'] == 'AA' && $mProposta_segurado['prpsprpssoid'] == 1): ?>
						<select name="prpscombinacao" id='prpscombinacao'>
							<option selected value="AA">AA</option>
							<option  value="II">II</option>
						</select>
						<?php else: ?>
						<input type="text" readonly=" readonly" name="prpscombinacao" id='prpscombinacao' value="<?php echo $mProposta_segurado['prpscombinacao']?>" style="background-color:#F0F0F0;"  />
						<?php endIf; ?>
					</td>
				</tr>
				<?php } ?>
			    <tr> 
			    	<td>
	                	<label>
	                    	Status:
						</label>
					</td>
					<td>					
					<?php //$mProposta_segurado['prpsprpssoid'] != '6' && $mProposta_segurado['prpsprpssoid'] != '4' 
	                if($_SESSION['funcao']['proposta_seguradora_edicao']==1 && $mProposta_segurado['prpsprpssoid'] == '1' )
	                {
	                		
	                ?>
                        <SELECT id="prpsprpssoid" name="prpsprpssoid" >
							<option value=''></option>
						<?php
						$sql_proposta_seguradora_status = "
														SELECT 
															prpssoid,
															prpssdescricao,
															prpsstatus_principal
														FROM 
															proposta_seguradora_status
														WHERE
															prpsdt_exclusao IS NULL	
														AND
															prpsstatus_principal = 'true' 
					
														";
						$query_proposta_seguradora_status = pg_query($conn,$sql_proposta_seguradora_status);
						while($mProposta_seguradora_status = pg_fetch_array($query_proposta_seguradora_status))
						{
						?>
							<option value='<?php echo $mProposta_seguradora_status['prpssoid'];?>'  <?php if($mProposta_seguradora_status['prpssoid'] == $mProposta_segurado['prpsprpssoid']){ echo 'selected';} ?> ><?php echo $mProposta_seguradora_status['prpssdescricao'];?></option>							
						<?php	
						}
						?>	
                        </SELECT>
                    <?php    
	                }
	                else
	                {   
	                	if ($_SESSION['funcao']['proposta_seguradora_status_pendente'] == 1) {
	                	?>
	                	<select id="prpsprpssoid" name="prpsprpssoid" >
	                	<?php 
							$mProposta_segurado['prpsprpssoid'] = intval($mProposta_segurado['prpsprpssoid']);
		                	$sql_proposta_seguradora_status = "
			                	SELECT
				                	prpssoid,
				                	prpssdescricao
			                	FROM
			                		proposta_seguradora_status
			                	WHERE
			                	prpsdt_exclusao IS NULL
			                	AND prpssoid = ".$mProposta_segurado['prpsprpssoid'].";
		                	";
		                	
		                	
		                	$query_proposta_seguradora_status = pg_query($conn,$sql_proposta_seguradora_status);
		                	$mProposta_seguradora_status = pg_fetch_array($query_proposta_seguradora_status);
		                	
		                	?>
		                	<option value="<?php echo $mProposta_seguradora_status['prpssoid'];?>" selected><?php echo $mProposta_seguradora_status['prpssdescricao'];?></option>
		                	<option value='1'>PENDENTE</option>
		                	</select>
		                	<?php 
	                	} else {
							
							if($mProposta_segurado['prpsprpssoid'] != "") {
							$sql_proposta_seguradora_status = "
															SELECT 
																prpssoid,
																prpssdescricao
															FROM 
																proposta_seguradora_status
															WHERE
																prpsdt_exclusao IS NULL	
															AND 
																prpssoid = ".$mProposta_segurado['prpsprpssoid'].";";
						
															
							$query_proposta_seguradora_status = pg_query($conn,$sql_proposta_seguradora_status);
							$mProposta_seguradora_status = pg_fetch_array($query_proposta_seguradora_status);	                	
							
						  
						  ?>
		                	<input type="text"  name="" id='' value="<?php echo $mProposta_seguradora_status['prpssdescricao']?>" size='40' maxlength="40" readonly="true" style="background-color:#F0F0F0;" />
	 	                	<input type="hidden"  name="prpsprpssoid" id='prpsprpssoid' value="<?php echo $mProposta_seguradora_status['prpssoid']?>"   />
	 	                	<!---<input type="hidden"  name="prpsaoid" id='prpsaoid' value="<?php echo $mProposta_seguradora_status['prpssoid']?>"   />-->
                      
					  
					  <?php
					  		}
	                	}
	                }
	                ?>  
					</td>
				</tr>
        		<tr> 
          			<td>
          				<label>
          					DMV:
						</label>
          			</td>
          			<td >
          				<input type="text"  name="name" value="<?php echo $mProposta_segurado['rczdescricao']?>"  maxlength="50" readonly="true" style="background-color:#F0F0F0;" />
      				</td>
        		</tr>
			</table>
    		<table class="tableMoldura" id="filtro_pesquisa">
        		<tr class="tableSubTitulo">
            		<td colspan="4"><h2>Dados do Segurado</h2></td>
        		</tr>
		        <tr> 
				<td colspan="4">
				<label>
				<?
				if($mProposta_segurado['prpsscnpj_cpf']>0){
                        
						$b_pcorcpf_cnpj=$mProposta_segurado['prpsscnpj_cpf'];
                        $b_pcorcpf_cnpj = str_replace("-","",$b_pcorcpf_cnpj);
                        $b_pcorcpf_cnpj = str_replace(".","",$b_pcorcpf_cnpj);
                        $b_pcorcpf_cnpj = str_replace("/","",$b_pcorcpf_cnpj);
                        
                        if($b_pcorcpf_cnpj>0){
                            
                            $sql_cli = "SELECT clivoid AS clioid,
                                        cliv_dsc, connumero
                                        FROM cliente_view, contrato
                                        WHERE conclioid=clivoid 
                                        AND condt_exclusao IS NULL 
                                        AND cliv_exclusao IS NULL 
                                        AND cliv_cpf=$b_pcorcpf_cnpj";
                            $resu_cli = pg_query($conn,$sql_cli);
                            
                            $lin_cli = pg_num_rows($resu_cli);
                        }
                        
                        if($lin_cli>0){
                            
                         echo "&nbsp;Cliente com Contratos Ativos.";
                        }   else { 
                         echo "&nbsp;Cliente sem Contratos Ativos.";
						}	
				}	?>	
                </label>
				</td>
                </tr>		
                <tr>				
        			<td width="10%">
        				<label>
        					Nome
        				</label>
        			</td>
          			<td >
				    	<input name="prpssegurado" id='prpssegurado' value='<?php echo $mProposta_segurado['prpssegurado'];?>' type="text" size="50" maxlength="50">
            		</td>
		        </tr>
        		<tr> 
          			<td>
          				<label>
          					Tipo
  						</label>
          			</td>
          			<td >
                        <SELECT name="prpstipo_pessoa" id="prpstipo_pessoa"  onchange="verificaCpfCnpj()">
							<option value='F' <?php if($mProposta_segurado['prpstipo_pessoa'] == 'F'){echo 'selected';}?> >Física</option>
							<option value='J' <?php if($mProposta_segurado['prpstipo_pessoa'] == 'J'){echo 'selected';}?> >Jurídica</option>		
                        </SELECT>
                	</td>
        		</tr>
        		<tr> 
					<td colspan='2' >
		 	        	<div id='div_cpf' style='display:inline' >
		 	        	<table width='100%'>
		 	        		<tr>
		 	        			<td width='10%' >
			          				<label>
			          					CPF
			  						</label>
			  					</td>
					 	        <td ><div id='cpf '> 
									<input type="text" name="prpsscnpj_cpf_cpf" id='prpsscnpj_cpf_cpf'  value="<?php echo $mProposta_segurado['prpsscnpj_cpf']?>"  onblur="revalidar(this,'@@@.@@@.@@@-@@','cpf');xajax_busca_cliente(document.getElementById('prpsscnpj_cpf_cpf').value, '<?php echo $fNovo; ?>');" onKeyup="formatar(this, '@@@.@@@.@@@-@@');"  size="20" maxlength="14">
									</div>	
		            			</td>
		            		</tr>
		            	</table>			
		            	</div>
		 	        	<div id='div_cnpj' style='display:none' >
		 	        	<table width='100%'>
		 	        		<tr>
		 	        			<td width='10%' >
			          				<label>
			          					CNPJ
			  						</label>
			  					</td>
					 	        <td ><div id='cnpj'> 
									<input type="text" name="prpsscnpj_cpf_cnpj" id='prpsscnpj_cpf_cnpj'  value="<?php echo $mProposta_segurado['prpsscnpj_cpf']?>"     onblur="revalidar(this,'@@.@@@.@@@/@@@@-@@','cnpj');xajax_busca_cliente(document.getElementById('prpsscnpj_cpf_cnpj').value, '<?php echo $fNovo; ?>');" onKeyup="formatar(this, '@@.@@@.@@@/@@@@-@@');"  size="20" maxlength="18">
									</div>		            				
		            			</td>	
		            		</tr>
		            	</table>		
            			</div>
            		</td>
        		</tr>
        		
        		<tr> 
          			<td colspan='2'>
        				<div id='div_rg' style='display:none'>
        				<table width="100%">
        					<tr>
        						<td width="10%">
			          				<label>
			          					RG
			  						</label>
			          			</td>
			          			<td >
									<input type="text" name="prpssrg" id='prpssrg'  value="<?php echo $mProposta_segurado['prpssrg']?>"   onKeyup="formatar(this, '@@@@@@@@@@@');"  size="20" maxlength="11">
								</td>
							</tr>
						</table>
						</div>				
        				<div id='div_i_est' style='display:inline'>
        				<table width="100%">
        					<tr>
        						<td width="10%">
			          				<label>
			          					Insc.Estadual
			  						</label>
			          			</td>
			          			<td >
									<input type="text" name="prpssinscr_estadual" id='prpssinscr_estadual'  value="<?php echo $mProposta_segurado['prpssinscr_estadual']?>"    size="20" maxlength="11">
								</td>
							</tr>
						</table>
						</div>				
                	</td>
        		</tr>        
	 		      		
        		<tr> 
          			<td colspan='2'>
        				<div id='div_nascimento' style='display:inline'>
        				<table width="100%">
        					<tr>
        						<td width="10%">
			          				<label>
			          					Data Nascimento

			  						</label>
			          			</td>
			          			<td >
				            		<input name="prpssdt_nascimento" id='prpssdt_nascimento' value='<?php echo $mProposta_segurado['prpssdt_nascimento_br']?>' type="text" size='10' maxlength="10" onKeyUp="formata_dt(this);" onBlur="ValidaDataPC(prpssdt_nascimento);">
			                        <img src="images/calendar_cal.gif" align="absmiddle" border="0" alt="Calendário..." onclick="displayCalendar(document.forms[0].prpssdt_nascimento,'dd/mm/yyyy',this)">
								</td>
							</tr>
						</table>
						</div>				
        				<div id='div_fundacao' style='display:none'>
        				<table width="100%">
        					<tr>
        						<td width="10%">
			          				<label>
			          					Fundação
			  						</label>
			          			</td>
			          			<td >
				            		<input name="prpssdt_fundacao" id='prpssdt_fundacao' value='<?php echo $mProposta_segurado['prpssdt_fundacao_br']?>' type="text" size='10' maxlength="10" onKeyUp="formata_dt(this);" onBlur="ValidaDataPC(prpssdt_fundacao);">
			                        <img src="images/calendar_cal.gif" align="absmiddle" border="0" alt="Calendário..." onclick="displayCalendar(document.forms[0].prpssdt_fundacao,'dd/mm/yyyy',this)">
								</td>
							</tr>
						</table>
						</div>				
		   				<div id='div_sexo' style='display:inline'>
		      				<table width="100%" >
				        		<tr> 
				          			<td width="10%" >
				          				<label>
				          					Sexo
				  						</label>
				          			</td>
				          			<td >
				                        <SELECT name="prpsssexo" id="prpsssexo" >
											<option value=''></option>                        
											<option value='M' <?php if($mProposta_segurado['prpsssexo'] == 'M'){echo 'selected';}?> >Masculino</option>		
											<option value='F' <?php if($mProposta_segurado['prpsssexo'] == 'F'){echo 'selected';}?> >Feminino</option>							
				                        </SELECT>
				                	</td>
				        		</tr>  	 		      		
							</table>
						</div>				
                	</td>
        		</tr>   	 		      		
        	</table>	
    		<table class="tableMoldura" id="filtro_pesquisa">
        		<tr class="tableSubTitulo">
            		<td colspan="6">
            			<h2>Dados de Endere&ccedil;o</h2>
            		</td>
        		</tr>  
                <tr> 
                    <td>
                        <label>
                            CEP
                        </label>
                    </td>
                    <td > 
                        <input name="prpscep" id='prpscep'  onkeypress="return numero(event, false, false);"   onblur="revalidar(this,'@');xajax_buscaCepCliente(this.value)"  value='<?php echo $mProposta_segurado['prpscep']?>' type="text" size="8" maxlength="8">
                    </td>
                </tr>
        		<tr> 
          			<td width="10%">
          				<label>
          					Endere&ccedil;o
          				</label>	 
            		</td>
			        <td> 
            			<input name="prpsendereco" id='prpsendereco' value='<?php echo $mProposta_segurado['prpsendereco']?>' type="text" size="30" maxlength="30"> 
            			N&ordm;:
            			<input name="prpsnumero" id='prpsnumero' onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');" value='<?php echo $mProposta_segurado['prpsnumero']?>' type="text" size="8" maxlength="8">
          			</td>
        		</tr>
                <tr> 
                    <td width="10%">
                        <label>
                            Complemento
                        </label>     
                    </td>
                    <td> 
                        <input name="prpsscomplemento" id='prpsscomplemento' value='<?php echo $mProposta_segurado['prpsscomplemento']?>' type="text" size="30" maxlength="30"> 
                    </td>
                </tr>
				<tr> 
          			<td>
          				<label>
          					Bairro
          				</label>
          			</td>
          			<td > 
            			<input name="prpsbairro" id='prpsbairro' value='<?php echo $mProposta_segurado['prpsbairro']?>' type="text" size="30" maxlength="50">
		           	</td>
        		</tr>
        		<tr> 
          			<td>
          				<label>
          					Cidade
          				</label>
          			</td>
          			<td > 
            			<input name="prpsmunicipio" id='prpsmunicipio' value='<?php echo $mProposta_segurado['prpsmunicipio']?>' type="text" size="30" maxlength="50">
		           	</td>
        		</tr>
        		<tr> 
	          		<td>
	          			<label>
	          				UF
	          			</label>
	          		</td>
	          		<td > 
	          			<select  name="prpsuf" id='prpsuf' >
	          				<option value=''></option>
	          			<?php
	          			$sql_uf = " SELECT estuf FROM estado WHERE estexclusao IS NULL ORDER BY estuf ";
	          			$query_uf = pg_query($conn,$sql_uf);
	          			while($mUf = pg_fetch_array($query_uf))
	          			{
	          			?>	
	          				<option value='<?php echo $mUf['estuf']?>' <?php if($mProposta_segurado['prpsuf'] == $mUf['estuf']){echo 'selected';}?> ><?php echo $mUf['estuf']?></option>
	          			<?php
	          			}
	          			?>		          				
	          			</select>
	            	</td>
	        	</tr>

	        	<tr> 
	          		<td>
	          			<label>
	          				Fone 1
	  					</font>
	  				</td>
	          		<td> 
	            		<input name="prpsddd" id='prpsddd' onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');"  value='<?php echo $mProposta_segurado['prpsddd']?>'  type="text" size="2" maxlength="2">
	            		<input name="prpsfone" id='prpsfone' value='<?php echo $mProposta_segurado['prpsfone']?>' type="text" size="9" maxlength="9" onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');" />
	            	</td>                
		      	</tr>
                <tr>
                    <td>
	          			<label>
	          				Fone 2
	  					</font>
	  				</td>
	          		<td > 
	            		<input name="prpsddd2" id='prpsddd2' onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');"  value='<?php echo $mProposta_segurado['prpsddd2']?>'  type="text" size="2" maxlength="2">
	            		<input name="prpsfone2" id='prpsfone2' value='<?php echo $mProposta_segurado['prpsfone2']?>' type="text" size="9" maxlength="9" onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');" />
	            	</td>
                </tr>
                <tr>                    
                    <td>
	          			<label>
	          				Fone 3
	  					</font>
	  				</td>
	          		<td > 
	            		<input name="prpsddd3" id='prpsddd3' onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');"  value='<?php echo $mProposta_segurado['prpsddd3']?>'  type="text" size="2" maxlength="2">
	            		<input name="prpsfone3" id='prpsfone3' value='<?php echo $mProposta_segurado['prpsfone3']?>' type="text" size="9" maxlength="9" onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');" />
	            	</td>
                </tr>
			</table>

			
			<table class="tableMoldura" id="filtro_pesquisa">
	    		<tr class="tableSubTitulo">
	        		<td colspan="6">
	        			<h2>Dados do Ve&iacute;culo</h2>
	        			<input type='hidden'  id='veioid'  name='veioid' value='<?php echo $mProposta_segurado['veioid']?>'  >
	        		</td>
	    		</tr>   		
		        <tr> 
	    			<td width="10%">
	    				<label>
	    					Marca
	    				</label>
	    			</td>
			        <td > 
	            		<select name="mlomcaoid" id='mlomcaoid'  onchange='busca_modelo()' >
	            			<option value=''></option>
	            		<?php
	            		$sql_marca = " SELECT 
											mcaoid,
											mcamarca
									 	FROM marca 
										WHERE mcadt_exclusao IS NULL 
										ORDER BY mcamarca
										";
						$query_marca = pg_query($conn,$sql_marca);
						while($mMarca = pg_fetch_array($query_marca))
						{
						?>	
							<option value='<?php echo $mMarca['mcaoid'];?>'    <?php if($mMarca['mcaoid'] == $mProposta_segurado['mlomcaoid']){ echo 'selected';} ?>  ><?php echo $mMarca['mcamarca'];?></option>
						<?php	
						}			 	
	            		?>
	            		</select>
	            	</td>
	        	</tr>
	        	<tr> 
	          		<td>
	          			<label>
	          				Modelo
	          			</label>
	          		</td>
	          		<td > 
	          			<div name='div_modelo' id='div_modelo'>
	            		<select name="veimlooid" id='veimlooid'>
	            		<?php
                             if($mProposta_segurado['mlomcaoid']>0){
	            		$sql_modelo = " SELECT 
											mlooid,
											mlomodelo 
										FROM  modelo 
										WHERE 
										mlodt_exclusao IS NULL
										AND
										mlomcaoid = {$mProposta_segurado['mlomcaoid']}
										ORDER BY mlomodelo
										";
                      
	            		$query_modelo = pg_query($conn,$sql_modelo);
	            		while($mModelo = pg_fetch_array($query_modelo))
	            		{
	            		?>	
	            			<option value='<?php echo $mModelo['mlooid']?>'  <?php if($mModelo['mlooid'] == $mProposta_segurado['veimlooid']){ echo 'selected';}?> ><?php echo $mModelo['mlomodelo']?></option>
	            		<?php	
	            		}}		
	            		?>
	            		</select>
	            		</div>
	            	</td>
	        	</tr>
	        	<tr> 
	          		<td>
	          			<label>
	          				Placa
	          			</label>
	          		</td>
	          		<td >
	          			<div  name="div_busca_placa"  id="div_busca_placa" > 
	        				<input name="veiplaca" id="veiplaca"   value='<?php echo $mProposta_segurado['prpsplaca']?>' type="text" size="10" maxlength="9">

	        				<?php 
	        				$array_placa = array('AAVISAR','A/C','AVI0000','S/PLACA','AIN0000','AAA1111','AAA0000','INF0001','AC0000','AAC0000','AVI0001','AVI1111','AVI2008','AVI2009','0000');
	        				if(in_array( $mProposta_segurado['prpsplaca'] , $array_placa) || $acao != "editar")
	        				{
	        				?>	
	        					<input type="button" value="Sequencial" class="botao"  onclick='busca_placa()' style="margin: 0 0 5px 5px !important;"> 
	        				<?php
	        				}
	        				?>
                            
                            <?php
                            /*
                             * STI 80998 - Botão: Gerar Placa 2
                             */
                            $prpsprpssoid = trim($mProposta_segurado['prpsprpssoid']);
                            $prpsveioid   = trim($mProposta_segurado['prpsveioid']);
                            $prpstpcoid   = trim($mProposta_segurado['prpstpcoid']);
                            $prpsoid      = trim($mProposta_segurado['prpsoid']);
                            
                            if($prpsprpssoid == 1 && $prpsveioid != "" && $prpstpcoid != "" && $prpsoid != ""){
                                $sql = "SELECT
                                            TRUE
                                        FROM
                                            contrato, veiculo
                                        WHERE
                                            conveioid = veioid
                                        AND
                                            veioid = $prpsveioid
                                        AND
                                            conno_tipo <> $prpstpcoid;";
                                            
                                $sql = pg_query($conn, $sql);
                                
                                $result = pg_num_rows($sql);
                                
                                if($result > 0){
                                    //A funcao xajax_gerarPlaca2 encontra-se no arquivo: prn_proposta_seguradora.php
                                    echo "<input type='button' value='Gerar Placa 2' class='botao' style='margin: 0 0 5px 5px !important;' onclick='xajax_gerarPlaca2($prpsveioid, $prpsoid);'/>";
                                }
                            }
                            
                            /*
                             * STI 80998 - Botão: Reenviar Resposta
                             */
                            $prpsproposta = trim($mProposta_segurado['prpsproposta']);
                            
                            if($prpsproposta != "" && $prpstpcoid != "" && $prpsoid != ""){
                                $sql = "SELECT
                                            TRUE
                                        FROM
                                            contrato, veiculo
                                        WHERE
                                            conveioid = veioid
                                        AND
                                            condt_exclusao is NULL
                                        AND
                                            ltrim(veino_proposta,'0') = '$prpsproposta'
                                        AND
                                            conno_tipo = $prpstpcoid";
                                            
                                $sql = pg_query($conn, $sql);
                                
                                $result = pg_num_rows($sql);
                                
                                if($result > 0){
                                    //A funcao xajax_reenviarResposta encontra-se no arquivo: prn_proposta_seguradora.php
                                    echo "<input type='button' value='Reenviar Resposta' class='botao' style='margin: 0 0 5px 5px !important;' onclick='xajax_reenviarResposta($prpsoid);'/>";
                                }
                            }
                            ?>
	        			</div>
	            	</td>
	        	</tr>
	        	
	        	<tr> 
	          		<td>
	          			<label>
	          				Chassi
	          			</label>
	          		</td>
		   	 	    <td > 
	            		<input name="prpschassi" id='prpschassi'  value='<?php echo $mProposta_segurado['prpschassi']?>' type="text" size="17" maxlength="17">
	            	</td>
	    		</tr>
	        	<tr> 
	          		<td>
	          			<label>
	          				Ano
	          			</label>
	          		</td>
	          		<td > 
	            		<input name="veino_ano" id='veino_ano' onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');"  value='<?php echo $mProposta_segurado['veino_ano']?>'  type="text" size="4" maxlength="4">
	            	</td>
	        	</tr>
	
	        	<tr> 
	          		<td>
	          			<label>
	          				Cor
	          			</label>
	          		</td>
	          		<td > 
	            		<input name="veicor" id='veicor'  value='<?php echo $mProposta_segurado['veicor']?>' type="text" size="30" maxlength="15">
	            	</td>
	        	</tr>
	        	<tr> 
	          		<td>
	          			<label>
	          				Renavam
	          			</label>
	          		</td>
	          		<td > 
	            		<input name="veino_renavan" id='veino_renavan' onKeyUp="onlyNumbers(this)" value='<?php echo $mProposta_segurado['veino_renavan']?>'  type="text" size="12" maxlength="10">
		            </td>
	        	</tr>
	        	<tr> 
	          		<td>
	          			<label>
	          				Chave Geral
	          			</label>
	          		</td>
	          		<td > 
	            		<input type="checkbox"  name='veichave_geral' id='veichave_geral' <?php if($mProposta_segurado['veichave_geral'] == 't'){ echo 'checked'; }?>  value="t">
	            	</td>
	        	</tr>
				<?
                    $pcorchassi = $mProposta_segurado['prpschassi'];
                    $pcorplaca  = $mProposta_segurado['prpsplaca'];
					
					                   
                    if($pcorplaca && $pcorchassi){
                            
                        $sql_vei = "SELECT veioid, veichassi, veiplaca,veino_proposta,
 
                                    (   SELECT 'Serial Nº'||equno_serie||' - Classe:'||eqcdescricao||' Versão:'||eveversao 
                                        FROM equipamento, equipamento_versao, equipamento_classe 
                                        WHERE conequoid=equoid 
                                        AND eveeqcoid=eqcoid 
                                        AND equeveoid=eveoid) as equipamento,
									to_char(condt_quarentena_seg,  'DD/MM/YYYY' ) AS condt_quarentena_seg,
                                    connumero AS contrato,
                                    conno_tipo,
									veiapolice,
									veino_item,
                                    (   SELECT clinome 
                                        FROM clientes 
                                        WHERE conclioid=clioid) AS locatario,

                                    (   SELECT tpcdescricao 
                                        FROM tipo_contrato 
                                        WHERE tpcoid=conno_tipo) AS tipo_contrato

                                    FROM veiculo, contrato
                                    WHERE conveioid=veioid 
                                    AND (   veichassi = '$pcorchassi' 
                                            OR (    veiplaca='$pcorplaca' 
                                                    AND veiplaca NOT IN (   'AAVISAR','A/C','AVI0000','S/PLACA',
                                                                            'AVIO','AVI0','AIN0000','AAA1111',
                                                                            'AAA0000','INF0001','AC0000','AAC0000',
                                                                            'AVI0001','AVI1111','AVI2008','AVI2009') ) ) 
									--AND veioid != {$mProposta_segurado['prpsveioid']}
                                    AND veidt_exclusao IS NULL 
                                    AND condt_exclusao IS NULL ";
                        $resu_vei = pg_query($conn,$sql_vei);
                        
                        $lin_vei  = pg_num_rows($resu_vei);
                        
                        if($lin_vei>0) 
                        { 
                          $desabita_gerar_contrato = "nao gerar contrato";
                        	?>
                            <tr>
                                <td colspan="4">
                                    <table width="100%">
                                    	<tr height='20'>
                                    		<td   bgcolor='#FF9B9B'  >	
                                    			<h2>Veículo já vinculado a um contrato</h2>
                                    		</td>                                    		
                                    	</tr>
                                    </table>
                                    <table width="100%">
                                        <tr class="tableTituloColunas">
                                            <td align="center"><h3>Nº Contrato</h3></td>
											<td align="center"><h3>Nº Proposta</h3></td>
											<td align="center"><h3>Apólice</h3></td>
											<td align="center"><h3>Item</h3></td>
                                            <td align="center"><h3>Placa</h3></td>
                                            <td align="center"><h3>Chassi</h3></td>
                                            <td align="center"><h3>Equipamento</h3></td>
                                            <td align="center"><h3>Quarentena</h3></td>
                                            <td align="center"><h3>Locatário</h3></td>
                                            <td align="center"><h3>Tipo Contrato</h3></td>
                                        </tr>
                                        <?
                                        for($i = 0; $i < $lin_vei;$i++){
                                            $vei = pg_fetch_array($resu_vei,$i);
                                            $class = ( $classr == "tdc" ) ? "tde" : "tdc"; ?>
                                            <tr class="<?=$class;?>">
                                                <td align="center">
                                                    <?
                                                    //Alterado 08/07/2010 - Req. 58273
                                                    if($_SESSION["usuario"]["depoid"]==10 || $_SESSION["usuario"]["depoid"]==36 || $_SESSION["usuario"]["depoid"]==8){ ?>
                                                        <a href="contrato_servicos.php?connumero=<?=$vei['contrato']?>&acao=consultar" target="_blank">
                                                        <?
                                                    } 
                                                    
                                                    echo $vei['contrato'];
                                                    
                                                    //Alterado 08/07/2010 - Req. 58273
                                                    if($_SESSION["usuario"]["depoid"]==10 || $_SESSION["usuario"]["depoid"]==36 || $_SESSION["usuario"]["depoid"]==8){ ?>
                                                        </a>
                                                        <?
                                                    } ?>
                                                </td>
												<td><?=$vei['veino_proposta'];?></td>
												<td><?=$vei['veiapolice'];?></td>
												<td><?=$vei['veino_item'];?></td>
                                                <td><?=$vei['veiplaca'];?></td>
                                                <td><?=$vei['veichassi'];?></td>
                                                <td><?=$vei['equipamento'];?></td>
                                                <td width="170">
                                                	<div id="quarentena_<?php echo  $vei['contrato'] ?>" style="display: inline;">
                                                		<?=$vei['condt_quarentena_seg'];?>
                                                	</div>
                                                	<?php 
                                                		$proposta = ($vei['veino_proposta']>0) ? $vei['veino_proposta'] : 0;
                                                		$tipo_contrato = ($vei['conno_tipo']>0) ? $vei['conno_tipo'] : 0;
                                                	?>
                                                	<input id="connumero_quarentena" type="hidden" value="<?=$vei['contrato'];?>" />
                                                	<input id="del_quarentena_<?php echo  $vei['contrato'] ?>" type="button" value="Retirar" class="botao" onclick="delQuarentena(<?php echo  $vei['contrato'] ?>,<?= $proposta ?>)" <?php if (!strlen($vei['condt_quarentena_seg'])) { echo 'style="display:none;" '; } else { echo 'style="display:inline;" ';} ?>/>
                                                	<input id="inc_quarentena_<?php echo  $vei['contrato'] ?>" type="button" value="Incluir" class="botao" onclick="incQuarentena(<?php echo  $vei['contrato'] ?>,<?= $proposta ?>,<?= $tipo_contrato ?>)" <?php if (strlen($vei['condt_quarentena_seg'])) { echo 'style="display:none;" '; } else { echo 'style="display:inline;" ';} ?>/>
                                                	<div id="loading2_<?php echo  $vei['contrato'] ?>" style="display: inline; width:10px; height: 10px;"></div>
                                                </td>
                                                <td><?=$vei['locatario'];?></td>
                                                <td><?=$vei['tipo_contrato'];?></td>
                                            </tr>
                                            <?
                                        } ?>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr> 
                                <td width="10%">
                                    <label>
                                        Contrato:
                                    </label>     
                                </td>
                                <td> 
                                    <input name="connumero_original" id='connumero_original' value='<?php echo $connumero_original?>' type="text" size="15" maxlength="15"> 
                                    Ação:
                                    <select ID='acao_contrato' name='acao_contrato' onchange='buscaDadosMigracao()' >
                                        <option value='0'>Escolha</option>
                                        <option value='1'>Transferência de Titularidade</option>
                                        <option value='2'>Migração</option>  
                                        <option value='3'>Migração com Transferência</option>                                                   
                                    </select >
                                    <input type="button" name="alterar_contrato" onclick="alterar_contrato_seg()"  value="Confirmar" class="botao">
                                </td>
                        </tr>  

                        <tr id='mostraMigrarPara' style='display:none'>
							<td>
								<label>
									Migrar para:
								</label>     
							</td> 
							<td>
							    <select ID='tpcoid' name='tpcoid' >
                                	<option value=''>Escolha</option>
                                <?php
                                $sqlMigrarPara = " SELECT 
														tpcoid,
														tpcdescricao
													FROM 
														tipo_contrato 
													WHERE 
														tpcdescricao ILIKE 'Ex-%' 
													OR 
														tpcseguradora IS TRUE
													AND 
														tpcprocesso_unificado_seg IS TRUE 
													ORDER BY tpcdescricao ";
								$queryMigrarPara = pg_query($conn, $sqlMigrarPara);
								while($mMigrarPara = pg_fetch_array($queryMigrarPara))		
								{			
                                ?>	
                                    <option value='<?php echo $mMigrarPara['tpcoid']?>'><?php echo $mMigrarPara['tpcdescricao']?></option>
                                 <?php
								}
								?>                    
                                </select >
                            </td>     
                        </tr>                          
                            <?
                        }
                    } ?>


			</table>
			<input type="hidden" name="contratos_vinculados" value="<?php echo $lin_vei;?>" /> 
			<table class="tableMoldura" id="filtro_pesquisa">
	    		<tr class="tableSubTitulo">
	        		<td colspan="6">
	        			<h2>Dados do Seguro</h2>
	        		</td>
	    		</tr>   		
		        <tr> 
	    			<td width="10%">
	    				<label>		
							Seguradora
						</label>
					</td>
	      			<td > 
	            		<select name="veisegoid" id='veisegoid' >
	            			<option value=''></option>
	            		<?php
						$sql_seguradora = "select segoid, segseguradora from seguradora where segdt_exclusao is null and segoid in (77,17,61,25,9,3,46,32,116) order by segseguradora;";
						$query_segurado = pg_query($conn,$sql_seguradora);
						while($mSeguradora = pg_fetch_array($query_segurado))
						{
						?>	
							<option value='<?php echo $mSeguradora['segoid'];?>'  <?php if($mSeguradora['segoid'] == $mProposta_segurado['veisegoid']) { echo 'selected';}?> ><?php echo $mSeguradora['segseguradora'];?></option>							
						<?php	
						}		            		
	            		?>
			            </select>
	            	</td>
	        	</tr>
	        	<tr>
	        		<td>
	        			<label>Nome Corretor</label>
	        		</td>
	        		<td>
	        			<input type="text" <?php if ($acao=="editar") echo 'readonly="readonly" '; ?>id="prpscorretor" name="prpscorretor" value="<?php echo $mProposta_segurado['prpscorretor']?>" size="50" />
	        		</td>
	        	</tr>
                <tr>
                    <td><label for="prpscorroid">Corretora:</label></td>
                    <td nowrap>
                        <?        
                        CE_desenhar('prpscorroid','corrnome','buscaCorretor',$mProposta_segurado['prpscorroid'],$mProposta_segurado['corrnome'],true,3,"Digite no minimo 3 caracteres para pesquisar um corretor");
                        ?>
                    </td>
                </tr>	        	
	        	<tr> 
	          		<td>
	          			<label>
	          				Email
	          			</label>
	          		</td>
	          		<td > 
		                	<input type="text"  name="prpsemail_corretor" id='prpsemail_corretor'  value="<?php echo $mProposta_segurado['prpsemail_corretor']?>" size='50' maxlength="50" /> 
	            	</td>
	        	</tr>
	        	<tr> 
	          		<td >
	          			<label>
	          				Telefone
	  					</font>
	  				</td>
	          		<td > 
	            		<input name="prpsddd_corretor" id='prpsddd_corretor' value='<?php echo $mProposta_segurado['prpsddd_corretor']?>'  type="text" size="2" maxlength="2" onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');" />
	            		<input name="prpsfone_corretor" id='prpsfone_corretor' value='<?php echo $mProposta_segurado['prpsfone_corretor']?>' type="text" size="9" maxlength="9" onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');" />
	            	</td>
		      	</tr>	        	
	        	<tr> 
	          		<td>
	          			<label>
	          				N&ordm; Proposta
	          			</label>
	          		</td>
	          		<td > 
		                	<input type="text"  name="veino_proposta" id='veino_proposta' maxlength="50"  value="<?php echo $mProposta_segurado['veino_proposta']?>"  readonly="true" style="background-color:#F0F0F0;" /> 
	            	</td>
	        	</tr>
		        <tr> 
	    			<td>
	    				<label>
	    					C&oacute;d CIA 
	    				</label>
	    			</td>
	          		<td >
	            		<input name="veicod_cia" id='veicod_cia'  onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');" value='<?php echo $mProposta_segurado['veicod_cia']?>'  type="text" size="12" maxlength="4">
	            	</td>
	        	</tr>
		        <tr> 
	    			<td>
	    				<label>
	    					Sucursal 
	    				</label>
	    			</td>
	          		<td >
	            		<input name="prpscod_unid_emis" id='prpscod_unid_emis'  onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');" value='<?php echo $mProposta_segurado['prpscod_unid_emis']?>'  type="text" size="12" maxlength="4">
	            	</td>
	        	</tr>
	        	<tr> 
	          		<td>
	          			<label>
	          				N&ordm; Ap&oacute;lice
	          			</label>
	          		</td>
			        <td > 
	          	<?php
	          		$mProposta_segurado['prpsapolice'] = intval($mProposta_segurado['prpsapolice']);
	          		?>
	            		<input name="prpsapolice" id='prpsapolice'  onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');"  value='<?php echo $mProposta_segurado['prpsapolice']?>' type="text" size="12" maxlength="12">
	            	</td>
	        	</tr>
	        	<tr> 
	          		<td>
	          			<label>
	          				N&ordm; Item
	          			</label>	
	          		</td>
	          		<td > 
	            		<input name="prpsno_item" id='prpsno_item' onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');"  value='<?php echo $mProposta_segurado['prpsno_item']?>'  type="text" size="5" maxlength="4">
	            	</td>
		        </tr>
		        <tr>
		        	<td>
		        		<label>N&ordm; Adiantamento:</label>
		        	</td>
		        	<td>
		        		<input type="text" id="prpsaditamento" onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');" name="prpsaditamento" size="10" value="<?php echo $mProposta_segurado['prpsaditamento']?>" />
		        	</td>
		        </tr>
	    	    <tr> 
	        	  	<td>
	        	  		<label>
	        	  			Vig&ecirc;ncia
	        	  		</label>
	        	  	</td>
	      			<td > 
	            		<input name="prpsinicio_vigencia" id='prpsinicio_vigencia' value='<?php echo $mProposta_segurado['prpsinicio_vigencia']?>'  type="text" size="10" maxlength="10"  onKeyUp="formata_dt(this);" onBlur="ValidaDataPC(prpsinicio_vigencia);validar_prpsinicio_vigencia()"   >
	        			&agrave; 
	            		<input name="prpsfim_vigencia" ID='prpsfim_vigencia' value='<?php echo $mProposta_segurado['prpsfim_vigencia']?>' type="text" size="10" maxlength="10"  onKeyUp="formata_dt(this);" onBlur="ValidaDataPC(prpsfim_vigencia);validar_prpsinicio_vigencia()"  >
	            	</td>
	        	</tr>
		        <tr> 
	    		    <td>
	    		    	<label>
	    		    		Prazo Instala&ccedil;&atilde;o:
	    		    	</label>
	    		    </td>
	          		<td > 
	            		<input name="veiprazo_inst" id='veiprazo_inst' value='<?php echo $mProposta_segurado['veiprazo_inst']?>' type="text" size='10' maxlength="10" onKeyUp="formata_dt(this);" onBlur="ValidaDataPC(veiprazo_inst);">
                            <img src="images/calendar_cal.gif" align="absmiddle" border="0" alt="Calendário..." onclick="displayCalendar(document.forms[0].veiprazo_inst,'dd/mm/yyyy',this)">
	            	</td>
	        	</tr>
	        	<tr> 
	          		<td>
	          			<label>
	          				Novo Prazo
	          			</label>
	          		</td>
		 	        <td > 
	            		<input name="veinovo_prazo" id='veinovo_prazo'   value='<?php echo $mProposta_segurado['veinovo_prazo']?>' type="text" size='10' maxlength="10" onKeyUp="formata_dt(this);" onBlur="ValidaDataPC(veinovo_prazo);">
                            <img src="images/calendar_cal.gif" align="absmiddle" border="0" alt="Calendário..." onclick="displayCalendar(document.forms[0].veinovo_prazo,'dd/mm/yyyy',this)">
	            	</td>
	        	</tr>
		        <tr> 
	    		    <td colspan="8">
	    		    	<label>
	    		      		Observa&ccedil;&otilde;es Gerais 
	    		      	</label>
					</td>
		        </tr>
	    	    <tr> 
					<td colspan="8">
	    		    	<label>
		        		  	<textarea cols="110" id='prpsobs_geral' name='prpsobs_geral' rows="18"  wrap="VIRTUAL"><?php echo $mProposta_segurado['prpsobs_geral']?></textarea><br><br>
	    		    	</label>  	
	        		</td>
	        	</tr>
			</table>
			<?php if ($acao=="editar") { ?>
    		<table class="tableMoldura" id="filtro_pesquisa">
        		<tr class="tableSubTitulo">
            		<td colspan="6">
            			<h2>Dados de Contato</h2>
            		</td>
        		</tr>
        		<tr>   
        			<td width="10%">
        				<label>
        					Tipo
        				</label>
        			</td>
		        	<td>
						<select ID='pscttipo' name='pscttipo' >
							<option value='T'>TODOS</option>
							<option value='E'>EMERGENCIA</option>
							<option value='I'>INSTALACAO</option>							
							<option value='A'>AUTORIZADO</option>					
						</select >
					</td>
				</tr>
		        <tr> 
        			<td width="10%">
        				<label>
        					Nome
        				</label>
        			</td>
          			<td  >
				    	<input name="psctnome_instalacao" id='psctnome_instalacao' value='' type="text" size="50" maxlength="50">
            		</td>
        			<td >
        				<label>
        					CPF
        				</label>
        			</td>
          			<td width='50%' >
				    	<input name="psctcpf_instalacao" onKeyup="formatar(this, '@@@.@@@.@@@-@@');" onkeypress="javascript:return numero(event,false,false);" id='psctcpf_instalacao' value='' type="text" size="20" maxlength="50">
            		</td>
		        </tr>		        
	        	<tr> 
	          		<td >
	          			<label>
	          				Residencial
	  					</font>
	  				</td>
	          		<td > 
	            		<input name="psctddd_residencial_instalacao" id='psctddd_residencial_instalacao' value=''  type="text" size="2" maxlength="2" onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');" />
	            		<input name="psctfone_residencial_instalacao" id='psctfone_residencial_instalacao' value='' type="text" size="9" maxlength="9" onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');" />
	            	</td>
		      	</tr>
	        	<tr> 
	          		<td >
	          			<label>
	          				Comercial
	  					</font>
	  				</td>
	          		<td > 
	            		<input name="psctddd_comercial_instalacao" id='psctddd_comercial_instalacao' value=''  type="text" size="2" maxlength="2" onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');" />
	            		<input name="psctfone_comercial_instalacao" id='psctfone_comercial_instalacao' value='' type="text" size="9" maxlength="9" onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');" />
	            	</td>
		      	</tr>
	        	<tr> 
	          		<td >
	          			<label>
	          				Celular
	  					</font>
	  				</td>
	          		<td > 
	            		<input name="psctddd_celular_instalacao" id='psctddd_celular_instalacao' value=''  type="text" size="2" maxlength="2" onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');" />
	            		<input name="psctfone_celular_instalacao" id='psctfone_celular_instalacao' value='' type="text" size="9" maxlength="9" onkeypress="return numero(event, false, false);" onblur="revalidar(this,'@');" />
              			<input type="button" name="alterar" value="&nbsp;&nbsp;+&nbsp;&nbsp;" onclick='busca_dados_instalacao()' class="botao">
	            	</td>
		      	</tr>
			</table>
			<?php } ?>
			<div id='div_dados_instalacao' >
					<!--dados do conteudo ajax estao no includes/php/busca_dados_ajax_prn_proposta_seguradora.php-->     
					<!-- carregar a lista cadastrada -->  
				
			</div>
			
    		<table class="tableMoldura" id="filtro_pesquisa">				
	            <tr class="tableRodapeModelo1">
	                <td colspan="4" align="center">
	                <?php 
	                if($_SESSION['funcao']['proposta_seguradora_edicao']==1 || $_SESSION['funcao']['proposta_seguradora_status_pendente'] == 1)
	                {
	                	if($mProposta_segurado['prpsprpssoid'] == '4' || $mProposta_segurado['prpsprpssoid'] == '6' || $mProposta_segurado['prpsprpssoid'] == '2' )
	                	{
	                		if ($_SESSION['funcao']['proposta_seguradora_status_pendente'] == 1) {
	                			?>
	                			<input type="button" name="alterar" onclick="alterar_contrato_seguradora()"  value="Confirmar" class="botao">
	                			<?php 
	                		}
	                	}
	                	else
	                	{
	                ?>
              			<input type="button" name="alterar" onclick="<?php 
              				if ($acao!="editar") {
								echo "incluir_contrato_seguradora()";
							} else {
								echo "alterar_contrato_seguradora()";
							}
              			?>"  value="Confirmar" class="botao">
              			<?PHP
              			if(($mProposta_segurado['prpsprpsgoid'] == '1' || $mProposta_segurado['prpsprpsgoid'] == '6') && $mProposta_segurado['prpsprpssoid'] == '1' && $mProposta_segurado['mlomcaoid'] != '' && $mProposta_segurado['veimlooid'] != '' && $mProposta_segurado['veimlooid'] != '' && $mProposta_segurado['veicor'] != '' && $mProposta_segurado['veino_renavan'] != '')
              			{
              				//if(empty($desabita_gerar_contrato))
              				//{
              				?>
              					<input type="button" name="gerarContrato" value="Gerar Contrato" onclick="gerar_contrato()" class="botao" >
              			
              				<?php
              				//}
              			}
	                	}
	                }
	                ?>	

              			<input type="button" onclick="retornar()" name="Submit22" value="Retornar" class="botao" >
              			<?php if ($acao=="editar") { ?>
						<input type="button" onclick="window.open('agenda_instalacao_comercial.php')" class="botao" value="Consultar Agenda">
						<?php } ?>
                                                    
	                </td>
	            </tr>
			</table>
			<?php	
				$query_proposta_seguradora_historico = $objPropostaSeguradora->propostaSeguradoHistorico($id);
			?>	
			<?php if ($acao=="editar") { ?>
			<table class="tableMoldura" id="filtro_pesquisa">
	    		<tr class="tableSubTitulo">
	        		<td colspan="6">
	        			<h2>Historico</h2>
	        		</td>
	    		</tr>   		
		        <tr> 
	    			<td width="10%">
	    				<label>		
							Motivo:
						</label>
					</td>
					<td>
						<select id='prpshpsmtoid' name='prpshpsmtoid' onchange="busca_obs_motivo(this.value)">
							<option value=''></option>
						<?php

						
						$sql_motivo = " SELECT 
											psmtoid, 
											psmtdescricao 
										FROM proposta_seguradora_motivo 
									WHERE 
										psmtdt_exclusao IS NULL
									AND
										psmtmodulo_proposta = TRUE
									ORDER BY psmtdescricao

									";
						$query_motivo = pg_query($conn,$sql_motivo);
						while($mMotivo = pg_fetch_array($query_motivo))
						{
						?>
							<option value='<?php echo $mMotivo['psmtoid'];?>'><?php echo $mMotivo['psmtdescricao'];?></option>
						<?php	
						}
						?>
						</select>
						<DIV ID='osb_motivo'></DIV>
					</td>
				</tr>
	    		<tr> 
	    			<td >
	    				<label>		
							Contato com:
						</label>
					</td>
					<td>
						<input type='text' id='prpshcontato' name='prpshcontato' value=''  size='40' maxlength="50" >
					</td>
				</tr>
		        <tr> 
	    			<td colspan='2'>
	    				<div id='agenda19' style='display:none'>
	    					<table width='100%'>
	    						<tr>
	    							<td width="10%">
					    				<label>		
											Data/Hora:
										</label>
									</td>
					          		<td > 
					            		<input name="prpshdata" id='prpshdata' value='' type="text" size='10' maxlength="10" onKeyUp="formata_dt(this);" onBlur="ValidaDataPC(prpshdata);">
				                        <img src="images/calendar_cal.gif" align="absmiddle" border="0" alt="Calendário..." onclick="displayCalendar(document.forms[0].prpshdata,'dd/mm/yyyy',this)">
					    				<label>		
											Hora:
										</label>
					            		<input name="prpshhora" OnKeyUp="Mascara_Hora(this.value)" id='prpshhora' onblur="Verifica_Hora()" value='' type="text" size='5' maxlength="5" >                        
					            	</td>
								</tr>
							</table>
						</div>
					</td>
				</tr>				
		        <tr> 
	    			<td >
	    				<label>		
							Observação:
						</label>
					</td>
					<td>
						<textarea id='prpshobservacao' name='prpshobservacao' cols='60'  rows='4'></textarea>
						<input type='hidden' value='SAS' id='prpshentrada' name='prpshentrada'>
						
					</td>
				</tr>
	            <tr class="tableRodapeModelo1">
	                <td colspan="4" align="center">
	                <?php
	                if($_SESSION['funcao']['proposta_seguradora_edicao']==1 || $_SESSION['funcao']['proposta_seguradora_historico'] == 1)
	                {
	                ?>
              			<input type="button" name="alterar" value="Incluir" onclick='incluir_historico()'  class="botao">
              		<?php
	                }
	                ?>	
	                </td>
	            </tr>
	        </table>
			<table class="tableMoldura" id="filtro_pesquisa">
                <tr class="tableTituloColunas">
                    <td align="center" >
                    	<h3>Data</h3>
                    </td>	        	
                    <td align="center" >
                    	<h3>Solicitação</h3>
                    </td>
                    <td align="center" >
                    	<h3>Ação</h3>
                    </td>	 
                    <td align="center" >
                    	<h3>Combinação</h3>
                    </td>	    
                    <td align="center" >
                    	<h3>Apólice/Item</h3>
                    </td>	          	        	
	        		<td align="center" >
	        			<h3>Motivo</h3>
	        		</td>
	        		
	        		<td align="center" >
	        			<h3>Status</h3>
	        		</td>
	        		<td align="center" >
	        			<h3>Contato</h3>
	        		</td>
	        		<td align="center" >
	        			<h3>Agendamento</h3>
	        		</td>
	        		<td align="center" >
	        			<h3>Observação</h3>
	        		</td>
	        		<!--
	        		<td align="center" >
	        			<h3>Responsável</h3>
	        		</td>
	        		-->
	        		<td align="center" >
	        			<h3>Usuário</h3>
	        		</td>
	    		</tr>
	    	<?php
	    	while($mProposta_seguradora_historico = pg_fetch_array($query_proposta_seguradora_historico))
	    	{
	    		if($n == 1)
	    		{
	    			$cor = "#FFFFFF";
	    			$n = 0;
	    		}
	    		else
	    		{
	    			$cor = "#DEE6F6";
	    			$n = 1;
	    		}
	    		
				if($_SESSION['usuario']['depoid'] != '56' && $mProposta_seguradora_historico['psmtenvia_seguradora'] != 'F' && ($_SESSION['funcao']['proposta_seguradora_edicao']==1 || $_SESSION['funcao']['proposta_seguradora_historico'] == 1 ))
				{
					
					    		
			?>	    		
	    		<tr bgcolor='<?php echo $cor ?>' onmouseover="jQuery(this).attr('bgcolor','#F9E5A9');" onmouseout="jQuery(this).attr('bgcolor', jQuery(this).find('input:hidden').val());">
	    			<input type="hidden" value="<?=$cor?>" name="tr_cor"/>
                    <td align='center' height='15'>
	    				<?php echo $mProposta_seguradora_historico['prpshdt_cadastro_br']?>	    				
	    			</td>
	    			<td>
	    				<?php echo $mProposta_seguradora_historico['solicitacao']?>	    				
	    			</td>
	    			<td>
	    				<?php echo $mProposta_seguradora_historico['prpsadescricao']?>	    				
	    			</td>
	    			<td align="center">
	    				<?php echo $mProposta_seguradora_historico['prpshcombinacao']?>	    				
	    			</td>
	    			<td align="center">
	    				<?php echo $mProposta_seguradora_historico['prpshapolice'].' - '.$mProposta_seguradora_historico['prpshno_item'] ?>	    				
	    			</td>
	    			<td>
	    				<?php echo $mProposta_seguradora_historico['psmtdescricao']?>	    				
	    			</td>
	    			
	    			<td>
	    				<?php echo $mProposta_seguradora_historico['prpssdescricao']?>	    				
	    			</td>
	    			<td>
	    				<?php echo $mProposta_seguradora_historico['prpshcontato']?>	    				
	    			</td>
	    			<td>
	    				<?php echo $mProposta_seguradora_historico['prpshdata']." ".$mProposta_seguradora_historico['prpshhora']?>	    				
	    			</td>
	    			<td>
	    				<?php echo $mProposta_seguradora_historico['prpshobservacao']?>	  
	    			</td>
	    			<td>
	    				<?php echo $mProposta_seguradora_historico['nm_usuario']?>	    				
	    			</td>
	    		</tr>	
	    	<?php	
				}
	    	}
	    	?>	   		
	        </table>
	        <?php } ?>		            
		</td>
	</tr>					
</table>
</center>     
                  	
<?php

} catch (Exception $e) {

	pg_query($conn, "ROLLBACK");

	$msg = $e->getMessage();

}

?>