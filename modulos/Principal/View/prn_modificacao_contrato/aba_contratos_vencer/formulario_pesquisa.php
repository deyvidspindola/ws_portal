  <div class="bloco_titulo">Dados para Pesquisa</div>
    <div class="bloco_conteudo">
        <div class="formulario">
            <form action="" id="form_pesquisa_contratos_vencer" method="post">
                <input type="hidden" value="pesquisar" id="acao" name="acao" />
                <input type="hidden" value="contratos_vencer" id="tela_pesquisa" name="tela_pesquisa" />
                <input type="hidden" value="S" id="form_pesquisa_contratos_vencer" name="form_pesquisa_contratos_vencer" />

                <div class="campo data periodo">
                    <div class="inicial">
                        <label for="data_inicial">Período *</label>
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
                <div class="clear"></div>

                <div class="campo medio">
                    <label for="cmtoid">Tipo Modificação *</label>
                    <select id="cmtoid" name="cmtoid">
                        <option value="">Escolha</option>
                        <?php foreach ($this->view->comboTipoModificacaoContratoVencer as $dados): ?>
                            <option value="<?php echo $dados->cmtoid; ?>" <?php echo ($this->view->parametros->cmtoid == $dados->cmtoid) ? 'selected="true"' : '' ; ?>>
                                <?php echo $dados->cmtdescricao; ?>
                            </option>
                        <?php endForeach; ?>
                    </select>
                    <img id="img_cmtoid" class="carregando invisivel" src="images/ajax-loader-circle.gif">
                    <input type="hidden" id="cmtoid_recarga_tela" name="cmtoid_recarga_tela" value="<?php echo $this->view->parametros->cmtoid;?>" />
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
            </form>
        </div>
    </div>
    <div class="bloco_acoes">
        <button type="button" id="btn_pesquisar_contratos_vencer">Pesquisar</button>
        <button type="button" id="btn_novo" <?php echo isset($_SESSION['funcao']['permite_modificacao_contrato']) ? '' : 'disabled'; ?>>Novo</button>
    </div>
    <div class="separador"></div>







