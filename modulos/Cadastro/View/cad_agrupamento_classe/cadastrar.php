

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_agrupamento_classe/cabecalho.php"; ?>
    
    <form id="form_cadastrar"  method="post" action="">
    <input type="hidden" id="acao" name="acao" value="cadastrar"/>
    <input type="hidden" id="agcoid" name="agcoid" value="<?php echo $this->view->parametros->agcoid; ?>"/>
    
    
    <?php require_once _MODULEDIR_ . "Cadastro/View/cad_agrupamento_classe/formulario_cadastro.php"; ?>
    
    </form>
    
    <?php if (count($this->view->dados) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >jQuery(document).ready(function() {
        showFormErros(<?php echo json_encode($this->view->dados); ?>); 
    });
    </script>
    
    <?php endif; ?>

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_agrupamento_classe/rodape.php"; ?>
