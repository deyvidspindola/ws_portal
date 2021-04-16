<?php require_once 'header.php'; ?>

<div class="modulo_titulo">Equivalência de Equipamento por Classe de Contrato</div>
<div class="modulo_conteudo">
    
    <!--se existir erro no processamento de dados -->
    <?php if ( isset($this->tipoErro) && $this->tipoErro == 'E') : ?>
        <div id="div_mensagem_listagem" class="mensagem erro" ><?php echo $this->mensagemErro; ?></div>
    <?php endif; ?>
        
    <!--se for pesquisar e for vazio, exibe a mensagem abaixo-->
    <?php if ( isset($this->listagem) && empty($this->listagem)) : ?>
        <div id="div_mensagem_listagem" class="mensagem alerta" >Nenhum registro encontrado.</div>
    <?php endif; ?>
        
    <div id="mensagens_carregamento_combos" class="mensagem erro invisivel" ></div>
    <div id="mensagens_alerta" class="mensagem alerta invisivel" ></div>
            
    <form id="form"  method="post" action="cad_equivalencia_equipamentos.php">
    <input type="hidden" id="acao" name = "acao" value = "pesquisar"/>
    <input type="hidden" id="acao" name = "pesquisar" value = "pesquisar"/>
    
    <!--renderiza formulário de pesquisa-->
    <?php require_once 'pesquisa_formulario.php'; ?>
    
    <div id="resultado_pesquisa" >
    
	    <!--caso seja pesquisa e seja diferente de vazio, exibe a listagem-->
	    <?php if ( isset($this->listagem) && !empty($this->listagem)) : ?>
	        <?php require_once 'pesquisa_listagem.php'; ?>
	    <?php endif; ?>
	    
    </div>
        
    </form>
</div>
<div class="separador"></div>

<?php require_once 'footer.php'; ?>