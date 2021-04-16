<form id="form-nova-os" action="<?php echo _PROTOCOLO_ . _SITEURL_; ?>prn_ordem_servico.php" method="POST">
   <input type="hidden" name="acao" value="novo" />
</form> 

<form id="form-pesquisar" method="get">
    <input type="hidden" id="acao" name="acao" value="pesquisar"/>
    <div class="bloco_titulo">Dados para Pesquisa</div>
    <div class="bloco_conteudo">
        <div class="formulario">

            <div class="campo maior">
                <label id="lbl_cliente" for="cmp_cliente">Cliente <img class="btn-help" src="images/help10.gif" style="cursor: pointer" onclick="mostrarHelpComment(this,'Mínimo três letras para a auto pesquisa.','D' , '');"></label>
                <input id="cmp_cliente_autocomplete" class="campo" type="text" value="<?php echo $this->view->parametros->cmp_cliente_autocomplete; ?>" name="cmp_cliente_autocomplete">
                <input id="cmp_cliente" type="hidden" value="<?php echo $this->view->parametros->cmp_cliente; ?>" class="validar" name="cmp_cliente">
            </div>

            <div class="campo medio">
                <label id="lbl_cpf_cnpj" for="cmp_cpf_cnpj">CPF\CNPJ</label>
                <input id="cmp_cpf_cnpj" class="campo validar numerico" maxlength="14" type="text" value="<?php echo $this->view->parametros->cmp_cpf_cnpj; ?>" name="cmp_cpf_cnpj">
            </div>

            <div class="clear"></div>

            <div class="campo menor">
                <label id="lbl_numero_os" for="cmp_numero_os">O.S.</label>
                <input id="cmp_numero_os" class="campo validar numerico" maxlength="9" type="text" value="<?php echo $this->view->parametros->cmp_numero_os; ?>" name="cmp_numero_os">
            </div>

            <div class="campo data">
                <label id="lbl_data_os" for="cmp_data_os">Data O.S.</label>
                <input id="cmp_data_os" type="text" name="cmp_data_os" maxlength="10" value="<?php echo $this->view->parametros->cmp_data_os; ?>" class="campo validar" />
            </div>

            <div class="campo menor">
                <label id="lbl_tipo_servico" for="cmp_tipo_servico">Tipo de Serviço</label>
                <select id="cmp_tipo_servico" name="cmp_tipo_servico" class="validar">
                    <option value="0">Escolha</option>
                    <?php foreach ($this->view->tiposServicos as $id => $descricao): ?>
                    <option value="<?php echo $id; ?>" <?php if ($this->view->parametros->cmp_tipo_servico == $id): ?>selected="selected"<?php endif; ?>><?php echo $descricao; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo menor">
                <label id="lbl_contrato" for="cmp_contrato">Contrato</label>
                <input id="cmp_contrato" class="campo validar numerico" maxlength="10" type="text" value="<?php echo $this->view->parametros->cmp_contrato; ?>" name="cmp_contrato">
            </div>

            <div class="clear"></div>

            <div class="campo menor">
                <label id="lbl_placa" for="cmp_placa">Placa</label>
                <input id="cmp_placa" class="campo validar" maxlength="15" type="text" value="<?php echo $this->view->parametros->cmp_placa; ?>" name="cmp_placa">
            </div>

            <div class="campo medio">
                <label id="lbl_chassi" for="cmp_chassi">Chassi</label>
                <input id="cmp_chassi" type="text" name="cmp_chassi" maxlength="30" value="<?php echo $this->view->parametros->cmp_chassi; ?>" class="campo validar" />
            </div>

            <div class="clear"></div>

            <div class="campo menor">
                <label id="lbl_cep" for="cpm_cep">CEP</label>
                <input id="cpm_cep" type="text" name="cpm_cep" maxlength="9" value="<?php echo $this->view->parametros->cpm_cep; ?>" class="campo validar" />
            </div>

            <div class="campo menor">
                <label id="lbl_uf" for="cmp_uf">UF</label>
                <select id="cmp_uf" name="cmp_uf" class="validar">
                    <option value="0">Escolha</option>
                    <?php foreach ($this->view->estados as $id => $descricao): ?>
                        <option value="<?php echo $id; ?>" <?php if ($this->view->parametros->cmp_uf == $id): ?>selected="selected"<?php endif; ?>><?php echo $descricao; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo medio">
                <label id="lbl_cidade" for="cmp_cidade">Cidade</label>
                <select id="cmp_cidade" name="cmp_cidade" class="validar">
                    <option value="0">Escolha</option>
                </select>
                <img id="carregar-cidade" class="carregando esconder" src="<?php echo _PROTOCOLO_ . _SITEURL_; ?>modulos/web/images/ajax-loader-circle.gif">
            </div>

            <div class="clear"></div>

            <div class="campo data data-intervalo">
                <label id="lbl_data_agendamento" for="cmp_data_inicio">Data de Agendamento</label>
                <div class="calendario float-left">
                    <input id="cmp_data_inicio" class="campo validar" type="text" value="<?php echo $this->view->parametros->cmp_data_inicio; ?>" name="cmp_data_inicio">
                </div>
                <div class="ate float-left">
                    à
                </div>
                <div class="calendario float-left">
                    <input id="cmp_data_fim" class="campo validar" type="text" value="<?php echo $this->view->parametros->cmp_data_fim; ?>" name="cmp_data_fim">
                </div>
            </div>

            <fieldset class="medio">
                <input id="cmp_agendamento_aberto" type="checkbox" class="validar"
                    <?php if ($this->view->parametros->cmp_agendamento_aberto): ?>checked="checked"<?php endif; ?> 
                    value="1" name="cmp_agendamento_aberto">
                <label for="cmp_agendamento_aberto">Agendamento em Aberto</label>
            </fieldset>

            <div class="clear"></div>

        </div>
    </div>

    <div class="bloco_acoes">
        <button type="submit" id="bto-pesquisar">Pesquisar</button>
        <button type="button" id="bto-limpar-pesquisa">Limpar</button>
        <button type="button" id="bto-nova-os">Nova O.S.</button>
    </div>
</form>






