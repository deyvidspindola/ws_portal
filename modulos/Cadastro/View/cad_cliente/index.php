
<div class="bloco_titulo">Pesquisa</div>

<form action="" name="pesquisa_cliente" id="pesquisa_cliente" method="post">
    <input type="hidden" name="acao" id="acao" value="pesquisar" />
	<div class="bloco_conteudo">
		<div class="conteudo">
			
			<div class="campo maior">
				<label for="nome_busca">Cliente: </label>
				<input type="text" id="nome_busca" name="nome_busca" value="<?=(isset($_POST['nome_busca']) && $_POST['nome_busca'] != "")?$_POST['nome_busca']:''?>" class="campo" />
			</div>
			
			<div class="campo medio">
				<label for="pesq_clitipo">Tipo Pessoa: </label>
				<select id="pesq_clitipo" name="pesq_clitipo">
					<option value="">Selecione</option>
					<option value="F" <?=(isset($_POST['pesq_clitipo']) && $_POST['pesq_clitipo'] == "F") ? "selected = 'selected'" : ''?>>Física</option>
					<option value="J" <?=(isset($_POST['pesq_clitipo']) && $_POST['pesq_clitipo'] == "J") ? "selected = 'selected'" : ''?>>Jurídica</option>
				</select>
			 </div>
			 
			<div class="clear" ></div>
			
			<div class="campo medio">
				<label for="cpf_busca">CPF / CNPJ: </label>
				<input type="text" id="cpf_busca" name="cpf_busca" value="<?=(isset($_POST['cpf_busca']) && $_POST['cpf_busca'] != "")?$_POST['cpf_busca']:''?>" class="campo" />
			</div>
			
			<div class="clear" ></div>
			
			<div class="campo medio">
				<label for="pesq_clicloid">Classe: </label>
				<select id="pesq_clicloid" name="pesq_clicloid">
					<option value="">Selecione</option>
					
					<?php foreach($resultadoPesquisa['comboClassesCliente'] as $classe): 
					
					$sel = (isset($_POST['pesq_clicloid']) && $_POST['pesq_clicloid'] == $classe['clicloid']) ? "selected = 'selected'" : "";?>
					<option value="<?php echo $classe['clicloid']; ?>" <?php echo $sel; ?>><?php echo utf8_decode($classe['clicldescricao']); ?></option>
	                
	                <?php endforeach; ?> 
				</select>
			</div>				
		
			<div class="clear" ></div>
		</div>
	</div>
	
		
	<div class="bloco_acoes">
		<button type="button" value="Pesquisar" id="buttonPesquisar" name="buttonPesquisar" onclick="javascript:return false;">Pesquisar</button>
		<?php if($this->fn_cadastra_cliente) {?>
		    <button type="button" value="Novo" id="buttonNovo" name="buttonNovo" onclick="javascript:return false;">Novo</button>
		<?php }?>
	</div>
</form>
<div class="separador"></div>