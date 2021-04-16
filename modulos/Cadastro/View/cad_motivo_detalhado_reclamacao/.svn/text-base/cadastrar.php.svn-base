

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_motivo_detalhado_reclamacao/cabecalho.php"; ?>

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


    <form id="form_cadastrar"  method="post" action="">
    <input type="hidden" id="acao" name="acao" value=""/>


    <?php require_once _MODULEDIR_ . "Cadastro/View/cad_motivo_detalhado_reclamacao/formulario_cadastro.php"; ?>

    <?php
        if ($this->view->parametros->existeSemVinculo) {

            require_once _MODULEDIR_ . "Cadastro/View/cad_motivo_detalhado_reclamacao/resultado_registro_sem_vinculo.php";
        }
    ?>

  
    </form>

    <?php if (count($this->view->dados) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >
        jQuery(document).ready(function() {
            showFormErros(<?php echo json_encode($this->view->dados); ?>);
        });
    </script>
    <?php endif; ?>

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_motivo_detalhado_reclamacao/rodape.php"; ?>
