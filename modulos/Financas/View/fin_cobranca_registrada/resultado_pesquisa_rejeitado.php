<div class="separador"></div>

<div class="resultado_pesquisa">
    <div class="bloco_titulo">Resultado da Pesquisa</div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
    					<th class="medio">Banco</th>
                        <th class="menor">Nº da Remessa</th>
                        <th class="maior">Cliente</th>
    					<th class="menor">Nº do Título</th>
                        <th class="menor">Data Envio</th>
    					<th class="menor" style="min-width:200px;">Retorno</th>
                        <th class="menor">Desvincular</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($this->view->dados->resultadoPesquisaRejeitado) > 0) {
                        $classeLinha = "par";
                        ?>

                        <?php foreach ($this->view->dados->resultadoPesquisaRejeitado as $resultado) { 
                            $data = new DateTime($resultado->data_cadastro); ?>
                            <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
    							<tr class="<?php echo $classeLinha; ?>" id="<?php echo $resultado->id_remessa; ?>">
    								<td class="esquerda"><?php echo $resultado->nome_banco; ?></td>
                                    <td class="direita"><?php echo $resultado->numero_remessa; ?></td>
                                    <td class="esquerda"><?php echo $resultado->nome_cliente; ?></td>
    								<td class="direita"><?php echo $resultado->numero_titulo; ?></td>
    								<td class="centro"><?php echo date_format($data, 'd/m/Y'); ?></td>
                                    <td class="esquerda">
                                    <?php

                                        $arrCodRetorno = explode(',', substr($resultado->cod_retorno, 1, -1));
                                        $arrMsgRetorno = explode(',', substr($resultado->msg_retorno, 1, -1));

                                    ?>
                                    <?php for($i = 0; $i < count($arrCodRetorno); $i++): ?>
                                        <?php if(!is_null($arrCodRetorno[$i]) && $arrCodRetorno[$i] !== 'NULL'): ?>
                                            <?php echo $i > 0 ? '<br><br>' : ''; ?>
                                            <?php echo $arrCodRetorno[$i] . ' - ' . substr($arrMsgRetorno[$i], 1, -1); ?>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    </td>
                                    <td class="centro">
                                        <input type="checkbox" class="chk_rejeitado" data-id="<?php echo $resultado->numero_titulo; ?>" data-tipo="<?php echo $resultado->tipo; ?>" name="check_desvincular" id="check_desvincular"/>
                                    </td>
    							</tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="9" class="centro">
                            <?php
                            $totalRegistros = count($this->view->dados->resultadoPesquisaRejeitado);
                            echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="9" class="centro">
                            <button style="cursor: default;" type="button" id="btn_desvincular_rejeitado">Desvincular</button>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <?php echo ($this->view->totalResultados > 10) ? $this->view->paginacao : ''; ?>
        </div>
    </div>
</div>