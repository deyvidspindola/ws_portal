
<div class="bloco_titulo">Filtro de Pesquisa</div>

<form action="" name="cadastro_codigo_venda_ct" id="cadastro_codigo_venda_ct" method="post">
    <input type="hidden" name="acao" id="acao" value="pesquisar" />
	
	<div class="bloco_conteudo">
		<div class="conteudo">
			<div class="campo">
				<div class="campo data periodo">
					<div class="inicial">
						<label for="data_inicial">Período de Cadastro </label>
						<input type="text" id="data_inicial" name="data_inicial" value="<?php echo $this->data_inicial; ?>" class="campo" />
					</div>
					<div class="campo label-periodo">a</div>
					<div class="final">
						<label for="data_final">&nbsp;</label>
						<input type="text" id="data_final" name="data_final" value="<?php echo $this->data_final; ?>" class="campo" />
					</div>
				</div>
			
				<div class="clear" ></div>
				<div class="campo menor">
					<label for="nota_fiscal">Nº Nota Fiscal </label>
					<input type="text" id="nota_fiscal" name="nota_fiscal" value="<?php echo $this->nota_fiscal; ?>" class="campo numerico" maxlength="12"/>
				</div>
				
				<div class="campo menor">
					<label for="serie">Série Nota Fiscal </label>
					<input type="text" id="serie" name="serie" value="<?php echo $this->serie; ?>" class="campo" maxlength="17"/>
				</div>
				
				<div class="clear" ></div>
				
				<div class="campo pesquisaMaior">
					<label for="fornecedor_busca">Fornecedor </label>
					<input type="text" id="fornecedor_busca" name="fornecedor_busca" value="<?=$this->fornecedor_busca?>" class="campo" style="width:287px" onblur="jQuery(this).val(jQuery.trim(jQuery(this).val()));"/>
				    <input type="hidden" name="foroid" id="foroid" value="<?php echo $this->foroid?>" />
					<button type="button" id="buttonPesquisarFornecedor" name="buttonPesquisarFornecedor">Pesquisar</button>
					<div class="carregando" id="carregando_fornecedor" style="display: none;"></div>
				</div>
				
				<div class="clear" ></div>
			
				<div class="campo maior" style="display: none">
				 	<label for="fornecedoresEncontrados">Fornecedores Encontrados </label>
				 	<select id="fornecedoresEncontrados" multiple="multiple"></select>
				</div>
			
				<div class="clear" ></div>
					
				<div class="campo maior">
					<label for="repoid">Representante </label>
					<select id="repoid" name="repoid">
						<option value="">Selecione</option>
						<?php foreach($this->representantes as $representante){?>
							<option value="<?php echo $representante['repoid']; ?>" <? if($this->repoid == $representante['repoid']){?> selected="selected" <?php }?>><?php echo $representante['repnome']; ?></option>
						<?php }?>
					</select>
				</div>
				
				<div class="clear" ></div>
					
				<div class="campo maior">
					<label for="relroid">Representante Estoque </label>
					<select id="relroid" name="relroid">
						<option value="">Selecione</option>
						<?php 
						    if(is_array($this->representanteEstoque)) {
    						    foreach($this->representanteEstoque as $estoque){?>
    							<option value="<?php echo $estoque['relroid']; ?>" <? if($this->relroid == $estoque['relroid']){?> selected="selected" <?php }?>><?php echo utf8_decode($estoque['repnome']); ?></option>
    						<?php 
                                }
						    }?>
					</select>
				</div>
				
				
				<div class="clear" ></div>
				
				<div class="campo maior" style="display: none">
				 	<label for="clientesEncontrados">Clientes Encontrados </label>
				 	<select id="clientesEncontrados" multiple="multiple"></select>
				</div>
			</div>
			
			<div class="campo">
				<div class="campo pesquisaMaior">
					<label for="produto_busca">Equipamento CT Descartável </label>
					<input type="text" id="produto_busca" name="produto_busca" value="<?=$this->produto_busca?>" class="campo" style="width:287px" onblur="jQuery(this).val(jQuery.trim(jQuery(this).val()));"/>
				    <input type="hidden" name="prdoid" id="prdoid" value="<?php echo $this->prdoid?>" />
					<button type="button" id="buttonPesquisarProduto" name="buttonPesquisarProduto">Pesquisar</button>
					<div class="carregando" id="carregando_produto" style="display: none;"></div>
				</div>
				
				
				<div class="clear" ></div>
			
				<div class="campo maior" style="display: none">
				 	<label for="produtoEncontrado">Produtos Encontrados </label>
				 	<select id="produtoEncontrado" multiple="multiple"></select>
				</div>
				
				<div class="clear" ></div>
				
				<div class="campo maior">
					<label for="eqsoid">Status </label>
					<select id="eqsoid" name="eqsoid">
						<option value="">Selecione</option>
						<?php foreach($this->status as $status){?>
							<option value="<?php echo $status['eqsoid']?>" <? if($this->eqsoid == $status['eqsoid']){?> selected="selected" <?php }?>><?php echo $status['eqsdescricao']?></option>
						<?php }?>
					</select>
				</div>
				
				<div class="clear" ></div>
				
				<fieldset class="maior">
					<legend>Serial</legend>
						<label for="numero_serie" style="float:left; margin: 3px 5px 0 0">Nº Serial </label>
						<input type="text" id="numero_serie" name="numero_serie" value="<?php echo $this->numero_serie?>" class="campo numerico <?php if(isset($this->sem_serial)){?> desabilitado <?php }?>" <?php if(isset($this->sem_serial)){?> readonly="readonly" <?php }?> style="width: 150px"  maxlength="17"/>
						
						<input type="checkbox" id="sem_serial" name="sem_serial" value="sim" <?php if(isset($this->sem_serial)){?> checked="checked" <?php }?>  style="margin-left: 25px" />
						<label for="sem_serial">Sem serial</label>
				</fieldset>
				
				<div class="clear" ></div>
				
				<div class="campo menor">
					<label for="cod_venda">Cód. Venda </label>
					<input type="text" id="cod_venda" name="cod_venda" value="<?=$this->cod_venda?>" class="campo" maxlength="12"/>
				</div>
	
				<div class="clear" ></div>
				
				<div class="campo pesquisaMaior">
					<label for="cliente_busca">Cliente </label>
					<input type="text" id="cliente_busca" name="cliente_busca" value="<?=$this->cliente_busca?>" class="campo"  style="width:287px" onblur="jQuery(this).val(jQuery.trim(jQuery(this).val()));"/>
				    <input type="hidden" name="clioid" id="clioid" value="<?php echo $this->clioid?>" />
					<button type="button" id="buttonPesquisarCliente" name="buttonPesquisarCliente">Pesquisar</button>
					<div class="carregando" id="carregando_cliente" style="display: none;"></div>
				</div>
				
				<div class="campo maior" style="display: none">
				 	<label for="operacao">Operação </label>
				 	<select id="operacao" name="operacao"></select>
				</div>
				
				<div class="clear" ></div>
			
				<div class="campo maior" style="display: none">
				 	<label for="clienteEncontrado">Clientes Encontrados </label>
				 	<select id="clienteEncontrado" multiple="multiple"></select>
				</div>
			</div>

			
			
			<div class="clear" ></div>
			
		</div>
	</div>		
	<div class="bloco_acoes">
		<button type="button" value="pesquisar" id="buttonPesquisar" name="buttonPesquisar" class="validacao">Pesquisar</button>
	</div>
