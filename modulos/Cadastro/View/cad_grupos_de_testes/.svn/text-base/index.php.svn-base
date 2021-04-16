<?php require_once _MODULEDIR_ . "Cadastro/View/cad_grupos_de_testes/cabecalho.php"; ?>

    
    <!-- Mensagens-->
    <div id="mensagem_info" class="mensagem info">
        Os campos com * são obrigatórios.
    </div>
    
    <div id="mensagem_erro" class="mensagem erro <?php if (empty($this->view->mensagemErro)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemErro; ?>
    </div>
    
    <div id="mensagem_alerta" class="mensagem alerta <?php if (empty($this->view->mensagemAlerta)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemAlerta; ?>
    </div>
    
    <div id="mensagem_sucesso" class="mensagem sucesso <?php if (empty($this->view->mensagemSucesso)): ?>invisivel<?php endif;?>">
        <?php echo $this->view->mensagemSucesso; ?>
    </div>
    
    
    <form id="form"  method="post" action="cad_grupos_de_testes.php">
	    <input type="hidden" id="acao" name="acao" value="<?php echo $_POST['acao']; ?>" />
	    <input type="hidden" id="epcvoid" name="epcvoid" value="<?php echo $_GET['epcvoid']; ?>" />
		<input type="hidden" id="egtoid" name="egtoid" value="<?php echo $_GET['egtoid']; ?>" />
		<input type="hidden" id="arrayEptpoid" name="arrayEptpoid" value=""/>
		
	    <?php require_once _MODULEDIR_ . "Cadastro/View/cad_grupos_de_testes/formulario_pesquisa.php"; ?>
	    
	    <div id="resultado_pesquisa" >
	    
		    <?php
	        if ($this->view->status && count($this->view->dados) > 0) { 
	            require_once 'resultado_pesquisa.php'; 
	        }
	        elseif ($this->view->parametros->acao == 'editar' && (count($this->view->parametrosGrupo) > 0 || count($this->view->parametrosEdicao) > 0)) {
				require_once 'formulario_cadastro.php';
			} 
	        ?>
		    
	    </div>
        
    </form>
    
<?php require_once _MODULEDIR_ . "Cadastro/View/cad_grupos_de_testes/rodape.php"; ?>
