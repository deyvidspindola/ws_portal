<div class="bloco_titulo">Resultado da Pesquisa</div>
<div class="bloco_conteudo">
	<div class="conteudo">
		<fieldset>
			<legend>Legenda</legend>
				<ul class="legenda">
				<li><img alt="Item" src="images/apr_bom.gif"> Item a estornar</li>
				<li><img alt="Item" src="images/apr_ruim.gif"> Item a baixar</li>
				</ul>
		</fieldset>
	</div>
	
	<div class="listagem1">
		<table>
		
			<thead>
				<tr>
                    <th class="selecao"><input id="selecao_todos" type="checkbox" title="Selecionar Todos" checked="checked" tabindex="13"/></th>
					<th class="centro">Dt. Entrada</th>
					<th class="centro">O.S.</th>
					<th class="centro">Contrato</th>
					<th class="maior centro">Cliente</th>
					<th class="maior centro">Repr. Técnico</th>
					<th class="centro">Equipamento</th>
					<th class="maior centro">Tipo - Motivo</th>
					<th class="medio centro">Cliente Seg.</th>
					<th class="medio centro">Tipo Contrato</th>
				</tr>
			</thead>
			
			<tbody>
                <?php if (count($this->view->dados) > 0): ?>
                	
                    <?php $tabindex = 13; ?>
                    <?php $arrayDados = $this->view->dados;?>
                    <?php foreach ($arrayDados as $chave => $valor) : ?>
                    	<?php $tabindex ++; ?>
						<tr class="par1"> 
							<td><input type="checkbox" class="selecao" name="check[]" title="Selecionar" value="<?php echo $chave ?>" checked="checked" tabindex="<?php echo $tabindex ?>" /></td>
							<td class="centro"><?php echo $arrayDados[$chave]['dt_ordem'] ?></td>
							<td class="direita"><a href="prn_ordem_servico.php?ESTADO=cadastrando&acao=editar&ordoid=<?php echo $chave ?>" target="_blank"><?php echo $chave ?></a></td>
							<td class="direita"><?php echo $arrayDados[$chave]['connumero'] ?></td>
							<td><?php echo $arrayDados[$chave]['clinome'] ?></td>
							<td><?php echo $arrayDados[$chave]['repnome'] ?></td>
							<td><?php echo $arrayDados[$chave]['projeto'] ?></td>
							<td><?php echo $arrayDados[$chave]['tipo_motivo'] ?></td>
							<td><?php echo $arrayDados[$chave]['seguradora'] ?></td>
							<td><?php echo $arrayDados[$chave]['tpcdescricao'] ?></td>
						</tr>
						<tr>
							<td colspan = "11">
								<div class="listagem">
									<table>
									
										<thead>
											<tr>
											    <th class="centro">Código</th>
												<th class="maior centro">Produto</th>
												<th class="menor centro">Qtd. Baixada</th>
												<th class="menor centro">Qtd. Necessária</th>
												<th class="menor centro">Qtd. Corrigir</th>
												<th class="menor centro">Mín.</th>
												<th class="menor centro">Máx.</th>
												<th class="menor centro">Qtd. Estoque</th>
												<th class="menor centro">Qtd. Trânsito</th>
											</tr>
				                		</thead>
				                		
			                			<tbody> 
		                    				<?php foreach ($arrayDados[$chave]['prdoid'] as $prdoid) : ?>
			                    				<?php $classeLinha = ($classeLinha == "") ? "par" : "";  ?>
			                        			<?php $arrayDados[$chave]['qtd_corrigir'][$prdoid] = ($arrayDados[$chave]['qtd_baixada'][$prdoid] - $arrayDados[$chave]['qtd_necessaria'][$prdoid]); ?>
			                        			<?php $color = ($arrayDados[$chave]['qtd_corrigir'][$prdoid] < 0) ? 'style="color:RED;"' : 'style="color:GREEN;"'; ?>
			                        			
												<tr class="<?php echo $classeLinha; ?>">
												    <td class="direita"><?php echo $prdoid ?></td>
													<td><?php echo $arrayDados[$chave]['prdproduto'][$prdoid] ?></td>
													<td class="direita"><?php echo $arrayDados[$chave]['qtd_baixada'][$prdoid] ?></td>
													<td class="direita"><?php echo $arrayDados[$chave]['qtd_necessaria'][$prdoid] ?></td>
													<td class="direita" <?php echo $color ?>><?php echo $arrayDados[$chave]['qtd_corrigir'][$prdoid] ?></td>
													<td class="direita"><?php echo $arrayDados[$chave]['prdlimitador_minimo'][$prdoid] ?></td>
													<td class="direita"><?php echo $arrayDados[$chave]['prdlimitador_maximo'][$prdoid] ?></td>
													<td class="direita"><?php echo $arrayDados[$chave]['espqtde'][$prdoid] ?></td>
													<td class="direita"><?php echo $arrayDados[$chave]['espqtde_trans'][$prdoid] ?></td>
												</tr>
											<?php endforeach;?>
			                			</tbody>
							            
							        </table>
						        </div>
						    </td>
						</tr> 
					<?php endforeach;?>
				<?php endif;?>
			</tbody>
				        
			<tfoot>
				<tr>
					<td colspan="11">
						<?php $totalRegistros = count($this->view->dados); ?>
                    	<?php echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.'; ?>
				    </td>
				</tr>
				<tr>
				    <td colspan="11">
						<button type="button" id="bt_corrigir" tabindex="<?php echo $tabindex ?>" >Corrigir Baixas</button>
				    </td>
				</tr>
			</tfoot>
		</table>
	</div>    
</div>
<div class="separador"></div>
