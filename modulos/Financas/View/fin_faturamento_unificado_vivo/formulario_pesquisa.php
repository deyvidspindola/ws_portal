<div class="bloco_titulo">Dados para Pesquisa</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo <?php if (!$this->view->processoExecutando) : ?>mes_ano<?php endif; ?>">
            <label for="dataReferencia">Referência *</label>
            <input id="dataReferencia" class="campo" type="text" value="<?php echo $this->view->parametros->dataReferencia ?>" name="dataReferencia" <?php if ($this->view->processoExecutando) : ?>disabled="disabled"<?php endif; ?>>
        </div>
        <div class="clear"></div>

        <fieldset class="medio" <?php if ($this->view->processoExecutando) : ?>disabled="disabled"<?php endif; ?>>
            <legend>Tipo de Contrato</legend>
            <input id="tipoContratoLocacao" type="radio" value="L" name="tipoContrato" <?php echo ( ($this->view->parametros->tipoContrato == 'L' || $this->view->parametros->tipoContrato != 'V') ? 'checked="checked"' : '') ?>>
            <label for="tipoContratoLocacao">Locação</label>
            <input id="tipoContratoVenda" type="radio" value="V" name="tipoContrato" <?php echo ( $this->view->parametros->tipoContrato == 'V' ? 'checked="checked"' : '') ?>>
            <label for="tipoContratoVenda">Venda</label>
        </fieldset>
        <div class="clear"></div>

        <fieldset class="medio" <?php if ($this->view->processoExecutando) : ?>disabled="disabled"<?php endif; ?>>
            <legend>Serviços Faturados</legend>
            <input id="servicosFaturadosLocacao" type="radio" value="L" name="servicosFaturados" <?php echo ( ($this->view->parametros->servicosFaturados == 'L' || !in_array($this->view->parametros->servicosFaturados, array('T', 'A')) ) ? 'checked="checked"' : '') ?>>
            <label for="servicosFaturadosLocacao">Locação</label>
            <input id="servicosFaturadosTaxas" type="radio" value="T" name="servicosFaturados" <?php echo ( $this->view->parametros->servicosFaturados == 'T' ? 'checked="checked"' : '') ?>>
            <label for="servicosFaturadosTaxas">Taxas</label>
        </fieldset>
        <div class="clear"></div>

        <!--  COMPONENTE  DE PESQUISA DE CLIENTES -->
        <!--já existente-->

        <fieldset class="medio" <?php if ($this->view->processoExecutando) : ?>disabled="disabled"<?php endif; ?>>
            <legend>Tipo</legend>
            <input type="radio" id="tipoPessoaFisica" name="tipoPessoa" value="F" <?php echo ( $this->view->parametros->tipoPessoa == 'F' ? 'checked="checked"' : '') ?> />
            <label for="tipoPessoaFisica">Física</label>
            <input type="radio" id="tipoPessoaJuridica" name="tipoPessoa" value="J" <?php echo ( ( $this->view->parametros->tipoPessoa == 'J' || $this->view->parametros->tipoPessoa != 'F' ) ? 'checked="checked"' : '' ) ?>  />
            <label for="tipoPessoaJuridica">Jurídica</label>
        </fieldset>
        <div class="clear"></div>

        <div class="campo medio">
            <div id="juridicaNome" <?php echo ( ($this->view->parametros->tipoPessoa == 'J' || $this->view->parametros->tipoPessoa != 'F') ? 'class="visivel"' : 'class="invisivel"' ) ?>>
                <label for="razaoSocial" class="razaoSocial">Nome do Cliente</label>
                <input type="text" id="razaoSocial" maxlength="250" name="razaoSocial" value="<?php echo trim($this->view->parametros->razaoSocial); ?>" class="campo razaoSocial" <?php if ($this->view->processoExecutando) : ?>disabled="disabled"<?php endif; ?>/>
            </div>
            <div id="fisicaNome" <?php echo ($this->view->parametros->tipoPessoa == 'F') ? 'class="visivel"' : 'class="invisivel"' ?>>
                <label for="nomeCliente" class="nomeCliente">Nome do Cliente</label>
                <input type="text" id="nomeCliente" name="nomeCliente" maxlength="250" value="<?php echo trim($this->view->parametros->nomeCliente); ?>" class="campo nomeCliente" <?php if ($this->view->processoExecutando) : ?>disabled="disabled"<?php endif; ?>/>
            </div>
        </div>
        <div class="clear"></div>

        <div class="campo medio">
            <div id="juridicaDoc" <?php echo ( ($this->view->parametros->tipoPessoa == 'J' || $this->view->parametros->tipoPessoa != 'F') ? 'class="visivel"' : 'class="invisivel"' ) ?>>
                <label for="cnpj" class="cnpj">CNPJ</label>
                <input type="text" id="cnpj" name="cnpj" value="<?php echo trim($this->view->parametros->cnpj) ?>" class="campo cnpj" <?php if ($this->view->processoExecutando) : ?>disabled="disabled"<?php endif; ?>/>
            </div>

            <div id="fisicaDoc" <?php echo ($this->view->parametros->tipoPessoa == 'F') ? 'class="visivel"' : 'class="invisivel"' ?>>
                <label for="cpf" class="cpf">CPF</label>
                <input type="text" id="cpf" name="cpf" value="<?php echo trim($this->view->parametros->cpf) ?>" class="campo cpf" <?php if ($this->view->processoExecutando) : ?>disabled="disabled"<?php endif; ?>/>
            </div>
        </div>
        <!--  FIM COMPONENTE -->

        <div class="campo menor">
            <label for="placa">Placa</label>
            <input id="placa" class="campo" name="placa" maxlength="8" type="text" value="<?php echo trim($this->view->parametros->placa) ?>" <?php if ($this->view->processoExecutando) : ?>disabled="disabled"<?php endif; ?>/>
        </div>
        <div class="clear"></div>


    </div>
</div>

<div class="bloco_acoes">
    <button type="button" id="bt_limparResumo" <?php if ($this->view->processoExecutando) : ?>disabled="disabled"<?php endif; ?>>Limpar Resumo</button>
    <button type="button" id="bt_gerarResumo" <?php if ($this->view->processoExecutando) : ?>disabled="disabled"<?php endif; ?>>Gerar Resumo</button>
    <button type="button" id="bt_consultarResumo" <?php if ($this->view->processoExecutando) : ?>disabled="disabled"<?php endif; ?>>Consultar Resumo</button>
    <button type="button" id="bt_pararResumo" class="<?php if (!$this->view->processoExecutando || (isset($this->view->parametrosProcesso) && $this->view->parametrosProcesso->efvtipo_processo == 'F') ) : ?>invisivel<?php endif; ?>">Parar Resumo</button>
</div>







