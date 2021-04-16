<?php cabecalho(); ?>

<!-- CSS -->
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css" />

<!-- JAVASCRIPT -->
<script type="text/javascript" src="includes/js/auxiliares.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>

<!-- Arquivo javascript da demanda -->
<script type="text/javascript" src="modulos/web/js/man_parametrizacao_smart_agenda.js"></script>

<style type="text/css">
    .btn-help {
        position: absolute;
        margin: inherit;
        top: -6px;
        margin-left: 5px;
    }

     /* Usado no help comment */
    #helpComment{
        position:absolute;
        width:300px;
        border:1px solid #94ADC2;
        padding:1px;
        background-color:#eff3f7;
        font-family:arial;
        font-size:10px;
        visibility:hidden;
    }

    #helpCommentSeta{
        position:absolute;
    }

    #helpComment .cell-mensagem{
        padding:5px;
        color: black;
        font-family: Verdana, Arial, Helvetica, sans-serif;
        font-size:11px;
    }

    #helpComment .cell-topo-mensagem{
        padding-left:5px;
        background-color: #DFEAF3;
        color: black;
        font-family:arial;
        font-size:11px;
        font-weight:bold;
        height:18px;
    }

</style>

<div class="modulo_titulo">Parametrização Smart Agenda (Negócio)</div>
<div class="modulo_conteudo">