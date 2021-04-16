<div class="resultado">
<div class="bloco_titulo">Resultados do Processamento</div>
<div class="bloco_conteudo">

	 <div class="listagem">
		<table>
			<thead>
				<tr>
					<th style="text-align: left;">Total de contratos no arquivo</th>
					<th style="text-align: right;"><?php echo count($this->relatorio['total'])?></th>
				</tr>
			</thead>
			<thead>
				<tr>
					<th style="text-align: left;">Contratos não localizados na base</th>
					<th style="text-align: right;"><?php echo count($this->relatorio['naoLocalizados'])?></th>
				</tr>
			</thead>
			<?php
				if(count($this->relatorio['naoLocalizados']) > 0):
					foreach($this->relatorio['naoLocalizados'] as $naoLocalizados): ?> 
					<tr class="zebra">
						<td colspan="2" style="text-align: right;"><?php echo $naoLocalizados; ?></td>
					</tr>
					<?php endForeach;
				endif;
			?>
			<thead>
				<tr>
					<th style="text-align: left;">Contratos não alterados para rescisão por inadimplência</th>
					<th style="text-align: right;"><?php echo count($this->relatorio['naoAlterados'])?></th>
				</tr>
			</thead>
			<?php
				if(count($this->relatorio['naoAlterados']) > 0):
					foreach($this->relatorio['naoAlterados'] as $naoAlterados): ?> 
					<tr class="zebra">
						<td style="text-align: left;"><?php echo $naoAlterados['motivo']; ?></td>
						<td style="text-align: right;"><?php echo $naoAlterados['contrato']; ?></td>
					</tr>
					<?php endForeach;
				endif;
			?>
			<thead>
				<tr>
					<th style="text-align: left;">Contratos alterados para rescisão por inadimplência</th>
					<th style="text-align: right;"><?php echo count($this->relatorio['alterados'])?></th>
				</tr>
			</thead>
			<?php
				if(count($this->relatorio['alterados']) > 0):
					foreach($this->relatorio['alterados'] as $alterados): ?> 
					<tr class="zebra">
						<td style="text-align: left;"><?php echo $alterados['motivo']; ?></td>
						<td style="text-align: right;"><?php echo $alterados['contrato']; ?></td>
					</tr>
					<?php endForeach;
				endif;
			?>
			<thead>
				<tr>
					<th style="text-align: left;">Contratos com ordens de serviço de retirada geradas</th>
					<th style="text-align: right;"><?php echo count($this->relatorio['retiradaGeradas'])?></th>
				</tr>
			</thead>
			<?php
				if(count($this->relatorio['retiradaGeradas']) > 0):
					foreach($this->relatorio['retiradaGeradas'] as $retiradaGeradas): ?> 
					<tr class="zebra">
						<td colspan="2" style="text-align: right;"><?php echo $retiradaGeradas; ?></td>
					</tr>
					<?php endForeach;
				endif;
			?>
			<thead>
				<tr>
					<th style="text-align: left;">Contratos com ordens de serviço canceladas</th>
					<th style="text-align: right;"><?php echo count($this->relatorio['canceladas'])?></th>
				</tr>
			</thead>
			<?php
				if(count($this->relatorio['canceladas']) > 0):
					foreach($this->relatorio['canceladas'] as $canceladas): ?> 
					<tr class="zebra">
						<td colspan="2" style="text-align: right;"><?php echo $canceladas; ?></td>
					</tr>
					<?php endForeach;
				endif;
			?>
			<thead>
				<tr>
					<th colspan="2">&nbsp;</th>
				</tr>
			</thead>
		</table>
	</div>
</div>
</div>
