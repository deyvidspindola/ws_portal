<div class="bloco_titulo">Editar</div>
<form action="" name="cadastro_interv_posicionamento_equipamento" id="cadastro_interv_posicionamento_equipamento" method="post">
    <input type="hidden" name="acao" id="acao" value="atualizar" />
    <input type="hidden" name="iposeqpoid" id="iposeqpoid" value="<?php echo $this->iposeqpoid ?>" />
	<div class="bloco_conteudo">
		<div class="conteudo">			
			<div class="campo maior">
				<label for="nome_busca">Descrição </label>
				<input type="text" id="iposeqpdescricao" name="iposeqpdescricao" value="<?php echo $this->iposeqpdescricao?>" class="campo obrigatorio" />
			</div>		
			<div class="clear" ></div>
		</div>
	</div>		
	<div class="bloco_acoes">
		<button type="button" value="salvar" id="buttonSalvar" name="buttonSalvar" class="validacao">Salvar</button>
		<button type="button" value="excluir" id="buttonExcluir" name="buttonExcluir">Excluir</button>
		<button type="button" value="cancelar" id="buttonCancelar" name="buttonCancelar">Cancelar</button>
	</div>
</form>
<div class="separador"></div>