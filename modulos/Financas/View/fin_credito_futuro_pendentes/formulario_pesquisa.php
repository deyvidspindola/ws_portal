


<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo data periodo">
            <div class="inicial">
                <label for="">Período inclusão *</label>
                <input type="text" id="cfodt_inclusao_de" name="cfodt_inclusao_de" value="<?php echo $this->view->parametros->cfodt_inclusao_de ?>" class="campo" />
            </div>							
            <div class="campo label-periodo">a</div>
            <div class="final">
                <label for="">&nbsp;</label>
                <input type="text" id="cfodt_inclusao_ate" name="cfodt_inclusao_ate" value="<?php echo $this->view->parametros->cfodt_inclusao_ate ?>" class="campo" />
            </div>                            
        </div>

        <div class="clear"></div>

        <div class="campo data periodo maior">
            <div class="inicial">
                <label for="">Período Análise</label>
                <input type="text" name="cfodt_avaliacao_de" id="cfodt_avaliacao_de" value="<?php echo $this->view->parametros->cfodt_avaliacao_de ?>" class="campo" />
            </div>							
            <div class="campo label-periodo">a</div>
            <div class="final">
                <label for="">&nbsp;</label>
                <input type="text" id="cfodt_avaliacao_ate" name="cfodt_avaliacao_ate" value="<?php echo $this->view->parametros->cfodt_avaliacao_ate ?>" class="campo" />
            </div>                            
        </div>

        <div class="clear"></div>
        <!--  COMPONENTE  DE PESQUISA DE CLIENTES -->

        <div class="campo medio">
            <div id="campo_nome_cliente">
                <label id="lbl_nome_cliente" for="razao_social" class="razao_social">Nome do Cliente</label>
                <input type="text" id="nome_cliente" maxlength="255" name="cliente_nome" value="<?php echo trim($this->view->parametros->cliente_nome); ?>" class="campo razao_social limpar_campos" />
            </div>
        </div>

        <div class="clear"></div>

        <fieldset class="medio">
            <legend id="ldg_tipo_pessoa">Tipo Pessoa</legend>

            <input type="radio" id="tipo_pessoa_fisica" name="tipo_pessoa" value="F" <?php echo trim($this->view->parametros->tipo_pessoa) != '' && $this->view->parametros->tipo_pessoa == 'F' ? 'checked="checked"' : ($this->view->parametros->tipo_pessoa != 'J') ? 'checked="checked"' : ''  ?> />
            <label id="lbl_tipo_pessoa_fisica" for="tipo_pessoa_fisica">Física</label>

            <input type="radio" id="tipo_pessoa_juridica" name="tipo_pessoa" value="J" <?php echo trim($this->view->parametros->tipo_pessoa) != '' && $this->view->parametros->tipo_pessoa == 'J' ? 'checked="checked"' : '' ?>  />
            <label id="lbl_tipo_pessoa_juridica" for="tipo_pessoa_juridica">Jurídica</label>                

        </fieldset>

        <div class="clear"></div>        

        <div class="campo medio">
            <div id="juridicoDoc" <?php echo ($this->view->parametros->tipo_pessoa == 'J') ? 'class="visivel"' : 'class="invisivel"' ?>>
                <label id="lbl_cnpj" for="cnpj" class="cnpj">CNPJ</label>
                <input type="text" id="cnpj" name="cnpj" value="<?php echo trim($this->view->parametros->cnpj) ?>" class="campo cnpj limpar_campos" />  
            </div>


            <div id="fiscaDoc" <?php echo ($this->view->parametros->tipo_pessoa == 'F') ? 'class="visivel"' : ($this->view->parametros->tipo_pessoa != 'J') ? 'class="visivel"' : 'class="invisivel"'  ?>>
                <label id="lbl_cpf" for="cpf" class=" cpf">CPF</label>
                <input type="text" id="cpf" name="cpf" value="<?php echo trim($this->view->parametros->cpf) ?>" class="campo cpf  limpar_campos" />  
            </div>
        </div>

        <div class="clear"></div>

        <div class="campo medio">
            <label id="lbl_contrato" for="contrato_pesquisa" class="contrato">Protocolo</label>
            <input type="text" id="contrato_pesquisa" maxlength="10" name="contrato" value="<?php echo trim($this->view->parametros->contrato); ?>" class="campo razao_social limpar_campos" />
        </div>

        <div class="clear"></div>
        <!--  FIM COMPONENTE -->


        <div class="campo medio">
            <label id="lbl_cfostatus" for="cfostatus">Status</label>
            <select id="cfostatus" name="cfostatus">
                <option <?php echo ($this->view->parametros->cfostatus == '3') ? 'selected="selected"' : ($this->view->parametros->cfostatus != '-1' && $this->view->parametros->cfostatus != '1' && $this->view->parametros->cfostatus != '4' ? 'selected="selected"' : '') ?> value="3">Pendente</option>
                <option <?php echo ($this->view->parametros->cfostatus == '1') ? 'selected="selected"' : '' ?> value="1">Aprovado</option>                
                <option <?php echo ($this->view->parametros->cfostatus == '4') ? 'selected="selected"' : '' ?> value="4">Reprovado</option>
                <option <?php echo ($this->view->parametros->cfostatus == '-1') ? 'selected="selected"' : '' ?> value="-1">Todos</option>
                
            </select>
        </div>
        <div class="clear"></div>



        <div class="campo medio">
            <label id="lbl_cfousuoid_inclusao" for="cfousuoid_inclusao">Incluso por</label>
            <select id="cfousuoid_inclusao" name="cfousuoid_inclusao">
                <option value="">SELECIONE</option>
                <?php if (isset($this->view->parametros->usuarioInclusaoCreditoFuturo) && count($this->view->parametros->usuarioInclusaoCreditoFuturo) > 0) : ?>
                    <?php foreach ($this->view->parametros->usuarioInclusaoCreditoFuturo as $item) : ?>
                        <?php if ($this->view->parametros->cfousuoid_inclusao == $item->cd_usuario) : ?>
                            <option selected="selected" value="<?php echo $item->cd_usuario ?>"><?php echo $item->nm_usuario ?></option>
                        <?php else: ?>
                            <option value="<?php echo $item->cd_usuario ?>"><?php echo $item->nm_usuario ?></option>
                        <?php endif; ?>                        
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button id="btn_gerar_xls" type="button">Gerar XLS</button>
    <button id="btn_pesquisar" type="submit">Pesquisar</button>
</div>