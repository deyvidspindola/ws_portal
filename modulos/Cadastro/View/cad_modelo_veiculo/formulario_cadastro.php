<div class="bloco_titulo">Cadastro</div>
<div class="bloco_conteudo">
    <div class="formulario">
          <div class="campo">
            <label for="mlomcaoid">Marca *</label>
            <select id="mlomcaoid" name="mlomcaoid" class="medio obrigatorio">
                <option value="">Escolha</option>
                <?foreach ($this->view->listaMarcas as $row) { ?>
                    <option value="<?echo $row->mcaoid;?>" <?echo ($this->view->parametros->mlomcaoid == $row->mcaoid ? "SELECTED" : "") ?>>
                        <?echo $row->mcamarca;?>
                    </option>
                <?}?>
            </select>
        </div>
        <div class="campo">
            <button type="button" id="bt_nova_marca">+</button>
        </div>


        <div class="campo">
            <label for="mlomcfoid">Marca Família</label>
            <select id="mlomcfoid" name="mlomcfoid" class="medio">
                <option value="">Escolha</option>
                <?foreach ($this->view->listaMarcaFamilia as $row) { ?>
                    <option value="<?echo $row->mcfoid;?>" <?php echo ($this->view->parametros->mlomcfoid == $row->mcfoid ? "SELECTED" : "") ?>>
                        <?echo $row->mcffamilia;?>
                    </option>
                <?}?>
            </select>
        </div>
        <div class="clear"></div>


        <div class="campo">
            <label for="mlomodelo">Modelo *</label>
            <input id="mlomodelo" name="mlomodelo" type="text" class="campo maior texto upper obrigatorio" maxlength="50"
            value="<?php echo $this->view->parametros->mlomodelo; ?>" >
        </div>

        <div class="campo">
            <label for="mlopaisoid">País *</label>
            <select id="mlopaisoid" name="mlopaisoid" class="menor obrigatorio">
                <option value="">Escolha</option>
                <?foreach ($this->view->listaPaises as $row) { ?>
                    <option value="<?echo $row->paisoid;?>" <?php echo ($this->view->parametros->mlopaisoid == $row->paissigla ? "SELECTED" : "") ?>>
                        <?echo $row->paisnome;?>
                    </option>
                <?}?>
            </select>
        </div>

        <div class="clear"></div>

        <div class="campo">
            <label for="mlotipveioid">Tipo de Veículo *</label>
            <select id="mlotipveioid" name="mlotipveioid" class="medio obrigatorio">
                <option value="">Escolha</option>
                <?foreach ($this->view->listaTipoVeiculo as $row) { ?>
                    <option value="<?echo $row->tipvoid;?>" <?php echo ($this->view->parametros->mlotipveioid == $row->tipvoid ? "SELECTED" : "") ?>>
                        <?echo $row->tipvdescricao;?>
                    </option>
                <?}?>
            </select>
        </div>
        <div class="campo">
            <label for="mlovstoid">Subtipo de Veículo *</label>
            <select id="mlovstoid" name="mlovstoid" class="medio obrigatorio">
                <option value="">Escolha</option>
                <?foreach ($this->view->listaSubTipoVeiculo as $row) { ?>
                    <option value="<?echo $row->vstoid;?>" <?php echo ($this->view->parametros->mlovstoid == $row->vstoid ? "SELECTED" : "") ?>>
                        <?echo $row->vstdescricao;?>
                    </option>
                <?}?>
            </select>
        </div>
        <div class="clear"></div>

         <div class="campo maior">
             <label for="mlodica">Dicas de Instalação</label>
             <textarea id="mlodica" name="mlodica" rows="6" class="texto"><?php echo $this->view->parametros->mlodica; ?></textarea>
         </div>
         <div class="clear"></div>

          <fieldset class="">
            <legend>Particularidades</legend>
            <input type="checkbox" value="t" id="mloconversor" name="mloconversor"
            <?php echo  ($this->view->parametros->mloconversor == 't') ? 'checked="true"' : '';?> >
            <label for="mloconversor">Conversor</label>

            <input type="checkbox" value="t" id="mlovalvula" name="mlovalvula"
            <?php echo  ($this->view->parametros->mlovalvula == 't') ? 'checked="true"' : '';?> >
            <label for="mlovalvula">Válvula</label>

            <input type="checkbox" value="t" id="mlosensor_volvo" name="mlosensor_volvo"
            <?php echo  ($this->view->parametros->mlosensor_volvo == 't') ? 'checked="true"' : '';?> >
            <label for="mlosensor_volvo">Sensor Volvo</label>

            <input type="checkbox" value="t" id="mlobloqueio" name="mlobloqueio"
            <?php echo  ($this->view->parametros->mlobloqueio == 't') ? 'checked="true"' : '';?> >
            <label for="mlobloqueio">Bloqueio</label>

             <input type="checkbox" value="t" id="mlosleep" name="mlosleep"
            <?php echo  ($this->view->parametros->mlosleep == 't') ? 'checked="true"' : '';?> >
            <label for="mlosleep">Sleep</label>
        </fieldset>

         <fieldset class="menor">
            <legend>Modelo Ativo?</legend>
            <input type="radio" value="t" id="mlostatus_ativo" name="mlostatus"
            <?php echo  ($this->view->parametros->mlostatus == 't') ? 'checked="true"' : '';?> >
            <label for="mlostatus_ativo">Sim</label>
            <input type="radio" value="f" id="mlostatus_inativo" name="mlostatus"
            <?php echo  ($this->view->parametros->mlostatus == 'f') ? 'checked="true"' : '';?> >
            <label for="mlostatus_inativo">Não</label>
        </fieldset>
        <div class="clear"></div>

        <div id="valvula" class="<?php echo ($this->view->parametros->mlovalvula == 't') ? '' : 'invisivel' ;?>">
            <div class="campo medio">
                 <label for="mlovlmoid1">Modelo Válvula 1 *</label>
                 <select id="mlovlmoid1" name="mlovlmoid1" class="medio">
                    <option value="">Escolha</option>
                    <?foreach ($this->view->listaValvula as $row) { ?>
                        <option value="<?echo $row->vlmoid;?>" <?php echo ($this->view->parametros->mlovlmoid1 == $row->vlmoid ? "SELECTED" : "") ?>>
                            <?echo $row->vlmdescricao;?>
                        </option>
                    <?}?>
                </select>
            </div>
            <div class="campo medio">
                 <label for="mlovlmoid2">Modelo Válvula 2</label>
                 <select id="mlovlmoid2" name="mlovlmoid2" class="medio">
                    <option value="">Escolha</option>
                    <?foreach ($this->view->listaValvula as $row) { ?>
                        <option value="<?echo $row->vlmoid;?>" <?php echo ($this->view->parametros->mlovlmoid2 == $row->vlmoid ? "SELECTED" : "") ?>>
                            <?echo $row->vlmdescricao;?>
                        </option>
                    <?}?>
                </select>
            </div>
            <div class="campo medio">
                 <label for="mlovlmoid3">Modelo Válvula 3</label>
                 <select id="mlovlmoid3" name="mlovlmoid3" class="medio">
                    <option value="">Escolha</option>
                    <?foreach ($this->view->listaValvula as $row) { ?>
                        <option value="<?echo $row->vlmoid;?>" <?php echo ($this->view->parametros->mlovlmoid3 == $row->vlmoid ? "SELECTED" : "") ?>>
                            <?echo $row->vlmdescricao;?>
                        </option>
                    <?}?>
                </select>
            </div>
        </div>
        <div class="clear"></div>

        <input id="retModelo" type="hidden" name="retModelo" value="<?php echo str_replace("=","%3D",str_replace("&","%26",$this->view->parametros->retModelo)); ?>">
        <input id="url_retorno" type="hidden" name="url_retorno" value="<?php echo trata_retorno($this->view->parametros->retModelo,$this->view->parametros->mlooid); ?>">
    </div>
