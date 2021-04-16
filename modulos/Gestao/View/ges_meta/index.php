

<?php require_once _MODULEDIR_ . "Gestao/View/ges_meta/cabecalho.php"; ?>


    <div id="mensagem_info" class="mensagem info">Os campos com * são obrigatórios.</div>
    
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

    <?php if ( ( isset($this->recarregarArvore) && $this->recarregarArvore == true ) || (isset($this->view->recarregarArvore) && $this->view->recarregarArvore == true) ) :?>
    <script type="text/javascript">
        parent.recarregaArvore();
    </script>
    <?php endif; ?>
    
    
    <form id="form"  method="post" action="ges_meta.php">
    <input type="hidden" id="acao" name="acao" value="pesquisar"/>
    <input type="hidden" id="gmeoid" name="gmeoid" value=""/>
    
    
    <?php require_once _MODULEDIR_ . "Gestao/View/ges_meta/formulario_pesquisa.php"; ?>

    <?php if ( $this->view->status && isset($this->view->parametros->nomeArquivo)) : ?>

        <div class="separador"></div>

        <div class="bloco_titulo resultado">Download</div>
        <div class="bloco_conteudo">

            <div class="conteudo centro">
                <a href="download.php?arquivo=<?php echo $this->view->parametros->nomeArquivo ?>" target="_blank">
                    <img src="images/icones/t3/caixa2.jpg"><br><?php echo "Export Metas de " . $this->view->parametros->anoReferencia ?>
                </a>
            </div>
        </div>

    <?php else : ?>
    
    <div id="resultado_pesquisa" >
    
	    <?php 
        if ( $this->view->status && count($this->view->dados) > 0) { 
            require_once 'resultado_pesquisa.php'; 
        } 
        ?>
	    
    </div>

    <?php endif; ?>
        
    </form>

    <?php if (count($this->view->destaque) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >
        jQuery(document).ready(function() {
            showFormErros(<?php echo json_encode($this->view->destaque); ?>);
        });
    </script>
<?php endif; ?>
    
<?php require_once _MODULEDIR_ . "Gestao/View/ges_meta/rodape.php"; ?>
