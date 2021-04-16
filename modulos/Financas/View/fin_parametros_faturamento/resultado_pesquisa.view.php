 
 <div class="resultado_pesquisa">
 <div class="bloco_titulo">Resultado da Pesquisa</div>
   <div class="bloco_conteudo">
     <div class="listagem">
                        <table>
                            <thead>
                                <tr>
                                    <th class="menor">Contrato</th>
                                    <th class="medio">Cliente</th>
                                    <th class="medio">Tp. Contrato</th> 
                                    <th class="medio">Obrig. Financ.</th>
                                    <th class="mini">Prazo de Vencimento(Dias)</th>
                                    <th class="mini">Valor</th>
                                    <th class="medio">Per&iacute;odo Valor</th>
                                    <th class="mini">Isento de Cobran&ccedil;a</th>
                                    <th class="medio">Per&iacute;odo Isen&ccedil;&atilde;o</th>
                                    <th class="mini">%Desc.</th>
                                    <th class="medio">Per&iacute;odo Desconto</th>
                                    <th class="medio">Motivo Macro</th>
                                    <th class="medio">Motivo Micro</th>
                                    <th class="mini">Vigência</th>
                                    <th class="mini">Observa&ccedil;&atilde;o</th>
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

									      $quant_obr =explode(',', $dados['obr_financeira_multiplo']); ?>
		                               
		                                <tr class="<?php echo $key%2==0 ? $classe = "impar" : $classe = "par"; ?>">
		                                    <td class="centro"><?php echo $dados['contrato'];?></td>
		                                    <td class="left"><?php echo $dados['id_cliente'];?></td>
		                                    <td class="left"><?php echo $dados['id_tipo_contrato'];?></td>
		                                    
		                                    <td class="left"> 
		                                    
		                                    <?php if(count($quant_obr) == 1 ) :

		                                         echo $dados['obr_financeira_multiplo'];

		                                    else :?>
		                                       
		                                         <a onclick="ver_dados('<?php echo $dados['obr_financeira_multiplo'];?>','obriga');" href="javascript:void(0);">Exibir obrigação financeira</a>
		                                   
		                                    <?php endif;?>
		                                    
		                                    </td>
		                                    
		                                    <td class="centro"><?php echo $dados['prazo_vencimento'];?></td>
		                                    <td class="centro"><?php echo $dados['valor'];?></td>
                                            <td class="centro"><?php echo $dados['periodo_valor'];?></td>
		                                    <td class="centro"><?php echo $dados['isento'];?></td>
		                                    <td class="centro"><?php echo $dados['periodo_isencao'];?></td>
		                                    <td class="centro"><?php echo $dados['desconto'];?></td>
		                                    <td class="centro"><?php echo $dados['periodo_desconto'];?></td>
		                                    <td class="centro"><?php echo $dados['macro_motivo'];?></td>
		                                    <td class="centro"><?php echo $dados['micro_motivo'];?></td>
                                            <td class="centro"><?php echo $dados['vigencia'];?></td>
		                                   <!--  <td class="centro"><?php echo $dados['quantidade_faturamento'];?></td> -->
		                                   <!-- <td class="centro"><?php echo $dados['periodicidade'];?></td> -->
		                                   
		                                    <td class="centro">
		                                    
		                                     <?php if($dados['parfobservacao_usuario'] !='' ) : ?>
		                                     
		                                          <a onclick="ver_dados('<?php echo trim(str_replace("\n", '\n', str_replace('"', '\"', addcslashes(str_replace("\r", '', (string)$dados['parfobservacao_usuario']), "\0..\37'\\"))));?>','observa');" href="javascript:void(0);">Observação</a>

		                                   <?php else :
		                                       
		                                       echo '-';
		                                   
		                                     endif;?>
		                                    
		                                    </td> 
		                                   
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
                                    <td colspan="16"> <?php echo $num_registros; ?> registro(s) encontrados.</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div> 
            </div>