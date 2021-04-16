<?php 
    $script = array(
        'lib/jquery.PrintArea.js',
        'ges_grafico_meta.js'
    );
    $this->layout->renderizarCabecalho('Gr&aacute;fico da Meta', $script);

?>


<!--
<script src="modulos/web/js/lib/jquery.PrintArea.js" type="text/javascript"></script>
<script src="modulos/web/js/ges_grafico_meta.js" type="text/javascript"></script>

<div class="layout-no-margin modulo_titulo">Gr&aacute;fico da Meta</div>
		<div class="layout-no-margin modulo_conteudo">-->

            <div id="div_mensagem_geral" class="mensagem invisivel"></div>

            <?php if (!empty($this->view->mensagem->erro)) : ?>
                <div class="mensagem erro"><?php echo $this->view->mensagem->erro; ?></div>
            <?php endif; ?>

            <?php if (!empty($this->view->mensagem->sucesso)) : ?>
                <div class="mensagem sucesso"><?php echo $this->view->mensagem->sucesso; ?></div>
            <?php endif; ?>

            <?php if (!empty($this->view->mensagem->alerta)) : ?>
                <div class="mensagem alerta"><?php echo $this->view->mensagem->alerta; ?></div>
            <?php endif; ?>

          <?php
                if(!empty($this->view->dados)) {
                    include $this->view->caminho . 'grafico.php';
                }
            ?>
        </div>
        <div class="separador"></div>