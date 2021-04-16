<!-- Lib -->
<link href="modulos/web/js/lib/fancytree/skin-custom/ui.fancytree.css" rel="stylesheet" type="text/css">
<script src="modulos/web/js/lib/fancytree/jquery.fancytree-all.min.js" type="text/javascript"></script>

<!-- Arquivo javascript da demanda -->
<script type="text/javascript" src="modulos/web/js/ges_layout.js"></script>

<div class="modulo_titulo" id="titulo_principal">Sistema de Gestão</div>
<div style="padding-top:0px" id="conteudo_princial" class="modulo_conteudo">

<div id="layout-sidebar">

	<div id="info">
		<?php $anoCorrente = date('Y') == '2013' ? '2014' : date('Y'); ?>
		Data: <?php echo date('d/m/Y H:i:s') ?> <br/>
		Usuário: <?php echo $_SESSION['usuario']['nome'] ?><br/>
		Ano de referência: <select id="ano_referencia" class="<?php echo $this->superUsuario ? 'superusuario' : '' ?>">
								<?php foreach ($this->listarAnos AS $anos) : ?>
									<option <?php echo $anoCorrente == $anos ? 'selected="selected"' : '' ?> value="<?php echo $anos ?>"><?php echo $anos ?></option>
								<?php endforeach; ?>
						   </select>
	</div>


	<div id="arvore-conteudo">

		<?php require_once _MODULEDIR_ . 'Gestao/View/ges_layout/arvore_ajax.php'; ?>

	</div>

	<div id="navegacao-arvore">
		<div style="border: 0px; margin: 0px !important" class="bloco_acoes">
			<div id="bt_voltar_arvore" data-count="0" class="<?php echo $this->superUsuario ? 'voltar-multiplo' : '' ?>" style="cursor: pointer; display:none"></div>
		</div>
	</div>

</div>

<div id="dialogo_acao"></div>
