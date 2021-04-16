        

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div id="bloco_itens" class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="maior">Empresa</th>
                    <th class="menor">Data</th>
                    <th class="medio">Banco</th>
                    <th class="medio" style='width: 180px;'>Status</th>
                    <th class="menor">Nº Remessa</th>
                </tr>
            </thead>
        <?php 
use module\Parametro\ParametroIntegracaoTotvs;
if (count($this->view->dados['envioArquivos']) > 0): ?>
            <tbody>

                <?php
                    $classeLinha = "par";
                    ?>
                    <?php foreach ($this->view->dados['envioArquivos'] as $linha => $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "impar") ? "par" : "impar"; ?>
                            <tr class="<?php echo $classeLinha; ?>">
                                <td class="esquerda"><?php echo $resultado->empresa; ?></td>
                                <td class="centro"><?php echo date('d/m/Y H:i:s', strtotime($resultado->apcedt_envio)); ?></td>
                                <td class="centro"><?php echo $resultado->banco; ?></td>
                                <td class="centro"><?php echo ($resultado->apcedt_retorno == NULL ? "Aguardando Processamento" : "Processado"); ?></td>
                                <td class="centro"><?php echo $resultado->apceapgno_remessa; ?></td>
                            </tr>
                    <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" id="registros_encontrados" class="centro">
                        <?php
                        $totalRegistros = count($this->view->dados['envioArquivos']);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <div class="acoes">
                            
                            <? // [START][ORGMKTOTVS-1185] - Leandro Corso ?>
                            <input
                                type="button" 
                                value="Verificar retorno"
                                <?= INTEGRACAO 
                                    ? 'disabled readonly'
                                    : 'id="atualizar_status"' ?>
                            />
                            <? if (INTEGRACAO) echo ParametroIntegracaoTotvs::message('O botão "verificar retorno"');
                            // [END][ORGMKTOTVS-1185] - Leandro Corso ?>

                        </div>
                    </td>
                </tr>
            </tfoot>
        <?php else : ?>
            <tbody>
                <tr>
                    <td colspan="6" class="centro">
                        Nenhum registro encontrado.
                    </td>
                </tr>
            </tbody>
        <?php endif; ?>
        </table>
    </div>
    <?php echo $this->view->paginacao; ?>
</div>