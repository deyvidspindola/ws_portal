    <?php  require_once _MODULEDIR_ . "Principal/View/prn_modificacao_contrato/aba_detalhes_modificacao/sub_abas.php"; ?>
    <div class="bloco_titulo">Detalhes da Modificação</div>
    <div class="bloco_conteudo">
        <div class="separador"></div>

        <div class="bloco_titulo">Desfazer</div>
            <div id="msg_confirmar_desfazer" class="invisivel">Deseja realmente desfazer este(s) contrato(s)?</div>
            <div class="bloco_conteudo">
                <div class="formulario">
                    <div class="campo maior">
                        <label for="observacao_desfazer">Observação *</label>
                        <textarea id="observacao_desfazer" name="observacao_desfazer"><?php echo $this->view->parametros->observacao_desfazer;?></textarea>
                    </div>
                </div>
                    <div class="clear"></div>
                    <div class="separador"></div>
                    <div class="bloco_conteudo">
                        <div class="listagem">
                            <form id="form_desfazer" method="post" action="">
                                <input type="hidden" id="mdfstatus" name="mdfstatus" value="<?php echo $this->view->parametros->mdfstatus;?>">
                                <input type="hidden" id="acao" name="acao" value="">
                                <input type="hidden" id="mdfoid" name="mdfoid" value="<?php echo $this->view->parametros->mdfoid;?>">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="selecao"><input id="selecao_todos_desfazer" type="checkbox" data-bloco="desfazer"></th>
                                            <th class="menor centro">Contrato</th>
                                            <th class="menor centro">Contrato (Novo)</th>
                                            <th class="menor centro">Placa</th>
                                            <th class="medio centro">Chassi</th>
                                            <th class="medio centro">Classe (Nova)</th>
                                            <th class="medio centro">Classe (Original)</th>
                                            <th class="medio centro">Tipo Contrato (Novo)</th>
                                            <th class="medio centro">Tipo Contrato (Original)</th>
                                            <th class="medio centro">O.S. / Tipo</th>
                                            <th class="medio centro">Status O.S.</th>
                                            <th class="medio centro">Status Modificação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach ($this->view->parametros->contratos as $dados):
                                                $cor = ($cor=="par") ? "impar" : "par";
                                        ?>
                                            <tr class="<?php echo $cor; ?>">
                                                <td class="centro">
                                                    <?php if($dados->is_desfazer): ?>
                                                        <input id="<?php echo $dados->cmfconnumero; ?>" name="contratos_marcados[]" data-bloco="desfazer"
                                                                type="checkbox" value="<?php echo $dados->cmfconnumero; ?>"/>
                                                    <?php endIf; ?>
                                                </td>
                                                <td class="direita">
                                                    <?php echo $dados->cmfconnumero; ?>
                                                </td>
                                                 <td class="direita">
                                                    <?php echo $dados->cmfconnumero_novo; ?>
                                                </td>
                                                <td class="direita">
                                                    <a href="veiculo.php?veioid=<?php echo $dados->cmfveioid;?>" target="_blank">
                                                        <?php echo $dados->placa; ?>
                                                    </a>
                                                </td>
                                                <td class="direita">
                                                    <?php echo $dados->chassi; ?>
                                                </td>
                                                <td class="esquerda">
                                                    <?php echo $dados->classe_nova; ?>
                                                </td>
                                                <td class="esquerda">
                                                    <?php echo $dados->classe_original; ?>
                                                </td>
                                                <td class="esquerda">
                                                    <?php echo $dados->tipo_contrato_novo; ?>
                                                </td>
                                                <td class="esquerda">
                                                    <?php echo $dados->tipo_contrato_original; ?>
                                                </td>
                                                <td class="esquerda">
                                                    <?php echo $dados->ordem_servico_tipo; ?>
                                                </td>
                                                <td class="esquerda">
                                                    <?php echo $dados->status_os; ?>
                                                </td>
                                                <td class="esquerda">
                                                    <?php echo $this->view->legenda_status[$dados->status_modificacao]; ?>
                                                </td>
                                            </tr>
                                        <?php endForeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="12">
                                                <button type="button" id="btn_desfazer" disabled="true" class="desabilitado">Desfazer</button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </form>
                        </div>
                    </div>
                <div class="separador"></div>
            </div>
            <div class="separador"></div>
        </div>
    </div>

    <div class="bloco_acoes">
            <button type="button" id="btn_voltar">Voltar</button>
    </div>
