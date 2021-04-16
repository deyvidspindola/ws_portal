<?php //cabecalho(); ?>


<!-- CSS -->
<head>
	<!-- CSS -->
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/cupertino/jquery-ui-1.10.0.custom.min.css" />

<!-- jQuery -->
<script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>

<!-- Arquivos básicos de javascript -->
<script type="text/javascript" src="lib/js/jquery-ui-1.10.0.custom.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jquery.maskMoney.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>

	<!-- Arquivo javascript da demanda -->
    <?php if (count($this->moduloScripts) > 0) : ?>
        <?php foreach ($this->moduloScripts as $script): ?>
            <script type="text/javascript" src="modulos/web/js/<?php echo $script;?>"></script>
        <?php endforeach;?>
    <?php endif;?>
   
    <link href="modulos/web/css/ges_layout.css" rel="stylesheet" type="text/css">
</head>

<body class="iframe_gestao">
	
	<?php //$this->layout->renderizarSidebar(); ?>
	<?php //$this->layout->renderizarWrapper(); ?>
	<?php //$this->layout->renderizarMenu(); ?>
	<div class="layout-no-margin modulo_titulo"><?php echo $this->moduloTitulo; ?></div>
		<div class="layout-no-margin modulo_conteudo">