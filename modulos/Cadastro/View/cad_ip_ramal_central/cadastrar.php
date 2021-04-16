

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_ip_ramal_central/cabecalho.php"; ?>
    
    <form id="form_cadastrar"  method="post" action="">
    <input type="hidden" id="acao" name="acao" value="cadastrar"/>
    <input type="hidden" id="oid" name="oid" value="<?php echo $this->view->parametros->oid; ?>">
    
    <?php require_once _MODULEDIR_ . "Cadastro/View/cad_ip_ramal_central/formulario_cadastro.php"; ?>
    
    </form>
    
    <?php if (count($this->view->dados) > 0) : ?>
    <!--  Caso contenha erros, exibe os campos destacados  -->
    <script type="text/javascript" >

        jQuery(document).ready(function() {

            function showFormErros2(campos) {
                resetFormErros();
                jQuery.each(campos, function() {
                    jQuery('#lbl_' + this.campo).addClass('erro');
                    jQuery('#' + this.campo).addClass('erro');
                });
            }
            showFormErros2(<?php echo json_encode($this->view->dados); ?>);

        });

    </script>
    
    <?php endif; ?>

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_ip_ramal_central/rodape.php"; ?>
