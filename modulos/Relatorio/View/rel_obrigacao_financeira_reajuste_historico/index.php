

<?php require_once _MODULEDIR_ . "Relatorio/View/rel_obrigacao_financeira_reajuste_historico/cabecalho.php"; ?>


    
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
    <div class="mensagem info">Para gerar o relatório, preencha pelo menos um dos filtros.</div>
    
    <form id="form"  method="post" action="">
    <input type="hidden" id="acao" name="acao" value="pesquisar"/>
    <input type="hidden" id="ofrhoid" name="ofrhoid" value=""/>
    
    
    <?php require_once _MODULEDIR_ . "Relatorio/View/rel_obrigacao_financeira_reajuste_historico/formulario_pesquisa.php"; ?>
    
    <div id="resultado_pesquisa" >
    
	    <?php 
            if ( $this->view->status && count($this->view->dados) > 0) { 
             require_once 'csv.php'; 
        } 
        ?>
	    
    </div>
        
    </form>
    
<?php require_once _MODULEDIR_ . "Relatorio/View/rel_obrigacao_financeira_reajuste_historico/rodape.php"; ?>
