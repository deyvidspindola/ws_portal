<div class="listagem">
			<table>
			<?php
			$itensSemTipo=0;	 
			if($this->vo->getItens() && count($this->vo->getItens())): ?>
				<thead>
					<tr>
					<th style="text-align: center""><input title="Selecionar Todas as Notas" type="checkbox" id="chk_all" name="chk_all" /></th>
						<th style="text-align: center;">Contrato</th>
						<th style="text-align: center;">NF/Série</th>
						<th style="text-align: center;">Referência</th>
						<th style="text-align: center;">Tp. Contrato</th>
						<th style="text-align: center;">Obrigação Financeira</th>
						<th style="text-align: center;">Tp. Item</th>
						<th style="text-align: center;">Valor Unit.</th>
						<th style="text-align: center;">Desconto</th>
						<th style="text-align: center;">Valor Total</th>
						<th style="text-align: center;">Ação</th>
						
					</tr>
				</thead>
				<tbody>		
					<?php 
					$venc=0;
						foreach ($this->vo->getItens() as $key => $item ): 
               				$class = $class == '' ? 'class="par"' : ''; 
							$venc =	(($venc < $item['dt_emissao']) ? $item['dt_emissao'] : $venc );
							if(!in_array($item['nfitipo'], array("L","M")))
								$itensSemTipo++;
					?>						
					<tr <?=$class?>>
						<td><input title="Selecionar Nota"  type="checkbox" name="chk_oid[]" value="<?=$key?>" rel="<?=$key?>" /></td>
						<td><?=$item['connumero']?></td>
						<td>
							<?=$item['nflno_numero']?><?=(($item['nflno_numero'] && $item['nflserie'])?"/":"")?><?=$item['nflserie']?>
						</td>
						<td style="text-align: center;"><?=$item['nfldt_referencia']?></td>
						<td><?=$item['tpcdescricao']?></td>
						<td><?=$item['obrobrigacao']?></td>
						<td><?=($item['nfitipo']== "L" ? "Locação" :  ($item['nfitipo']=="M" ? "Monit./Serviços" : "") )?></td>
						<td style="text-align: right;"> <?=($item['nfivl_item'] != '') ? number_format($item['nfivl_item'],2,",",".") : '0,00';?></td>
						<td style="text-align: right;"> <?=($item['nfidesconto'] != '') ? number_format($item['nfidesconto'],2,",",".") : '0,00'?></td>
						<td style="text-align: right;"> <?=number_format($item['nfivl_item']-$item['nfidesconto'],2,",",".")?></td>
						<td class="centro">
                            <a href="javascript:void(0);" class="edita_item" id="editar_<?=$key?>" title="Editar" rel="<?=$key?>"><img alt="Editar" src="images/edit.png" border="0" class="icone" /></a>
                           <!--   <a href="javascript:void(0);" class="exclui_item" id="excluir_<?=$key?>" title="Excluir" rel="<?=$key?>"><img alt="Excluir" src="images/icon_error.png" border="0" class="icone" /></a>-->
                        </td>
					</tr>
					<?php endforeach; ?>							
				</tbody>
			<?php else: ?>
			<thead><tr><th style="text-align: center;">Nenhum item cadastrado.</th></tr></thead>
			<?php endif ?>
			</table>
		</div>
	
<input type="hidden" name="qt_itens"          id="qt_itens"          value="<?=(($this->vo->getItens() && count($this->vo->getItens())) ? count($this->vo->getItens()) : "0")?>" />
<input type="hidden" name="qt_itens_sem_tipo" id="qt_itens_sem_tipo" value="<?=$itensSemTipo?>" />