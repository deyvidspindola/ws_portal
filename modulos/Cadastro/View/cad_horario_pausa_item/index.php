

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_horario_pausa_item/cabecalho.php"; ?>

    <!-- Mensagens-->    
    <div class="mensagem info">Os campos com * são obrigatórios no lançamento das pausas.</div>
    
    <div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemErro; ?>
    </div>
    
    <div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemAlerta; ?>
    </div>
    
    <div id="mensagem_alerta_atendente" class="mensagem alerta invisivel">
        
    </div>
           
    <div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemSucesso; ?>
    </div>
    
    
    <form id="form"  method="post" action="cad_horario_pausa_item.php">
    <input type="hidden" id="acao" name="acao" value="cadastrar" />
    <input type="hidden" id="hrpioid" name="hrpioid" value="" />
    <input type="hidden" id="hrpimotivo_inibicao" name="hrpimotivo_inibicao" value="" />

    <?php require_once _MODULEDIR_ . "Cadastro/View/cad_horario_pausa_item/formulario_cadastro.php"; ?>
    
    <div id="resultado_pesquisa">
        
        <?php if ( $this->view->status && count($this->view->dados) > 0) {
            require_once 'resultado_pesquisa.php'; 
        }?>

    </div>
        
    </form>
    
<?php require_once _MODULEDIR_ . "Cadastro/View/cad_horario_pausa_item/rodape.php"; ?>
