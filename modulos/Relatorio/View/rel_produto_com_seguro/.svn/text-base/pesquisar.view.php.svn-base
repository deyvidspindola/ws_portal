 
 <div class="resultado_pesquisa">
 <div class="bloco_titulo">Resultado da Pesquisa</div>
   <div class="bloco_conteudo">
     <div class="listagem">
                        <table>
                            <thead>
                                <tr>
                                    <th class="menor">N&uacute;mero Contrato</th>
                                    <th class="menor">N&uacute;mero Seguradora</th>
                                    <th class="menor">Tipo</th> 
                                    <th class="menor">CPF/CNPJ</th>
                                    <th class="menor">Data Instala&ccedil;&atilde;o</th>
                                    <th class="menor">Data Ativa&ccedil;&atilde;o</th>
                                    <th class="menor">Data Cadastro</th>
                                    <th class="medio">Status</th>
                                    <th class="menor">Ação</th>
                                    <th class="menor">E-mail ao Cliente</th>
                                </tr>
                            </thead>
                            <tbody>
                            
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
		                                    <td class="centro"><?php echo $dados['num_contrato'];?></td>
		                                    <td class="centro"><?php echo $dados['numero_seguradora'];?></td>
		                                    <td class="centro"><?php echo $dados['tipo'];?></td>
		                                    <td class="centro"><?php echo $RelProdutoComSeguro->aplicarMascaraCPF_CNPJ($dados['cpf_cnpj']);  ?></td>
		                                    <td class="centro"><?php echo $dados['dt_instalacao'];?></td>
		                                    <td class="centro"><?php echo $dados['dt_ativacao'];?></td>
		                                    <td class="centro"><?php echo $dados['dt_cadastro'];?></td>
		                                    <td class="centro">
		                                    
		                                    <?php if($dados['tipo'] == 'Apolice' &&  empty($dados['numero_seguradora'])) {?>
		                                    
		                                    	<form id="form_detalhes<?php echo $dados['id_apolice']; ?>" method="post" class="detalhesForm">
					                                <input type="hidden" name="acao" id="acao" value="detalharDadosErro" />
					                                <input type="hidden" id="data_ini" name="data_ini" value="<?php echo $_POST['data_ini']; ?>" />
													<input type="hidden" id="data_fim" name="data_fim" value="<?php echo $_POST['data_fim']; ?>"/>
													<input type="hidden" id="documento" name="documento" value="<?php echo $_POST['documento']; ?>"/>
													<input type="hidden" id="num_contrato" name="num_contrato" value="<?php echo $_POST['num_contrato']; ?>" />
													<input type="hidden" id="placa" name="placa"  value="<?php echo $_POST['placa']; ?>"/>
													<input type="hidden" id="status_apolice" name="status_apolice"  value="<?php echo $_POST['status_apolice']; ?>"/>
					                                <input type="hidden" name="id_apolice" id="id_apolice" value="<?php echo $dados['id_apolice']; ?>" />
					                                <a href="javascript:void(0);" onclick="jQuery('#form_detalhes<?php echo $dados['id_apolice']; ?>').submit();"> <?php echo $dados['status'];?></a>
	                                            </form>
	                                            
	                                            
	                                         <?php }elseif($dados['tipo'] == 'Proposta' &&  !empty($dados['numero_seguradora'])){
	                                         
	                                         	     echo $dados['status'];  ?>   
	                                            
		                                    <?php }else{ ?>
		                                    			                                    	
		                                    	 <form id="form_detalhes_sucesso<?php echo $dados['id_apolice']; ?>" method="post" class="detalhesForm">
			                                    	 <input type="hidden" name="acao" id="acao" value="detalharDadosSucesso" />
			                                    	 <input type="hidden" id="data_ini" name="data_ini" value="<?php echo $_POST['data_ini']; ?>" />
			                                    	 <input type="hidden" id="data_fim" name="data_fim" value="<?php echo $_POST['data_fim']; ?>"/>
			                                    	 <input type="hidden" id="documento" name="documento" value="<?php echo $_POST['documento']; ?>"/>
			                                    	 <input type="hidden" id="num_contrato" name="num_contrato" value="<?php echo $_POST['num_contrato']; ?>" />
			                                    	 <input type="hidden" id="placa" name="placa"  value="<?php echo $_POST['placa']; ?>"/>
			                                    	 <input type="hidden" id="status_apolice" name="status_apolice"  value="<?php echo $_POST['status_apolice']; ?>"/>
			                                    	 <input type="hidden" name="id_apolice" id="id_apolice" value="<?php echo $dados['id_apolice']; ?>" />
			                                    	 <a href="javascript:void(0);" onclick="jQuery('#form_detalhes_sucesso<?php echo $dados['id_apolice']; ?>').submit();"> <?php echo $dados['status'];?></a>
		                                    	 </form>
		                                    	 
											<?php  }?>
											
		                                    </td>
		                                    <td class="centro">
		                                    
		                                    <?php  if($dados['tipo'] == 'Apolice' &&  empty($dados['numero_seguradora']) && !empty($dados['num_contrato'])) {?>
	                                        
	                                                <a href="javascript:void(0);" class="reenviarApolice" id="linha_apo<?php echo $dados['num_contrato']; ?>" id_apolice="<?php echo $dados['id_apolice'];?>" contrato_cli="<?php echo $dados['num_contrato']; ?>" >Reenviar Apólice</a>
		                                            
		                                            <div id="loading_linha_apo<?php echo $dados['num_contrato']; ?>" style="display:none;">
		                                               <img src="images/ajax-loader-circle.gif" alt='Processando'/>
		                                            </div>
	                                        
		                                    <?php }?>
		                                    
		                                    		                                    
		                                    <?php if(trim($dados['tipo']) == 'Proposta' &&  !empty($dados['numero_seguradora'] ) && !empty($dados['num_contrato']) ){?>
		                               
		                               			  <a href="javascript:void(0);" class="ativarApolice" id="linha_prop<?php echo $dados['num_contrato']; ?>" id_apolice="<?php echo $dados['id_apolice'];?>" contrato_cli="<?php echo $dados['num_contrato']; ?>" >Ativar Apólice</a>
		                                            
		                                            <div id="loading_linha_prop<?php echo $dados['num_contrato']; ?>" style="display:none;">
		                                               <img src="images/ajax-loader-circle.gif" alt='Processando'/>
		                                            </div>
		                                    
		                                    <?php }?>
		                                    
		                                    </td>
		                                    <td class="centro">
		                                      <?php 
		                                         //exibe link para reenviar e-mail caso exista número da apólice                               
		                                         if(!empty($dados['numero_seguradora']) && $dados['tipo'] == 'Apolice' ) {?>
		                                             <a href="javascript:void(0);" class="reenviarEmail" id="linha_<?php echo $dados['num_contrato']; ?>" num_apolice="<?php echo $dados['numero_seguradora'];?>" contrato_cli="<?php echo $dados['num_contrato']; ?>" >Reenviar E-mail</a>
		                                             <span id="loading_linha_<?php echo $dados['num_contrato']; ?>"></span>
		                                      <?php }?>
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
                    </div>
                </div> 
            </div>