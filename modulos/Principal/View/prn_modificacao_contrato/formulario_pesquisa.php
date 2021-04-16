 <div class="bloco_titulo">Dados para Pesquisa</div>
    <div class="bloco_conteudo">
        <div class="formulario">
            <form action="" id="form_modificacao_pesquisa" method="post">
                <input type="hidden" id="acao" name="acao" value="pesquisar" />

                <div class="campo data periodo">
                    <div class="inicial">
                        <label for="data_inicial">Período</label>
                        <input id="data_inicial" name="data_inicial" maxlength="10"
                                value="<?php echo $this->view->parametros->data_inicial;?>" class="campo" type="text">
                    </div>
                    <div class="campo label-periodo">à</div>
                    <div class="final">
                        <label for="data_final">&nbsp;</label>
                        <input id="data_final" name="data_final" maxlength="10"
                                value="<?php echo $this->view->parametros->data_final;?>" class="campo" type="text">
                    </div>
                </div>
                 <div class="campo menor">
                    <label for="mdfoid_pesq">Nº da Modificação</label>
                     <input id="mdfoid_pesq" name="mdfoid_pesq" value="<?php echo $this->view->parametros->mdfoid_pesq;?>" class="campo numerico" type="text">
                </div>
                <div class="clear"></div>

                <div class="campo medio">
                    <label for="cmgoid">Grupo Modificação</label>
                    <select id="cmgoid" name="cmgoid">
                        <option value="">Escolha</option>
                        <?php foreach ($this->view->comboGrupoModificacao as $dados): ?>
                            <option value="<?php echo $dados->cmgoid; ?>" <?php echo ($this->view->parametros->cmgoid == $dados->cmgoid) ? 'selected="true"' : '' ; ?>>
                                <?php echo $dados->cmgdescricao; ?>
                            </option>
                        <?php endForeach; ?>
                    </select>
                </div>

                <div class="campo medio">
                    <label for="cmtoid">Tipo Modificação</label>
                    <select id="cmtoid" name="cmtoid">
                        <option value="">Escolha</option>
                    </select>
                    <img id="img_cmtoid" class="carregando invisivel" src="images/ajax-loader-circle.gif">
                    <input type="hidden" id="cmtoid_recarga_tela" name="cmtoid_recarga_tela" value="<?php echo $this->view->parametros->cmtoid;?>" />
                </div>
                <div class="clear"></div>

                <div class="campo medio">
                    <label for="msubdescricao">Motivo Substituição <br>(mínimo três letras para a autopesquisa)</label>
                    <input type="text" id="msubdescricao" name="msubdescricao" value="<?php echo $this->view->parametros->msubdescricao;?>" class="campo"/>
                    <input type="hidden" id="mdfmsuboid" name="mdfmsuboid" value="<?php echo $this->view->parametros->mdfmsuboid;?>" />
                </div>
                <div class="clear"></div>

                <div class="campo medio">
                    <label for="status">Status Modificação</label>
                    <select id="status" name="status">
                        <option value="">Escolha</option>
                        <?php foreach ($this->view->legenda_status as $chave => $valor): ?>
                            <option value="<?php echo $chave; ?>" <?php echo ($chave == $this->view->parametros->status) ? 'selected="true"' : '' ; ?>>
                                <?php echo $valor; ?>
                            </option>
                        <?php endForeach; ?>
                    </select>
                </div>

                <div class="campo medio">
                    <label for="status_financeiro">Status Financeiro</label>
                    <select id="status_financeiro" name="status_financeiro">
                        <option value="">Escolha</option>
                         <?php foreach ($this->view->legenda_status_financeiro as $chave => $valor): ?>
                            <option value="<?php echo $chave; ?>" <?php echo ($chave == $this->view->parametros->status_financeiro) ? 'selected="true"' : '' ; ?>>
                                <?php echo $valor; ?>
                            </option>
                        <?php endForeach; ?>
                    </select>
                </div>
                <div class="clear"></div>

                <div class="campo medio">
                    <label for="depoid">Departamento</label>
                    <select id="depoid" name="depoid">
                        <option value="">Escolha</option>
                        <?php foreach ($this->view->comboDepartamento as $dados): ?>
                            <option value="<?php echo $dados->depoid; ?>" <?php echo ($this->view->parametros->depoid == $dados->depoid) ? 'selected="true"' : '' ; ?>>
                                <?php echo $dados->depdescricao; ?>
                            </option>
                        <?php endForeach; ?>
                    </select>
                </div>

                <div class="campo medio">
                    <label for="mdfusuoid_cadastro">Usuário</label>
                    <select id="mdfusuoid_cadastro" name="mdfusuoid_cadastro">
                        <option value="">Escolha</option>
                    </select>
                    <img id="img_usuario" class="carregando invisivel" src="images/ajax-loader-circle.gif">
                    <input type="hidden" id="usuoid_recarga_tela" name="usuoid_recarga_tela" value="<?php echo $this->view->parametros->mdfusuoid_cadastro;?>" />
                </div>
                <div class="clear"></div>

                <div class="campo medio">
                    <label for="clinome_pesq">Cliente <br> (mínimo três letras para a autopesquisa)</label>
                    <input id="clinome_pesq" name="clinome_pesq" value="<?php echo $this->view->parametros->clinome_pesq;?>" class="campo" type="text">
                    <input id="clioid_pesq" name="clioid_pesq" value="<?php echo $this->view->parametros->clioid_pesq;?>" class="campo" type="hidden">
                </div>

                <div class="campo medio">
                    <label for="texto">&nbsp; <br> Contrato</label>
                    <input id="connumero" name="connumero" value="<?php echo $this->view->parametros->connumero;?>" class="campo numerico" type="text">
                </div>
                <div class="clear"></div>

                <div class="campo medio">
                    <label for="chassi">Chassi</label>
                    <input id="chassi" name="chassi" value="<?php echo $this->view->parametros->chassi;?>" class="campo" type="text" maxlength="18">
                </div>

                <div class="campo medio">
                    <label for="placa">Placa</label>
                    <input id="placa" name="placa" value="<?php echo $this->view->parametros->placa;?>" class="campo" type="text" maxlength="15">
                </div>
                <div class="clear"></div>

                <fieldset class="medio opcoes-inline">
                    <legend>Tipo resultado</legend>
                    <input id="resultado_tela" name="tipo_resultado" value="T" type="radio"
                            <?php echo ($this->view->parametros->tipo_resultado == 'T') ? 'checked="checked"' : (empty($this->view->parametros->resultado_tela) ? 'checked="checked"' :'') ; ?>
                            /><label for="resultado_tela">Tela</label>
                    <input id="resultado_csv" name="tipo_resultado" value="A" type="radio"
                            <?php echo ($this->view->parametros->tipo_resultado == 'A') ? 'checked="checked""' : '' ; ?>
                            /><label for="resultado_csv">CSV</label>
                </fieldset>
                <div class="clear"></div>
            </form>
        </div>
    </div>
    <div class="bloco_acoes">
        <button type="button" id="btn_pesquisar">Pesquisar</button>
        <button type="button" id="btn_novo" <?php echo isset($_SESSION['funcao']['permite_modificacao_contrato']) ? '' : 'disabled'; ?>>Novo</button>
    </div>
    <div class="separador"></div>







