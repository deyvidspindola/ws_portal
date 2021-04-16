<?php
/**
 * Cabeçalho e Estilos
 */
cabecalho();

include("calendar/calendar.js");
require("lib/funcoes.js");


?>


<head>
<!-- CSS -->
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css" />
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/multiple-select.css" />

<!-- JAVASCRIPT -->
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.maskMoney.js"></script>
<script type="text/javascript" src="js/jquery.maskedinput.js"></script>
<script type="text/javascript" src="js/jquery.multiple.select.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>
<script type="text/javascript" src="includes/js/validacoes.js"></script>

<script type="text/javascript" src="modulos/web/js/rel_boletagem_massiva.js?rand=<?=rand(1, 9999);?>"></script>
</head>

<style>

#valor_divida_ini{
	width: 100px;
	float: left;
}


#valor_divida_fim{
	width: 100px;
	float: left;
}

#a{
 float: left;
 font-size: 11px;
 padding-top: 6px;
}


</style>


