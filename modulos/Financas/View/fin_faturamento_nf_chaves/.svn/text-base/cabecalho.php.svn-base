<?php cabecalho(); ?>

<!-- CSS -->
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css" />
<link type="text/css" rel="stylesheet" href="css/css_final.css" />


<!-- JAVASCRIPT -->
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.maskedinput.min.js"></script>
<script language="Javascript" type="text/javascript" src="includes/js/auxiliares.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>

<script>
    function revalidar(campo,mask,validacao){
        campo.style.background = "#FFFFFF";
        if(mask != ''){
            var aux = campo.value;
            campo.value = '';
            if(mask == '@'){
                campo.value = aux.replace(/\D/g,"");
            }else{
                for(i=0;i<aux.length;i++){
                    campo.value += aux.substr(i,1);
                    formatar(campo,mask);
                }
            }
        }
        campo.style.background = "";
        return true;
    }
</script>

<!-- Arquivo javascript da demanda -->
<!-- <script type="text/javascript" src="modulos/web/js/fin_contas_a_pagar.js"></script> -->

<style type="text/css">
    .codigo{
        text-transform: uppercase;
    }
    .conteudo fieldset img {
        vertical-align: bottom;
    }

    .tabela_titulo {
        background: #e6eaee none repeat scroll 0 0;
        border-bottom: medium none !important;
        border-top: 1px solid #94adc2;
        font-size: 12px;
        font-weight: bold;
        height: 25px;
        line-height: 25px;
        padding: 0 10px;
        vertical-align: middle;
    }

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
        /*border-bottom: 1px solid #E0E0E0; */
        color: black;
        font-family:arial;
        font-size:11px;
        font-weight:bold;
        height:18px;
    }

</style>


<div class="modulo_titulo">Faturamento de entrada de nota fiscal (chaves) </div>
<div class="modulo_conteudo">
    