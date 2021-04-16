<?php cabecalho(); ?>

<!-- CSS -->
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css" />

<!-- Arquivo CSS da demanda -->
<link type="text/css" rel="stylesheet" href="modulos/web/css/prn_agendamento_unitario.css"></script>

<!-- Arquivo CSS do componente de CEP -->
<link type="text/css" rel="stylesheet" href="lib/Components/Enderecos/assets/componente.css" />


<style>
	#componente_endereco div.formulario { padding: 0;} 

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

    /* Modal Dialog*/
    .mensagem-modal {
        margin: 10px 0 !important;
    }

    #cmp_contato_email{
        text-transform: lowercase;
    }
}
</style>

<!-- JAVASCRIPT -->
<script language="Javascript" type="text/javascript" src="includes/js/auxiliares.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.numeric.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>
<script type="text/javascript" src="lib/funcoes_masc_novo.js"></script>

<!-- Arquivo JAVASCRIPT do componente de CEP -->
<script type="text/javascript" src="lib/Components/Enderecos/assets/componente.js"></script>

<?php if (isset($this->view->parametros->acao) && in_array($this->view->parametros->acao, array('detalhe', 'notificarOS'))): ?>
<!-- Arquivo javascript da demanda -->
<script type="text/javascript" src="modulos/web/js/prn_agendamento_unitario_detalhe.js"></script>
<?php else: ?>
<!-- Arquivo javascript da demanda -->
<script type="text/javascript" src="modulos/web/js/prn_agendamento_unitario.js"></script>
<?php endif; ?>
<div class="modulo_titulo">Agendamento Unitário</div>
<div class="modulo_conteudo">