<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">
        
        <div class="campo medio">
            <label id="lbl_nfino_numero" for="nfino_numero">Número da Nota</label>
            <input id="nfino_numero" class="campo" type="text" value="" name="nfino_numero">
        </div>
        
        <div class="campo medio">
            <label id="lbl_nficonoid" for="nficonoid">Contrato</label>
            <input id="nficonoid" class="campo" type="text" value="" name="nficonoid">
        </div>

		<div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>    
</div>

<?php if (count($this->view->dados) > 0) : ?>
<!--  Caso contenha erros, exibe os campos destacados  -->
<script type="text/javascript" >jQuery(document).ready(function() {
    showFormErros(<?php echo json_encode($this->view->dados); ?>); 
});
</script>

<?php endif; ?>