</form>
<div class="separador"></div>
<?php
	if($this->acao == 'PESQUISAR'):
		$numResultado = count($this->resultadoPesquisa);
	    
	    if($numResultado > 1){
            $textoResult = $numResultado ." registros encontrados.";
        }elseif($numResultado == 1){
            $textoResult = "1 registro encontrado.";
        }elseif($numResultado == 0){
            $textoResult = "Nenhum Resultado Encontrado";
        }
?>

<div class="resultado_pesquisa">
	<div class="bloco_titulo">Resultado da Pesquisa</div>
		<div class="bloco_conteudo">
		    <div class="listagem">
		        <table>
	                <?php if($numResultado > 0) {?>
		            <thead>
		                <tr>	                	
		                    <th>Equipamento CT Descartável</th>
		                    <th>Nº Série</th>
		                    <th>Status</th>
		                    <th>Repres. Estoque</th>
		                    <th>Cliente</th>
		                    <th>Cód. Venda</th>
		                    <th>Termo</th>
		                </tr>
		            </thead>
		            <tbody>
		            	
		            <?php 
	                	$cor = 'par';
		            	foreach($this->resultadoPesquisa as $linha){
							
							$cor = ($cor=="par") ? "" : "par";?>
							<tr <?php if ($cor != '') { ?> class="<?=$cor?>" <?php } ?>>
								<td><?php echo ($linha['prdproduto'])?$linha['prdproduto']:'-'; ?></td>
								<td align="right">
									<?php 
										if($linha['equno_serie']){
									?>
									<a href="<?php echo ($linha['equno_serie']?'equipamento.php?acao=consultar&equoid='.$linha['equoid']:'#') ?>" target="_blanck">
										<?php echo $linha['equno_serie']; ?>
									</a>
									<?php 
										}else{
											echo '-';
										} 
									?>
								</td>
								<td><?php echo ($linha['status'])?$linha['status']:'-'; ?> </td>
								<td><?php echo ($linha['representante_etoque'])?$linha['representante_etoque']:'-'; ?></td>
								<td><?php echo ($linha['cliente'])?$linha['cliente']:'-'; ?></td>
								<td><?php echo ($linha['cod_venda'])?$linha['cod_venda']:'-'; ?></td>
								<td align="right"><?php echo ($linha['contrato'])?$linha['contrato']:'-'; ?></td>
							</tr>
							<?
							
						}
		            ?>			
		            </tbody>
                    <?php } ?>
		            <tfoot>
		                <tr>
		                    <td colspan="7">
		                        <?php echo $textoResult ?>
		                    </td>
		                </tr>
		            </tfoot>
		        </table>
		    </div>
		</div>
</div>
<?php endif; ?>