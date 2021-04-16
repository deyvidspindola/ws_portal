<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <?php// echo $this->view->ordenacao; ?>
    <div id="bloco_itens" class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="menor"></th>
                    <th class="medio">Nº Remessa</th>
                    <th class="medio">Cód. Título</th>
                    <th class="maior">Fornecedor</th>
                    <th class="medio">Banco</th>
                    <th class="medio">Empresa</th>
                    <th class="medio">Documento</th>
                    <th class="medio">Vencimento</th>
                    <th class="medio">Dt. Entrada</th>
                    <th class="medio">Valor Bruto</th>                    
                    <th class="menor">Valor&nbsp;Pago / Título&nbsp;<img class="" src="images/help10.gif" style="cursor: pointer" onclick="mostrarHelpComment(this,'Valor impresso no código de barras do título a pagar .','E' , '');"></th>
                    <th class="medio">Valor Total</th>
                    <th class="medio">Tipo Contas a Pagar</th>
                    <th class="medio">Forma de Pagamento</th>
                    <th class="medio">Status</th>
                    <th class="maior">Status do Retorno</th>
                </tr>
            </thead>
        <?php if (count($this->view->dados['titulosProcessados']) > 0): ?>
            <tbody>

                <?php
                    $classeLinha 	= 'par';
                    $selecao_todos 	= 'f';
                    ?>
                    <?php foreach ($this->view->dados['titulosProcessados'] as $linha => $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "impar") ? "par" : "impar"; ?>
                            <tr class="<?php echo $classeLinha; ?>">
                                <td class="esquerda">
	                            	<?php if($resultado->check == 't' && ($resultado->apgscodigo == 41 || $resultado->apgscodigo == 21)): ?>
	                            	<?php $selecao_todos = 't'; ?>
	                            		<input type="checkbox" name="ck[]" id="selecionar" value="<?php echo $resultado->apgoid; ?>" class="selecao">
	                                <?php endif; ?> 
                                </td>
                                <td class="direita"><?php echo $resultado->apgno_remessa; ?></td>
                                <td class="direita"><?php echo $resultado->apgoid; ?></td>
                                <td class="esquerda" style="color:<?php echo ($resultado->apgscodigo == 41) ? '#bf3a2e' : ''; ?>"><?php echo $resultado->fornecedor; ?></td>
                                <td class="esquerda"><?php echo $resultado->bannome; ?></td>                                
                                <td class="esquerda"><?php echo $resultado->tecrazao; ?></td>
                                <td class="direita"><?php echo $resultado->doc; ?></td>
                                <td class="esquerda"><?php echo $resultado->apgdt_vencimento; ?></td>
                                <td class="esquerda"><?php echo $resultado->apgdt_entrada; ?></td>
                                <td class="direita"><?php echo $resultado->apgvl_apagar; ?></td>
                                <td class="direita"><?php echo number_format($resultado->valor_titulo_equal_boleto,2, ",", ".");?></td>
                                <td class="direita"><?php echo $resultado->valor; ?></td>
                                <td class="esquerda"><?php echo $resultado->tipo_contas; ?></td>
                                <td class="esquerda"><?php echo $resultado->ocorrencia; ?></td>
                                <td class="esquerda"><?php echo $resultado->apgsdescricao; ?></td>
                                <td class="esquerda"><?php echo $resultado->codigo_erros; ?></td>
                            </tr>
                    <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
	                <?php if($selecao_todos == 't'): ?>
	                    <td class="selecao">
	                        <input id="selecao_todos" type="checkbox" title="Selecionar Todos"/>
	                    </td>
                	<?php endif; ?> 
                    <td colspan="20" id="registros_encontrados" class="centro">
                        <?php
                        $totalRegistros = count($this->view->dados['titulosProcessados']);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="20">
                        <div class="acoes">
                            <button type="button" id="gerar_csv">Gerar CSV</button>
                            <button type="button" id="liberar_reenvio">Liberar Reenvio</button>
                        </div>
                    </td>
                </tr>
            </tfoot>
        <?php else : ?>
            <tbody>
                <tr>
                    <td colspan="20" class="centro">
                        Nenhum registro encontrado.
                    </td>
                </tr>
            </tbody>
        <?php endif; ?>
        </table>
    </div>
    <?php echo $this->view->paginacao; ?>
</div>