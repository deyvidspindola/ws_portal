                <div class="separador"></div>
                <div class="mensagem alerta invisivel" id="msg_alerta_acessorio"></div>
                <div id="msg_confirmar_excluir_acessorio" class="invisivel">Deseja realmente excluir este acessório?</div>
                <div class="bloco_titulo">Acessórios</div>
                <div class="bloco_conteudo">
                    <div class="separador"></div>
                    <div class="bloco_conteudo">
                         <div id="bloco_acessorio" class="formulario">
                            <form id="form_acessorio" method="post" action="">
                                <div class="campo medio">
                                    <label for="cmsobroid">Acessório</label>
                                    <select id="cmsobroid" name="cmsobroid" class="obrigatorio_acessorio">
                                        <option value="">Escolha</option>
                                    </select>
                                     <img id="img_cmsobroid" class="carregando invisivel" src="images/ajax-loader-circle.gif">
                                    <input type="hidden" id="cmsobroid_recarga_tela" value="<?php echo $this->view->parametros->cmsobroid;?>" >
                                </div>
                                 <div class="campo medio">
                                     <label for="cmssituacao">Tipo de Negociação</label>
                                    <select id="cmssituacao" name="cmssituacao" class="obrigatorio_acessorio">
                                        <option value="">Escolha</option>
                                        <?php foreach ($this->view->tipoNegociacao as $chave => $valor): ?>
                                            <option value="<?php echo $chave; ?>"><?php echo $valor; ?></option>
                                        <?php endForeach; ?>
                                    </select>
                                </div>
                                 <div class="clear"></div>
                                 <div class="campo medio">
                                    <label for="cmsvalor_negociado">Valor Parcela de Locação</label>
                                    <input type="text" id="cmsvalor_negociado" name="cmsvalor_negociado" value="" class="moeda campo obrigatorio_acessorio" maxlength="7">
                                    <input type="hidden" id="cmsvalor_tabela" name="cmsvalor_tabela" value="" >
                                </div>
                                <div class="campo medio">
                                     <label for="cmsqtde">Quantidade</label>
                                    <input type="text" id="cmsqtde" name="cmsqtde" value="1" class="numerico campo obrigatorio_acessorio" maxlength="4">
                                </div>
                                <div class="clear"></div>
                            </form>
                        </div>
                    </div>
                    <div class="bloco_acoes">
                        <button type="button" id="btn_adicionar_acessorio" class="">Adicionar</button>
                    </div>
                    <div class="separador"></div>

                    <div id="lista_acessorios"  class="bloco_conteudo  <?php echo (empty($this->view->parametros->acessorios)) ? 'invisivel' : '' ;?>">
                        <div class="listagem">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="maior centro">Acessório</th>
                                        <th class="medio centro">Tipo de Negociação</th>
                                        <th class="menor centro">Valor da Parcela</th>
                                        <th class="menor centro">Quantidade</th>
                                        <th class="acao">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     <?php
                                        foreach ($this->view->parametros->acessorios as $dados):
                                        $cor = ($cor=="par") ? "impar" : "par";
                                    ?>
                                        <tr class="<?php echo $cor; ?>" id="">
                                            <td class="esquerda">
                                               <?php echo $dados->obrobrigacao; ?>
                                               <input type="hidden" id="acessorio_obroid" name="acessorio_obroid[]" value="<?php echo $dados->cmsobroid; ?>">
                                               <input type="hidden" id="acessorio_nome" name="acessorio_nome[]" value="<?php echo $dados->obrobrigacao; ?>">
                                               <input type="hidden" id="acessorio_cpvoid" name="acessorio_cpvoid[]" value="<?php echo $dados->cmscpvoid; ?>">
                                            </td>
                                            <td class="esquerda">
                                               <?php echo $this->view->tipoNegociacao[$dados->cmssituacao]; ?>
                                               <input type="hidden" id="acessorio_situacao" name="acessorio_situacao[]" value="<?php echo $dados->cmssituacao; ?>">
                                            </td>
                                            <td class="direita">
                                               R$ <?php echo (empty($dados->cmsvalor_negociado)) ? '0,00' : $dados->cmsvalor_negociado ; ?>
                                               <input type="hidden" id="acessorio_valor_negociado" name="acessorio_valor_negociado[]" value="<?php echo $dados->cmsvalor_negociado; ?>">
                                               <input type="hidden" id="acessorio_valor_tabela" name="acessorio_valor_tabela[]" value="<?php echo $dados->cmsvalor_tabela; ?>">
                                            </td>
                                            <td class="direita">
                                               <?php echo $dados->cmsqtde; ?>
                                               <input type="hidden" id="acessorio_qtde" name="acessorio_qtde[]" value="<?php echo $dados->cmsqtde; ?>">
                                            </td>
                                            <td class="centro">
                                                <img class="icone hand excluir" src="images/icon_error.png" title="Excluir" >
                                            </td>
                                        </tr>
                                    <?php endForeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="separador"></div>
                </div>
