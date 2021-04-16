<div class="separador"></div>

        <div id="msg_erro2" class="mensagem erro invisivel"></div>
        <div id="msg_sucesso2" class="mensagem sucesso invisivel"></div>        
        <div id="msg_alerta2" class="mensagem alerta invisivel"></div>  
        
        <div class="div-loding">
            <img class="invisivel loading" src="modulos/web/images/loading.gif" />
        </div>
        <?php if ($this->resultadoPesquisa[0]->avisarUsuario) : ?>
        	<div class="mensagem alerta">
        		Reincidência desse equipamento maior que três vezes para o mesmo Defeito Lab.
        	</div>	
        <?php endif; ?>        
        <div id="resultado_pesquisa" class="">
            <div class="bloco_titulo">Histórico</div>
            <div class="bloco_conteudo">					
                <div class="listagem">
                    <table>
                        <thead>
                            <tr>
                            	<th class="centro">
                            		<input title="Excluir" type="checkbox" class="chk_codigo"  id="chk_all" name="chk_all" >
                            	</th>
                                <th>Serial</th>
                                <th>Produto</th>                                 
                                <th>Defeito Lab.</th>
                                <th>Ação Lab.</th>
                                <th>Componente Afetado Lab.</th>
                                <th>Data Entrada Lab.</th>                              
                            </tr>
                        </thead>
                        <tbody id="conteudo_listagem">		                        
	                                             
	                        <?php
							$class="";
							$cont = 0;
							$editaveis = 0;
								foreach ($this->resultadoPesquisa as $resultado): 
									$class = ($class=="" ? "class=\"par\"" : "") 
									?>
									<tr <?=$class?>>
										<td class="centro">
										<?php if ($resultado->editarExcluir) : ?>
												<input title="Excluir" type="checkbox" class="chk_codigo" 
													name="chk_codigo[]" value='<?php echo $resultado->cfaoid; ?>'
													<?php //if (!$resultado->editarExcluir) : echo "disabled"; endif;  ?>>
										<?php endif;  ?>
										</td>   
										<td>
										<?php if($resultado->editarExcluir): 
													$editaveis++;
										?>
											<a href='#' class='linkEditar' id='<?php echo $resultado->cfaoid; ?>'><?php echo $resultado->imobserial; ?></a>
										<?php 
											else: 
											  	echo $resultado->imobserial; 
											endif;
										?>
										</td>
										<td><?php echo $resultado->prdproduto; ?></td>
										<td><?php  echo wordwrap($resultado->ifddescricao, 25, "<br />", true); ?></td>
										<td><?php  echo wordwrap($resultado->ifadescricao, 25, "<br />", true); ?></td>
										<td><?php  echo wordwrap($resultado->ifcdescricao, 25, "<br />", true); ?></td>
										<td><?php  echo $resultado->cfadt_entrada; ?></td>										             
									</tr>
									<?php
									$cont++; 
								endforeach; 
						?>                                                     
                       
                       </tbody>
                    </table>
                </div>
            </div>
            <div class="bloco_acoes">
            	<button id="btn_excluir" name="btn_excluir" disabled="disabled" type="button">Excluir</button>
        	</div>
        </div>
        
<script type="text/javascript">
<?php if($editaveis == 0): ?>
		jQuery(document).ready(function(){
			jQuery('#chk_all').hide();			
		});
<?php endif; ?>
</script>
