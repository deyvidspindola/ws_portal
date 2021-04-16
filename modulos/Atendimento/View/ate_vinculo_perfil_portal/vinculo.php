    <div id="msg_info" class="mensagem info">Campos com * são obrigatórios</div>
    <div class="bloco_titulo">Vincular</div>
        <div class="bloco_conteudo">

            <div class="formulario">
                <div class="campo maior">
                    <label for="usuoid">Atendente *</label>
                    <select id="usuoid" name="usuoid" 
                            class="obrigatorio <?php echo ($this->permissao) ? '' : 'desabilitado';?>"
                            <?php echo ($this->permissao) ? '' : 'disabled="true"';?>
                        >
                        <option value="">Escolha</option>
                        <?php foreach($this->view->comboAtendente as $atendente): ?>
                            <option value="<?php echo $atendente->id ?>" 
                                <?php echo ($this->view->parametros->usuoid == $atendente->id) ? 'selected="true"' : '' ; ?>>
                                <?php echo $atendente->nome ?>
                            </option>
                        <?php endForeach;?>
                    </select>
                </div>
                <div class="clear"></div>

                 <div class="campo maior">
                    <label for="repnome">Representante * (mínimo três letras para a autopesquisa)</label>
                    <input type="text" id="repnome" name="repnome" class="campo maior obrigatorio representante" 
                            value="<?php echo $this->view->parametros->repnome;?>"/>
                    <input type="hidden" id="aprrepoid" name="aprrepoid" value="<?php echo $this->view->parametros->aprrepoid;?>"/>
                </div>
                <div class="clear"></div>

                <div class="campo maior">
                    <label for="aprusuoid_instalador">Instalador *</label>
                    <select id="aprusuoid_instalador" name="aprusuoid_instalador" class="obrigatorio">
                        <option value="">Escolha</option>
                    </select>
                </div>
                <div class="clear"></div>

                <div class="campo maior">
                	<label for="aprmotivo">Motivo *</label>
                	<textarea id="aprmotivo" name="aprmotivo" class="obrigatorio"></textarea>
                </div>
                <div class="clear"></div>

            </div>
        </div>
        <div class="bloco_acoes">
            <button type="button" id="btn_confirmar">Confirmar</button>
            <button type="button" id="btn_voltar">Voltar</button>
        </div>

    <div id="bloco_itens" class="invisivel">
        <div class="separador"></div>
        <div class="bloco_titulo">Perfis Vinculados</div>
        <div class="bloco_conteudo">
            <div class="listagem">
                <table>
                    <thead>
                        <tr>
                            <th class="medio">Atendente</th>
                            <th class="medio">Representante</th>
                            <th class="medio">Instalador</th>
                            <th class="menor">Data Cadastro</th>
                            <th class="maior">Motivo</th>
                            <th class="acao">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>