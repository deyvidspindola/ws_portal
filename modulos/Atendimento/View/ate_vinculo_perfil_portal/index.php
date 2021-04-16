<?php require_once _MODULEDIR_ . "Atendimento/View/ate_vinculo_perfil_portal/cabecalho.php";?>

<form method="post" action="ate_vinculo_perfil_portal.php">
    <input type="hidden" id="acao" name="acao" value="pesquisar"/>
    <input type="hidden" id="tela" name="tela" value="<?php echo $this->view->parametros->tela; ?>"/>

<?php
    if($this->view->parametros->tela == 'vinculo') {
        require_once _MODULEDIR_ . "Atendimento/View/ate_vinculo_perfil_portal/vinculo.php";
    } else {
        require_once _MODULEDIR_ . "Atendimento/View/ate_vinculo_perfil_portal/pesquisa.php";
    }
?>
</form>

<?php

    if(($this->view->parametros->tela != 'vinculo') && !empty($this->view->dados)) {
        require_once _MODULEDIR_ . "Atendimento/View/ate_vinculo_perfil_portal/resultado_pesquisa.php";
   }

    require_once _MODULEDIR_ . "Atendimento/View/ate_vinculo_perfil_portal/rodape.php";
?>
