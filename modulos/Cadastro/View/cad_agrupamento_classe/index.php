

<?php require_once _MODULEDIR_ . "Cadastro/View/cad_agrupamento_classe/cabecalho.php"; ?>    
    
    <form id="form"  method="post" action="">
    <input type="hidden" id="acao" name="acao" value="pesquisar"/>
    <input type="hidden" id="agcoid" name="agcoid" value=""/>
    
    
    <?php require_once _MODULEDIR_ . "Cadastro/View/cad_agrupamento_classe/formulario_pesquisa.php"; ?>
    
    <div id="resultado_pesquisa" >
    
	    <?php 
        if ( $this->view->status && count($this->view->dados) > 0) { 
            require_once 'resultado_pesquisa.php'; 
        } 
        ?>
	    
    </div>
        
    </form>
    
<?php require_once _MODULEDIR_ . "Cadastro/View/cad_agrupamento_classe/rodape.php"; ?>
