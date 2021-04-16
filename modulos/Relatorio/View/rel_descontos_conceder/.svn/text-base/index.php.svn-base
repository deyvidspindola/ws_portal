
<?php require_once _MODULEDIR_ . "Relatorio/View/rel_descontos_conceder/cabecalho.php"; ?>

<div class="bloco_titulo">Relatório de Descontos a Conceder</div>
<div class="bloco_conteudo">

	<div class="formulario">
		<div id="info_principal" class="mensagem info">Campos com * são obrigatórios.</div>

        <!--div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
            <?php echo $this->view->mensagemErro; ?>
        </div>

        <div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
            <?php echo $this->view->mensagemAlerta; ?>
        </div>

        <div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif;?>">
            <?php echo $this->view->mensagemSucesso; ?>
        </div-->

        <?php if (isset($_SESSION['flash_message']) && count($_SESSION['flash_message'])): ?>
        <div id="mensagem_flash_<?php echo $_SESSION['flash_message']['tipo'] ?>" class="mensagem <?php echo $_SESSION['flash_message']['tipo'] ?>">
            <?php echo $_SESSION['flash_message']['mensagem'] ?>
            <?php unset($_SESSION['flash_message']); ?>
        </div>
        <?php endif; ?>

        <form id="form"  method="post" action="">
            <input type="hidden" id="acao" name="acao" value="pesquisar"/>
            <input type="hidden" name="cfoclioid" id="cfoclioid" />
            <input type="hidden" name="nm_usuario" id="nm_usuario" value="<?php echo $this->view->parametros->nm_usuario ?>" />

            <?php require_once _MODULEDIR_ . "Relatorio/View/rel_descontos_conceder/formulario_pesquisa.php"; ?>

        </form>

        <div id="resultado_pesquisa" >

            <?php
            if ( $this->view->status && count($this->view->dados) > 0) {
                require_once 'resultado_pesquisa.php';
            }
            ?>

        </div>
        <?php if ( isset($this->view->arquivo) ): ?>
        <div class="separador"></div>
        <div class="bloco_titulo resultado">Download</div>

        <div class="bloco_conteudo">

                <div class="conteudo centro">
                    <a href="download.php?arquivo=/var/www/docs_temporario/<?php echo $this->view->arquivo; ?>" target="_blank">
                        <img src="images/icones/t3/caixa2.jpg"><br><?php echo $this->view->arquivo; ?>
                    </a>
                </div>


        </div>
        <?php endif; ?>

        <?php if (!$this->view->status && count($this->view->dados) > 0) : ?>
        <!--  Caso contenha erros, exibe os campos destacados  -->
        <script type="text/javascript" >jQuery(document).ready(function() {
                showFormErros(<?php echo json_encode($this->view->dados); ?>);
            });
        </script>

    <?php endif; ?>

    <div title="Enviar o relatório por e-mail" id="dialog-motivos" class="invisivel">
        <form id="form_email"  method="post" action="">
            <div class="formulario">
                <div class="campo maior">
                    <label for="para">Para</label>
                    <input class="campo" type="text" id="para" name="email_para" />
                </div>

                <div class="clear"></div>

                <div class="campo maior">
                    <label for="cc">Cc</label>
                    <textarea rows="3" class="desabilitado" readonly="readonly" id="cc" name="email_cc"><?php echo implode(';', $this->view->parametros->usuariosAprovadores); ?></textarea>
                </div>

                <div class="clear"></div>

                <div class="campo maior">
                    <label for="assunto">Assunto</label>
                    <input class="campo desabilitado" readonly="readonly" type="text" id="assunto" name="email_assunto" value="" />
                </div>

                <div class="clear"></div>

                <div class="campo maior">
                    <label for="corpo">Corpo</label>
                    <textarea rows="7" id="corpo" name="email_corpo"></textarea>
                </div>

                <div class="clear"></div>

            </div>
        </form>
    </div>

<?php require_once _MODULEDIR_ . "Relatorio/View/rel_descontos_conceder/rodape.php"; ?>
