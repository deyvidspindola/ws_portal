<div class="bloco_titulo">Detalhes</div>
<div class="bloco_conteudo">
	<div class="listagem">
	
	                <table>
                            <thead>
                                <tr>
                                    <th class="mini">Data Cadastro</th>
                                    <th class="medio">Descri&ccedil;&atilde;o Interna</th> 
                                    <th class="medio">Descri&ccedil;&atilde;o Para o Cliente</th>
                                    <th class="medio">XML Envio</th>
                                    <th class="medio">XML Retorno</th>
                                    <th class="maior">Informa&ccedil;&otilde;es Adicionais</th>
                                 </tr>
                            </thead>
                            <tbody>
                            
                            <?php 

                               if(is_array($dadosPesquisa)) {

                            		$num_registros = count($dadosPesquisa);
                            		
                            	     foreach ($dadosPesquisa as $key => $dados){ 
									
											if($key%2==0){
											   $classe = "impar";
											}else{
											   $classe = "par";
											}?>
	                               
	                                <tr class="<?php echo $classe; ?>">
	                                    <td class="centro"><?php echo $dados['dt_cad'];?></td>
	                                    <td class="centro"><?php echo stripslashes($dados['descricao_interna']);?></td>
	                                    <td class="centro"><?php echo $dados['descricao_para_cliente'];?></td>
	                                    <td class="esquerdo"><?php echo htmlspecialchars($dados['xml_envio']);?></td>
	                                    <td class="esquerdo"><?php echo htmlspecialchars($dados['xml_retorno']);?></td>
	                                    <td class="centro"><?php echo $dados['info_adicionais'];?></td>
		                                 </tr>
                                 <?php }
                                    } else{ 
                                        $num_registros = 0; ?>
                                    
                                     <tr class="par">
	                               
                                     </tr>
                                    
                                    <?php }?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6"> <?php echo $num_registros; ?> registro(s) encontrados.</td>
                                </tr>
                            </tfoot>
                        </table>
	</div>
</div>

 <div class="bloco_acoes">
    <button type="button" id="voltar"  onclick="$('#busca_dados').submit();">Voltar</button>
 </div>    
