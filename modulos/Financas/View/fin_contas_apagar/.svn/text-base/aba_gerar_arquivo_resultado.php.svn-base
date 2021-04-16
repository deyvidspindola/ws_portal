		

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="conteudo">
        <fieldset>
            <legend>Legenda</legend>
            <ul>
                <li><img alt="Autorizado"             src="images/indicadores/redondos/ap/ap01.jpg"> Autorizado</li>
                <li><img alt="Aguardando Autorização" src="images/indicadores/redondos/ap/ap03.jpg"> Aguardando Autorização</li>
                <li><img alt="Título Excluído"        src="images/indicadores/redondos/ap/ap04.jpg"> Título Excluído (a ser excluído da contabilização do banco)</li>
            </ul>
        </fieldset>
    </div>
    <div id="bloco_itens" class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="menor"></th>
                    <th class="menor"></th>
                    <th class="menor">Cód.<br>Título</th>
                    <th class="medio">Fornecedor</th>
                    <th class="menor">Docto.</th>
                    <th class="menor">Data<br>Entrada</th>
                    <th class="menor">Data<br>Vencimento</th>
                    <th class="menor">Data<br>Pagamento</th>
                    <th class="menor">Valor Bruto</th>
                    <th class="menor">Desconto</th>
                    <th class="menor">Juros</th>
                    <th class="menor">Multa</th>
                    <th class="menor">Impostos</th>
                    <th class="menor">Tarifa Bancária</th>                                        
                    <th class="menor">Valor&nbsp;Pago / Título&nbsp;<img class="" src="images/help10.gif" style="cursor: pointer" onclick="mostrarHelpComment(this,'Valor impresso no código de barras do título a pagar .','E' , '');"></th>
                    <th class="menor">Valor<br>Total</th>
                    <th class="medio">Forma&nbsp;de<br>Pagamento</th>
                    <th class="menor">Tipo&nbsp;de<br>Pagamento</th>
                    <th class="maior">Conta<br>Contabil</th>
                    <th class="maior">Tipo Contas<br>a Pagar</th>                 
                    <th class="maior">Nº<br>Remessa</th>
                </tr>
            </thead>
        <?php 
