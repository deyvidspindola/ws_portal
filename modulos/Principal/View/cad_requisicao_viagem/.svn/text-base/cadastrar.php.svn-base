

<?php require_once _MODULEDIR_ . "Principal/View/cad_requisicao_viagem/cabecalho.php"; ?>

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


    <form id="form_cadastrar"  method="post" action="">
    <input type="hidden" id="acao" name="acao" value="<?php echo $this->view->parametros->acao ?>"/>
    <input type="hidden" id="idRequisicao" name="idRequisicao" value="<?php echo isset($this->view->parametros->idRequisicao) ? $this->view->parametros->idRequisicao : '' ?>"/>


    <?php require_once _MODULEDIR_ . "Principal/View/cad_requisicao_viagem/formulario_cadastro.php"; ?>


    <?php
        if($this->view->parametros->permissaoEdicao == 'Aprovador' && $this->view->parametros->statusRequisicao == 'P') :
            require_once _MODULEDIR_ . "Principal/View/cad_requisicao_viagem/formulario_aprovacao_requisicao.php";
        endif;
    ?>

    <?php
        if ($this->view->parametros->statusRequisicao == 'S' || $this->view->parametros->statusRequisicao == 'F' || $this->view->parametros->statusRequisicao == 'R' || ($this->view->parametros->statusRequisicao == 'A' && $this->view->parametros->tipoRequisicao == 'A' ) || ( $this->view->parametros->tipoRequisicao == 'L' && ( empty($this->view->mensagemAlerta) || isset($this->view->parametros->statusRequisicao) ) ) ) {

        	require_once _MODULEDIR_ . "Principal/View/cad_requisicao_viagem/formulario_prestacao_contas.php";
		}
    ?>

    <?php
        if($this->view->parametros->permissaoEdicao == 'Aprovador' && $this->view->parametros->statusRequisicao == 'F') :
            require_once _MODULEDIR_ . "Principal/View/cad_requisicao_viagem/formulario_conferencia_prestacao_contas.php";
        endif;
    ?>
    
    <?php 
	    if ($this->view->parametros->permissaoEdicao == 'Aprovador' && $this->view->parametros->statusRequisicao == 'R') {
	    	require_once _MODULEDIR_ . "Principal/View/cad_requisicao_viagem/formulario_aprovacao_reembolso.php";
	    }
	?>
    	
    
    </form>

    <?php if (count($this->view->dados) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >
        jQuery(document).ready(function() {
            showFormErros(<?php echo json_encode($this->view->dados); ?>);
        });
    </script>
    <?php endif; ?>

<?php require_once _MODULEDIR_ . "Principal/View/cad_requisicao_viagem/rodape.php"; ?>
