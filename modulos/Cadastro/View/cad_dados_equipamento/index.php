<?php require_once _MODULEDIR_ . "Cadastro/View/cad_dados_equipamento/cabecalho.php"; ?>    
    
<form id="form"  method="post" action="">
    <input type="hidden" id="acao" name="acao" value="pesquisar"/>
    
    <?php require_once _MODULEDIR_ . "Cadastro/View/cad_dados_equipamento/formulario_pesquisa.php"; ?>
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
    if ( $this->view->status && count($this->view->dados['COMMAND_MTC']) > 0) { 
        require_once 'tabela_mtc.php'; 
    } 
    if ( $this->view->status && count($this->view->dados['COMMAND_LMU']) > 0) { 
        require_once 'tabela_lmu.php'; 
    } 
    if ( $this->view->status && count($this->view->dados['OUTROS']) > 0) { 
        require_once 'tabela_geral.php'; 
    } 
    ?>
</div>
<?php require_once _MODULEDIR_ . "Cadastro/View/cad_dados_equipamento/rodape.php"; ?>
