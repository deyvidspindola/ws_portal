<div class="bloco_titulo">Escolha o cliente para dar crédito</div>
<div class="bloco_conteudo" style="height: 530px">
    <div class="formulario">
        <form id="form_cadastrar"  method="post" action="">
            <input type="hidden" id="acao" name="acao" value="cadastrar"/>
            <input type="hidden" id="cliente_id" name="cadastro[cfoclioid]" value="<?php echo $this->view->parametros->cfoclioid; ?>"/>
            <input type="hidden" id="step" name="step" value = "step_1" />
            <input type="hidden" id="voltar" name="voltar" value = "0" />

            <div class="campo maior">
                <label style="color: gray">
                    Faça a busca digitando o <strong>nome</strong>, <strong>documento</strong> ou <strong>contrato</strong> do cliente.
                </label>
            </div>
            <div class="clear"></div>

            <!--  COMPONENTE  DE PESQUISA DE CLIENTES -->


            <div class="campo medio">
                <div id="campo_nome_cliente">
                    <label id="lbl_nome_cliente" for="razao_social" class="razao_social">Nome do Cliente *</label>
                    <input type="text" id="nome_cliente" maxlength="255" name="cadastro[razao_social]" value="<?php echo trim($this->view->parametros->razao_social); ?>" class="campo razao_social limpar_campos" />
                </div>
            </div>

            <div class="clear"></div>

            <fieldset class="medio">
                <legend id="ldg_tipo_pessoa">Tipo Pessoa</legend>
                
                <input type="radio" id="tipo_pessoa_fisica" name="cadastro[tipo_pessoa]" value="F" <?php echo trim($this->view->parametros->tipo_pessoa) != '' && $this->view->parametros->tipo_pessoa == 'F' ? 'checked="checked"' : ($this->view->parametros->tipo_pessoa != 'J') ? 'checked="checked"' : ''  ?> />
                <label id="lbl_tipo_pessoa_fisica" for="tipo_pessoa_fisica">Física</label>
                
                <input type="radio" id="tipo_pessoa_juridica" name="cadastro[tipo_pessoa]" value="J" <?php echo trim($this->view->parametros->tipo_pessoa) != '' && $this->view->parametros->tipo_pessoa == 'J' ? 'checked="checked"' : '' ?>  />
                <label id="lbl_tipo_pessoa_juridica" for="tipo_pessoa_juridica">Jurídica</label>                
                                        
            </fieldset>

            <div class="clear"></div>        

            <div class="campo medio">
                <div id="juridicoDoc" <?php echo ($this->view->parametros->tipo_pessoa == 'J') ? 'class="visivel"' : 'class="invisivel"' ?>>
                    <label id="lbl_cnpj" for="cnpj" class="cnpj">CNPJ *</label>
                    <input type="text" id="cnpj" name="cadastro[cnpj]" value="<?php echo trim($this->view->parametros->cnpj) ?>" class="campo cnpj limpar_campos" />  
                </div>
                
                
                <div id="fiscaDoc" <?php echo ($this->view->parametros->tipo_pessoa == 'F') ? 'class="visivel"' : ($this->view->parametros->tipo_pessoa != 'J') ? 'class="visivel"' : 'class="invisivel"'  ?>>
                    <label id="lbl_cpf" for="cpf" class=" cpf">CPF *</label>
                    <input type="text" id="cpf" name="cadastro[cpf]" value="<?php echo trim($this->view->parametros->cpf) ?>" class="campo cpf  limpar_campos" />  
                </div>
            </div>

            <div class="clear"></div>

            <div class="campo medio">
                <label id="lbl_contrato" for="contrato" class="contrato">Contrato</label>
                <input type="text" id="contrato" maxlength="10" name="cadastro[contrato]" value="<?php echo trim($this->view->parametros->contrato); ?>" class="campo razao_social limpar_campos" />
            </div>
            <!--  FIM COMPONENTE -->

        </form>
    </div>
</div>