 <div class="resultado_pesquisa">
 <div class="bloco_titulo">Resultado da Pesquisa</div>
   <div class="bloco_conteudo">
   
    <?php echo $this->view->ordenacao; ?>
   
     <div class="listagem">
                        <table>
                            <thead>
                                <tr>
                                    <th class="menor">Nome da Campanha</th>
                                    <th class="menor">Usu&aacute;rio Cadastro</th>
                                    <th class="menor">Valor da D&icirc;vida</th> 
                                    <th class="menor">Aging da D&icirc;vida</th>
                                    <th class="menor">Vencimento</th>
                                    <th class="menor">Formato de Arquivo</th>
                                    <th class="menor">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                            <form name="frm_campanha" id="frm_campanha" method="POST" action="fin_boletagem_massiva.php">
                            
                            <input type="hidden" name="acao" id="acao" value="prepararEnvioBoletoEmail" />
                            
                              <input type="hidden" name="id_campanha" id="id_campanha" value="" />
                            
                            </form>
                            
                            
                            <?php 
                             
                               $num_registros = 0;

                               if(is_array($dadosPesquisa)) {
                            	
                            		$num_registros = count($dadosPesquisa);
                            		
                            	     foreach ($dadosPesquisa as $key => $dados){ 
                            	     	
											if($key%2==0){
											   $classe = "impar";
											}else{
											   $classe = "par";
											}?>
		                               
		                                <tr class="<?php echo $classe; ?>">
		                                    <td class="centro"><?php echo $dados['nome_campanha'];?></td>
		                                    <td class="centro"><?php echo $dados['nome_usuario'];?></td>
		                                    <td class="centro"><?php echo $dados['valor_divida'];?></td>
		                                    <td class="centro"><?php echo $dados['aging_divida'];  ?></td>
		                                    <td class="centro"><?php echo $dados['data_vencimento'];?></td>
		                                    <td class="centro"><?php echo $dados['formato_envio'];?></td>
		                                    <td class="centro"> 
		                                    
		                                    
		                                    <?php if ($funcao_acoes) {?>
		                                    
			                                    <?php if($dados['formato_envio'] == 'E-mail') { 
			                                    	
			                                    	   if($dados['aboarquivo'] != ''){
	
				                                    	   if($dados['data_envio'] == ''){
				                                          	
				                                    		?>
				                                    			 <a title="Envio por e-mail" rel="<?php  echo $dados['abooid']; ?>" id="envio_email" class="envio_em" href="javascript:void(0);">
						           		                         <IMG class=icone  src="images/ic_fechado.gif"></a><span class='loading_email'></span>
				                                    			                                            
			                                             <?php }?>
		                                             
		                                                      <a title="Download de Arquivos" target="_blank" href="download.php?arquivo=<?php echo $caminho.$dados['aboarquivo']; ?>">
			           							              <IMG class=icone src="images/download.png"></a>
			                                         <?php }else{
			                                         
			                                         			if($processamento && $id_campanha == $dados['abooid']){
			                                         				echo '<font color="red">Em processamento.</font>';
			                                         			}else{
			                                         				 echo 'Não foram gerados boletos.';
			                                         			}
			                                            } 
			                                    
			                                        }?>
			                                    
			                                    
			                                    <?php
			                                          if($dados['formato_envio'] == 'Gráfica') { 
			                                          
			                                          	if($dados['aboarquivo'] != ''){
			                                          		
				                            				if($dados['data_envio'] == ''){
				                                          	?>
						                                    	   <a title="Enviar para FTP da Gráfica" rel="<?php  echo $dados['abooid']; ?>" id="ftp" class="ftp" href="javascript:void(0);">
						           		                          <IMG class=icone src="images/ftp.png"></a><span class='loading_ftp<?php  echo $dados['abooid']; ?>'></span>
						           		                          
						           		                <?php }?>
					           		                   
					           		                          <a title="Download de Arquivos" target="_blank" href="download.php?arquivo=<?php echo $caminho.$dados['aboarquivo']; ?>">
			           							              <IMG class=icone src="images/download.png"></a>
			           		                            
												 <?php }else{ 
												 	
														 	if($processamento && $id_campanha == $dados['abooid']){
														 		echo '<font color="red">Em processamento.</font>';
														 	}else{
														 		echo 'Não foram gerados boletos.';
														 	}

												       }
			                                          
			                                          }//fim fomato ennvio
											
		                                          }//função acões ?>
											
											
		                                    </td>
		                                </tr>
                                
                                 <?php }//fim foreach
                                   
                                     } else{?>
                                    
                                     <tr class="par">
	                   
                                     </tr>
                                    
                                    <?php }?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="10"> <?php echo $num_registros; ?> registro(s) encontrados.</td>
                                </tr>
                            </tfoot>
                        </table>
                        
                        <?php echo ($this->view->totalResultados > 10) ? $this->view->paginacao : ''; ?>
                    </div>
                </div> 
            </div>