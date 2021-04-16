

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_chamadas_seguradora/cabecalho.php"; ?>


    
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
    <?php if(!count($this->view->dados) && !isset($this->view->parametros->acao)): ?>
    <div id="mensagem_info" class="mensagem info <?php if (empty($this->view->mensagemInfo)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemInfo; ?>
    </div>
<?php endif; ?>
    
    <!--<form id="form"  method="post" action="">-->
        <!--<input type="hidden" id="acao" name="acao" value="cadastrar"/>
        <input type="hidden" id="pscoid" name="pscoid" value=""/>-->
        
        
        <?php require_once _MODULEDIR_ . "Cadastro/View/cad_chamadas_seguradora/formulario_cadastro.php"; ?>
        
        <!--<div id="resultado_pesquisa">
        
    	    <?php 
                //if ( $this->view->status && count($this->view->dados) > 0) { 
                //    require_once 'resultado_pesquisa.php'; 
                //} 
            ?>
    	    
        </div>-->
        
    <!--</form>-->
    
<?php require_once _MODULEDIR_ . "Cadastro/View/cad_chamadas_seguradora/rodape.php"; ?>
