<div class="bloco_titulo">Operações</div>
	
<form action="" name="cad_cliente_operacoes" id="cad_cliente_operacoes" method="post">
	<input type="hidden" name="acao" id="acao" value="setClienteOperacao" />
	<input type="hidden" name="clioid" id="clioid" value="<?php echo $this->clioid; ?>" />
	<input type="hidden" name="octoid" id="octoid" value="" />
	
	
	<div class="bloco_conteudo">
		<div class="conteudo conteudo_validacao box">
			
			<div class="campo menor">
				<label for="octoprid">ID *: </label>
				<input type="text" id="octoprid" name="octoprid" value="" class="campo obrigatorio numerico" maxlength="3" />
			</div>
			
			<div class="campo medio">
				<label for="octresponsavel">Responsável na Operação *: </label>
				<input type="text" id="octresponsavel" name="octresponsavel" value="" class="campo obrigatorio alfanumCar" />
			</div>
			
			<div class="campo medio">
				<label for="octcnpj">CNPJ *: </label>
				<input type="text" id="octcnpj" name="octcnpj" value="" class="campo obrigatorio" />
			</div>
			
			<div class="clear" ></div>
			
			<div class="campo medio">
				<label for="octnome">Nome *: </label>
				<input type="text" id="octnome" name="octnome" value="" class="campo obrigatorio alfanumCar" />
			</div>
			
			<div class="campo menor">
				<label for="octtelefone">Fone *: </label>
				<input type="text" id="octtelefone" name="octtelefone" value="" class="campo telefone obrigatorio" />
			</div>
			
			<div class="campo medio">
				<label for="octinscr">Insc. Est. *: </label>
				<input type="text" id="octinscr" name="octinscr" value="" class="campo obrigatorio"  maxlength="20"/>
			</div>
			
			<div class="clear" ></div>
			
			
			<fieldset id="">
				<legend>Endereço da Operação</legend>
				<div class="campo maior" id="endereco">
				    <label for="octendoid">Seleção de Endereço:</label>
    				<select id="octendoid" name="octendoid" class="">
    				    <option value="">Selecione</option>
    				    <option value="N">[ NOVO ]</option>
    				    <?php foreach($this->clienteEnderecos as $endereco):?>
					
    						<option value="<?php echo $endereco['endoid'];?>"><?php echo $endereco['endlogradouro'].', '.$endereco['endno_numero'].' - '.$endereco['endbairro'].' - '.$endereco['endcidade'].' / '.$endereco['enduf'];?></option>
    						
    					<?php endforeach;?>
    				</select>
				</div>
				
				<div class="clear"></div>
				
    			<div id="formEnderecoOperacao">
        			<div class="chamada_correios">
        			    <input type="hidden" name="entrega_paisoid" id="entrega_paisoid" class="correios_pais obrigatorio" value="1" /> 
        				<div class="campo medio">
        					<label for="entrega_no_cep">CEP *: </label>
        					<input type="text" name="entrega_no_cep" id="entrega_no_cep" maxlength="8" value="<?php echo $this->getEndFavoritos[$chaveFavoritos]['endno_cep']; ?>" class="correios_cep campo obrigatorio" />
        					<img src="images/progress4.gif" class="loading_cep loader">
        					<button class="duvidas_cep" type="button" style="float:right" onclick="javascript:return false;">?</button>
        				</div>
        				<div class="clear"></div>
        				<div class="semresultado">        						
        				</div>
        				<div class="clear"></div>
        				<div class="campo maior descricao_duvidas">
        						<strong>Ajuda para o preenchimento do Endereço</strong>
        						<ul>
        							<li>Se inserido um CEP válido, serão preenchidos automaticamente os campos para o devido endereço.<br></li>
        							<li>Se inserido um CEP inválido estará disponível a pesquisa pelo logradouro.<br></li>
        							<li>Caso não encontrado nenhum resultado, estará disponível para inserir manualmente o endereço. <br/></li>
        							<li><a onclick="javascript:window.open('http://www.correios.com.br/servicos/cep/cep_loc_log.cfm')" href="#">Clique aqui</a> para consultar o site dos correios.</li>
        						</ul>
        						<div style="text-align: right;">
        							<a class="descricao_fechar" href=" javascript: void(null);">X Fechar</a>
        						</div>
        				</div>
        				<div class="clear"></div>
        				<div class="campo maior">
        					<label for="entrega_uf">Estado *:</label>
        					<select name="entrega_uf" id="entrega_uf" class="correios_estado obrigatorio">
        						<option value="">Selecione</option>
        						<?php 
        						$estado = $this->getEstado();
        						foreach ($estado as $chave => $valor) { ?>
        							<option value="<?php echo $valor?>"><?php echo $valor ?></option>
        						<?php }	?>
        					</select>
        				</div>
        				<div class="clear"></div>
        				<div class="campo maior">
        					<label for="entrega_cidade">Cidade *:</label>
        					<select name="entrega_cidade" id="entrega_cidade" class="correios_cidade campo  obrigatorio desabilitado">
        						<option value="">Selecione</option>
        					</select>
        				</div>
        				<div class="clear"></div>
        				<div class="campo maior">
        					<label for="entrega_bairro">Bairro *:</label>
        					<select name="entrega_bairro" id="entrega_bairro" class="correios_bairro campo obrigatorio desabilitado">
        						<option value="">Selecione</option>
        					</select>
        				</div>
        				<div class="clear"></div>
        				<div class="campo maior">
        					<label for="entrega_logradouro">Endereço *: </label>
        					<input type="text" name="entrega_logradouro" id="entrega_logradouro" value="" class="correios_endereco campo obrigatorio alfanumCar" />
        				</div>
        			</div>
    				<div class="clear"></div>
    				<div class="campo menor">
    					<label for="entrega_numero">Número *: </label>
    					<input type="text" name="entrega_numero" id="entrega_numero" value="<?php echo $this->getEndFavoritos[$chaveFavoritos]['endno_numero']; ?>" class="correios_numero campo obrigatorio numerico"  maxlength="7"/>
    				</div>
    				<div class="clear"></div>		
    				<div class="campo maior">
    					<label for="entrega_complemento">Complemento: </label>
    					<input type="text" name="entrega_complemento" id="entrega_complemento" value="<?php echo $this->getEndFavoritos[$chaveFavoritos]['endcomplemento']; ?>" class="correios_complemento campo" />
    				</div>
    				<div class="clear"></div>
				</div>

				<button type="button" value="add" id="buttonAdd" class="campo">Incluir Endereço</button>
		    </fieldset>
					
			
			<div class="clear" ></div>
		</div>
	</div>

	<div class="clear" ></div>

    <div class="bloco_conteudo">
	    <div class="listagem">
	    	<input type="hidden" value="" id="enderecosSelecionados" name="enderecosSelecionados" size="80"/>
	    	<table id="enderecos" style="display:none">
	    		<thead><tr><th>Endereço</th><th style="width: 30px;">Excluir</th></tr></thead>
	    	</table>
	    </div>
	</div>

	<div class="clear" ></div>

	<div class="bloco_acoes">
		<button type="button" value="Incluir" id="buttonIncluirOperacao" name="buttonIncluirOperacao" >Incluir</button>
		<button type="button" value="Salvar" id="buttonSalvarOperacao" name="buttonSalvarOperacao">Salvar</button>
		<button type="button" value="Excluir" id="buttonExcluirOperacao" name="buttonExcluirOperacao">Excluir</button>
		<button type="button" value="Voltar" id="buttonVoltar" name="buttonVoltar" onclick="window.location.href='<?php echo str_replace('/', '', strrchr($_SERVER['SCRIPT_NAME'], '/'));?>'">Voltar</button>
	    <? if($this->retCliente!=""){ ?>
    		<button type="button" value="Voltar" id="buttonVoltar" name="buttonVoltar" onclick="window.location.href='<?=trata_retorno($this->retCliente,$clioid)?>'">Retornar ao Contrato</button>
        <? } ?>
	</div>
