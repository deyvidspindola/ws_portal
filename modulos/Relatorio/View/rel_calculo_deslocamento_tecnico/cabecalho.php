<?php cabecalho(); ?>

<!-- CSS -->
<head>
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css" />
<link type="text/css" rel="stylesheet" href="modulos/web/css/rel_calculo_deslocamento_tecnico.css" />

<!-- JAVASCRIPT -->
<script language="Javascript" type="text/javascript" src="includes/js/auxiliares.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.numeric.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>
<script type="text/javascript" src="lib/funcoes_masc_novo.js"></script>

<!-- Arquivo javascript da demanda -->
<script type="text/javascript" src="modulos/web/js/rel_calculo_deslocamento_tecnico.js"></script>

<!--  Configura titile dos titulos  -->
<script type="text/javascript" >
    //Medida paliativa para quebrar linha nos atributos titles
    jQuery(document).ready(function() {
        jQuery('th').tooltip({
            content: function() {
                return jQuery(this).attr('title');
            }
        });
        jQuery('img').tooltip({
            content: function() {
                return jQuery(this).attr('title');
            }
        });
    });
</script>

<div class="modulo_titulo">Rota Realizada pelo Técnico</div>
<div class="modulo_conteudo">
</head>
