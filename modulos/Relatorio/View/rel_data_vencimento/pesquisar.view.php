<style type="text/css">
/* RESOLVE O BUG NA LISTAGEM SEM BORDA NO GOOGLE CHROME*/ 
div.bloco_conteudo {    
    padding: 0px 3px 0px 2px !important;    
}
</style>

 <div class="resultado_pesquisa">
 	<div class="bloco_titulo">Resultado da Pesquisa</div>
   		<div class="bloco_conteudo">
   	    	<?php echo $this->view->ordenacao; ?>
   
    			<div class="listagem">
                    <table>
                        <thead>
                            <tr>
                                <th class='menor' >Nº Título</th>
                        		<th class='menor' >Emissão</th>
                        		<th class='menor' >Valor Título</th>
                        		<th class='menor' >Vencimento</th>
                        		<th class='menor' >Vencimento (Alterado)</th>
                        		<th class='menor' >Motivo</th>
                        		<th class='menor' >Usuário</th>
                        		<th class='menor' >Data / Hora</th>                        		
                            </tr>
                        </thead>
                        <tbody>
                            <form name="frm_campanha" id="frm_campanha" method="POST" action="rel_data_vencimento.php">
                            
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
		                                    <td class="centro"><a href="titulos.php?titoid=<?php echo $dados['titoid']?>" target="_blank"><?php echo $dados['titoid'];?></a></td>
		                                    <td class="centro"><?php echo $dados['data_emissao'];?></td>
		                                    <td class="centro"><?php echo $dados['titvl_titulo'];?></td>
		                                    <td class="centro"><?php echo $dados['data_vencimento'];  ?></td>
		                                    <td class="centro"><?php echo $dados['data_vencimento_alterada'];?></td>
		                                    <td class="centro" style='width: 200px !important;'><?php echo $dados['tmavdescricao'];?></td>
		                                    <td class="centro" style='width: 200px !important;'><?php echo $dados['nm_usuario'];?></td> 
		                                    <td class="centro">
		                                    	<?php echo $dados['data'];?>&nbsp;<?php echo $dados['hora'];?>
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