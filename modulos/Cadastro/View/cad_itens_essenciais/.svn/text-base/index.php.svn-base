

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_itens_essenciais/cabecalho.php"; ?>


    
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

    <div id="mensagem_excluir" class="invisivel">
        Deseja realmente excluir este registro?
    </div>
    
    
    <form id="form"  method="post" action="">
    <input type="hidden" id="acao" name="acao" value="pesquisar"/>
    <input type="hidden" id="iesoid" name="iesoid" value=""/>
    
    
    <?php require_once _MODULEDIR_ . "Cadastro/View/cad_itens_essenciais/formulario_pesquisa.php"; ?>

     <?php if (count($this->view->validacao) > 0) : ?>
        <!--  Caso contenha erros, exibe os campos destacados  -->
        <script type="text/javascript" >
            jQuery(document).ready(function() {
                showFormErros(<?php echo json_encode($this->view->validacao); ?>); 
            });
        </script>

    <?php endif; ?>
            
    </form>

    <div id="resultado_pesquisa" >
    
	    <?php 
        if ( $this->view->status && count($this->view->dados) > 0) { 
            require_once 'resultado_pesquisa.php'; 
        } 
        ?>
	    
    </div>
        
    </form>
    
<?php require_once _MODULEDIR_ . "Cadastro/View/cad_itens_essenciais/rodape.php"; ?>
