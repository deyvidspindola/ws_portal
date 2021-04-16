

<?php require_once _MODULEDIR_ . "Principal/View/prn_agendamento_unitario/cabecalho.php"; ?>


    
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
    
    <div  class="mensagem info ">
       Obrigatório informar ao menos um filtro para a pesquisa.
    </div>
    
    <?php require_once _MODULEDIR_ . "Principal/View/prn_agendamento_unitario/formulario_pesquisa.php"; ?>


    <?php if (count($this->view->validacao) > 0) : ?>
        <!--  Caso contenha erros, exibe os campos destacados  -->
        <script type="text/javascript" >jQuery(document).ready(function() {
            showFormErros(<?php echo json_encode($this->view->validacao); ?>); 
        });
        </script>
    <?php endif; ?>

    
    <div id="resultado_pesquisa" >
    
	    <?php 
        if ( $this->view->status && count($this->view->dados) > 0) { 
            require_once 'resultado_pesquisa.php';
        } 
        ?>
	    
    </div>
        
    
    
<?php require_once _MODULEDIR_ . "Principal/View/prn_agendamento_unitario/rodape.php"; ?>
