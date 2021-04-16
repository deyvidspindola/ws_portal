<?php require_once _MODULEDIR_ . "Relatorio/View/rel_equip_instalado_sem_subscription/cabecalho.php"; ?>

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

    <?php require_once _MODULEDIR_ . "Relatorio/View/rel_equip_instalado_sem_subscription/formulario_pesquisa.php"; ?>

    <div id="resultado_pesquisa" >

	    <?php
        if ( $this->view->status && count($this->view->dados) > 0) {
            require_once 'resultado_pesquisa.php';
        }
        ?>

    </div>
    </form>

    <div id="loader_xls" class="carregando invisivel"></div>
    <div id="baixarXls" class="invisivel">
        <div class="separador"></div>
        <div class="bloco_titulo">Download</div>
        <div class="bloco_conteudo">
            <div class="conteudo centro">
                <a target="_blank" href="">
                    <img src="images/icones/t3/caixa2.jpg">
                <br />
                <span></span>
                 </a>
            </div>
        </div>
    </div>
    
<?php require_once _MODULEDIR_ . "Relatorio/View/rel_equip_instalado_sem_subscription/rodape.php"; ?>
