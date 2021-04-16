<?php require_once $this->view->caminho.'cabecalho.php'; ?>

<div id="mensagem_info" class="mensagem info">Os campos com * são obrigatórios.</div>

<div id="div_mensagem_geral" class="mensagem invisivel"></div>

<?php if (!empty($this->view->mensagemErro)) : ?>
    <div class="mensagem erro"><?php echo $this->view->mensagemErro; ?></div>
<?php endif; ?>

<?php if (!empty($this->view->mensagemAlerta)) : ?>
    <div class="mensagem alerta"><?php echo $this->view->mensagemAlerta; ?></div>
<?php endif; ?>

<?php if (count($this->view->destaque) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >jQuery(document).ready(function() {
            showFormErros(<?php echo json_encode($this->view->destaque); ?>);
        });
    </script>
<?php endif; ?>
    <form id="form_cadastrar" name="form_cadastrar" method="post" action="ges_indicador.php">
        <input type="hidden" name="acao" id="acao" value="" />
        <input type="hidden" name="gmioid" id="gmioid" value="<?php echo isset($this->view->paramatrosCadastro->gmioid) ? $this->view->paramatrosCadastro->gmioid : ''; ?>" />

        <?php require_once $this->view->caminho."/formulario_cadastro.php"; ?>

    </form>
<?php require_once $this->view->caminho."/rodape.php"; ?>