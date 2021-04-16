    <?php  require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/aba_detalhes_modificacao/sub_abas.php"; ?>
    <div class="bloco_titulo">Detalhes da Modificação</div>
    <div class="bloco_conteudo">
            <div class="separador"></div>

            <div class="bloco_titulo">Pesquisa</div>
                <div id="msg_confirmar_excluir_acessorio" class="invisivel">Deseja realmente excluir este(s) acessório(s)?</div>
                <div class="bloco_conteudo">
                    <div class="formulario">
                        <form id="form_pesquisa_acessorio" method="post" action="">
                            <input type="hidden" id="acao" name="acao" value="pesquisarAcessorio">
                            <input type="hidden" id="mdfoid_form_pesquisa" name="mdfoid" value="<?php echo $this->view->parametros->mdfoid;?>">
                            <input type="hidden" id="mdfstatus_form_pesquisa" name="mdfstatus" value="<?php echo $this->view->parametros->mdfstatus;?>">
                            <input type="hidden" id="is_nao_autorizar" name="is_nao_autorizar" value="t">
                            <div class="campo menor">
                                <label for="connumero">Contrato</label>
                                <input type="text" id="connumero" name="connumero" class="campo numerico" value="<?php echo $this->view->parametros->connumero;?>" >
                            </div>
                            <div class="campo menor">
                                <label for="veiplaca">Placa</label>
                                <input type="text" id="veiplaca" name="veiplaca" class="campo" value="<?php echo $this->view->parametros->veiplaca;?>" >
                            </div>
                            <div class="campo medio">
                                <label for="veichassi">Chassi</label>
                                <input type="text" id="veichassi" name="veichassi" class="campo" value="<?php echo $this->view->parametros->veichassi;?>" >
                            </div>
                            <div class="clear"></div>
                            <div class="campo maior">
                                <label for="obroid">Acessório</label>
                                <select id="obroid" name="obroid">
                                    <option value="">Escolha</option>
                                    <?php foreach ($this->view->comboAcessorio as $dados): ?>
                                            <option value="<?php echo $dados->obroid; ?>"
                                                    <?php echo ($this->view->parametros->obroid == $dados->obroid) ? ' selected="true"' : '' ; ?>>
                                            <?php echo $dados->obrobrigacao; ?>
                                            </option>
                                    <?php endForeach; ?>
                                </select>
                            </div>
                            <div class="clear"></div>
                        </form>
                    </div>
                </div>
                 <div class="bloco_acoes">
                    <button type="button" id="btn_pesquisar_acessorios">Pesquisar</button>
                </div>
            <div class="separador"></div>

                <div id="lista_acessorios" class="<?php echo (empty($this->view->parametros->acessorios)) ? 'invisivel' : ''; ?>">
                <div class="bloco_titulo">Acessórios</div>
                <div class="bloco_conteudo">
                     <div class="listagem">
                        <form id="form_acessorio" method="post" action="">
                            <input type="hidden" id="acao_excluir" name="acao" value="excluirAcessorio">
                            <input type="hidden" id="mdfoid" name="mdfoid" value="<?php echo $this->view->parametros->mdfoid;?>">
                            <input type="hidden" id="mdfstatus" name="mdfstatus" value="<?php echo $this->view->parametros->mdfstatus;?>">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="selecao"><input id="selecao_todos_autorizar" type="checkbox" data-bloco="autorizar"></th>
                                        <th class="menor centro">Contrato</th>
                                        <th class="maior centro">Acessório</th>
                                        <th class="medio centro">Tipo de Negociação</th>
                                        <th class="menor centro">Valor da Parcela</th>
                                        <th class="menor centro">Quantidade</th>
                                        <th class="menor centro">Placa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach ($this->view->parametros->acessorios as $dados):
                                        $cor = ($cor=="par") ? "impar" : "par";
                                    ?>
                                        <tr class="<?php echo $cor; ?>" id="">
                                             <td class="centro">
                                                <?php if($this->view->parametros->mdfstatus == 'A'): ?>
                                                    <input name="contratos_marcados[]" data-bloco="autorizar" type="checkbox" value="<?php echo $dados->cmsoid; ?>"/>
                                                <?php endIf; ?>
                                            </td>
                                            <td class="direita">
                                               <?php echo $dados->cmfconnumero; ?>
                                            </td>
                                            <td class="esquerda">
                                               <?php echo $dados->obrobrigacao; ?>
                                            </td>
                                            <td class="esquerda">
                                               <?php echo $this->view->tipoNegociacao[$dados->cmssituacao]; ?>
                                            </td>
                                            <td class="direita">
                                               R$ <?php echo (empty($dados->cmsvalor_negociado)) ? '0,00' : $dados->cmsvalor_negociado ; ?>
                                            </td>
                                            <td class="direita">
                                               <?php echo $dados->cmsqtde; ?>
                                            </td>
                                            <td class="direita">
                                               <?php echo $dados->veiplaca; ?>
                                            </td>
                                        </tr>
                                    <?php endForeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="12">
                                            <button type="button" id="btn_excluir_acessorio" disabled="true" class="desabilitado">Excluir</button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                         </form>
                    </div>
                </div>
                <div class="separador"></div>
            </div>
    </div>
    </div>
    <div class="bloco_acoes">
            <button type="button" id="btn_voltar">Voltar</button>
    </div>