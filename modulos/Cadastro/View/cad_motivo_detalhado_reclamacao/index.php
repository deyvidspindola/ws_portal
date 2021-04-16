

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_motivo_detalhado_reclamacao/cabecalho.php"; ?>

<!-- Mensagens-->
<div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif; ?>">
    <?php echo $this->view->mensagemErro; ?>
</div>

<div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif; ?>">
    <?php echo $this->view->mensagemAlerta; ?>
</div>

<div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif; ?>">
    <?php echo $this->view->mensagemSucesso; ?>
</div>

<form id="form"  method="GET" action="">
    <input type="hidden" id="acao" name="acao" value="pesquisar"/>


    <?php require_once _MODULEDIR_ . "Cadastro/View/cad_motivo_detalhado_reclamacao/formulario_pesquisa.php"; ?>

    <div id="resultado_pesquisa" >

        <?php
        if (count($this->view->dados) > 0) {

            if ($this->view->parametros->motivo_geral != "") {
                require_once 'resultado_pesquisa_motivo_geral.php';
            } else if ($this->view->parametros->detalhamento_motivo != "") {
                require_once 'resultado_pesquisa_motivo_detalhado.php';
            } else {
                require_once 'resultado_pesquisa_sem_filtro.php';
            }
            
        }
        ?>

    </div>

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

