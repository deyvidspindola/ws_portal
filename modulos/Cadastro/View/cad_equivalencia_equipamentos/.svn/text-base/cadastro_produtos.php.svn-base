<form id="formCadastroProdutos"  method="post" action="cad_equivalencia_equipamentos.php">
<input type="hidden" name="acao" value="cadastrarEquivalenciaProduto">
<input type="hidden" id="eeieeqoid" name="eeieeqoid" value="<?php echo isset($this->parametrosEquivalencia->eeqoid)  && $this->parametrosEquivalencia->eeqoid != '' ? $this->parametrosEquivalencia->eeqoid : '' ?>">
<div class="bloco_titulo">Dados dos Produtos</div>
        <div class="bloco_conteudo">
            <div class="formulario">
                <div class="campo medio">
                    <label for="eeitipo">Tipo do Produto *</label>
                    <select id="eeitipo" name="eeitipo">
                       <option value="" <?php echo  isset($this->parametrosProdutos->eeitipo) && $this->parametrosProdutos->eeitipo != 'A' && $this->parametrosProdutos->eeitipo != 'E' ? 'selected="selected"' : '' ?> >Selecione</option>
                       <option value="A" <?php echo isset($this->parametrosProdutos->eeitipo) && !empty($this->parametrosProdutos->eeitipo) && $this->parametrosProdutos->eeitipo == 'A' ? 'selected="selected"' : '' ?>>Acessório</option>
                       <option value="E" <?php echo isset($this->parametrosProdutos->eeitipo) && !empty($this->parametrosProdutos->eeitipo) && $this->parametrosProdutos->eeitipo == 'E' ? 'selected="selected"' : '' ?>>Equipamento</option>
                      </select>
                </div>
                
                <div class="clear"></div>
 				 <div class="campo medio">
                    <label for="eeiprdoid">Produto *</label>
                    <select id="eeiprdoid" name="eeiprdoid">
                       <option value="">Selecione</option>  
                    </select>
                    <img class="carregando" id="loading-cadastro-produtos" style="display: none;" src="<?php echo _PROTOCOLO_ . _SITEURL_ . 'modulos/web/images/ajax-loader-circle.gif' ?>" />
                </div>
				<div class="clear"></div>
				 <div class="campo medio">
                    <label for="eeiversao">Versão </label>
                    <select id="eeiversao" name="eeiversao">
                       <option value="">Selecione</option>  
                      </select>
                      <img class="carregando" id="loading-cadastro-versoes" style="display: none;" src="<?php echo _PROTOCOLO_ . _SITEURL_ . 'modulos/web/images/ajax-loader-circle.gif' ?>" />
                                        
                </div>
				<div class="clear"></div>
            </div>            
        </div>
        
        <div class="bloco_acoes">
            <button type="button" id="bt_incluir_produto" name="bt_incluir_produto">Incluir</button>
            <button type="button" id="bt_voltar" name="pesquisar">Voltar</button>  
        </div>
		<div class="separador"></div>
</form>