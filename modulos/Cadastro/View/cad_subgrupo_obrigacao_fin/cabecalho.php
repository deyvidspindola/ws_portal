<?php cabecalho(); ?>
<!-- CSS -->
<link type="text/css" rel="stylesheet" href="lib/css/datatables/bootstrap.min.css"/>
<link type="text/css" rel="stylesheet" href="lib/css/datatables/dataTables.bootstrap.css"/>
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css" />
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
<style>
	body {
		margin-top: 0px;
	}
</style>
<!-- JAVASCRIPT -->
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>
<script type="text/javascript" src="modulos/web/js/cad_subgrupo_obrigacao_fin.js"></script>
<script type="text/javascript" src="lib/js/datatables/jquery.dataTables.js"></script>
<script type="text/javascript" src="lib/js/datatables/dataTables.bootstrap.js"></script>
<div class="modulo_titulo">Cadastro de Subgrupos</div>
	<div class="modulo_conteudo">
	<!-- msgErro -->
	<?php if(!empty($this->view->msgErro)): ?>
		<div class="mensagem erro"><?php echo $this->view->msgErro; ?></div>
	<?php endif; ?>
	<!-- msgSucesso -->
	<?php if(!empty($this->view->msgSucesso)): ?>
		<div class="mensagem sucesso"><?php echo $this->view->msgSucesso; ?></div>
	<?php endif; ?>