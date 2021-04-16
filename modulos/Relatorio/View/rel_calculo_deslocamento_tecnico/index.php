<?php require_once $this->view->caminho.'cabecalho.php'; ?>

<?php if (!empty($this->view->mensagem->info)) : ?>
    <div class="mensagem info"><?php echo $this->view->mensagem->info; ?></div>
<?php endif; ?>

<div id="div_mensagem_geral" class="mensagem invisivel"></div>

<?php if (!empty($this->view->mensagem->alerta)) : ?>
    <div class="mensagem alerta"><?php echo $this->view->mensagem->alerta; ?></div>
<?php endif; ?>

<?php if (!empty($this->view->mensagem->erro)) : ?>
    <div class="mensagem erro"><?php echo $this->view->mensagem->erro; ?></div>
<?php endif; ?>

<?php if (!empty($this->view->mensagem->sucesso)) : ?>
    <div class="mensagem sucesso"><?php echo $this->view->mensagem->sucesso; ?></div>
<?php endif; ?>
    
<?php require_once _MODULEDIR_ . "Relatorio/View/rel_calculo_deslocamento_tecnico/formulario_pesquisa.php"; ?>
    
<?php if (isset($this->view->dados->pesquisa) && $this->view->dados->pesquisa) : ?>
        <?php require_once $this->view->caminho.'resultado_pesquisa.php'; ?>
<?php endif; ?>

<?php if (count($this->view->destaque) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >
        jQuery(document).ready(function() {
            mostrarErros(<?php echo json_encode($this->view->destaque); ?>);
        });
    </script>

<?php endif; ?>

<?php require_once _MODULEDIR_."Relatorio/View/rel_calculo_deslocamento_tecnico/rodape.php"; ?>
