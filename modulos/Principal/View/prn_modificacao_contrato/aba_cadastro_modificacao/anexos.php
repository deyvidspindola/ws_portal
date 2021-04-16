        <?php  require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/aba_cadastro_modificacao/sub_abas.php"; ?>
                <div id="msg_confirmar_excluir_anexo" class="invisivel">Deseja realmente excluir este anexo?</div>
                <div class="bloco_titulo">Cadastro Modificação</div>
                <div class="bloco_conteudo">
                    <div class="separador"></div>
                    <div class="mensagem info">Anexo obrigatório para modificação de  Placa 2 entre clientes.</div>
                    <div class="bloco_titulo">Anexos</div>
                    <div id="bloco_anexo" class="bloco_conteudo">
                        <div class="formulario">
                            <form id="form_arquivo" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" id="acao" name="acao" value="gravarArquivoTemporario" />
                                <div class="campo maior">
                                     <label for="arquivo_anexo">Arquivo</label>
                                     <input id="arquivo_anexo" name="arquivo_anexo" type="file" class="campo"/>
                                </div>

                                 <div class="campo medio">
                                      <label for="local_instalacao">Local Original do Arquivo</label>
                                      <input type="text" id="local_instalacao" name="local_instalacao" value="" class="campo" />
                                 </div>
                                 <div class="clear"></div>
                            </form>
                        </div>
                    </div>
                     <div class="bloco_acoes">
                        <button type="button" id="btn_anexar" class="">Anexar</button>
                    </div>
                    <div class="separador"></div>

                    <div id="lista_anexos" class="bloco_conteudo <?php echo empty($this->view->parametros->anexos) ? 'invisivel' : ''; ?>">
                    <div class="listagem">
                        <table>
                            <thead>
                                <tr>
                                    <th class="medio esquerda">Arquivo</th>
                                    <th class="medio esquerda">Local Original do Arquivo</th>
                                    <th class="acao">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                               <?php
                                  foreach ($this->view->parametros->anexos as $dados):
                                  $cor = ($cor=="par") ? "impar" : "par";
                              ?>
                                  <tr class="<?php echo $cor; ?>">
                                      <td class="esquerda">
                                         <?php echo $dados['nome']; ?>
                                      </td>
                                      <td class="esquerda">
                                         <?php echo $dados['local']; ?>
                                      </td>
                                      <td class="centro">
                                          <img id="<?php echo $dados['nome']; ?>" class="icone hand excluir" src="images/icon_error.png" title="Excluir" />
                                      </td>
                                  </tr>
                              <?php
                                endForeach;
                              ?>
                            </tbody>
                        </table>
                    </div>
                    </div>
                    <div class="separador"></div>
                </div>

    <div class="bloco_acoes">
        <button type="button" id="btn_confirmar_mofificacao" class="<?php echo ($this->view->parametros->acoes_lote !='t') ? '' : ' invisivel' ;?>">Confirmar</button>
        <button type="button" id="btn_voltar">Voltar</button>
    </div>