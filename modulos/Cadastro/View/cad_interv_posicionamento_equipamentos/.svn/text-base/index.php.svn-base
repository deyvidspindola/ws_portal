
<div class="bloco_titulo">Pesquisa</div>

<form action="" name="pesquisa_interv_posicionamento_equipamento" id="pesquisa_interv_posicionamento_equipamento" method="post">
	<input type="hidden" name="iposeqpoid" id="iposeqpoid" value="" />
    <input type="hidden" name="acao" id="acao" value="pesquisar" />
	<div class="bloco_conteudo">
		<div class="conteudo">			
			<div class="campo maior">
				<label for="nome_busca">Descrição </label>
				<input type="text" id="iposeqpdescricao" name="iposeqpdescricao" value="<?php echo $this->iposeqpdescricao?>" class="campo" />
			</div>		
			<div class="clear" ></div>
		</div>
	</div>
		
	<div class="bloco_acoes">
		<button type="button" value="pesquisar" id="buttonPesquisar" name="buttonPesquisar" >Pesquisar</button>
		<button type="button" value="novo" id="buttonNovo" name="buttonNovo">Novo</button>
	</div>
</form>
<div class="separador"></div>
<?php 
	if($this->acao == 'pesquisar'):
		$numResultado = count($this->resultadoPesquisa);
?>
<div class="resultado_pesquisa">
	<div class="bloco_titulo">Resultado da Pesquisa</div>
	<?php if($numResultado > 0) {?>
	<div class="bloco_conteudo">
	    <div class="listagem">
	        <table>
	            <thead>
	                <tr>	                	
	                    <th>Descrição</th>
	                </tr>
	            </thead>
	            <tbody>
	            	
	            <?php 
                	$cor = 'par';
	            	foreach($this->resultadoPesquisa as $linha){
						
						$cor = ($cor=="par") ? "" : "par";?>
						<tr <?php if ($cor != '') { ?> class="<?=$cor?>" <?php } ?>>
							<td>
								<a class="clickEditar" href="javascript:void(0);" id="<?php echo $linha['iposeqpoid']; ?>">
									<?php echo $linha['iposeqpdescricao']; ?>
								</a>
							</td>
						</tr>
						<?
						
					}
	            ?>			
	            </tbody>
	        </table>
	    </div>
	</div>
    <?php } ?>
    <div class="bloco_acoes"><p><strong><?php echo $this->getMensagemTotalRegistros($numResultado);?></strong></p></div>
</div>
<?php endif; ?>