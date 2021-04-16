<div class="separador"></div>
<div id="resultados_relatorio" class="<?php echo ($this->view->resultados) ? '' : 'invisivel'; ?>">
    <div class="bloco_conteudo">
        <div class="listagem" style="overflow-x: scroll; white-space: nowrap;">
            <table <?php echo (trim($_POST['sub_acao'])=='gerarPdf') ? 'border="1"' : "";  ?>>
                <thead>
                    <tr>
                        <th class="medio">Seguradora</th>
                        <?php
                            $arrayMeses = array();
                            $arrayMeses[1] = "Jan";
                            $arrayMeses[2] = "Fev";
                            $arrayMeses[3] = "Mar";
                            $arrayMeses[4] = "Abr";
                            $arrayMeses[5] = "Mai";
                            $arrayMeses[6] = "Jun";
                            $arrayMeses[7] = "Jul";
                            $arrayMeses[8] = "Ago";
                            $arrayMeses[9] = "Set";
                            $arrayMeses[10] = "Out";
                            $arrayMeses[11] = "Nov";
                            $arrayMeses[12] = "Dez";

                            $ultimoDataHead = array();
                            $ultimaSeguradora = '#';
                            $totais = array();
                        ?>
                        <?php foreach($this->view->dadosResumo->resumo_mensal as $dadosSeguradora): ?>
                            <?php foreach ($dadosSeguradora as $dataOcorrencia => $totaisRecup): ?>
                            <?php
                                $mes = explode("-", $dataOcorrencia);
                                $dataHead = $arrayMeses[intval($mes[1])] . "/" . substr($mes[0], 2,2);

                                if(!in_array($dataHead,$ultimoDataHead)):
                            ?>
                                <th class="menor"><?php echo $dataHead; ?></th>
                                <th class="menor">Rec</th>
                                <th class="menor">NRec</th>
                                <?php   $ultimoDataHead[] = $dataHead; ?>
                                <?php endif;?>
                            <?php endForEach;?>
                        <?php endForEach;?>
                    </tr>
                </thead>
                <tbody>
                        <?php foreach($this->view->dadosResumo->resumo_mensal as $seguradora => $dadosSeguradora): ?>

                            <?php $rowClass = ($rowClass == "impar") ? "par" : "impar"; ?>
                                <?php if ($ultimaSeguradora != $seguradora): ?>
                                    <tr class="<?php echo $rowClass; ?>">
                                    <td class="medio"><?php echo $seguradora; ?></td>
                                <?php endif;?>
                                 <?php foreach ($dadosSeguradora as $dataOcorrencia => $totaisRecup): ?>
                                <td class="menor centro">
                                    <?php
                                        echo ($totaisRecup->total_recup + $totaisRecup->total_nao_recup);
                                        $totais[$dataOcorrencia]['total_mes'] += ($totaisRecup->total_recup + $totaisRecup->total_nao_recup);
                                    ?>
                                </td>
                                <td class="centro menor">
                                    <?php
                                        echo $totaisRecup->total_recup;
                                        $totais[$dataOcorrencia]['total_rec'] += $totaisRecup->total_recup;
                                    ?>
                                </td>
                                <td class="centro menor">
                                    <?php
                                        echo $totaisRecup->total_nao_recup;
                                        $totais[$dataOcorrencia]['total_nrec'] += $totaisRecup->total_nao_recup;
                                    ?>
                                </td>
                                <?php endForEach;?>
                                 <?php if ($ultimaSeguradora != $seguradora): ?>
                                    </tr>
                                    <?php $ultimaSeguradora = $seguradora; ?>
                                <?php endif;?>
                        <?php endForEach;?>

                </tbody>
                <tfoot>
                    <tr>
                        <td>Total</td>
                        <?php foreach($totais as $key => $total): ?>
                            <td class="medio centro"><?php echo $total['total_mes'] ?></td>
                            <td class="centro menor"><?php echo $total['total_rec'] ?></td>
                            <td class="centro menor"><?php echo $total['total_nrec'] ?></td>
                        <?php endForEach;?>
                    </tr>
                </tfoot>

            </table>
        </div>
    </div>
    <div class="separador"></div>

    <div class="bloco_titulo">
        Índice de Ocorrências Comunicadas e/ou Recuperadas no Período de
        <?php
            echo $this->view->parametros->ococdperiodo_inicial . " à ";
            echo $this->view->parametros->ococdperiodo_final;
        ?>
    </div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table <?php echo (trim($_POST['sub_acao'])=='gerarPdf') ? 'border="1"' : "";  ?>>
                <tbody>
                     <?php require_once 'resumo.php';?>
                </tbody>

            </table>
        </div>
    </div>


    <div class="separador"></div>
    <div class="bloco_titulo">
        Por Classe de Equipamento no período de
        <?php
            echo $this->view->parametros->ococdperiodo_inicial . " à ";
            echo $this->view->parametros->ococdperiodo_final;
        ?>
    </div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table <?php echo (trim($_POST['sub_acao'])=='gerarPdf') ? 'border="1"' : "";  ?>>
                <thead>
                    <tr>
                        <th>Classe</th>
                        <th>Recuperados</th>
                        <th>Não Recuperados</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $totalRecup = 0;
                        $totalNaoRecup = 0;
                    ?>
                    <?php foreach($this->view->dados as $ocorrencia): ?>
                        <?php if($ocorrencia->tipo == 'por_equipamento'):?>
                        <?php $rowClass = ($rowClass == "impar") ? "par" : "impar"; ?>
                            <tr class="<?php echo $rowClass; ?>">
                               <td class="maior">
                                   <?php echo $ocorrencia->coluna1; ?>
                               </td>
                               <td class="medio direita">
                                   <?php
                                        echo $ocorrencia->recuperados;
                                        $totalRecup += $ocorrencia->recuperados;
                                    ?>
                               </td>
                               <td class="medio direita">
                                   <?php
                                        echo $ocorrencia->nao_recuperados;
                                        $totalNaoRecup += $ocorrencia->nao_recuperados;
                                    ?>
                               </td>
                            </tr>
                        <?php endif;?>
                    <?php endForEach;?>
                </tbody>
                <tfoot>
                    <tr>
                       <td class="esquerda">Total Geral</td>
                       <td class="medio direita"><?php echo $totalRecup;?></td>
                       <td class="medio direita"><?php echo $totalNaoRecup;?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>


    <div class="separador"></div>

    <div class="bloco_titulo">Por Modelo de Veículo no período de
        <?php
            echo $this->view->parametros->ococdperiodo_inicial . " à ";
            echo $this->view->parametros->ococdperiodo_final;
        ?>
    </div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table <?php echo (trim($_POST['sub_acao'])=='gerarPdf') ? 'border="1"' : "";  ?>>
                <thead>
                    <tr>
                        <th>Marca/Modelo Veículo </th>
                        <th>Recuperados</th>
                        <th>Não Recuperados</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                     <?php
                        $totalRecup = 0;
                        $totalNaoRecup = 0;
                        $totalGeral = 0;
                    ?>
                    <?php foreach($this->view->dados as $ocorrencia): ?>
                        <?php if($ocorrencia->tipo == 'por_modelo_veiculo'):?>
                        <?php $rowClass = ($rowClass == "impar") ? "par" : "impar"; ?>
                        <tr class="<?php echo $rowClass; ?>">
                           <td class="maior">
                               <?php echo $ocorrencia->coluna1; ?>
                           </td>
                           <td class="medio direita">
                               <?php
                                    echo $ocorrencia->recuperados;
                                    $totalRecup += $ocorrencia->recuperados;
                                ?>
                           </td>
                           <td class="medio direita">
                               <?php
                                    echo $ocorrencia->nao_recuperados;
                                    $totalNaoRecup += $ocorrencia->nao_recuperados;
                                ?>
                           </td>
                           <td class="medio direita">
                               <?php
                                    echo (intval($ocorrencia->recuperados) + intval($ocorrencia->nao_recuperados));
                                    $totalGeral +=  (intval($ocorrencia->recuperados) + intval($ocorrencia->nao_recuperados));
                                ?>
                           </td>
                        </tr>
                        <?php endif;?>
                    <?php endForEach;?>
                </tbody>
                <tfoot>
                    <tr>
                       <td class="esquerda">Total Geral</td>
                       <td class="medio direita"><?php echo $totalRecup;?></td>
                       <td class="medio direita"><?php echo $totalNaoRecup;?></td>
                       <td class="medio direita"><?php echo $totalGeral;?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>



    <div class="separador"></div>
    <div class="bloco_titulo">Por Estado no período de
        <?php
            echo $this->view->parametros->ococdperiodo_inicial . " à ";
            echo $this->view->parametros->ococdperiodo_final;
        ?>
    </div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table <?php echo (trim($_POST['sub_acao'])=='gerarPdf') ? 'border="1"' : "";  ?>>
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th>Recuperados</th>
                        <th>Não Recuperados</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $totalRecup = 0;
                        $totalNaoRecup = 0;
                    ?>
                    <?php foreach($this->view->dados as $ocorrencia): ?>
                        <?php if($ocorrencia->tipo == 'por_estado'):?>
                        <?php $rowClass = ($rowClass == "impar") ? "par" : "impar"; ?>
                            <tr class="<?php echo $rowClass; ?>">
                               <td class="maior">
                                   <?php echo $ocorrencia->coluna1; ?>
                               </td>
                               <td class="medio direita">
                                   <?php
                                        echo $ocorrencia->recuperados;
                                        $totalRecup += $ocorrencia->recuperados;
                                    ?>
                               </td>
                               <td class="medio direita">
                                   <?php
                                        echo $ocorrencia->nao_recuperados;
                                        $totalNaoRecup += $ocorrencia->nao_recuperados;
                                    ?>
                               </td>
                            </tr>
                        <?php endif;?>
                    <?php endForEach;?>
                </tbody>
                <tfoot>
                    <tr>
                       <td class="esquerda">Total Geral</td>
                       <td class="medio direita"><?php echo $totalRecup;?></td>
                       <td class="medio direita"><?php echo $totalNaoRecup;?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="separador"></div>


    <div class="bloco_titulo">Por Estado/Cidade/Marca/Modelo/Veículo Tipo no período de
        <?php
            echo $this->view->parametros->ococdperiodo_inicial . " à ";
            echo $this->view->parametros->ococdperiodo_final;
        ?>
    </div>
    <div class="bloco_conteudo">
        <div class="listagem">
                <table <?php echo (trim($_POST['sub_acao'])=='gerarPdf') ? 'border="1"' : "";  ?>>
                    <thead>
                        <tr>
                            <th>Cidade</th>
                            <th>Marca/Modelo</th>
                            <th>Veículo Tipo</th>
                            <th>Recuperados</th>
                            <th>Não Recuperados</th>
                        </tr>
                    </thead>
                    <tbody>
                <?php
                    $totalRecup = 0;
                    $totalNaoRecup = 0;
                    $subTotalRecup = 0;
                    $subTotalNaoRecup = 0;
                    $subTotalTipoRecup = 0;
                    $subTotalTipoNaoRecup = 0;
                    $uf = "";
                    $tipo = "";
                    foreach($this->view->dados as $ocorrencia):
                        if($ocorrencia->tipo == 'por_est_cidade_modelo_tipo'):
                            $rowClass = ($rowClass == "impar") ? "par" : "impar";
                            if($tipo !=  $ocorrencia->coluna2 || $uf !=  $ocorrencia->coluna1):
                                if($tipo != ""):
                                    echo "<tr class=\"$rowClass\">
                                           <td class=\"esquerda\" colspan=\"3\" ><b>Subtotal $uf - $tipo</b></td>
                                           <td class=\"medio direita\"><b>$subTotalTipoRecup</b></td>
                                           <td class=\"medio direita\"><b>$subTotalTipoNaoRecup</b></td>
                                        </tr>";
                                        $rowClass = ($rowClass == "impar") ? "par" : "impar";
                                        $subTotalTipoRecup = 0;
                                        $subTotalTipoNaoRecup = 0;
                                endif;
                                $tipo = $ocorrencia->coluna2;
                            endif;
                            if($uf !=  $ocorrencia->coluna1):
                                if($uf != ""):
                                    echo "<tr class=\"$rowClass\">
                                           <td class=\"esquerda\" colspan=\"3\" ><b>Total $uf</b></td>
                                           <td class=\"medio direita\"><b>$subTotalRecup</b></td>
                                           <td class=\"medio direita\"><b>$subTotalNaoRecup</b></td>
                                        </tr>";
                                        $rowClass = ($rowClass == "impar") ? "par" : "impar";
                                        $subTotalRecup = 0;
                                        $subTotalNaoRecup = 0;
                                endif;
                                echo "<tr class=\"$rowClass\"><td class=\"esquerda\" colspan=\"5\"><b>".$ocorrencia->coluna1."</b></td></tr>";
                                $rowClass = ($rowClass == "impar") ? "par" : "impar";
                                $uf = $ocorrencia->coluna1;
                            endif;
                            ?>
                            <tr class="<?php echo $rowClass;?>">
                               <td class="maior"><?php echo $ocorrencia->coluna9; ?></td>
                               <td class="medio"><?php echo $ocorrencia->coluna5; ?></td>
                               <td class="medio"><?php echo $ocorrencia->coluna2; ?></td>
                               <td class="menor direita"><?php echo $ocorrencia->recuperados; ?></td>
                               <td class="menor direita"><?php echo $ocorrencia->nao_recuperados; ?></td>
                            </tr>
                            <?php
                            $totalRecup += $ocorrencia->recuperados;
                            $totalNaoRecup += $ocorrencia->nao_recuperados;
                            $subTotalRecup += $ocorrencia->recuperados;
                            $subTotalNaoRecup += $ocorrencia->nao_recuperados;
                            $subTotalTipoRecup += $ocorrencia->recuperados;
                            $subTotalTipoNaoRecup += $ocorrencia->nao_recuperados;
                        endif;
                    endforeach;
                    if($tipo != ""):
                        echo "<tr class=\"$rowClass\">
                               <td class=\"esquerda\" colspan=\"3\" ><b>Subtotal $uf - $tipo</b></td>
                               <td class=\"medio direita\"><b>$subTotalTipoRecup</b></td>
                               <td class=\"medio direita\"><b>$subTotalTipoNaoRecup</b></td>
                            </tr>";
                            $rowClass = ($rowClass == "impar") ? "par" : "impar";
                            $subTotalTipoRecup = 0;
                            $subTotalTipoNaoRecup = 0;
                    endif;
                    if($uf != ""):
                        echo "<tr class=\"$rowClass\">
                               <td class=\"esquerda\" colspan=\"3\" ><b>Total $uf</b></td>
                               <td class=\"medio direita\"><b>$subTotalRecup</b></td>
                               <td class=\"medio direita\"><b>$subTotalNaoRecup</b></td>
                            </tr>";
                            $rowClass = ($rowClass == "impar") ? "par" : "impar";
                            $subTotalRecup = 0;
                            $subTotalNaoRecup = 0;
                    endif;
                ?>
                </tbody>
                <tfoot>
                    <tr>
                       <td class="esquerda" colspan="3" ><b>Total Geral</b></td>
                       <td class="medio direita"><b><?php echo $totalRecup;?></b></td>
                       <td class="medio direita"><b><?php echo $totalNaoRecup;?></b></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="separador"></div>

    <div class="bloco_titulo">Por Estado/Horário Ocorrência no período de
        <?php
            echo $this->view->parametros->ococdperiodo_inicial . " à ";
            echo $this->view->parametros->ococdperiodo_final;
        ?>
    </div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table <?php echo (trim($_POST['sub_acao'])=='gerarPdf') ? 'border="1"' : "";  ?>>
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th>Horário</th>
                        <th>Recuperados</th>
                        <th>Não Recuperados</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $totalRecup = 0;
                        $totalNaoRecup = 0;
                        $subTotalRecup = 0;
                        $subTotalNaoRecup = 0;
                        $uf = "";
                    ?>
                    <?php
                    foreach($this->view->dados as $ocorrencia):

                        if($ocorrencia->tipo == 'por_estado_horario'):

                            if($uf != $ocorrencia->coluna1 && $uf != ""): ?>
                                    <tr class="par">
                                        <td class="maior" colspan="2" >Total <?php echo $uf; ?></td>
                                        <td class="medio direita">
                                            <?php
                                            $totalLinha = ($subTotalRecup + $subTotalNaoRecup);
                                                echo $subTotalRecup;
                                                $subTotalRecup=0;
                                            ?>
                                        </td>
                                        <td class="medio direita">
                                           <?php
                                                echo $subTotalNaoRecup;
                                                $subTotalNaoRecup=0;
                                            ?>
                                        </td>
                                        <td class="medio direita">
                                           <?php
                                                echo $totalLinha;
                                            ?>
                                        </td>
                                    </tr>
                <?php 		endif;  ?>

                            <tr class="impar">
                                <td class="maior">
                                   <?php
                                        if($uf != $ocorrencia->coluna1):
                                            echo $ocorrencia->coluna1;
                                            $uf=$ocorrencia->coluna1;
                                        endif;
                                   ?>
                                </td>
                                <td class="centro"><?php echo $ocorrencia->coluna2; ?></td>
                                <td class="medio direita">
                                   <?php
                                        echo $ocorrencia->recuperados;
                                        $totalRecup += $ocorrencia->recuperados;
                                        $subTotalRecup += $ocorrencia->recuperados;
                                    ?>
                                </td>
                                <td class="medio direita">
                                   <?php
                                        echo $ocorrencia->nao_recuperados;
                                        $totalNaoRecup += $ocorrencia->nao_recuperados;
                                        $subTotalNaoRecup += $ocorrencia->nao_recuperados;
                                    ?>
                                </td>
                                <td class="medio direita">
                                   <?php
                                        echo ($ocorrencia->recuperados + $ocorrencia->nao_recuperados);
                                    ?>
                                </td>
                            </tr>
                                <?php $totalLinha = ($subTotalRecup + $subTotalNaoRecup); ?>
                        <?php endif;
                     endForEach;

                    if($uf != ""): ?>
                            <tr class="par">
                                <td class="maior" colspan="2" >Total <?php echo $uf; ?></td>
                                <td class="medio direita">
                                    <?php
                                        echo $subTotalRecup;
                                        $subTotalRecup=0;
                                    ?>
                                </td>
                                <td class="medio direita">
                                   <?php
                                        echo $subTotalNaoRecup;
                                        $subTotalNaoRecup=0;
                                    ?>
                                </td>
                                <td class="medio direita">
                                   <?php
                                        echo $totalLinha;
                                    ?>
                                </td>
                            </tr>
            <?php 	endif;  ?>
                </tbody>
                <tfoot>
                    <tr>
                       <td class="esquerda" colspan="2" >Total Geral</td>
                       <td class="medio direita"><?php echo $totalRecup; ?></td>
                       <td class="medio direita"><?php echo $totalNaoRecup; ?></td>
                       <td class="medio direita"><?php echo $totalNaoRecup+$totalRecup; ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="separador"></div>

    <div class="bloco_titulo">Por Estado/Dia da Semana no período de
        <?php
            echo $this->view->parametros->ococdperiodo_inicial . " à ";
            echo $this->view->parametros->ococdperiodo_final;
        ?>
    </div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table <?php echo (trim($_POST['sub_acao'])=='gerarPdf') ? 'border="1"' : "";  ?>>
                <thead>
                    <tr>
                        <th class="menor">Estado</th>
                        <th>Dia da Semana</th>
                        <th>Recuperados</th>
                        <th>Não Recuperados</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalRecup = 0;
                    $totalNaoRecup = 0;
                    $subTotalRecup = 0;
                    $subTotalNaoRecup = 0;
                    $uf = "";

                    foreach($this->view->dados as $ocorrencia):

                        if($ocorrencia->tipo == 'por_estado_dia_semana'):

                            if($uf != $ocorrencia->coluna1 && $uf != ""): ?>
                                    <tr class="par">
                                        <td class="maior" colspan="2" >Total <?php echo $uf; ?></td>
                                        <td class="medio direita">
                                            <?php
                                                $totalLinha = ($subTotalRecup + $subTotalNaoRecup);
                                                echo $subTotalRecup;
                                                $subTotalRecup=0;
                                            ?>
                                        </td>
                                        <td class="medio direita">
                                           <?php
                                                echo $subTotalNaoRecup;
                                                $subTotalNaoRecup=0;
                                            ?>
                                        </td>
                                        <td class="medio direita">
                                           <?php
                                                echo $totalLinha;
                                            ?>
                                        </td>
                                    </tr>
                <?php 		endif;  ?>

                            <tr class="impar">
                                <td class="menor">
                                   <?php
                                        if($uf != $ocorrencia->coluna1):
                                            echo $ocorrencia->coluna1;
                                            $uf=$ocorrencia->coluna1;
                                        endif;
                                   ?>
                                </td>
                                <td class="esquerda"><?php echo $ocorrencia->coluna2; ?></td>
                                <td class="medio direita">
                                   <?php
                                        echo $ocorrencia->recuperados;
                                        $totalRecup += $ocorrencia->recuperados;
                                        $subTotalRecup += $ocorrencia->recuperados;
                                    ?>
                                </td>
                                <td class="medio direita">
                                   <?php
                                        echo $ocorrencia->nao_recuperados;
                                        $totalNaoRecup += $ocorrencia->nao_recuperados;
                                        $subTotalNaoRecup += $ocorrencia->nao_recuperados;
                                    ?>
                                </td>
                                <td class="medio direita">
                                   <?php
                                        echo $ocorrencia->nao_recuperados+$ocorrencia->recuperados;
                                    ?>
                                </td>
                            </tr>
                            <?php $totalLinha = ($subTotalRecup + $subTotalNaoRecup); ?>
                        <?php endif;
                     endforeach;

                    if($uf != ""): ?>
                            <tr class="par">
                                <td class="maior" colspan="2" >Total <?php echo $uf; ?></td>
                                <td class="medio direita">
                                    <?php
                                        echo $subTotalRecup;
                                        $subTotalRecup=0;
                                    ?>
                                </td>
                                <td class="medio direita">
                                   <?php
                                        echo $subTotalNaoRecup;
                                        $subTotalNaoRecup=0;
                                    ?>
                                </td>
                                <td class="medio direita">
                                   <?php
                                        echo $totalLinha;
                                    ?>
                                </td>
                            </tr>
            <?php 	endif;  ?>
                </tbody>
                <tfoot>
                    <tr>
                       <td class="esquerda" colspan="2" >Total Geral</td>
                       <td class="medio direita"><?php echo $totalRecup; ?></td>
                       <td class="medio direita"><?php echo $totalNaoRecup; ?></td>
                       <td class="medio direita"><?php echo $totalNaoRecup+$totalRecup; ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="separador"></div>

    <div class="bloco_titulo">Por Estado/Veículo Tipo no período de
        <?php
            echo $this->view->parametros->ococdperiodo_inicial . " à ";
            echo $this->view->parametros->ococdperiodo_final;
        ?>
    </div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table <?php echo (trim($_POST['sub_acao'])=='gerarPdf') ? 'border="1"' : "";  ?>>
                <thead>
                    <tr>
                        <th class="medio">Estado</th>
                        <th>Veículo Tipo</th>
                        <th>Recuperados</th>
                        <th>Não Recuperados</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalRecup = 0;
                    $totalNaoRecup = 0;
                    $subTotalRecup = 0;
                    $subTotalNaoRecup = 0;
                    $uf = "";

                    foreach($this->view->dados as $ocorrencia):

                        if($ocorrencia->tipo == 'por_estado_veiculo_tipo'):

                            if($uf != $ocorrencia->coluna1 && $uf != ""): ?>
                                    <tr class="par">
                                        <td class="maior" colspan="2" >Total <?php echo $uf; ?></td>
                                        <td class="medio direita">
                                            <?php
                                                $totalLinha = ($subTotalRecup + $subTotalNaoRecup);
                                                echo $subTotalRecup;
                                                $subTotalRecup=0;
                                            ?>
                                        </td>
                                        <td class="medio direita">
                                           <?php
                                                echo $subTotalNaoRecup;
                                                $subTotalNaoRecup=0;
                                            ?>
                                        </td>
                                        <td class="medio direita">
                                           <?php
                                                echo $totalLinha;
                                            ?>
                                        </td>
                                    </tr>
                <?php 		endif;  ?>

                            <tr class="impar">
                                <td class="menor">
                                   <?php
                                        if($uf != $ocorrencia->coluna1):
                                            echo $ocorrencia->coluna1;
                                            $uf=$ocorrencia->coluna1;
                                        endif;
                                   ?>
                                </td>
                                <td class="esquerda"><?php echo $ocorrencia->coluna2; ?></td>
                                <td class="medio direita">
                                   <?php
                                        echo $ocorrencia->recuperados;
                                        $totalRecup += $ocorrencia->recuperados;
                                        $subTotalRecup += $ocorrencia->recuperados;
                                    ?>
                                </td>
                                <td class="medio direita">
                                   <?php
                                        echo $ocorrencia->nao_recuperados;
                                        $totalNaoRecup += $ocorrencia->nao_recuperados;
                                        $subTotalNaoRecup += $ocorrencia->nao_recuperados;
                                    ?>
                                </td>
                                <td class="medio direita">
                                   <?php
                                        echo $ocorrencia->nao_recuperados+$ocorrencia->recuperados;
                                    ?>
                                </td>
                            </tr>
                            <?php $totalLinha = ($subTotalRecup + $subTotalNaoRecup); ?>
                        <?php endif;

                     endforeach;

                    if($uf != ""): ?>
                            <tr class="par">
                                <td class="maior" colspan="2" >Total <?php echo $uf; ?></td>
                                <td class="medio direita">
                                    <?php
                                        echo $subTotalRecup;
                                        $subTotalRecup=0;
                                    ?>
                                </td>
                                <td class="medio direita">
                                   <?php
                                        echo $subTotalNaoRecup;
                                        $subTotalNaoRecup=0;
                                    ?>
                                </td>
                                <td class="medio direita">
                                   <?php
                                        echo $totalLinha;
                                    ?>
                                </td>
                            </tr>
            <?php 	endif;  ?>
                </tbody>
                <tfoot>
                    <tr>
                       <td class="esquerda" colspan="2" >Total Geral</td>
                       <td class="medio direita"><?php echo $totalRecup; ?></td>
                       <td class="medio direita"><?php echo $totalNaoRecup; ?></td>
                       <td class="medio direita"><?php echo $totalNaoRecup+$totalRecup; ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="separador"></div>

    <div class="bloco_titulo">Recuperações no período de
        <?php
            echo $this->view->parametros->ococdperiodo_inicial . " à ";
            echo $this->view->parametros->ococdperiodo_final;
        ?>
    </div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table <?php echo (trim($_POST['sub_acao'])=='gerarPdf') ? 'border="1"' : "";  ?>>
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Placa</th>
                        <th>Ano</th>
                        <th>Chassi</th>
                        <th>Veículo</th>
                        <th>CNPJ/CPF</th>
                        <th>Data do Evento</th>
                        <th>Data da Comunicação</th>
                        <th>Cidade de Ocorrência </th>
                        <th>Data recuperação</th>
                        <th>Cidade de Recuperação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($this->view->dados as $ocorrencia):
                        $rowClass = ($rowClass == "impar") ? "par" : "impar";

                        if($ocorrencia->tipo == 'detalhado' && $ocorrencia->recuperados > 0):
                            ?>
                            <tr class="<?php echo $rowClass; ?>">
                               <td class=""><?php echo $ocorrencia->coluna1; ?></td>
                               <td class=""><?php echo $ocorrencia->coluna2; ?></td>
                               <td class="direita"><?php echo $ocorrencia->coluna3; ?></td>
                               <td class=""><?php echo $ocorrencia->coluna4; ?></td>
                               <td class=""><?php echo $ocorrencia->coluna5; ?></td>
                               <td class="direita"><?php echo $ocorrencia->coluna6; ?></td>
                               <td class="centro"><?php echo $ocorrencia->coluna7; ?></td>
                               <td class="centro"><?php echo $ocorrencia->coluna8 . ":00"; ?></td>
                               <td class=""><?php echo $ocorrencia->coluna9; ?></td>
                               <td class="centro"><?php echo $ocorrencia->coluna10; ?></td>
                               <td class=""><?php echo $ocorrencia->coluna11; ?></td>
                            </tr>
                            <?php
                        endif;
                     endforeach;
                    ?>

                </tbody>
            </table>
        </div>
    </div>

    <div class="separador"></div>

    <div class="bloco_titulo">Veículos não Recuperados no período de
        <?php
            echo $this->view->parametros->ococdperiodo_inicial . " à ";
            echo $this->view->parametros->ococdperiodo_final;
        ?>
    </div>
    <div class="bloco_conteudo">
        <div class="listagem">
            <table <?php echo (trim($_POST['sub_acao'])=='gerarPdf') ? 'border="1"' : "";  ?>>
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Placa</th>
                        <th>Ano</th>
                        <th>Chassi</th>
                        <th>Veículo</th>
                        <th>CNPJ/CPF</th>
                        <th>Data do Evento</th>
                        <th>Data da Comunicação</th>
                        <th>Cidade de Ocorrência </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($this->view->dados as $ocorrencia):
                        $rowClass = ($rowClass == "impar") ? "par" : "impar";
                        if($ocorrencia->tipo == 'detalhado' && $ocorrencia->nao_recuperados > 0):
                            ?>
                            <tr class="<?php echo $rowClass; ?>">
                               <td class=""><?php echo $ocorrencia->coluna1; ?></td>
                               <td class=""><?php echo $ocorrencia->coluna2; ?></td>
                               <td class="direita"><?php echo $ocorrencia->coluna3; ?></td>
                               <td class=""><?php echo $ocorrencia->coluna4; ?></td>
                               <td class=""><?php echo $ocorrencia->coluna5; ?></td>
                               <td class="direita"><?php echo $ocorrencia->coluna6; ?></td>
                               <td class="centro"><?php echo $ocorrencia->coluna7; ?></td>
                               <td class="centro"><?php echo $ocorrencia->coluna8 . ":00"; ?></td>
                               <td class=""><?php echo $ocorrencia->coluna9; ?></td>
                            </tr>
                            <?php
                        endif;
                     endforeach;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="separador"></div>

<?php if ((trim($_POST['sub_acao'])!='gerarPdf')): ?>
    <div class="bloco_acoes">
        <button type="button" id="gerar_pdf">Gerar PDF</button>
        <button type="button" id="btn_gerar_xls">Gerar XLS</button>
        <button type="button" onclick="javascript:window.print();">Imprimir</button>
    </div>
<?php endif; ?>
</div>

<?php if (trim($_POST['sub_acao'])!='gerarPdf'): ?>
     <?php require_once _MODULEDIR_ . "Relatorio/View/rel_ocorrencia_novo_irv_congelado/bloco_csv.php"; ?>
<?php endif; ?>