<? require 'header.php'; ?>
<script>
	$(document).ready(function(){		
		$('#buttonSalvar').click(salvar);
		$('#buttonVoltar').click(voltar);
	});
</script>

<?= $resultado['msgErro']?>
<div id="carregando" class="carregando " style="display: none;"></div>

<div class="bloco_titulo">Cadastrar</div>

<form name="form" id="form" method="post" action="">
	
	<input type="hidden" name="acao" id="acao" value="cadastrar">
	<input type="hidden" name="emeeqpoid" id="emeeqpoid" value="<?=$resultado['emeeqpoid']?>">

	<div class="bloco_conteudo">
		<div class="conteudo">
			<div class="campo maior">
				<label for="nome_busca">Descrição </label>
				<input type="text" id="emeeqpdescricao" name="emeeqpdescricao" value="<?=$resultado['emeeqpdescricao']?>" class="campo obrigatorio" />
			</div>

			<div class="clear" ></div>
		</div>
	</div>
	
	<div class="bloco_acoes">
		<button type="button" value="Salvar" id="buttonCadastrar" name="buttonCadastrar" onclick="javascript:return false;" class="validacao">Salvar</button>
		<button type="button" value="cancelar" id="buttonCancelar" name="buttonCancelar">Cancelar</button>
	</div>
</form>

<br />

<div class="separador"></div>
<? require 'footer.php' ?>