</form>

<div class="separador"></div>

<form action="" method="post" id="excluirClienteContato">
    <input type="hidden" name="acao" value="excluirClienteContato">
    <input type="hidden" name="clicoid" id="clicoid" value="" />
    <input type="hidden" name="clioid" id="clioid" value="<?php echo $this->clioid; ?>" />
    
	<div class="resultado_pesquisa">
		<div class="bloco_titulo">Operações Cadastradas</div>
		<div class="bloco_conteudo">
		    <div class="listagem">
		        <table>
		            <thead>
		                <tr>
		                    <th>ID</th>
		                    <th>Nome</th>
		                    <th>Responsável</th>
		                    <th>Fone</th>
		                    <th>CNPJ</th>
		                    <th>Inscrição Estadual</th>
		                    <th>Endereço</th>
		                </tr>
		            </thead>
		            <tbody>
		            	<?php 
		            	    if(count($this->clienteOperacoes) > 0) {
		            	        $cor = "par";
		            	        foreach($this->clienteOperacoes as $operacoes) {

	            	        	//if($aux == "" || $aux != $operacoes['octoid']){
		            	        		$countRegistros++;		            	        		
		            	        		//$aux = $operacoes['octoid'];
		            	        	
	                                    //formata mascara CNPJ                                   
	                                    $operacoes['octcnpj'] = str_pad($operacoes['octcnpj'], 14, "0", STR_PAD_LEFT);
	                                    $operacoes['octcnpj'] = $this->mascaraString('##.###.###/####-##',$operacoes['octcnpj']);
	                                    
	    			            		$cor = ($cor=="par") ? "" : "par";
	    			            	?>

	    			                <tr <?php if ($cor != '') { ?> class="<?=$cor?>" <?php } ?>> 
	    			                    <td class="td_acao_link"><a class="idOperacao" id="<?php echo $operacoes['octoid']?>"><?php echo $operacoes['octoprid']?></a></td>
	    			                    <td><?php echo $operacoes['octnome']?></td>
	    			                    <td><?php echo $operacoes['octresponsavel']?></td>
	    			                    <td class="direita"><?php echo $operacoes['octtelefone']?></td>
	    			                    <td class="direita"><?php echo $operacoes['octcnpj']?></td>
	    			                    <td class="direita"><?php echo $operacoes['octinscr']?></td>
								
	    			                	<td>
	    			                		<?php $dados = $this->getEnderecoClienteOperacoesById($operacoes['octoid'])?>
	    			                		<?php foreach($dados as $endereco){ ?>
	    			                			<p>
	    			                				<?php echo utf8_decode($endereco['endlogradouro'].', 
	    			                				'.$endereco['endno_numero'].' - 
	    			                				'.$endereco['endbairro'].' - 
	    			                				'.$endereco['endcidade'].' / 
	    			                				'.$endereco['enduf'].' - 
	    			                				'.$endereco['endcep'])?>
	    			                			</p>
	    			                		<?php }?>
	    			                	</td>
    			                	</tr>
    			                	<?php //}?>
			                <?php } ?>
			                
			            <?php } else { ?>
			            
			            	<tr>
			            		<td colspan="8">Nenhuma operação cadastrada</td>
			            	</tr>
			            	
		                <?php } ?>
		            </tbody>
		        </table>
		    </div>
		</div>
		<div class="bloco_acoes"><p><?php echo $this->getMensagemTotalRegistros($countRegistros);?></p></div>
	</div>
</form>
