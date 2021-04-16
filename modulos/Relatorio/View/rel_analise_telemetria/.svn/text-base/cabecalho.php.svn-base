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
<script type="text/javascript" src="modulos/web/js/rel_analise_telemetria.js"></script>

<style type="text/css">
body {
    margin: 0px 0px; 
}
.inline label {
    display: inline !important;
    vertical-align: 2px !important;
}
.inline {
    vertical-align: middle !important;
    top: 15px;
}

.dialog label {
    display: inline-block;
}
.cabecalho_fixo    { overflow-y: auto; max-height: 300px; }
.cabecalho_fixo th { position: sticky; top: 0px; }

/* Just common table stuff. */
.cabecalho_fixo table  { border-collapse: collapse; width: 100%; }
.cabecalho_fixo th, .cabecalho_fixo td { padding: 8px 16px; }
.cabecalho_fixo th     { background: #bad0e5; }

.resultado.bloco_mensagens p {
    text-align: left;
    margin-left: 10px;
    font-weight: bold;
}

#resultado_pesquisa a,
#resultado_pesquisa a {
    color: unset !important;
    text-decoration: none !important; 
}

</style>

<div class="modulo_titulo">Analise Telemetria</div>
<div class="modulo_conteudo">
<!-- Mensagens-->
<div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
    <?php echo $this->view->mensagemErro; ?>
</div>

<div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
    <?php echo $this->view->mensagemAlerta; ?>
</div>

<div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif;?>">
    <?php echo $this->view->mensagemSucesso; ?>
</div>
