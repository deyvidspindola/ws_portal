

<?php require_once _MODULEDIR_ . "Financas/View/fin_faturamento_unificado_vivo/cabecalho.php"; ?>



<!-- Mensagens-->
<div id="mensagem_info" class="mensagem info">Campos com * são obrigatórios.</div>

<div id="mensagem_faturamento_iniciado" class="mensagem info <?php if (empty($this->view->mensagemFaturamento)): ?>invisivel<?php endif; ?>">
    <?php echo $this->view->mensagemFaturamento; ?>
</div>

<div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif; ?>">
    <?php echo $this->view->mensagemErro; ?>
</div>

<div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif; ?>">
    <?php echo $this->view->mensagemAlerta; ?>
</div>

<div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif; ?>">
    <?php echo $this->view->mensagemSucesso; ?>
</div>


<form id="form"  method="post" action="">
    <input type="hidden" id="acao" name="acao" value=""/>

    <?php require_once _MODULEDIR_ . "Financas/View/fin_faturamento_unificado_vivo/formulario_pesquisa.php"; ?>

    <div id="resultado_pesquisa" >

        <?php
        if ($this->view->mostrarConsulta && count($this->view->dados) > 0) {
            require_once 'resultado_pesquisa.php';
        }
        ?>

        <?php if ($this->view->mostrarArquivo && isset($this->view->nomeArquivo) && $this->view->nomeArquivo != '') : ?>

            <div class="resultado">

                <div class="separador"></div>

                <div class="bloco_titulo resultado">Download</div>
                <div class="bloco_conteudo">

                    <div class="conteudo centro">
                        <a href="download.php?arquivo=<?php echo $this->view->nomeArquivo ?>" target="_blank">
                            <img src="images/icones/t3/caixa2.jpg"><br><?php echo basename($this->view->nomeArquivo) ?>
                        </a>
                    </div>
                </div>
            </div>

        <?php endif; ?>

        <?php
        if ($this->view->mostrarRelatorioPreFaturamento && count($this->view->dadosPreFaturamento) > 0) {
            require_once 'relatorio_pre_faturamento.php';
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

<?php require_once _MODULEDIR_ . "Financas/View/fin_faturamento_unificado_vivo/rodape.php"; ?>
