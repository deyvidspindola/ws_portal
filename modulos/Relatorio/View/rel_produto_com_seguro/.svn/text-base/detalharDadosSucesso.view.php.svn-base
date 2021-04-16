<div class="bloco_titulo">Detalhes</div>
	<div class="bloco_conteudo">
		<div class="listagem">

					<table>
						<thead>
							<tr>
								<th class="medio">Data Cadastro</th>
								<th class="medio">XML Envio</th>
								<th class="medio">XML Retorno</th>
								<th class="mini">Origem Chamada</th>
								<th class="medio">Origem Sistema</th>
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
		                           <td class="esquerdo"><?php echo htmlspecialchars($dados['xml_envio']);?></td>
		                           <td class="esquerdo"><?php echo htmlspecialchars($dados['xml_retorno']);?></td>
		                           <td class="centro"><?php echo $dados['origem_chamada'];?></td>
		                           <td class="centro"><?php echo $dados['origem_sistema'];?></td>
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
		                <td colspan="5"> <?php echo $num_registros; ?> registro(s) encontrados.</td>
		              </tr>
		            </tfoot>
		        </table>
	</div>
</div>

 <div class="bloco_acoes">
    <button type="button" id="voltar"  onclick="$('#busca_dados').submit();">Voltar</button>
 </div>    
