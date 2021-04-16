
<?php require_once _MODULEDIR_ . "Cadastro/View/cad_modelo_veiculo/cabecalho.php"; ?>


    <!-- Mensagens-->
    <div id="mensagem_info" class="mensagem info">Os campos com * são obrigatórios.</div>

    <div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemErro; ?>
    </div>

    <div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemAlerta; ?>
    </div>

    <div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemSucesso; ?>
    </div>


    <form id="form-cadastro"  method="post" action="">
    <input type="hidden" id="acao" name="acao" value="cadastrar"/>
    <input type="hidden" id="mlooid" name="mlooid" value="<?php echo $this->view->parametros->mlooid; ?>"/>

        <?php  require_once _MODULEDIR_ . "Cadastro/View/cad_modelo_veiculo/formulario_cadastro.php"; ?>

    </form>

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_modelo_veiculo/rodape.php"; ?>
