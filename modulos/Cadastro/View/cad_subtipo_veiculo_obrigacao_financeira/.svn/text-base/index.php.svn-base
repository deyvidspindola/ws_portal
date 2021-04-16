

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_subtipo_veiculo_obrigacao_financeira/cabecalho.php"; ?>

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

    <form id="form-pesquisa"  method="post" action="">
    <input type="hidden" id="acao" name="acao" value="pesquisar"/>

    <?php require_once _MODULEDIR_ . "Cadastro/View/cad_subtipo_veiculo_obrigacao_financeira/formulario_pesquisa.php"; ?>

    <div id="resultado_pesquisa" >

	    <?php
        if ( $this->view->TotalRegistros > 0) {
            require_once 'resultado_pesquisa.php';
        }
        ?>

    </div>

    </form>

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_subtipo_veiculo_obrigacao_financeira/rodape.php"; ?>
