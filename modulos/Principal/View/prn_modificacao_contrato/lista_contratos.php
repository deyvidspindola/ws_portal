        <div id="" class="<?php echo empty($this->view->parametros->listaContratos) ? 'invisivel' : ''; ?>">
            <div class="bloco_titulo">Modificações do Contrato: <?php echo $this->view->parametros->connumero;  ?></div>
                <div class="bloco_conteudo" >
                    <div class="listagem">
                        <form id="form_updown_lote" method="post" action="">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="medio centro">Data</th>
                                            <th class="menor centro">Nº</th>
                                            <th class="maior centro">Tipo Modificação</th>
                                            <th class="menor centro">Status</th>
                                            <th class="medio centro">Motivo</th>
                                            <th class="maior centro">Descrição</th>
                                            <th class="maior centro">Contrato Novo</th>
                                            <th class="medio centro">Tipo Contrato Orig.</th>
                                            <th class="medio centro">Tipo Contrato Novo</th>
                                            <th class="menor centro">O.S.</th>
                                            <th class="menor centro">Revertido</th>
                                            <th class="medio centro">Usuário</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                         <?php
                                            foreach ($this->view->parametros->listaContratos as $dados):
                                                $cor = ($cor=="par") ? "impar" : "par";
                                        ?>
                                            <tr class="<?php echo $cor; ?>">
                                                <td class="centro">
                                                    <?php echo $dados->data; ?>
                                                </td>
                                                <td class="direita">
                                                    <?php echo $dados->mdfoid; ?>
                                                </td>
                                                <td class="esquerda">
                                                    <?php echo $dados->tipo; ?>
                                                </td>
                                                <td class="esquerda">
                                                    <?php echo $this->view->legenda_status[$dados->status]; ?>
                                                </td>
                                                <td class="esquerda">
                                                    <?php echo $dados->motivo; ?>
                                                </td>
                                                <td class="esquerda">
                                                    <?php echo $dados->observacao; ?>
                                                </td>
                                                <td class="esquerda">
                                                    <?php echo $dados->contrato_novo; ?>
                                                </td>
                                                <td class="esquerda">
                                                    <?php echo $dados->tipo_contrato_original; ?>
                                                </td>
                                                <td class="esquerda">
                                                    <?php echo $dados->tipo_contrato_novo; ?>
                                                </td>
                                                <td class="direita">
                                                    <?php echo $dados->os; ?>
                                                </td>                                            
                                                <td class="esquerda">
                                                    <?php echo $dados->revertido; ?>
                                                </td>
                                                <td class="esquerda">
                                                    <?php echo $dados->ususario; ?>
                                                </td>
                                            </tr>
                                       <?php endForeach; ?>                                       
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="12"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </form>
                    </div>
                </div>
            </div>

