
<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo data periodo">
            <div class="inicial">
                <label id="lbl_periodo_inclusao_ini" for="periodo_inclusao_ini">Período inclusão *</label>
                <input type="text" id="periodo_inclusao_ini" name="periodo_inclusao_ini" value="<?php echo trim($this->view->parametros->periodo_inclusao_ini); ?>" class="campo" />
            </div>
            <div class="campo label-periodo">a</div>
            <div class="final">
                <label id="lbl_periodo_inclusao_fim" for="periodo_inclusao_fim">&nbsp;</label>
                <input type="text" id="periodo_inclusao_fim" name="periodo_inclusao_fim" value="<?php echo trim($this->view->parametros->periodo_inclusao_fim); ?>" class="campo" />
            </div>
        </div>
        <!--  COMPONENTE  DE PESQUISA DE CLIENTES -->
        <!--já existente-->
        <div class="clear"></div>

        <fieldset class="medio">
            <legend id="ldg_tipo_pessoa">Tipo Pessoa</legend>
            <input type="radio" id="tipo_pessoa_juridica" name="tipo_pessoa" value="J" <?php echo trim($this->view->parametros->tipo_pessoa) != '' && $this->view->parametros->tipo_pessoa == 'J' ? 'checked="checked"' : '' ?>  />

            <label id="lbl_tipo_pessoa_juridica" for="tipo_pessoa_juridica">Jurídica</label>
            <input type="radio" id="tipo_pessoa_fisica" name="tipo_pessoa" value="F" <?php echo trim($this->view->parametros->tipo_pessoa) != '' && $this->view->parametros->tipo_pessoa == 'F' ? 'checked="checked"' : ''  ?> />
            <label id="lbl_tipo_pessoa_fisica" for="tipo_pessoa_fisica">Física</label>
        </fieldset>

        <div class="clear"></div>

        <div class="campo maior">

            <div id="juridicoNome" <?php echo ($this->view->parametros->tipo_pessoa == 'J') ? 'class="visivel"' : 'class="invisivel"' ?>>
                <label id="lbl_razao_social" for="razao_social" class="razao_social">Nome do Cliente</label>
                <input type="text" id="razao_social" maxlength="255" name="razao_social" value="<?php echo trim($this->view->parametros->razao_social); ?>" class="campo razao_social limpar_campos" />
            </div>

            <div id="fiscaNome" <?php echo ($this->view->parametros->tipo_pessoa == 'F') ? 'class="visivel"' : ($this->view->parametros->tipo_pessoa != 'J') ? 'class="visivel"' : 'class="invisivel"'  ?>>
                <label id="lbl_nome" for="nome" class=" nome">Nome do Cliente</label>
                <input type="text" id="nome" name="nome" maxlength="255" value="<?php echo trim($this->view->parametros->nome); ?>" class="campo nome  limpar_campos" />
            </div>


        </div>

        <div class="clear"></div>

        <div class="campo medio">

            <div id="juridicoDoc" <?php echo ($this->view->parametros->tipo_pessoa == 'J') ? 'class="visivel"' : 'class="invisivel"' ?>>
                <label id="lbl_cnpj"  for="cnpj" class="cnpj">CNPJ</label>
                <input type="text" id="cnpj" name="cnpj" value="<?php echo trim($this->view->parametros->cnpj) ?>" class="campo cnpj limpar_campos" />
            </div>

            <div id="fiscaDoc" <?php echo ($this->view->parametros->tipo_pessoa == 'F') ? 'class="visivel"' : ($this->view->parametros->tipo_pessoa != 'J') ? 'class="visivel"' : 'class="invisivel"'  ?>>
                <label id="lbl_cpf" for="cpf" class=" cpf">CPF</label>
                <input type="text" id="cpf" name="cpf" value="<?php echo trim($this->view->parametros->cpf) ?>" class="campo cpf  limpar_campos" />
            </div>


        </div>
        <!--  FIM COMPONENTE -->

        <div class="clear"></div>


        <div class="campo medio">
            <label id="lbl_cfoancoid" for="cfoancoid">Protocolo</label>
            <input type="text" id="cfoancoid" name="cfoancoid" maxlength="10" value="<?php echo trim($this->view->parametros->cfoancoid) ?>" class="campo" />
        </div>

        <div class="clear"></div>

        <div class="campo maior">
            <label id="lbl_cfoobroid_desconto" for="obr_fin_desc">Obrigação financeira de desconto</label>
            <select id="cfoobroid_desconto" name="cfoobroid_desconto" >
                <option value="">SELECIONE</option>
                <?php if (isset($this->view->parametros->obrigacaoFinanceiraDesconto) && count($this->view->parametros->obrigacaoFinanceiraDesconto) > 0) : ?>
                    <?php foreach ($this->view->parametros->obrigacaoFinanceiraDesconto as $item) : ?>
                        <?php if ($this->view->parametros->cfoobroid_desconto == $item->obroid) : ?>
                            <option selected="selected" value="<?php echo $item->obroid ?>"><?php echo $item->obrobrigacao ?></option>
                        <?php else: ?>
                            <option value="<?php echo $item->obroid ?>"><?php echo $item->obrobrigacao ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="clear"></div>

        <div class="campo maior">
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

        <!-- Tipo de desconto-->
        <fieldset class="maior">
            <legend id="lgd_cfotipo_desconto">Tipo de desconto</legend>
            <input class="tipo_desconto" type="radio" id="cfotipo_desconto_1" name="cfotipo_desconto" value="1" <?php echo trim($this->view->parametros->cfotipo_desconto) != '' && $this->view->parametros->cfotipo_desconto == '1' ? 'checked="checked"' : ''  ?> />
            <label id="lbl_cfotipo_desconto_1" for="cfotipo_desconto_1">Percentual</label>
            <input class="tipo_desconto" type="radio" id="cfotipo_desconto_2" name="cfotipo_desconto" value="2" <?php echo trim($this->view->parametros->cfotipo_desconto) != '' && $this->view->parametros->cfotipo_desconto == '2' ? 'checked="checked"' : '' ?>/>
            <label id="lbl_cfotipo_desconto_2" for="cfotipo_desconto_2">Valor</label>
            <input class="tipo_desconto" type="radio" id="cfotipo_desconto_3" name="cfotipo_desconto" value="3" <?php echo trim($this->view->parametros->cfotipo_desconto) != '' && $this->view->parametros->cfotipo_desconto == '3' ? 'checked="checked"' : ($this->view->parametros->cfotipo_desconto != '2' && $this->view->parametros->cfotipo_desconto != '1') ? 'checked="checked"' : '' ?>/>
            <label id="lbl_cfotipo_desconto_3" for="cfotipo_desconto_3">Todos</label>
        </fieldset>

        <!--Tipo de desconto percentual-->
        <div id="tipo_descont_1" <?php echo ($this->view->parametros->cfotipo_desconto == '1') ? 'class="visivel"' : 'class="invisivel"' ?>>
            <div class="campo menor">
                <label id="lbl_cfopercentual_de" for="cfopercentual_de">De (%)</label>
                <input type="text" id="cfopercentual_de" maxlength="6" value="<?php echo trim($this->view->parametros->cfopercentual_de) ?>" class="campo porcentagem" name="cfopercentual_de" />
            </div>
            <div class="campo menor">
                <label id="lbl_cfopercentual_ate" for="cfopercentual_ate">Até (%)</label>
                <input type="text" id="cfopercentual_ate" maxlength="6" name="cfopercentual_ate" value="<?php echo trim($this->view->parametros->cfopercentual_ate) ?>" class="campo porcentagem" />
            </div>
        </div>

        <!--Tipo de desconto valor-->

        <div id="tipo_descont_2" <?php echo ($this->view->parametros->cfotipo_desconto == '2') ? 'class="visivel"' : 'class="invisivel"' ?>>
            <div class="campo menor">
                <label id="lbl_cfovalor_de" for="cfovalor_de">De (R$)</label>
                <input type="text" id="cfovalor_de" value="<?php echo trim($this->view->parametros->cfovalor_de) ?>" maxlength="17" class="campo moeda" name="cfovalor_de" />
            </div>
            <div class="campo menor">
                <label id="lbl_cfovalor_ate" for="cfovalor_ate">Até (R$)</label>
                <input type="text" id="cfovalor_ate" name="cfovalor_ate" value="<?php echo trim($this->view->parametros->cfovalor_ate) ?>" maxlength="17" class="campo moeda" />
            </div>
        </div>

        <div class="clear"></div>

        <fieldset class="maior">
            <legend id="lgd_cfoforma_aplicacao">Forma de aplicação</legend>
            <input type="radio" id="cfoforma_aplicacao_1" name="cfoforma_aplicacao" value="1" <?php echo trim($this->view->parametros->cfoforma_aplicacao) != '' && $this->view->parametros->cfoforma_aplicacao == '1' ? 'checked="checked"' : ''  ?> />
            <label id="lbl_cfoforma_aplicacao_1" for="cfoforma_aplicacao_1">Integral</label>
            <input type="radio" id="cfoforma_aplicacao_2" name="cfoforma_aplicacao" value="2" <?php echo trim($this->view->parametros->cfoforma_aplicacao) != '' && $this->view->parametros->cfoforma_aplicacao == '2' ? 'checked="checked"' : '' ?> />
            <label id="lbl_cfoforma_aplicacao_2" for="cfoforma_aplicacao_2">Parcelas</label>
            <input type="radio" id="cfoforma_aplicacao_3" name="cfoforma_aplicacao" value="3" <?php echo trim($this->view->parametros->cfoforma_aplicacao) != '' && $this->view->parametros->cfoforma_aplicacao == '3' ? 'checked="checked"' : ($this->view->parametros->cfoforma_aplicacao != '2' && $this->view->parametros->cfoforma_aplicacao != '1') ? 'checked="checked"' : '' ?> />
            <label id="lbl_cfoforma_aplicacao_3" for="cfoforma_aplicacao_3">Todos</label>
        </fieldset>

        <fieldset class="maior">
            <legend id="lgd_cfoforma_inclusao">Forma de inclusão</legend>
            <input type="radio" id="cfoforma_inclusao_1" name="cfoforma_inclusao" value="1" <?php echo trim($this->view->parametros->cfoforma_inclusao) != '' && $this->view->parametros->cfoforma_inclusao == '1' ? 'checked="checked"' : ''  ?> />
            <label id="lbl_cfoforma_inclusao_1" for="cfoforma_inclusao_1">Manual</label>
            <input type="radio" id="cfoforma_inclusao_2" name="cfoforma_inclusao" value="2" <?php echo trim($this->view->parametros->cfoforma_inclusao) != '' && $this->view->parametros->cfoforma_inclusao == '2' ? 'checked="checked"' : '' ?>/>
            <label id="lbl_cfoforma_inclusao_2" for="cfoforma_inclusao_2">Automático</label>
            <input type="radio" id="cfoforma_inclusao_3" name="cfoforma_inclusao" value="3" <?php echo trim($this->view->parametros->cfoforma_inclusao) != '' && $this->view->parametros->cfoforma_inclusao == '3' ? 'checked="checked"' : ($this->view->parametros->cfoforma_inclusao != '2' && $this->view->parametros->cfoforma_inclusao != '1') ? 'checked="checked"' : '' ?> />
            <label id="lbl_cfoforma_inclusao_3" for="cfoforma_inclusao_3">Todos</label>
        </fieldset>

        <div class="clear"></div>


        <div class="campo maior">
            <label id="lbl_cfocfmcoid" for="cfocfmcoid">Motivo do crédito</label>
            <select id="cfocfmcoid" name="cfocfmcoid[]" multiple="multiple">
                <option <?php echo is_array($this->view->parametros->cfocfmcoid) && in_array('-1', $this->view->parametros->cfocfmcoid) ? 'selected="selected"' : '' ?> value="-1">Todos</option>
                <?php if (isset($this->view->parametros->motivoDoCredito) && count($this->view->parametros->motivoDoCredito) > 0) : ?>
                    <?php foreach ($this->view->parametros->motivoDoCredito as $item) : ?>
                        <option <?php echo is_array($this->view->parametros->cfocfmcoid) && in_array($item->cfmcoid, $this->view->parametros->cfocfmcoid) ? 'selected="selected"' : '' ?> value="<?php echo $item->cfmcoid ?>"><?php echo $item->cfmcdescricao ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="clear"></div>

    </div>
</div>

<div class="bloco_acoes">
    <button type="submit" id="bt_pesquisar">Pesquisar</button>
    <button type="button" id="bt_gerar_xls">Gerar XLS</button>
    <button type="button" id="bt_enviar_email">Enviar e-mail</button>
</div>