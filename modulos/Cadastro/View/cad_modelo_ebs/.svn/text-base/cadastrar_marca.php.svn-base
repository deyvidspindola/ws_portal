

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_modelo_ebs/cabecalho.php"; ?>
    
    <form id="form_cadastrar"  method="post" action="">
    <input type="hidden" id="acao" name="acao" value="cadastrarMarca"/>
    <input type="hidden" id="mmeoid" name="mmeoid" value="<?php echo $this->view->parametros->mmeoid; ?>"/>
    
    
    <?php require_once _MODULEDIR_ . "Cadastro/View/cad_modelo_ebs/formulario_cadastro_marca.php"; ?>

    <div id="resultado_pesquisa" >
    
        <?php 
        if ( $this->view->status && count($this->view->marcas) > 0) { 
            require_once 'resultado_pesquisa_marca.php'; 
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

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_modelo_ebs/rodape.php"; ?>
