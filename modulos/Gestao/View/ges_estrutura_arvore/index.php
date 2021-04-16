

<?php require_once _MODULEDIR_ . "Gestao/View/ges_estrutura_arvore/cabecalho.php"; ?>


    
    <!-- Mensagens-->
     <div id="mensagem_info" class="mensagem info">Os campos com * são obrigatórios.</div>
     
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

    <div id="div_mensagem_geral" class="mensagem invisivel"></div>
    
    
    <form id="form"  method="post" action="">
    <input type="hidden" id="acao" name="acao" value="pesquisar"/>
    <input type="hidden" id="gmaoid" name="gmaoid" value=""/>
    
    
    <?php require_once _MODULEDIR_ . "Gestao/View/ges_estrutura_arvore/formulario_pesquisa.php"; ?>
    
    <div id="resultado_pesquisa" >
    
	    <?php 
        if ( $this->view->status && count($this->view->dados) > 0) { 
            require_once 'resultado_pesquisa.php'; 
        } 
        ?>
	    
    </div>
        
    </form>
      <?php if (count($this->view->dados) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >jQuery(document).ready(function() {
        showFormErros(<?php echo json_encode($this->view->dados); ?>); 
    });
    </script>
    
    <?php endif; ?>
    
<?php require_once _MODULEDIR_ . "Gestao/View/ges_estrutura_arvore/rodape.php"; ?>
