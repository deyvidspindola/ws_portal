<? require 'header.php'; ?>
<script>
	$(document).ready(function(){		
		$('#buttonSalvar').click(salvar);
		$('#buttonExcluir').click(excluir);
		$('#buttonVoltar').click(voltar);
	});
</script>

<?= $resultado['msgErro']?>
<div id="carregando" class="carregando " style="display: none;"></div>

<div class="bloco_titulo">Editar</div>

<form name="form" id="form" method="post" action="">
	
	<input type="hidden" name="acao" id="acao" value="salvar">
	<input type="hidden" name="ciceqpoid" id="ciceqpoid" value="<?=$resultado['ciceqpoid']?>">

	<div class="bloco_conteudo">
		<div class="conteudo">
			<div class="campo maior">
				<label for="nome_busca">Descrição </label>
				<input type="text" id="ciceqpdescricao" name="ciceqpdescricao" value="<?=$resultado['ciceqpdescricao']?>" class="campo obrigatorio" />
			</div>

			<div class="clear" ></div>
		</div>
	</div>
	
	<div class="bloco_acoes">
		<button type="button" value="Salvar" id="buttonSalvar" name="buttonSalvar" onclick="javascript:return false;" class="validacao">Salvar</button>
		<button type="button" value="Excluir" id="buttonExcluir" name="buttonExcluir">Excluir</button>
		<button type="button" value="cancelar" id="buttonCancelar" name="buttonCancelar">Cancelar</button>
	</div>
</form>

<div class="separador"></div>
<? require 'footer.php' ?>