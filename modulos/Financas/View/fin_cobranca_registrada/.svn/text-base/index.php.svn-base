<?php require_once _MODULEDIR_ . "Financas/View/fin_cobranca_registrada/cabecalho.php"; ?>   
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

    <div id="mensagem_sucesso_resultado" class="mensagem sucesso <?php if (empty($this->view->parametros->respostaSucesso)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->parametros->respostaSucesso; ?>
    </div>

    <div id="mensagem_erro_resultado" class="mensagem erro <?php if (empty($this->view->this->view->parametros->respostaErro)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->parametros->respostaErro; ?>
    </div>

    <?php if(!count($this->view->dados) && !isset($this->view->parametros->acao)) { ?>
    <div id="mensagem_info" class="mensagem info <?php if (empty($this->view->mensagemInfo)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemInfo; ?>
    </div>
<?php } ?>
                  
<?php require_once _MODULEDIR_ . "Financas/View/fin_cobranca_registrada/formulario_cadastro.php"; ?>
    
<?php require_once _MODULEDIR_ . "Financas/View/fin_cobranca_registrada/rodape.php"; ?>
