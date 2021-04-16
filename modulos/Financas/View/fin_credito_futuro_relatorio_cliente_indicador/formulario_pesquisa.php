
<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo data periodo">
            <div class="inicial">
                <label for="cfcidt_inclusao_de">Período inclusão *</label>
                <input type="text" id="cfcidt_inclusao_de" name="cfcidt_inclusao_de" value="<?php echo $this->view->parametros->cfcidt_inclusao_de ?>" class="campo" />
            </div>							
            <div class="campo label-periodo">a</div>
            <div class="final">
                <label for="cfcidt_inclusao_ate">&nbsp;</label>
                <input type="text" id="cfcidt_inclusao_ate" name="cfcidt_inclusao_ate" value="<?php echo $this->view->parametros->cfcidt_inclusao_ate ?>" class="campo" />
            </div>                            
        </div>

        <div class="clear"></div>
        <div class="separador"></div>

        <!-- PESQUISA CLIENTE INDICADOR -->        
        <div class="campo medio">
            <div id="juridicoNome" <?php echo ($this->view->parametros->tipo_pessoa == 'J') ? 'class="visivel"' : 'class="invisivel"' ?>>
                <label id="lbl_razao_social" for="razao_social" class="razao_social">Nome do Cliente Indicador</label>
                <input type="text" id="razao_social" maxlength="255" name="razao_social" value="<?php echo trim($this->view->parametros->razao_social); ?>" class="campo razao_social limpar_campos" />
            </div>

            <div id="fiscaNome" <?php echo ($this->view->parametros->tipo_pessoa == 'F') ? 'class="visivel"' : ($this->view->parametros->tipo_pessoa != 'J') ? 'class="visivel"' : 'class="invisivel"'  ?>>
                <label id="lbl_nome" for="nome" class=" nome">Nome do Cliente Indicador</label>
                <input type="text" id="nome" name="nome" maxlength="255" value="<?php echo trim($this->view->parametros->nome); ?>" class="campo nome  limpar_campos" />
            </div>
        </div>

        <fieldset class="medio">
            <legend id="ldg_tipo_pessoa">Tipo Pessoa</legend>

            <input type="radio" id="tipo_pessoa_fisica" name="tipo_pessoa" value="F" <?php echo trim($this->view->parametros->tipo_pessoa) != '' && $this->view->parametros->tipo_pessoa == 'F' ? 'checked="checked"' : ($this->view->parametros->tipo_pessoa != 'J') ? 'checked="checked"' : ''  ?> />
            <label id="lbl_tipo_pessoa_fisica" for="tipo_pessoa_fisica">Física</label>

            <input type="radio" id="tipo_pessoa_juridica" name="tipo_pessoa" value="J" <?php echo trim($this->view->parametros->tipo_pessoa) != '' && $this->view->parametros->tipo_pessoa == 'J' ? 'checked="checked"' : '' ?>  />
            <label id="lbl_tipo_pessoa_juridica" for="tipo_pessoa_juridica">Jurídica</label>                

        </fieldset>

        <div class="campo pesquisa componente_cliente_cnpj">
            <div id="juridicoDoc" <?php echo ($this->view->parametros->tipo_pessoa == 'J') ? 'class="visivel"' : 'class="invisivel"' ?>>
                <label id="lbl_cnpj" for="cnpj" class="cnpj">CNPJ</label>
                <input type="text" id="cnpj" name="cnpj" value="<?php echo trim($this->view->parametros->cnpj) ?>" class="campo cnpj limpar_campos" />  
            </div>
            <div id="fiscaDoc" <?php echo ($this->view->parametros->tipo_pessoa == 'F') ? 'class="visivel"' : ($this->view->parametros->tipo_pessoa != 'J') ? 'class="visivel"' : 'class="invisivel"'  ?>>
                <label id="lbl_cpf" for="cpf" class="cpf">CPF</label>
                <input type="text" id="cpf" name="cpf" value="<?php echo trim($this->view->parametros->cpf) ?>" class="campo cpf  limpar_campos" />  
            </div>
        </div>

        <div class="clear"></div>
        
       <!-- PESQUISA CLIENTE INDICADO -->
        <div class="campo medio">
            <div id="juridicoNomeIndicado" <?php echo ($this->view->parametros->tipo_pessoa_indicado == 'J') ? 'class="visivel"' : 'class="invisivel"' ?>>
                <label id="lbl_razao_social_indicado" for="razao_social_indicado" class="razao_social">Nome do Cliente Indicado</label>
                <input type="text" id="razao_social_indicado" maxlength="255" name="razao_social_indicado" value="<?php echo trim($this->view->parametros->razao_social_indicado); ?>" class="campo razao_social_indicado limpar_campos" />
            </div>

            <div id="fiscaNomeIndicado" <?php echo ($this->view->parametros->tipo_pessoa_indicado == 'F') ? 'class="visivel"' : ($this->view->parametros->tipo_pessoa_indicado != 'J') ? 'class="visivel"' : 'class="invisivel"'  ?>>
                <label id="lbl_nome_indicado" for="nome_indicado" class="nome">Nome do Cliente Indicado</label>
                <input type="text" id="nome_indicado" name="nome_indicado" maxlength="255" value="<?php echo trim($this->view->parametros->nome_indicado); ?>" class="campo nome_indicado  limpar_campos" />
            </div>
        </div>
        
        <fieldset class="medio">
            <legend id="ldg_tipo_pessoa">Tipo Pessoa</legend>

            <input type="radio" id="tipo_pessoa_fisica_indicado" name="tipo_pessoa_indicado" value="F" <?php echo trim($this->view->parametros->tipo_pessoa_indicado) != '' && $this->view->parametros->tipo_pessoa_indicado == 'F' ? 'checked="checked"' : ($this->view->parametros->tipo_pessoa_indicado != 'J') ? 'checked="checked"' : ''  ?> />
            <label id="lbl_tipo_pessoa_fisica" for="tipo_pessoa_fisica">Física</label>

            <input type="radio" id="tipo_pessoa_juridica_indicado" name="tipo_pessoa_indicado" value="J" <?php echo trim($this->view->parametros->tipo_pessoa_indicado) != '' && $this->view->parametros->tipo_pessoa_indicado == 'J' ? 'checked="checked"' : '' ?>  />
            <label id="lbl_tipo_pessoa_juridica" for="tipo_pessoa_juridica">Jurídica</label>                

        </fieldset>

        <div class="campo pesquisa componente_cliente_cnpj">
            <div id="juridicoDocIndicado" <?php echo ($this->view->parametros->tipo_pessoa_indicado == 'J') ? 'class="visivel"' : 'class="invisivel"' ?>>
                <label id="lbl_cnpj_indicado" for="cnpj_indicado" class="cnpj">CNPJ</label>
                <input type="text" id="cnpj_indicado" name="cnpj_indicado" value="<?php echo trim($this->view->parametros->cnpj_indicado) ?>" class="campo cnpj_indicado limpar_campos" />  
            </div>
            <div id="fiscaDocIndicado" <?php echo ($this->view->parametros->tipo_pessoa_indicado == 'F') ? 'class="visivel"' : ($this->view->parametros->tipo_pessoa_indicado != 'J') ? 'class="visivel"' : 'class="invisivel"'  ?>>
                <label id="lbl_cpf_indicado" for="cpf_indicado" class="cpf">CPF</label>
                <input type="text" id="cpf_indicado" name="cpf_indicado" value="<?php echo trim($this->view->parametros->cpf_indicado) ?>" class="campo cpf_indicado  limpar_campos" />  
            </div>
        </div>
        
        <div class="clear"></div>

        <!-- USUÁRIO INCLUSÃO -->
        <div class="campo maior">
            <label id="lbl_cfciusuoid_inclusao" for="cfciusuoid_inclusao">Incluso por</label>
            <select id="cfciusuoid_inclusao" name="cfciusuoid_inclusao">
                <option value="">SELECIONE</option>