use module\Parametro\ParametroIntegracaoTotvs;
if (count($this->view->dados['geraArquivo']) > 0): ?>
            <tbody>

                <?php
                    $classeLinha = "par";
                    ?>
                    <?php foreach ($this->view->dados['geraArquivo'] as $linha => $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "impar") ? "par" : "impar"; ?>
							<tr class="<?php echo $classeLinha; ?>">
                                <td class="centro">
                                    <?php if($resultado->checkbox) : ?>
                                        <input type="checkbox" name="ck[]" id="selecionar" value="<?php echo $resultado->apgoid; ?>" data-autorizado="<?php echo $resultado->apgautorizado; ?>" data-linha="<?php echo $linha; ?>" class="selecao">
                                    <?php endif; ?>
                                </td>
                                <td class="esquerda"><img src="images/indicadores/redondos/ap/<?php echo $resultado->status_imagem; ?>" /></td>
                                <td class="esquerda"><?php echo $resultado->apgoid; ?></td>
								<td class="esquerda" style="color:<?php echo $resultado->cor_fornecedor; ?>"><?php echo $resultado->fornecedor; ?></td>
								<td class="direita"><?php echo $resultado->doc; ?></td>
                                <td class="centro"><?php echo $resultado->apgdt_entrada; ?></td>
                                <td class="centro"><?php echo $resultado->apgdt_vencimento; ?></td>
                                <td class="centro"><?php echo $resultado->apgdt_pagamento; ?></td>
                                <td class="direita"><?php echo $resultado->apgvl_apagar; ?></td>
                                <td class="direita" style="color:<?php echo $resultado->cor_desconto; ?>"><?php echo $resultado->desconto; ?></td>
                                <td class="direita"><?php echo $resultado->juros; ?></td>
                                <td class="direita"><?php echo $resultado->multa; ?></td>
                                <td class="direita"><?php echo $resultado->imposto; ?></td>
                                <td class="direita"><?php echo $resultado->tarifa; ?></td>
                                <td class="direita"><?php echo (is_numeric($resultado->valor_titulo_equal_boleto) ? number_format($resultado->valor_titulo_equal_boleto,2, ",", ".") : $resultado->valor_titulo_equal_boleto);?></td>
                                <td class="direita"><?php echo $resultado->valor; ?></td>
                                <td class="centro"><?php echo $resultado->ocorrencia; ?></td>
                                <td class="centro"><?php echo $resultado->tipo_documento; ?></td>
                                <td class="esquerda"><?php echo $resultado->conta_contabil; ?></td>
                                <td class="esquerda"><?php echo $resultado->tipo_contas; ?></td>
                                <td class="esquerda"><?php echo ($resultado->apgno_remessa == '' ? '-' : $resultado->apgno_remessa); ?></td>
							</tr>
                    <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class="selecao">
                        <input id="selecao_todos" type="checkbox" title="Selecionar Todos"/>
                    </td>
                    <td class="esquerda" colspan="7">
                        <strong>TOTAL:</strong>
                    </td>
                    <td class="direita"><strong><?php echo number_format($this->view->total['geraArquivo']->valor, 2, ",", ".");?></strong></td>
                    <td class="direita"><strong><?php echo number_format($this->view->total['geraArquivo']->tapgvl_desconto, 2, ",", ".");?></strong></td>
                    <td class="direita"><strong><?php echo number_format($this->view->total['geraArquivo']->tapgvl_juros, 2, ",", ".");?></strong></td>
                    <td class="direita"><strong><?php echo number_format($this->view->total['geraArquivo']->tapgvl_multa, 2, ",", ".");?></strong></td>
                    <td class="direita"><strong><?php echo number_format( ( $this->view->total['geraArquivo']->tapgvl_ir + $this->view->total['geraArquivo']->tapgvl_pis + $this->view->total['geraArquivo']->tapgvl_iss + $this->view->total['geraArquivo']->tapgvl_inss + $this->view->total['geraArquivo']->tapgvl_cofins + $this->view->total['geraArquivo']->tapgvl_csll + $this->view->total['geraArquivo']->tapapgcsrf ) , 2, ",", ".");?></strong></td>
                    <td class="direita"><strong><?php echo number_format($this->view->total['geraArquivo']->tapgvl_tarifa_bancaria, 2, ",", ".");?></strong></td>
                    <td>&nbsp;</td>
                    <td class="direita"><strong><?php echo number_format($this->view->total['geraArquivo']->tvalor_pagamento, 2, ",", ".");?></strong></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td colspan="21" id="registros_encontrados" class="centro">
                        <?php
                        $totalRegistros = count($this->view->dados['geraArquivo']);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="21">
                        <div class="acoes">
                            <button type="button" id="imprimir">Imprimir</button>

                            <? // [START][ORGMKTOTVS-1185] - Leandro Corso ?>
                            <input 
                                type="button" 
                                value="Enviar Remessa"
                                <?= INTEGRACAO 
                                    ? 'disabled readonly'
                                    : 'id="enviar-remessa"' ?>
                            />
                            <? // [END][ORGMKTOTVS-1185] - Leandro Corso ?>
                            
                            <?php if ($_SESSION['funcao']['autorizacao_pagamentos'] == 1) :?>
                            
                                <? // [START][ORGMKTOTVS-1185] - Leandro Corso ?>
                                <input
                                type="button"
                                value="Autorizar"
                                <?= INTEGRACAO 
                                        ? 'disabled readonly'
                                        : 'id="autorizar"' ?>
                                />
                                <? 
                                if (INTEGRACAO) echo ParametroIntegracaoTotvs::message(array('Os botões "Enviar"', '"Autorizar"'));
                                // [END][ORGMKTOTVS-1185] - Leandro Corso
                                ?>
                            
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            </tfoot>
        <?php else : ?>
            <tbody>
                <tr>
                    <td colspan="21" class="centro">
                        Nenhum registro encontrado.
                    </td>
                </tr>
            </tbody>
        <?php endif; ?>
        </table>
    </div>
</div>

        

