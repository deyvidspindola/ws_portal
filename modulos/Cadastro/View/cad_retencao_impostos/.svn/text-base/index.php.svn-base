

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_retencao_impostos/cabecalho.php"; ?>



    <!-- Mensagens-->

    <div class="mensagem info">Os campos com * são obrigatórios.</div>

    <div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemErro; ?>
    </div>

    <div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemAlerta; ?>
    </div>

    <div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemSucesso; ?>
    </div>


    <form id="form"  method="post" action="cad_retencao_impostos.php">
        <input type="hidden" id="acao" name="acao" value="cadastrar"/>
        <input type="hidden" id="id" name="prsrioid" value="" />


    <?php require_once _MODULEDIR_ . "Cadastro/View/cad_retencao_impostos/formulario_cadastro.php"; ?>

    <div id="resultado_pesquisa" >

	    <?php
        if ( $this->view->status && count($this->view->dados) > 0) {
            require_once 'resultado_pesquisa.php';
        }
        ?>

    </div>

    </form>

        <?php if (count($this->view->dados) > 0) : ?>
        <!--  Caso contenha erros, exibe os campos destacados  -->
        <script type="text/javascript" >jQuery(document).ready(function() {
            showFormErros(<?php echo json_encode($this->view->dados); ?>);
        });
        </script>

        <?php endif; ?>

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_retencao_impostos/rodape.php"; ?>
