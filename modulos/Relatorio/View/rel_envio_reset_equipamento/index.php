
<?php require_once _MODULEDIR_ . "Relatorio/View/rel_envio_reset_equipamento/cabecalho.php"; ?>

<div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
    <?php echo $this->view->mensagemErro; ?>
</div>

<div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
    <?php echo $this->view->mensagemAlerta; ?>
</div>

<div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif;?>">
    <?php echo $this->view->mensagemSucesso; ?>
</div>

<div class="mensagem alerta invisivel" id="msg_alerta_autocomplete"></div>

<div id="tela_ativa" class="invisivel"><?php echo  $this->view->tela; ?></div>
<div id="sub_tela_ativa" class="invisivel"><?php echo  $this->view->sub_tela; ?></div>
<div id="msg_confirmar_voltar" class="invisivel">Deseja voltar para a tela de pesquisa? Você perderá os dados não salvos.</div>

<?php
//Abas Prinicpais
require_once _MODULEDIR_ . "Relatorio/View/rel_envio_reset_equipamento/formulario_pesquisa.php";

//Bloco Resultado da pesquisa
if($this->view->statusPesquisa) {
    require_once _MODULEDIR_ . "Relatorio/View/rel_envio_reset_equipamento/resultado_pesquisa.php";
}

require_once _MODULEDIR_ . "Relatorio/View/rel_envio_reset_equipamento/rodape.php";

?>
