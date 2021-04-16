<? require 'header.php'; ?>

<?= $resultado['msgErro']?>
<div id="carregando" class="carregando " style="display: none;"></div>

<div class="bloco_titulo">Pesquisa</div>

<form name="form" id="form" method="post" action="">
	
	<input type="hidden" name="acao" id="acao" value="pesquisar">
	<input type="hidden" name="ciceqpoid" id="ciceqpoid" value="<?=$resultado['ciceqpoid']?>">

	<div class="bloco_conteudo">
		<div class="conteudo">
			<div class="campo maior">
				<label for="nome_busca">Descrição </label>
				<input type="text" id="ciceqpdescricao" name="ciceqpdescricao" value="<?=$resultado['ciceqpdescricao']?>" class="campo"  onblur="jQuery(this).val(jQuery.trim(jQuery(this).val()));"/>
			</div>

			<div class="clear" ></div>
		</div>
	</div>
	
	<div class="bloco_acoes">
		<button type="button" value="Pesquisar" id="buttonPesquisar" name="buttonPesquisar" onclick="javascript:return true;" class="validacao">Pesquisar</button>
		<button type="button" value="Novo" id="buttonNovo" name="buttonNovo">Novo</button>
	</div>
</form>
<div class="separador"></div>

<? require 'pesquisar.php' ?>

<? require 'footer.php' ?>