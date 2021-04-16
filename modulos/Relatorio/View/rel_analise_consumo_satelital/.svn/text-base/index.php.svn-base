<?php require_once _MODULEDIR_ . "Relatorio/View/rel_analise_consumo_satelital/cabecalho.php"; ?>    
    
<form id="form"  method="post" action="">
    <input type="hidden" id="acao" name="acao" value="pesquisar"/>
    
    <?php require_once _MODULEDIR_ . "Relatorio/View/rel_analise_consumo_satelital/formulario_pesquisa.php"; ?>
</form>

<div id="consolidado_pesquisa" >
    <?php 
        if ( $this->view->status && count($this->view->consolidadoCliente) > 0) { 
            require_once 'consolidado_cliente.php'; 
        } 
    ?>
</div>

<div id="resultado_pesquisa" >
    <?php 
    if ( $this->view->status && count($this->view->dados['OUTROS']) > 0) { 
        require_once 'tabela_geral.php'; 
    } 
    ?>
</div>
<?php require_once _MODULEDIR_ . "Relatorio/View/rel_analise_consumo_satelital/rodape.php"; ?>