<?php 
if (isset($this->view->parametros->usuarioInclusaoRelatorioClienteIndicador) && count($this->view->parametros->usuarioInclusaoRelatorioClienteIndicador) > 0) { 
    foreach ($this->view->parametros->usuarioInclusaoRelatorioClienteIndicador as $item) { 
        if ($this->view->parametros->cfciusuoid_inclusao == $item->cd_usuario) { 
                ?>
                <option selected="selected" value="<?php echo $item->cd_usuario ?>"><?php echo $item->nm_usuario ?></option>
                <?php
        } else { 
                ?>
                <option value="<?php echo $item->cd_usuario ?>"><?php echo $item->nm_usuario ?></option>
                <?php 
        } 
    }
} ?>
            </select>
        </div>

        <div class="clear"></div>
        
        <!-- CONTRATO -->
        <div class="campo menor">
            <label id="lbl_cfcitermo" for="cfcitermo_pesquisa" class="cfcitermo">Contrato</label>
            <input type="text" id="cfcitermo_pesquisa" maxlength="10" name="cfcitermo" value="<?php echo trim($this->view->parametros->cfcitermo); ?>" class="campo razao_social limpar_campos" />
        </div>

        <div class="clear"></div>
        
        <!-- EQUIPAMENTO INSTALADO -->
        <fieldset class="maior">
            <legend id="lgd_cfcieqpto_instalado">Equipamento instalado ?</legend>
            <input class="eqpto_instalado" type="radio" id="cfcieqpto_instalado_1" name="cfcieqpto_instalado" value="t" <?php echo trim($this->view->parametros->cfcieqpto_instalado) != '' && $this->view->parametros->cfcieqpto_instalado == 't' ? 'checked="checked"' : ''  ?> />
            <label id="lbl_cfcieqpto_instalado_1" for="cfcieqpto_instalado_1">Sim</label>
            <input class="eqpto_instalado" type="radio" id="cfcieqpto_instalado_2" name="cfcieqpto_instalado" value="f" <?php echo trim($this->view->parametros->cfcieqpto_instalado) != '' && $this->view->parametros->cfcieqpto_instalado == 'f' ? 'checked="checked"' : '' ?>/>
            <label id="lbl_cfcieqpto_instalado_2" for="cfcieqpto_instalado_2">Não</label>
            <input class="eqpto_instalado" type="radio" id="cfcieqpto_instalado_3" name="cfcieqpto_instalado" value="" <?php echo trim($this->view->parametros->cfcieqpto_instalado != 't' && $this->view->parametros->cfcieqpto_instalado != 'f') ? 'checked="checked"' : '' ?>/>
            <label id="lbl_cfcieqpto_instalado_3" for="cfcieqpto_instalado_3">Todos</label>
        </fieldset>
        
        <div class="clear"></div>
        
       <!-- FORMA DE INCLUSÃO -->
        <fieldset class="maior">
            <legend id="lgd_cfciforma_inclusao">Inclusão</legend>
            <input class="forma_inclusao" type="radio" id="cfciforma_inclusao_1" name="cfciforma_inclusao" value="M" <?php echo trim($this->view->parametros->cfciforma_inclusao) != '' && $this->view->parametros->cfciforma_inclusao == 'M' ? 'checked="checked"' : ''  ?> />
            <label id="lbl_cfciforma_inclusao_1" for="cfciforma_inclusao_1">Manual</label>
            <input class="forma_inclusao" type="radio" id="cfciforma_inclusao_2" name="cfciforma_inclusao" value="A" <?php echo trim($this->view->parametros->cfciforma_inclusao) != '' && $this->view->parametros->cfciforma_inclusao == 'A' ? 'checked="checked"' : '' ?>/>
            <label id="lbl_cfciforma_inclusao_2" for="cfciforma_inclusao_2">Automático</label>
            <input class="forma_inclusao" type="radio" id="cfciforma_inclusao_3" name="cfciforma_inclusao" value="" <?php echo trim($this->view->parametros->cfciforma_inclusao != 'M' && $this->view->parametros->cfciforma_inclusao != 'A') ? 'checked="checked"' : '' ?>/>
            <label id="lbl_cfciforma_inclusao_3" for="cfciforma_inclusao_3">Todos</label>
        </fieldset>
        
        <div class="clear"></div>
        
    </div>
</div>

<div class="bloco_acoes">
    <button id="btn_pesquisar" type="submit">Pesquisar</button>
</div>
