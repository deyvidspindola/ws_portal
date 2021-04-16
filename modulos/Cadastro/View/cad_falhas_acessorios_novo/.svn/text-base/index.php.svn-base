<?php cabecalho(); ?>

<!-- LINKS PARA CSS E JS -->
<?php require _MODULEDIR_ . 'Cadastro/View/cad_falhas_acessorios_novo/head.php' ?>

<div class="modulo_titulo">Controle de Falhas Acessórios</div>
<div class="modulo_conteudo">
	<div id="msginfo" class="mensagem info">Os campos com * são obrigatórios.</div>

	<div class="mensagem alerta"  id="msg_alerta"   <?php if(!$this->mensagemAlerta)  echo "style=\"display:none;\"";?> ><?=$this->mensagemAlerta?></div>
	<div class="mensagem sucesso" id="msg_sucesso"  <?php if(!$this->mensagemSucesso) echo "style=\"display:none;\"";?> ><?=$this->mensagemSucesso?></div>
	<div class="mensagem erro"    id="msg_erro"     <?php if(!$this->erro)    echo "style=\"display:none;\"";?> ><?=$this->erro?></div>        
    
    
    <form  id="frm" name="frm" method="POST" >
    	<input type="hidden" name="acao" id="acao" value="<?php echo $this->acao; ?>"/>
  		<input type="hidden" name="cfaoid" id="hdn_cfaoid" value="" />
  		<input type="hidden" name="imoboid" id="hdn_imoboid" value="" />
    
     
     <div class="mensagem" style="display: none;"></div>
        <div class="bloco_titulo">Dados para Pesquisa</div>
        <div class="bloco_conteudo">
            <div class="formulario">
                <div class="clear"></div>
                <div id="combo_principal_novo" class="campo medio">
                    <label for="tipo">Tipo *</label>
                    <select id="tipo" name="tipo">
                       	<option value="">Selecione</option>  
                       	<option value="4"  <?php if ($this->parametros->tipo == 4)  : ?> selected='selected' <?php endif; ?>>Antena Satelital</option>		                    			                    		
		                <option value="23" <?php if ($this->parametros->tipo == 23) : ?> selected='selected' <?php endif; ?>>Computador Bordo</option>
		                <option value="21" <?php if ($this->parametros->tipo == 21) : ?> selected='selected' <?php endif; ?>>Teclado</option>	
		                <option value="11" <?php if ($this->parametros->tipo == 11) : ?> selected='selected' <?php endif; ?>>Trava Baú</option>
		                <option value="13" <?php if ($this->parametros->tipo == 13) : ?> selected='selected' <?php endif; ?>>Trava 5º roda</option>							
                     </select>
                </div>
                
                <div class="clear"></div>
                
                <div class="campo medio">
                    <label for="serial">Serial *</label>
                    <input type="text" id="serial" name="serial" value="<?php echo htmlentities(strip_tags($this->parametros->serial)); ?>" class="campo"  maxlength="20"/>
                </div>   
                <div class="clear"></div>
            </div>            
        </div>        
        
		<div class="bloco_acoes">
			<button type="button" id="btn_pesquisar" name="btn_pesquisar">Pesquisar</button>            
            <button type="button" id="btn_novo"   name="btn_novo" >Novo</button>
        </div>
    </form>
        
    <form  id="frm_action" name="frm_action" method="POST" >
    	<input type="hidden" name="acao"    id="acao" value="<?php echo $this->acao; ?>"/>
  		<input type="hidden" name="cfaoid"  id="hdn_cfaoid2" value="<?php echo $this->parametros->cfaoid; ?>" />
  		<input type="hidden" name="imoboid" id="hdn_imoboid2" value="<?php echo $this->registro->imoboid; ?>" />
  		<input type="hidden" name="serial"  id="serial2" value="<?php echo $this->parametros->serial; ?>" />
  		<input type="hidden" name="tipo"    id="tipo2" value="<?php echo $this->parametros->tipo; ?>" />
        
        <?php
        if ($this->acao == "salvar") {
         	include _MODULEDIR_.'Cadastro/View/cad_falhas_acessorios_novo/salvar.php';
		}
		else if( count($this->resultadoPesquisa) > 0 && $this->erro == "" && $this->acaoOrigem=='pesquisar') {
        	require _MODULEDIR_.'Cadastro/View/cad_falhas_acessorios_novo/resultado.php'; 
        } else if ($this->erro == "" && $this->acao != "index" && $this->acaoOrigem=='pesquisar') { 
			echo '<div class="separador"></div>
			      <div class="mensagem alerta">Nenhum registro encontrado.</div>';
		}
		?>
    </form>
</div>
<div class="separador"></div>
<?php include "lib/rodape.php"; ?>
