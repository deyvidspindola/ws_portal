<?php cabecalho(); ?>
<?php require_once 'header.php'; ?>

<!--[if IE ]>
<style type="text/css">
	.label-periodo {
		margin-left: -15px!important;
	}
</style>
<![endif]-->

<form id="frm_listagem" method="POST" name="frm_listagem">
	<input id="acao" type="hidden" name="acao" />
	<div class="modulo_titulo">INPC</div>
	<div class="modulo_conteudo">
		<div class="mensagem info">Os campos com * são obrigatórios.</div>
		<?php if(isset($mensagem)) : ?>
			<div id="div_mensagem_sucesso" class="mensagem <?php echo $mensagem->classe; ?>"><?php echo $mensagem->texto; ?></div>
		<?php endif; ?>
		<div id="div_mensagem" class="mensagem" style="display: none;"></div>
		<div class="bloco_titulo">Dados para Pesquisa</div>
		<div class="bloco_conteudo">
			<div class="formulario">
				<div class="campo mes_ano periodo">
					<label for="inpdt_inicial">Período *</label>
                    <input id="inpdt_inicial" type="text" maxlength="7" name="inpdt_inicial" value="<?php echo isset($pesquisa->inpdt_inicial) && !empty($pesquisa->inpdt_inicial) ? $pesquisa->inpdt_inicial : date('m/Y') ; ?>" class="campo" />
				</div>
				<p class="campo label-periodo">a</p>
				<div class="campo mes_ano periodo">
					<label for="inpdt_final">&nbsp;</label>
					<input id="inpdt_final" type="text" maxlength="7" name="inpdt_final" value="<?php echo isset($pesquisa->inpdt_final) && !empty($pesquisa->inpdt_final) ? $pesquisa->inpdt_final : date('m/Y') ;?>" class="campo" />
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<div class="bloco_acoes">
			<button id="btn_pesquisar" type="submit">Pesquisar</button>
			<button id="btn_novo" type="button">Novo</button>
		</div>
		<div class="separador" style="display: none;"></div>
		<div class="carregando" style="display: none;"></div>
		<div class="resultado bloco_titulo" style="display: none;">Resultado da Pesquisa</div>
		<div class="resultado bloco_conteudo" style="display: none;">
			<div class="listagem">
				<table>
					<thead>
						<tr>
							<th class="centro">Mês/Ano</th>
							<th class="centro">INPC</th>
							<th class="centro">Ação</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
		<div class="resultado bloco_mensagens" style="display: none;">
			<p></p>
		</div>
		<div id="div_mensagem_listagem" class="mensagem alerta" style="display: none; margin-bottom: 0px;">Nenhum registro encontrado.</div>
	</div>
</form>