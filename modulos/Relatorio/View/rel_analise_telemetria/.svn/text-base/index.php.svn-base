<?php require_once _MODULEDIR_ . "Relatorio/View/rel_analise_telemetria/cabecalho.php"; ?>
    
<form id="form"  method="post" action="">
    <input type="hidden" id="acao" name="acao" value="pesquisar"/>
    
    <?php require_once _MODULEDIR_ . "Relatorio/View/rel_analise_telemetria/formulario_pesquisa.php"; ?>
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
    if ( $this->view->status && count($this->view->dados->resultadoTelemetria) > 0) { 
        require_once 'resultado.php';
    }
    ?>
</div>
<?php require_once _MODULEDIR_ . "Relatorio/View/rel_analise_telemetria/rodape.php"; ?>
