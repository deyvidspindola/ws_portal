<?php cabecalho(); ?>

<!-- CSS -->
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css" />

<!-- JAVASCRIPT -->
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>

<!-- Arquivo javascript da demanda -->
<script type="text/javascript" src="modulos/web/js/cad_cidade_mapeada_bairro.js?rand=<?=rand(1, 9999);?>""></script>


<div class="modulo_titulo">OFSC - Cidades Mapeadas por Bairros</div>
<div class="modulo_conteudo">

<div id="mensagem_excluir" class="invisivel">Deseja realmente excluir este registro?</div>