</div>

<div class="separador"></div>

<div class="bloco_titulo">Particularidades FIPE</div>
<div class="bloco_conteudo">
    <div class="formulario">

        <div class="campo">
            <label for="mlofipe_codigo">Código FIPE</label>
            <input id="mlofipe_codigo" name="mlofipe_codigo" type="text" class="campo menor texto" maxlength="50" 
            value="<?php echo $this->view->parametros->mlofipe_codigo; ?>">
        </div>

        <div class="campo">
            <label for="mlocatbase_codigo">Código da Categoria</label>
            <input id="mlocatbase_codigo" name="mlocatbase_codigo" type="text" class="campo menor texto" maxlength="50"
            value="<?php echo $this->view->parametros->mlocatbase_codigo; ?>">
        </div>

        <div class="campo">
            <label for="mlocatbase_descricao">Descrição da Categoria</label>
            <input id="mlocatbase_descricao" name="mlocatbase_descricao" type="text" class="campo medio texto" maxlength="50"
            value="<?php echo $this->view->parametros->mlocatbase_descricao; ?>">
        </div>
        <div class="clear"></div>

        <div class="campo">
            <label for="mlonumpassag">Número de Passageiros</label>
            <input id="mlonumpassag" name="mlonumpassag" type="text" class="campo medio numerico" maxlength="2"
            value="<?php echo $this->view->parametros->mlonumpassag; ?>">
        </div>

        <div class="campo">
            <label for="mloprocedencia">Procedência</label>
            <input id="mloprocedencia" name="mloprocedencia" type="text" class="campo medio texto" maxlength="50"
            value="<?php echo $this->view->parametros->mloprocedencia; ?>">
        </div>

        <div class="clear"></div>
    </div>
