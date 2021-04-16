    <div class="mensagem alerta invisivel" id="msg_alerta_autocomplete"></div>
    <div class="bloco_titulo">Pesquisa</div>
        <div class="bloco_conteudo">
            <div class="formulario">

                <div class="campo data periodo">
                    <div class="inicial">
                        <label for="data_inicial">Data inicial</label>
                        <input type="text" id="data_inicial" name="data_inicial" value="<?php echo $this->view->parametros->data_inicial;?>" class="campo" />
                    </div>
                    <div class="campo label-periodo">à</div>
                    <div class="final">
                        <label for="data_final">Data final</label>
                        <input type="text" id="data_final" name="data_final" value="<?php echo $this->view->parametros->data_final;?>" class="campo" />
                    </div>
                </div>
                <div class="clear"></div>

                <div class="campo maior">
                	<label for="usuoid_psq">Atendente</label>
                	<select id="usuoid_psq" name="usuoid_psq" 
                            class="<?php echo ($this->permissao) ? '' : 'desabilitado';?>"
                            <?php echo ($this->permissao) ? '' : 'disabled="true"';?>
                        >
                		<option value="">Escolha</option>
                        <?php foreach($this->view->comboAtendente as $atendente): ?>
                            <option value="<?php echo $atendente->id ?>"
                                <?php echo ($this->view->parametros->usuoid_psq == $atendente->id) ? 'selected="true"' : '' ; ?>>
                                <?php echo $atendente->nome ?>
                            </option>
                        <?php endForeach;?>
                	</select>
                </div>
                <div class="clear"></div>

                 <div class="campo maior">
                    <label for="repnome_psq">Representante (mínimo três letras para a autopesquisa)</label>
                    <input type="text" id="repnome_psq" name="repnome_psq" class="campo maior representante" 
                            value="<?php echo $this->view->parametros->repnome_psq;?>"/>
                    <input type="hidden" id="aprrepoid" name="aprrepoid" value="<?php echo $this->view->parametros->aprrepoid;?>"/>
                </div>
                <div class="clear"></div>

                <fieldset class="maior">
                    <legend>Vínculos</legend>
                    <input type="radio" id="registros_ativos" name="registros" value="A" 
                            <?php echo ($this->view->parametros->registros == 'A') ? 'checked="checked"' : (empty($this->view->parametros->registros) ? 'checked="checked"' :'') ; ?> />
                    <label for="registros_ativos">Ativos</label>

                    <input type="radio" id="registros_inativos" name="registros" value="I" 
                            <?php echo ($this->view->parametros->registros == 'I') ? 'checked="checked""' : '' ; ?>/>
                    <label for="registros_inativos">Inativos</label>

                    <input type="radio" id="registros_todos" name="registros" value="T" 
                            <?php echo ($this->view->parametros->registros == 'T') ? 'checked="checked""' : '' ; ?>/>
                    <label for="registros_todos">Todos</label>
                </fieldset>
                <div class="clear"></div>
            </div>
        </div>
        <div class="bloco_acoes">
            <?php if(!$this->view->bloqueioBotoes): ?>
                <button type="button" id="btn_pesquisar">Pesquisar</button>
                <button type="button" id="btn_vincular">Vincular Perfil</button>
            <?php endIf; ?>
        </div>