<div class="separador"></div>
<div class="resultado bloco_titulo">Títulos Pagos no período</div>
<div class="resultado bloco_conteudo">

    <div id="bloco_itens" class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="menor">Cód.<br>Título</th>
                    <th class="medio">Fornecedor</th>
                    <th class="menor">Docto.</th>
                    <th class="menor">Data<br>Entrada</th>
                    <th class="menor">Data<br>Vencimento</th>
                    <th class="menor">Data<br>Pagamento</th>
                    <th class="menor">Valor Bruto</th>
                    <th class="menor">Desconto</th>
                    <th class="menor">Juros</th>
                    <th class="menor">Multa</th>
                    <th class="menor">Impostos</th>                    
                    <th class="menor">Valor&nbsp;Pago / Título&nbsp;<img class="" src="images/help10.gif" style="cursor: pointer" onclick="mostrarHelpComment(this,'Valor impresso no código de barras do título a pagar .','E' , '');"></th>
                    <th class="menor">Valor<br>Total</th>
                    <th class="medio">Forma&nbsp;de<br>Pagamento</th>
                    <th class="menor">Tipo&nbsp;de<br>Pagamento</th>
                    <th class="maior">Conta<br>Contabil</th>
                    <th class="maior">Tipo Contas<br>a Pagar</th>
                </tr>
            </thead>
            <?php if (count($this->view->dados['titulosPagos']) > 0): ?>
            <tbody>
                <?php
                    $classeLinha = "par";
                    ?>
                    <?php foreach ($this->view->dados['titulosPagos'] as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "impar") ? "par" : "impar"; ?>
                            <tr class="<?php echo $classeLinha; ?>">
                                <td class="esquerda"><?php echo $resultado->apgoid; ?></td>
                                <td class="esquerda" style="color:<?php echo $resultado->cor_fornecedor; ?>"><?php echo $resultado->forfornecedor; ?></td>
                                <td class="direita"><?php echo $resultado->doc; ?></td>
                                <td class="centro"><?php echo $resultado->apgdt_entrada; ?></td>
                                <td class="centro"><?php echo $resultado->apgdt_vencimento; ?></td>
                                <td class="direita"><?php echo $resultado->apgdt_pgto; ?></td>
                                <td class="direita"><?php echo number_format($resultado->apgvl_apagar, 2, ",", "."); ?></td>
                                <td class="direita" style="color:<?php echo $resultado->cor_desconto; ?>"><?php echo number_format($resultado->apgvl_desconto, 2, ",", "."); ?></td>
                                <td class="direita"><?php echo number_format($resultado->apgvl_juros, 2, ",", "."); ?></td>
                                <td class="direita"><?php echo number_format($resultado->apgvl_multa, 2, ",", "."); ?></td>                                
                                <td class="direita"><?php echo number_format($resultado->apgvl_ir + $resultado->apgvl_pis + $resultado->apgvl_iss + $resultado->apgvl_inss + $resultado->apgvl_cofins + $resultado->apgvl_csll + $resultado->apgcsrf , 2, ",", "."); ?></td>                                
                                <td class="direita"><?php echo number_format($resultado->valor_titulo_equal_boleto,2, ",", ".");?></td>
                                <td class="direita"><?php echo number_format($resultado->valor_pagamento, 2, ",", "."); ?></td>
                                <td class="centro"><?php echo $resultado->ocorrencia; ?></td>
                                <td class="centro"><?php echo $resultado->tipo_documento; ?></td>
                                <td class="esquerda"><?php echo $resultado->descricao; ?></td>
                                <td class="esquerda"><?php echo $resultado->tipo_contas; ?></td>
                            </tr>
                    <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class="esquerda" colspan="6">
                        <strong>TOTAL:</strong>
                    </td>
                    <td class="direita"><strong><?php echo number_format($this->view->total['titulosPagos']->valor, 2, ",", ".");?></strong></td>
                    <td class="direita"><strong><?php echo number_format($this->view->total['titulosPagos']->tapgvl_desconto, 2, ",", ".");?></strong></td>
                    <td class="direita"><strong><?php echo number_format($this->view->total['titulosPagos']->tapgvl_juros, 2, ",", ".");?></strong></td>
                    <td class="direita"><strong><?php echo number_format($this->view->total['titulosPagos']->tapgvl_multa, 2, ",", ".");?></strong></td>
                    <td class="direita"><strong><?php echo number_format( ( $this->view->total['titulosPagos']->tapgvl_ir + $this->view->total['titulosPagos']->tapgvl_pis + $this->view->total['titulosPagos']->tapgvl_iss + $this->view->total['titulosPagos']->tapgvl_inss + $this->view->total['titulosPagos']->tapgvl_cofins + $this->view->total['titulosPagos']->tapgvl_csll + $this->view->total['titulosPagos']->tapapgcsrf ) , 2, ",", ".");?></strong></td>
                    <td>&nbsp;</td>
                    <td><strong><?php echo number_format($this->view->total['titulosPagos']->tvalor_pagamento, 2, ",", ".");?></strong></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td colspan="17" id="registros_encontrados" class="centro">
                        <?php
                        $totalRegistros = count($this->view->dados['titulosPagos']);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        <?php else : ?>
            <tbody>
                <tr>
                    <td colspan="17" class="centro">
                        Nenhum registro encontrado.
                    </td>
                </tr>
            </tbody>
        <?php endif; ?>
        </table>
    </div>
</div>

