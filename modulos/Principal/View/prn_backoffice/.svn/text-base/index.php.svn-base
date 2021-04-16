

<?php require_once _MODULEDIR_ . "Principal/View/prn_backoffice/cabecalho.php"; ?>


    <div class="mensagem info">Os campos com * são obrigatórios.</div>
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


    <form id="form"  method="post" action="">
    <input type="hidden" id="acao" name="acao" value="pesquisar"/>
    <input type="hidden" id="cliente_id" name="cliente_id" value="<?php echo $this->view->parametros->bacoid ?>">


    <?php require_once _MODULEDIR_ . "Principal/View/prn_backoffice/formulario_pesquisa.php"; ?>

    <div id="resultado_pesquisa" >

	    <?php
        if ( $this->view->status && count($this->view->dados) > 0) {
            if($this->view->parametros->selecao_por==self::TIPO_RELATORIO_SINTETICO){
                require_once 'resultado_pesquisa_sintetico.php';
            }
            else {
                require_once 'resultado_pesquisa.php';
            }
        }
        ?>
        <?php if (isset($this->view->arquivo)) : ?>
        <div class="separador"></div>
        <div class="bloco_titulo resultado">Download</div>
        <div class="bloco_conteudo">

            <div class="conteudo centro">
                <a href="download.php?arquivo=/var/www/docs_temporario/<?php echo $this->view->arquivo; ?>" target="_blank">
                    <img src="images/icones/t3/caixa2.jpg"><br><?php echo $this->view->arquivo; ?>
                </a>
            </div>
        </div>

        <?php endif;?>

    </div>


    </form>

    <?php if (count($this->view->dados) > 0) : ?>
        <!-- Caso contenha erros, exibe os campos destacados -->
        <script type="text/javascript" >
            jQuery(document).ready(function() {
                showFormErros(<?php echo json_encode($this->view->dados); ?>);
            });
        </script>
    <?php endif; ?> 


<?php require_once _MODULEDIR_ . "Principal/View/prn_backoffice/rodape.php"; ?>
