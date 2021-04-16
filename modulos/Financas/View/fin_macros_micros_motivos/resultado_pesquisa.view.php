 
 <div class="resultado_pesquisa">
 <div class="bloco_titulo">Resultado da Pesquisa</div>
   <div class="bloco_conteudo">
     <div class="listagem">
                        <table>
                            <thead>
                                <tr>
                                    <th class="menor">Motivo</th>
                                    <th class="medio">Tipo</th>
                                    <th class="medio">Data Inclusão</th>
<!--                                     <th class="mini">Qtd. para Fat.</th> -->
<!--                                     <th class="mini">Per&iacute;od. Fat.</th> -->
                                    <th class="mini">A&ccedil;&atilde;o</th>
                                </tr>
                            </thead>
                            <tbody>
                            
                            <?php 
                             
                               $num_registros = 0;

                               if(is_array($dadosPesquisa)) {

                            		$num_registros = count($dadosPesquisa);
                                                   		
                            	     foreach ($dadosPesquisa as $key => $dados){ 
                            	         ?>
		                               
		                                <tr class="<?php echo $key%2==0 ? $classe = "impar" : $classe = "par"; ?>">
		                                    <td class="centro"><?php echo $dados['descricao'];?></td>
		                                    <td class="centro"><?php echo $dados['nivel'];?></td>
		                                    <td class="centro"><?php echo $dados['data_criacao'];?></td>
		                                    <td class="centro"><a class="link_editar" href="#" data-id="<?php echo $dados['id'];?>">
		                                        <img src="images/icones/file.gif" alt="Editar" title="Editar" /></a>
		                                    </td>
		                                </tr>
                                
                                 <?php }//fim foreach
                                   
                                     } else{?>
                                     	<tr class="par"></tr>
                                    <?php }?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4"> <?php echo $num_registros; ?> registro(s) encontrados.</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div> 
            </div>