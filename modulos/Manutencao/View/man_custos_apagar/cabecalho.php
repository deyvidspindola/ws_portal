<?php cabecalho(); ?>

<!-- CSS -->
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.css" />
<link type="text/css" rel="stylesheet" href="modulos/web/css/man_custos_apagar.css" />

<!-- JAVASCRIPT -->
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery-ui/jquery-ui.custom.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/jquery/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>
<script type="text/javascript" src="modulos/web/js/jquery.maskMoney.js" ></script>
<script type="text/javascript" src="includes/js/auxiliares.js"></script>

<!-- Arquivo javascript da demanda -->
<script type="text/javascript" src="modulos/web/js/man_custos_apagar.js?dmY=<?=date('dmYHis')?>"></script>

<div class="modulo_titulo">Contas a pagar</div>
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
    <div id="mensagem_excluir" class="invisivel">
        Você realmente deseja excluir esse lançamento? <br /><br />OBS.: Se o item selecionado for um agrupamento, todos os títulos que possuem esse mesmo agrupamento serão excluídos
    </div>