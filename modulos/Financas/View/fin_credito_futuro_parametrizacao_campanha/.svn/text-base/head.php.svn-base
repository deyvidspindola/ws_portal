<?php cabecalho(); ?>
<title>Crédito Futuro - Parametrização</title>
<meta charset="ISO-8859-1">


<link type="text/css" rel="stylesheet" href="lib/css/style.css" />

<script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>
<script type="text/javascript" src="lib/js/jquery-ui-1.10.0.custom.min.js"></script>
<script type="text/javascript" src="lib/js/bootstrap.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jquery.maskMoney.js"></script>
<script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>
<script type="text/javascript" src="modulos/web/js/fin_credito_futuro_parametrizao_aba_campanha.js"></script>


<!-- jQuery UI -->
<link type="text/css" rel="stylesheet" href="lib/css/cupertino/jquery-ui-1.10.0.custom.min.css" />        



<style type="text/css">

    .ui-autocomplete-loading {
        background: white url('modulos/web/images/ajax-loader-circle.gif') right center no-repeat;
    }

    .ui-widget {
        font: 12px Arial !important;
    }

    .ui-menu .ui-menu-item a {
        cursor: pointer;
    }

    .ui-helper-hidden-accessible {       
        display: none !important;
    }

    ul.ui-autocomplete {
        width: 253px !important;
    }
    
    #nome.ui-autocomplete-input {
        float: none !important;
    }

</style>

<div class="modulo_titulo">Crédito Futuro - Parametrização</div>

<div class="modulo_conteudo">
    <ul class="bloco_opcoes">
        <li class="<?php echo (strpos( $this->view, 'motivo_credito') > -1)  ? "ativo" : "";  ?>">
            <a href="fin_credito_futuro_parametrizacao.php?acao=pesquisarMotivoCredito" title="Motivo do Crédito">Motivo do Crédito</a>
        </li>
        <?php if ($_SESSION['funcao']['autoriza_credito_futuro_email_aprovacao']) : ?>
        <li class="<?php echo (strpos( $this->view, 'email_aprovacao/email_aprovacao') > -1)  ? "ativo" : "";  ?>">
                <a href="fin_credito_futuro_parametrizacao.php?acao=emailAprovacao" title="E-mail p/ Aprovação">E-mail p/ Aprovação</a>
        </li>
        <?php else: ?>
            <li>
                <a href="javascript:void(0);" title="E-mail p/ Aprovação" style="color: #333333; text-decoration: none;">E-mail p/ Aprovação</a>						
            </li>
        <?php endif; ?>
        <li class="<?php echo (strpos( $this->view, 'tipo_campanha_promocional') > -1)  ? "ativo" : ""; ?>">
            <a href="fin_credito_futuro_parametrizacao.php?acao=pesquisarTipoCampanhaPromocional" title="Tipo de Campanha Promocional">Tipo de Campanha Promocional</a>
        </li>
        <li class="ativo"><a href="?acao=index" title="Campanha Promocional">Campanha Promocional</a></li>
    </ul>
    
    <div class="modulo_titulo">Campanha Promocional</div>
    <div class="modulo_conteudo">
    

