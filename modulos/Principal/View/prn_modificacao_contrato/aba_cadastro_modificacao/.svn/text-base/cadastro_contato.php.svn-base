                <div class="separador"></div>
                <div class="mensagem alerta invisivel" id="msg_alerta_contato"></div>
                <div id="msg_confirmar_excluir" class="invisivel">Deseja realmente excluir este contato?</div>
                <div class="bloco_titulo">Contatos</div>
                <div class="bloco_conteudo">
                    <div class="separador"></div>
                    <div class="bloco_conteudo">
                         <div id="form_contatos" class="formulario">
                            <div class="campo maior">
                                <label for="cmctnome">Nome *</label>
                                <input id="cmctnome" name="cmctnome" value="" class="campo obrigatorio" type="text">
                            </div>
                            <div class="clear"></div>
                            <div class="campo medio">
                                <label for="cmctcpf">CPF </label>
                                <input id="cmctcpf" name="cmctcpf" value="" class="campo" type="text">
                            </div>
                            <div class="campo menor">
                                <label for="cmctrg">RG </label>
                                <input id="cmctrg" name="cmctrg" value="" class="campo numerico" type="text">
                            </div>
                            <div class="clear"></div>

                            <div class="campo medio">
                                <label for="cmctfone_res">Fone Residencial </label>
                                <input id="cmctfone_res" name="cmctfone_res" value="" class="campo telefone" type="text">
                            </div>
                            <div class="clear"></div>

                            <div class="campo medio">
                                <label for="cmctfone_cel">Fone Celular </label>
                                <input id="cmctfone_cel" name="cmctfone_cel" value="" class="campo telefone" type="text">
                            </div>
                            <div class="clear"></div>

                            <div class="campo medio">
                                <label for="cmctfone_com">Fone Comercial </label>
                                <input id="cmctfone_com" name="cmctfone_com" value="" class="campo telefone" type="text">
                            </div>
                                <div class="campo menor">
                                <label for="cmctfone_nextel">ID Nextel </label>
                                <input id="cmctfone_nextel" name="cmctfone_nextel" value="" class="campo numerico" type="text" maxlength="9">
                            </div>
                            <div class="clear"></div>

                            <fieldset id="tipo_contato" class="maior obrigatorio">
                                <legend>Tipo *</legend>
                                <input id="cmctautorizada" type="checkbox" value="t" name="cmctautorizada">
                                    <label for="cmctautorizada">Autorizada</label>
                                 <input id="cmctemergencia" type="checkbox"value="t" name="cmctemergencia">
                                    <label for="cmctemergencia">Emergência</label>
                                <input id="cmctinstalacao" type="checkbox" value="t" name="cmctinstalacao">
                                    <label for="cmctinstalacao">Instalação</label>
                            </fieldset>
                            <div class="clear"></div>

                             <div class="campo maior">
                                <label for="cmctobservacao">Observação</label>
                                <textarea id="cmctobservacao" name="cmctobservacao" class="obrigatorio"></textarea>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>

                    <div class="bloco_acoes">
                        <button type="button" id="btn_adicionar_contato" class="">Adicionar</button>
                    </div>
                    <div class="separador"></div>

                    <div id="lista_contatos" class="bloco_conteudo <?php echo (empty($this->view->parametros->contatos)) ? 'invisivel' : '' ;?>">
                        <div class="listagem">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="medio centro">Nome</th>
                                        <th class="medio centro">CPF</th>
                                        <th class="menor centro">RG</th>
                                        <th class="medio centro">Fone Residencial</th>
                                        <th class="medio centro">Fone Comercial</th>
                                        <th class="medio centro">Fone Celular</th>
                                        <th class="menor centro">Id Nextel</th>
                                        <th class="medio centro">Tipo</th>
                                        <th class="acao">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach ($this->view->parametros->contatos as $dados):
                                        $cor = ($cor=="par") ? "impar" : "par";
                                    ?>
                                        <tr class="<?php echo $cor; ?>">
                                            <td class="esquerda">
                                                <?php echo $dados->cmctnome; ?>
                                                 <input type="hidden" id="contatos_nome" name="contatos_nome[]" value="<?php echo $dados->cmctnome; ?>">
                                            </td>
                                            <td class="direita">
                                                <?php echo $dados->cmctcpf; ?>
                                                <input type="hidden" id="contatos_cpf" name="contatos_cpf[]" value="<?php echo $dados->cmctcpf; ?>">
                                            </td>
                                            <td class="direita">
                                                <?php echo $dados->cmctrg; ?>
                                                <input type="hidden" id="contatos_rg" name="contatos_rg[]" value="<?php echo $dados->cmctrg; ?>">
                                            </td>
                                            <td class="direita">
                                                <?php echo $dados->cmctfone_res; ?>
                                                <input type="hidden" id="contatos_fone_res" name="contatos_fone_res[]" value="<?php echo $dados->cmctfone_res; ?>">
                                            </td>
                                            <td class="direita">
                                                <?php echo $dados->cmctfone_com; ?>
                                                <input type="hidden" id="contatos_fone_com" name="contatos_fone_com[]" value="<?php echo $dados->cmctfone_com; ?>">
                                            </td>
                                            <td class="direita">
                                                <?php echo $dados->cmctfone_cel; ?>
                                                <input type="hidden" id="contatos_fone_cel" name="contatos_fone_cel[]" value="<?php echo $dados->cmctfone_cel; ?>">
                                            </td>
                                            <td class="direita">
                                                <?php echo $dados->cmctfone_nextel; ?>
                                                <input type="hidden" id="contatos_nextel" name="contatos_nextel[]" value="<?php echo $dados->cmctfone_nextel; ?>">
                                            </td>
                                            <td class="esquerda">
                                                <?php echo $dados->tipo_contato; ?>
                                                <input type="hidden" id="contatos_obs" name="contatos_obs[]" value="<?php echo $dados->cmctobservacao; ?>">
                                                <input type="hidden" id="contatos_autorizada" name="contatos_autorizada[]" value="<?php echo $dados->cmctautorizada; ?>">
                                                <input type="hidden" id="contatos_emergencia" name="contatos_emergencia[]" value="<?php echo $dados->cmctemergencia; ?>">
                                                <input type="hidden" id="contatos_instalacao" name="contatos_instalacao[]" value="<?php echo $dados->cmctinstalacao; ?>">
                                            </td>
                                            <td class="centro">
                                                <?php if ($this->view->parametros->mdfstatus != 'P'): ?>
                                                    <img class="icone hand excluir" src="images/icon_error.png" title="Excluir" />
                                                <?php endIf; ?>
                                                <img class="icone hand editar" src="images/edit.png" title="Editar" />
                                            </td>
                                        </tr>
                                    <?php endForeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                     <div class="separador"></div>
                </div>