</div>

<div class="separador"></div>
<div class="bloco_titulo">Acessórios Para Instalação</div>
<div class="bloco_conteudo">
<div class="separador"></div>
        <div class="bloco_conteudo">
            <div class="formulario">
                <div id="mensagem_alerta_item" class="mensagem alerta invisivel">
                    <?php echo $this->view->mensagemAlerta; ?>
                </div>
                  <div class="campo maior">
                         <label for="mlaiobroid">Acessório *</label>
                         <select id="mlaiobroid" name="mlaiobroid" class="medio">
                            <option value="">Escolha</option>
                            <?foreach ($this->view->listaListaAcessorio as $row) { ?>
                                <option value="<?echo $row->obroid;?>" <?php echo ( isset($this->view->dados_itens[$row->obroid])  ? 'disabled="true"' : "") ?> >
                                    <?echo $row->obrobrigacao;?>
                                </option>
                            <?}?>
                        </select>
                    </div>
                    <div class="clear"></div>

                    <div class="campo">
                         <label for="mlaiano_inicial">Ano Inicial</label>
                         <select id="mlaiano_inicial" name="mlaiano_inicial" class="menor">
                            <option value="">Escolha</option>
                            <?foreach ($this->view->listaAnos as $key => $value ) { ?>
                                <option value="<?echo $key;?>">
                                    <?echo $value;?>
                                </option>
                            <?}?>
                        </select>
                    </div>
                     <div class="campo">
                         <label for="mlaiano_final">Ano Final</label>
                          <select id="mlaiano_final" name="mlaiano_final" class="menor">
                            <option value="">Escolha</option>
                            <?foreach ($this->view->listaAnos as $key => $value ) { ?>
                                <option value="<?echo $key;?>">
                                    <?echo $value;?>
                                </option>
                            <?}?>
                        </select>
                    </div>

                    <fieldset class="menor">
                        <input type="checkbox" value="t" id="mlaiinstala_cliente" name="mlaiinstala_cliente" checked="true">
                        <label for="mlaiinstala_cliente">Cliente</label>
                        <input type="checkbox" value="t" id="mlaiinstala_seguradora" name="mlaiinstala_seguradora" checked="true">
                        <label for="mlaiinstala_seguradora">Seguradora</label>
                    </fieldset>
                    <div class="clear"></div>

                     <div class="campo maior">
                         <label for="copiar">Marca / Modelo </label>
                         <select id="copiar" name="copiar" class="medio">
                            <option value="">Escolha</option>
                            <?foreach ($this->view->listaMarcaModelo as $row) { ?>
                                <option value="<?echo $row->mlooid;?>" <?php echo ( $this->view->parametros->mlooid == $row->mlooid )  ? 'disabled="true"' : "";?>>
                                    <?echo $row->marca_modelo;?>
                                </option>
                            <?}?>
                        </select>
                    </div>
                    <button type="button" id="bt_copiar" class="desabilitado">Copiar</button>
                    <div class="clear"></div>
            </div>
        </div>
        <div class="bloco_acoes">
            <button type="button" id="bt_adicionar">Adicionar Item</button>
        </div>

        <div class="clear"></div>
        <div class="separador"></div>
    <?php require_once _MODULEDIR_ . "Cadastro/View/cad_modelo_veiculo/resultado_acessorios.php"; ?>

</div>


<div class="separador"></div>
<div class="bloco_acoes">
    <button <?php echo ( $this->permissaoCadastro ) ? '' : "disabled='disabled'";?> type="button" id="bt_gravar" name="bt_gravar">Gravar</button>
    <button type="button" id="bt_voltar">Voltar</button>

     <?php if ($this->view->parametros->retModelo != '' ):?>
        <button type="button" id="bt_retornar">Sair</button>
    <?php endif; ?>
</div>