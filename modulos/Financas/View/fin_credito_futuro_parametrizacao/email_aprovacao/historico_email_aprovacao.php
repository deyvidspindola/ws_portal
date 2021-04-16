  	<?php 
  	$historico = $this->dao->pesquisarHistorico(); 
  	if(count($historico) > 0){
  	?>  
    <div class="separador"></div>
  
    <div class="bloco_titulo">Dados do Histórico</div>
    <div class="bloco_conteudo">
    	<div class="listagem">
 			
			<table>
				<thead>
					<tr>
						<th style="text-align: center;">Cadastro</th>
						<th style="text-align: center;">Tipo</th>
						<th style="text-align: left;">Observação</th>
						<th style="text-align: left;">Usuário</th>
					</tr>
				</thead>
				<tbody>	
					<?php 
					$registroAnterior = null;
					$linhas="";
					foreach($historico as $registro): 	
               			$class = $class == '' ? 'par' : '';  
						$linhas = "<tr class=\"$class\">
										<td style=\"text-align: center;\">
											".$registro->cfeadt_inclusao."
										</td>
										<td style=\"text-align: center;\">
											".($registroAnterior==null ? "Inclusão" : "Alteração" )."
										</td>
										<td style=\"text-align: left;  width: 720px;\" >
											".$this->diferencaHistorico($registroAnterior,$registro)."
										</td>
										<td style=\"text-align: left;\">
											".$registro->usuario_inclusao."
										</td>
									</tr>".$linhas;
						$registroAnterior=$registro;
					endforeach; 
					echo $linhas;
					?>
				</tbody>
    	   </table>
    	</div>
	</div>
	<?php 
  	} 
  	?>