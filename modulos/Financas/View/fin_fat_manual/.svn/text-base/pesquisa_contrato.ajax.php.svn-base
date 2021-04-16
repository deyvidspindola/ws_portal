	<div class="bloco_titulo">Contratos do Cliente</div>
	<div class="bloco_conteudo">
		 	<!-- 
		 	
		 	[connumero] =&gt; 102350188
            [veioid] =&gt; 360359
            [veiplaca] =&gt; SNQ7551
            [ativo] =&gt; t
            [equoid] =&gt; 289311
            [equno_serie] =&gt; 153297
            [eqcoid] =&gt; 82
            [eqcdescricao] =&gt; SASCARGA FULL SAT 200
            [tpcoid] =&gt; 0
            [tpcdescricao] =&gt; Cliente
            [csioid] =&gt; 6
            [csidescricao] =
		 	
		 	 -->
		 <div class="listagem">
			<table>
			<?php if($listContratos && count($listContratos)): ?>
				<thead>
					<tr>
						<th style="text-align: center;"><?php echo "Número";?></th>
						<th style="text-align: center;"><?php echo "Veículo";?></th>
						<th style="text-align: center;"><?php echo "Equipamento";?></th>
						<th style="text-align: center;"><?php echo "Situação";?></th>
						<th style="text-align: center;">Classe do Termo</th>
						<th style="text-align: center;">Tipo do Termo</th>
						<th style="text-align: center;">Status do Termo</th>
					</tr>
				</thead>
				<tbody>		
					<?php foreach ($listContratos as $key => $contrato ): 
               				$class = $class == '' ? 'class="par"' : ''; ?>								
					<tr <?php echo $class?>>
						<td class="conoid link" title="Clique para usar este contrato."><a href="javascript:void(0)"><?php echo $contrato['connumero']?></a></td>
						<td><?php echo $contrato['veiplaca']?></td>
						<td><?php echo $contrato['equno_serie']?></td>
						<td><?php echo (($contrato['veioid'] && $contrato['veioid'] > 0) ? "ATIVO" : "CANCELADO")?></td>
						<td><?php echo $contrato['eqcdescricao']?></td>
						<td><?php echo $contrato['tpcdescricao']?></td>
						<td><?php echo $contrato['csidescricao']?></td>
					</tr>
					<?php endforeach; ?>							
				</tbody>
				<tfoot>								
					<tr><td colspan="7" style="text-align: center;"><?=count($listContratos)?> registro(s) encontrado(s)</td></tr>					
				</tfoot>	
			<?php else: ?>
			<thead><tr><th style="text-align: center;">Nenhum contrato encontrado.</th></tr></thead>
			<?php endif ?>
			</table>
		</div>
		
	</div>
	<div class="separador"></div>