<?php require_once _MODULEDIR_ . "Relatorio/View/rel_instalacao_equipamento/cabecalho.php"; ?>

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
    <input type="hidden" id="acao" name="acao" value="gerarRelatorio"/>
    
    <?php require_once _MODULEDIR_ . "Relatorio/View/rel_instalacao_equipamento/formulario_pesquisa.php"; ?>

    <?php if ( $this->view->status && isset($nomeArquivo)) : ?>

    <div class="resultado">

        <div class="separador"></div>

        <div class="bloco_titulo resultado">Download</div>
        <div class="bloco_conteudo">

            <div class="conteudo centro">
                <a href="download.php?arquivo=<?php echo $nomeArquivo ?>" target="_blank">
                    <img src="images/icones/t3/caixa2.jpg"><br><?php echo basename($nomeArquivo) ?>
                </a>
            </div>
        </div>
    </div>

    <?php endif; ?>
        <div class="carregando" id="loader_1" style="display:none;"></div>
    </form>
    
<?php require_once _MODULEDIR_ . "Relatorio/View/rel_instalacao_equipamento/rodape.php"; ?>
