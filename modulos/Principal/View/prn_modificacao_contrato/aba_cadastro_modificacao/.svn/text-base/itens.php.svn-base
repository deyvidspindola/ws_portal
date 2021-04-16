        <?php  require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/aba_cadastro_modificacao/sub_abas.php"; ?>
        <div class="bloco_titulo">Cadastro Modificação</div>
        <div class="bloco_conteudo">
            <div class="separador"></div>
            <?php if ( ($this->view->parametros->cmptleitura_arquivo == 'f') && (!$this->view->parametros->exibe_UpDown) ): ?>
                <div class="mensagem alerta">Nenhum contrato encontrato para a modificação Informada.</div>
            <?php endIf; ?>
            <div id="bloco_arquivo" class="<?php echo ($this->view->parametros->cmptleitura_arquivo == 't') ? '' : ' invisivel';?>">
                <div class="bloco_titulo">Arquivo (CSV ou TXT)</div>
                    <div class="bloco_conteudo">
                        <div class="formulario">
                           <form id="form_arquivo" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" id="acao" name="acao" value"">
                                <div class="campo maior">
                                    <input id="arquivo_chassi" name="arquivo_chassi" type="file" class="campo"/>
                                </div>
                                <div class="clear"></div>
                            </form>
                        </div>
                    </div>
                    <div class="bloco_acoes">
                        <button type="button" id="btn_importar">Importar</button>
                    </div>

                <div id="bloco_lista_contratos_migracao" class="<?php echo empty($this->view->parametros->migracaoLote) ? ' invisivel' : '';?>">
                    <div class="separador"></div>
                    <div class="bloco_titulo">Migração em Lote</div>
                        <div class="bloco_conteudo">
                             <div class="listagem">
                                <form id="form_migracao_lote" method="post" action="">
                                    <input type="hidden" id="acao_lote" name="acao" value"">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="selecao"><input id="selecao_todos_lote" type="checkbox" data-bloco="lote"></th>
                                                <th class="menor centro">Contrato</th>
                                                <th class="menor centro">Placa</th>
                                                <th class="medio centro">Chassi</th>
                                                <th class="medio centro">Tipo de Contrato</th>
                                                <th class="medio centro">Tipo Correspondente</th>
                                                <th class="medio centro">O.S. / Tipo</th>
                                                <th class="medio centro">Quarentena</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                             <?php
                                                foreach ($this->view->parametros->migracaoLote as $dados):
                                                    $cor = ($cor=="par") ? "impar" : "par";
                                            ?>
                                            <tr class="<?php echo $cor; ?>">
                                                <td class="centro">
                                                    <?php if($dados->bloqueia_migracao == 'f'): ?>
                                                        <input id="<?php echo $dados->connumero; ?>" name="contratos_marcados[]"
                                                                type="checkbox" data-bloco="lote" value="<?php echo $dados->connumero; ?>"/>
                                                    <?php endIf; ?>
                                                </td>
                                                <td class="menor direita">
                                                    <?php echo $dados->connumero; ?>
                                                </td>
                                                <td class="menor direita">
                                                    <?php echo $dados->veiplaca; ?>
                                                </td>
                                                <td class="medio direita">
                                                    <?php echo $dados->veichassi; ?>
                                                </td>
                                                <td class="medio esquerda">
                                                    <?php echo $dados->tpcdescricao; ?>
                                                </td>
                                                <td class="medio esquerda">
                                                    <?php echo $dados->tpcdescricao_correspondente; ?>
                                                </td>
                                                <td class="medio esquerda">
                                                    <?php echo $dados->os_tipo; ?>
                                                </td>
                                                <td class="medio esquerda">
                                                    <?php echo $dados->quarentena; ?>
                                                </td>
                                            </tr>
                                            <?php endForeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="8" class="bloco_acoes">
                                                    <button type="button" id="btn_confirmar_lote" disabled="true" class="desabilitado">Confirmar</button>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </form>
                            </div>
                        </div>
                </div>
            </div>

            <div class="separador"></div>
            <div id="bloco_lista_down_up" class="<?php echo (!$this->view->parametros->exibe_UpDown) ? 'invisivel' : '';?>">
                <div class="bloco_titulo">Upgrade / Downgrade em Lote</div>
                    <div class="bloco_conteudo">
                        <div class="listagem">
                            <form id="form_updown_lote" method="post" action="">
                                 <input type="hidden" id="acao_updown" name="acao" value"">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th class="selecao"><input id="selecao_todos_lote_updown" type="checkbox" data-bloco="updown" /></th>
                                                <th class="menor centro">Contrato</th>
                                                <th class="menor centro">Placa</th>
                                                <th class="medio centro">Chassi</th>
                                                <th class="medio centro">Classe</th>
                                                <th class="medio centro">Nova Classe</th>
                                                <th class="menor centro">Eqpto Compatível</th>
                                                <th class="maior centro">Acessórios</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                             <?php
                                                foreach ($this->view->parametros->dadosUpDown as $dados):
                                                    $cor = ($cor=="par") ? "impar" : "par";
                                            ?>
                                                    <tr class="<?php echo $cor; ?>">
                                                        <td class="centro">
                                                             <input id="<?php echo $dados->connumero; ?>" name="contratos_marcados[]"
                                                                type="checkbox" data-bloco="updown" value="<?php echo $dados->connumero; ?>"/>
                                                        </td>
                                                        <td class="direita">
                                                            <?php echo $dados->connumero; ?>
                                                        </td>
                                                        <td class="direita">
                                                            <?php echo $dados->veiplaca; ?>
                                                        </td>
                                                        <td class="direita">
                                                            <?php echo $dados->veichassi; ?>
                                                        </td>
                                                        <td class="esquerda">
                                                            <?php echo $dados->classe_original; ?>
                                                        </td>
                                                        <td class="esquerda">
                                                            <?php echo $dados->classe_nova; ?>
                                                        </td>
                                                        <td class="esquerda">
                                                            <?php echo $dados->compativel; ?>
                                                        </td>
                                                        <td class="esquerda">
                                                            <?php echo $dados->acessorios; ?>
                                                        </td>
                                                    </tr>
                                            <?php endForeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="8" class="bloco_acoes">
                                                    <button type="button" id="btn_confirmar_lote_updown" disabled="true" class="desabilitado">Confirmar</button>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </form>
                            </div>
                    </div>
                 </div>
        </div>
    <div class="bloco_acoes">
            <button type="button" id="btn_voltar">Voltar</button>
    </div>