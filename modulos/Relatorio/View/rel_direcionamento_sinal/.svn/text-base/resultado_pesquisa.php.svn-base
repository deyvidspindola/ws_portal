<?php if(isset($this->view->data)): ?>
<style>
	.resultado_pesquisa .nenhum-resultado {
		text-align: center;
		font-size: 11px;
	}

	.tabela_resultado_pesquisa {
		width: 100%;
		border-collapse: collapse;
	}
	.tabela_resultado_pesquisa th {
		padding: 10px 8px;
		background-color: #bad0e5;
	}
	.tabela_resultado_pesquisa td {
		padding: 6px 8px;
	}
	.tabela_resultado_pesquisa th,
	.tabela_resultado_pesquisa td {
		font-size: 11px;
	}
	.tabela_resultado_pesquisa tbody tr:nth-child(even) {
		background-color: #e6eaee;
	}

	.tabela_resultado_pesquisa tbody td[rowspan] {
		/*background-color: red;*/
	}

	.tabela_resultado_pesquisa .text-center {
		text-align: center;
	}
</style>
<br>
<div class="resultado_pesquisa">
  <div class="bloco_titulo">Resultado da Pesquisa</div>
  <div class="bloco_conteudo">
  	<?php if(count($this->view->data) > 0): ?>
		<table class="tabela_resultado_pesquisa">
			<thead>
				<tr>
					<th style="text-align:left;">Data Solicitação</th>
					<th style="text-align:left;">Ação</th>
					<th style="text-align:left;">Layout</th>
					<th class="text-center">Data Execução</th>
					<th class="text-center">Status</th>
					<th>Validade</th>
					<th class="text-center">ID</th>
					<th class="text-center">Veículo</th>
					<th style="text-align:left;">Gerenciadora</th>
					<th class="text-center">Prazo Direcionamento</th>
					<!-- <th>IP</th> -->
					<th>Usuário</th>
				</tr>
			</thead>
			<tbody>
				<?php

					$registroAnterior = null;
					$countRegistrosAnteriores = 1;

				?>
				<?php foreach($this->view->data as $row): ?>
				<?php

					if($registroAnterior == $row['lasgoid']){
						$countRegistrosAnteriores++;
					}else{
						$countRegistrosAnteriores = 1;
					}

					$registroAnterior = $row['lasgoid'];

				?>
				<tr>
					<?php if($countRegistrosAnteriores == 1): ?>
					<td style="white-space:nowrap;" <?php echo empty($row['num_comandos']) ? '' : "rowspan=\"$row[num_comandos]\""; ?>><?php echo date('d/m/Y H:i', strtotime($row['data_solicitacao'])); ?></td>
					<?php endif; ?>
					<?php if($countRegistrosAnteriores == 1): ?>
					<td style="text-align:left;" <?php echo empty($row['num_comandos']) ? '' : "rowspan=\"$row[num_comandos]\""; ?>><?php echo $row['acao']; ?></td>
					<?php endif; ?>
					<td style="text-align:left;"><?php echo !empty($row['layout']) ? $row['layout'] : '-';  ?></td>
					<td class="text-center"><?php echo !empty($row['data_execucao']) ? date('d/m/Y H:i', strtotime($row['data_execucao'])) : '-'; ?></td>
					<td class="text-center"><?php echo !empty($row['status']) ? $row['status'] : '-'; ?></td>
					<td class="text-center"><?php echo !empty($row['validade']) ? date('d/m/Y H:i', strtotime($row['validade'])) : '-'; ?></td>
					<?php if($countRegistrosAnteriores == 1): ?>
					<td class="text-center" <?php echo empty($row['num_comandos']) ? '' : "rowspan=\"$row[num_comandos]\""; ?>><?php echo !empty($row['veiculo_id']) ? $row['veiculo_id'] : '-';  ?></td>
					<?php endif; ?>
					<?php if($countRegistrosAnteriores == 1): ?>
					<td class="text-center" <?php echo empty($row['num_comandos']) ? '' : "rowspan=\"$row[num_comandos]\""; ?>><?php echo $row['veiculo']; ?></td>
					<?php endif; ?>
					<?php if($countRegistrosAnteriores == 1): ?>
					<td <?php echo empty($row['num_comandos']) ? '' : "rowspan=\"$row[num_comandos]\""; ?>><?php echo $row['gerenciadora']; ?></td>
					<?php endif; ?>
					<?php if($countRegistrosAnteriores == 1): ?>
					<td class="text-center" <?php echo empty($row['num_comandos']) ? '' : "rowspan=\"$row[num_comandos]\""; ?>>
					<?php 

						if(!empty($row['prazo_direcionamento'])){
							echo date('d/m/Y H:i', strtotime($row['prazo_direcionamento']));
						}else{
							if($row['acao'] === 'DIRECIONAMENTO'){
								echo 'Indeterminado';
							}else {
								echo '-';
							}
						}

					?>							
					</td>
					<?php endif; ?>
					<?php if($countRegistrosAnteriores == 1): ?>
					<!-- <td <?php echo empty($row['num_comandos']) ? '' : "rowspan=\"$row[num_comandos]\""; ?>><?php echo $row['ip']; ?></td> -->
					<?php endif; ?>
					<?php if($countRegistrosAnteriores == 1): ?>
					<td <?php echo empty($row['num_comandos']) ? '' : "rowspan=\"$row[num_comandos]\""; ?>><?php echo $row['usuario']; ?></td>
					<?php endif; ?>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php else: ?>
			<p class="nenhum-resultado">Nenhum resultado encontrado.</p>
		<?php endif; ?>
  </div>
  <?php if(count($this->view->data) > 0): ?>
  <div class="bloco_acoes">
  	<form method="post">
  		<input id="acao" name="acao" type="hidden">
			<button type="button" onclick="enviarAcao('exportarExcel');" style="cursor: default; ">Exportar Excel</button>
			<button type="button" onclick="enviarAcao('exportarPdf');" style="cursor: default; ">Exportar PDF</button>
		</form>
   </div>
  <?php endif; ?>
</div>
<script>
	
 function enviarAcao(acao){

 	document.getElementById('acao').value = acao;
	document.getElementById('form_pesquisa').submit();
 	
 }

</script>
<?php endif; ?>