<?php cabecalho(); ?>
<?php require_once 'header.php'; ?>

<!--[if IE ]>
<style type="text/css">
	.mes_ano img {
		margin-top: 1px!important;
		margin-right: 7px!important;
	}
</style>
<![endif]-->

<form id="frm_formulario" method="POST" name="frm_formulario">
	<input id="acao" type="hidden" name="acao" />
	<?php if($registro->inpdt_referencia) : ?>
		<input id="tipo" type="hidden" name="tipo" value="alterar" />
		<input id="inpdt_referencia" type="hidden" name="inpdt_referencia" value="<?php echo $registro->inpdt_referencia; ?>" />
	<?php else : ?>
		<input id="tipo" type="hidden" name="tipo" value="cadastrar" />
	<?php endif; ?>
	<div class="modulo_titulo">INPC</div>
	<div class="modulo_conteudo">
		<div class="mensagem info">Os campos com * são obrigatórios.</div>
		<div id="div_mensagem" class="mensagem" style="display: none;"></div>
		<div class="bloco_titulo">Cadastro/Edição</div>
		<div class="bloco_conteudo">
			<div class="formulario">
				<div class="campo mes_ano">
					<label for="inpdt_referencia">Mês/Ano *</label>
					<?php if($registro->inpdt_referencia) : ?>
						<input id="inpdt_apresentacao" type="text" disabled="disabled" maxlength="7" name="inpdt_apresentacao" value="<?php echo $registro->inpdt_referencia; ?>" class="campo">
					<?php else : ?>
						<input id="inpdt_referencia" type="text" maxlength="7" name="inpdt_referencia" value="<?php echo date('m/Y'); ?>" class="campo">
					<?php endif; ?>
				</div>
                <div class="clear"></div>
				<div class="campo menor" style="position: relative">
					<label for="inpvl_referencia">INPC *</label>
					<input id="inpvl_referencia" type="text" maxlength="4" style="width:81px !important; width: 84px\9!important; " name="inpvl_referencia" value="<?php echo $registro->inpvl_referencia; ?>" class="campo">
                    <div class="labelPercent" style="position:absolute; top: 16px; left: 83px; left: 89px\9; border: 0px; background: none">%</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<div class="bloco_acoes">
			<button id="btn_salvar" type="submit">Salvar</button>
			<button id="btn_retornar" type="button">Retornar</button>
		</div>
	</div>
</form>