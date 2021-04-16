

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_parametrizacao_rs_calculo_repasse/cabecalho.php"; ?>



    <!-- Mensagens-->
    <div id="mensagem_info" class="mensagem info">Os campos com * s�o obrigat�rios.</div>

    <div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemErro; ?>
    </div>

    <div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemAlerta; ?>
    </div>

    <div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemSucesso; ?>
    </div>


    <form id="form_cadastrar"  method="post" action="cad_parametrizacao_rs_calculo_repasse.php">
    <input type="hidden" id="acao" name="acao" value="cadastrar"/>
    <input type="hidden" id="prscroid" name="prscroid" value="<?php echo $this->view->parametros->prscroid ?>"/>


    <?php require_once _MODULEDIR_ . "Cadastro/View/cad_parametrizacao_rs_calculo_repasse/formulario_cadastro.php"; ?>

    </form>



<?php require_once _MODULEDIR_ . "Cadastro/View/cad_parametrizacao_rs_calculo_repasse/rodape.php"; ?>
