<?php if(!empty($this->view->dados)){ 

	if ( $this->view->status && count($this->view->dados) > 0) {
		require 'visualizar.php';
	}
	
?>

<div class="separador"></div>
<div class="bloco_titulo">Resultado da Pesquisa</div>
<div class="bloco_conteudo">
	<div class="listagem">
		<table>		  
			<thead>
				<tr>
					<th class="maior esquerda">Descrição<br> (SasWeb)</th>
					<th class="menor esquerda">Tipo</th>
					<th class="menor esquerda">Tag</th>
					<th class="menor esquerda">Obrigação Financeira</th>
					<th class="menor esquerda">Status</th>
					<th class="menor esquerda">Ação</th>	
				</tr>
			</thead>
			
			<tbody>	
                    <?php $classeLinha = "par"; ?>
                    <?php $totalGeral = 0;?>
					
                    <?php if(!empty($this->view->dados)):
                           foreach ($this->view->dados as $resultado) : $totalGeral++;?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
                        <tr class="<?php print $classeLinha; ?>">
							<td><?php print $resultado['rastdescricao']; ?></td>
							<td><?php print $resultado['tipo']; ?></td>
							<td><?php print $vl = $resultado['tipo'] =="Pacote"? $resultado['rasttag_pacote']:$resultado['rasttag_funcionalidade']?></td>
							<td><?php print $resultado['obrigacao_financeira']; ?></td>
							<td><?php print $resultado['rastsdescricao']; ?></td>
							<td>
							 &nbsp; &nbsp; 
							 <img src="modulos/web/images/search.png" title="Visualizar" height="15" width="15" border="0" id="opener_<?php print $resultado['rastoid']; ?>">
							 &nbsp;
							 <img src="/sistemaWeb/images/icones/file.gif" title="Histórico" height="15" width="15" border="0" id="historico_<?php print $resultado['rastoid']; ?>">
							 &nbsp;  
							 <?php if (!in_array($resultado['rastsdescricao'], array("Aprovada", "Depreciado", "Cancelado"))){?>
							   <a href="fin_pre_obrigacoes_pen_apro.php?acao=editar&rastoid=<?php print $resultado['rastoid']; ?>&status=<?php print $vl = $resultado['tipo'] =="Pacote"? "P":"F"?>"><img src="modulos/web/images/edit-icon.png" title="Editar" height="15" width="15" border="0"></a>
							 <?php }?>
							</td>						
                        </tr>
						<script>
						$(document).ready(function(){
						    $("#opener_<?php echo $resultado['rastoid']; ?>").click(function(){
						        $.ajax({url: "fin_pre_obrigacoes_pen_apro.php?acao=visualizar&rastoid=<?php print $resultado['rastoid']; ?>&status=<?php print $vl = $resultado['tipo'] =="Pacote"? "P":"F"?>", success: function(result){
						            $("#dialog").html(result);
						            $("#dialog").dialog( "option", "width", 1000 );
						            $("#dialog" ).dialog( "open" );
						        }});
						    });
						    $("#historico_<?php echo $resultado['rastoid']; ?>").click(function(){
						        $.ajax({url: "fin_pre_obrigacoes_pen_apro.php?acao=historico&rastoid=<?php print $resultado['rastoid']; ?>", success: function(result){
						            $("#dialog").html(result);
						            $("#dialog").dialog( "option", "width", 1200 );
						            $("#dialog" ).dialog( "open" );
						        }});
						    });
						});
						</script>
                    <?php endforeach; 
                    	endif; ?>                      
			</tbody>  
			 <tfoot>
                    <tr>
                        <td colspan="6"><?php print $totalGeral;?> registros encontrados.</td>
                    </tr>
                </tfoot>          
		</table>
	</div>
</div>
<?php } ?> 