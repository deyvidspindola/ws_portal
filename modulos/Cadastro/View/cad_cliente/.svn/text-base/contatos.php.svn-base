
<div class="bloco_titulo">Contatos</div>
	
<form action="" name="cad_cliente_contato" id="cad_cliente_contato" method="post">
	<input type="hidden" name="acao" id="acao" value="cadastroClienteContato" />
	<input type="hidden" name="clioid" id="clioid" value="<?php echo $this->clioid; ?>" />
	
	
	<div class="bloco_conteudo">
		<div class="conteudo">
			
			<div class="campo maior">
				<label for="clicnome">Nome *: </label>
				<input type="text" id="clicnome" name="clicnome" value="" class="campo obrigatorio" />
			</div>
			
			<div class="clear" ></div>
			
			<div class="campo menor">
				<label for="clicfone">Telefone *: </label>
				<input type="text" id="clicfone" name="clicfone" value="" class="campo telefone obrigatorio" />
			</div>
			
			<div class="clear" ></div>
			
			<div class="campo medio">
				<label for="clicsetor">Setor: </label>
				<input type="text" id="clicsetor" name="clicsetor" value="" class="campo" />
			</div>
			
			<div class="clear" ></div>
		</div>
	</div>

	<div class="bloco_acoes">
		<button type="submit" value="Confirmar" id="buttonConfirmarContatos" name="buttonConfirmarContatos" class="validacao">Confirmar</button>
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
		<div class="bloco_titulo">Contatos Cadastrados</div>
		<div class="bloco_conteudo">
		    <div class="listagem">
		        <table>
		            <thead>
		                <tr>
		                    <th>Nome</th>
		                    <th>Telefone</th>
		                    <th>Setor</th>
		                    <th>Excluir</th>
		                </tr>
		            </thead>
		            <tbody>
		            	<?php if(count($this->clienteContatos) > 0) :?>
			            	<?php
			            	$cor = 'par';
			            	foreach($this->clienteContatos as $contato):
			            		$cor = ($cor=="par") ? "" : "par";
			            				            	
    			            	$arrReplace = array('{','}');
    			            	$telefone = str_replace($arrReplace, '',$contato['clicfone_array']);
    			            	if($telefone != ''){
			            	        $telefone = explode(',',$telefone);
			            	        if(strlen($telefone[0])>10)
			            	            $mascara = '(##) #####-####';
			            	        else
			            	            $mascara = '(##) ####-####';
			            	        $telefone = $this->mascaraString($mascara,$telefone[0]);
			            	    }
			            	?>
			                    <tr <?php if ($cor != '') { ?> class="<?=$cor?>" <?php } ?>> 
			                    <td><?php echo $contato['clicnome']?></td>
			                    <td class="medio direita"><?php echo $telefone;?></td>
			                    <td class="medio"><?php echo $contato['clicsetor'];?></td>
			                    <td class="menor centro td_acao_excluir">
			                        <a href="javascript:return false;" class="excluirContato"  clicoid="<?php echo $contato['clicoid']?>" ><img src="images/icon_error.png" /></a>
                                </td>

			                </tr>
			                <?php endforeach; ?>
			                
			            <?php else : ?>
			            
			            	<tr>
			            		<td colspan="3">Nenhum contato cadastrado</td>
			            	</tr>
			            	
		                <?php endif; ?>
		            </tbody>
		        </table>
		    </div>
		</div>
		<div class="bloco_acoes"><p><?php echo $this->getMensagemTotalRegistros(count($this->clienteContatos));?></p></div>
	</div>
</form>