<div class="separador"></div>
<div class="resultado bloco_titulo">Títulos de adiantamento ao fornecedor</div>
<div class="resultado bloco_conteudo">

    <div id="bloco_itens" class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="menor">Cód. Título</th>
                    <th class="medio">Fornecedor</th>
                    <th class="menor">Docto.</th>
                    <th class="menor">Vcto.</th>
                    <th class="menor">Dt. Entrada</th>
                    <th class="menor">Valor Bruto</th>
                    <th class="menor">Desconto</th>
                    <th class="menor">Juros</th>
                    <th class="menor">Multa</th>
                    <th class="menor">Impostos</th>                    
                    <th class="menor">Valor&nbsp;Pago / Título&nbsp;<img class="" src="images/help10.gif" style="cursor: pointer" onclick="mostrarHelpComment(this,'Valor impresso no código de barras do título a pagar .','E' , '');"></th>
                    <th class="menor">Valor Total</th>
                    <th class="medio">Forma&nbsp;de&nbsp;Pagamento</th>
                    <th class="menor">Tipo&nbsp;de&nbsp;Pagamento</th>
                    <th class="maior">Conta Contabil</th>
                    <th class="maior">Tipo Contas a Pagar</th>
                </tr>
            </thead>
            <?php if (count($this->view->dados['adiantamento']) > 0): ?>
            <tbody>
                <?php
                    $classeLinha = "par";
                    ?>
                    <?php foreach ($this->view->dados['adiantamento'] as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "impar") ? "par" : "impar"; ?>
                            <tr class="<?php echo $classeLinha; ?>">
                                <td class="esquerda"><?php echo $resultado->apgoid; ?></td>
                                <td class="esquerda" style="color:<?php echo $resultado->cor_fornecedor; ?>"><?php echo $resultado->forfornecedor; ?></td>
                                <td class="direita"><?php echo $resultado->doc; ?></td>
                                <td class="centro"><?php echo $resultado->apgdt_vencimento; ?></td>
                                <td class="centro"><?php echo $resultado->apgdt_entrada; ?></td>
                                <td class="direita"><?php echo number_format($resultado->apgvl_apagar, 2, ",", "."); ?></td>
                                <td class="direita" style="color:<?php echo $resultado->cor_desconto; ?>"><?php echo number_format($resultado->apgvl_desconto, 2, ",", "."); ?></td>
                                <td class="direita"><?php echo number_format($resultado->apgvl_juros, 2, ",", "."); ?></td>
                                <td class="direita"><?php echo number_format($resultado->apgvl_multa, 2, ",", "."); ?></td>
                                <td class="direita"><?php echo number_format($resultado->apgvl_ir + $resultado->apgvl_pis + $resultado->apgvl_iss + $resultado->apgvl_inss + $resultado->apgvl_cofins + $resultado->apgvl_csll + $resultado->apgcsrf , 2, ",", "."); ?></td>                                
                                <td class="direita"><?php echo number_format($resultado->valor_titulo_equal_boleto,2, ",", ".");?></td>
                                <td class="direita"><?php echo number_format($resultado->valor_pagamento, 2, ",", "."); ?></td>
                                <td class="centro"><?php echo $resultado->ocorrencia; ?></td>
                                <td class="centro"><?php echo $resultado->tipo_documento; ?></td>
                                <td class="esquerda"><?php echo $resultado->descricao; ?></td>
                                <td class="esquerda"><?php echo $resultado->tipo_contas; ?></td>
                            </tr>
                    <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class="esquerda" colspan="6">
                        <strong>TOTAL:</strong>
                    </td>
                    <td class="direita"><strong><?php echo number_format($this->view->total['adiantamento']->valor, 2, ",", ".");?></strong></td>
                    <td class="direita"><strong><?php echo number_format($this->view->total['adiantamento']->tapgvl_desconto, 2, ",", ".");?></strong></td>
                    <td class="direita"><strong><?php echo number_format($this->view->total['adiantamento']->tapgvl_juros, 2, ",", ".");?></strong></td>
                    <td class="direita"><strong><?php echo number_format($this->view->total['adiantamento']->tapgvl_multa, 2, ",", ".");?></strong></td>
                    <td class="direita"><strong><?php echo number_format( ( $this->view->total['adiantamento']->tapgvl_ir + $this->view->total['adiantamento']->tapgvl_pis + $this->view->total['adiantamento']->tapgvl_iss + $this->view->total['adiantamento']->tapgvl_inss + $this->view->total['adiantamento']->tapgvl_cofins + $this->view->total['adiantamento']->tapgvl_csll + $this->view->total['adiantamento']->tapapgcsrf ) , 2, ",", ".");?></strong></td>
                    <td>&nbsp;</td>
                    <td class="direita"><strong><?php echo number_format($this->view->total['adiantamento']->tvalor_pagamento, 2, ",", ".");?></strong></td>
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td colspan="17" id="registros_encontrados" class="centro">
                        <?php
                        $totalRegistros = count($this->view->dados['adiantamento']);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        <?php else : ?>
            <tbody>
                <tr>
                    <td colspan="17" class="centro">
                        Nenhum registro encontrado.
                    </td>
                </tr>
            </tbody>
        <?php endif; ?>
        </table>
    </div>
</div>