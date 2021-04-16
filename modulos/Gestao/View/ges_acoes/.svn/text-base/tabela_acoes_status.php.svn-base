<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/style.css" />
<link type="text/css" rel="stylesheet" href="lib/layout/1.1.0/cupertino/jquery-ui-1.10.0.custom.min.css" />

<!-- jQuery -->
<script type="text/javascript" src="modulos/web/js/lib/jQuery.js"></script>

<!-- Arquivos básicos de javascript -->
<script type="text/javascript" src="lib/js/jquery-ui-1.10.0.custom.min.js"></script>
<script type="text/javascript" src="lib/layout/1.1.0/bootstrap.js"></script>

<script type="text/javascript" src="modulos/web/js/lib/jquery.maskedinput-1.3.min.js"></script>

<div class="separador"></div>
<div style="clear:both"></div>

<div style="position:relative; width: 100%; height:200px;">

    <div style=" width: 800px; position:absolute; top:0px; left:50%; margin-left: -400px">
            <div class="resultado bloco_titulo">Total de Ações dos Planos de Ação</div>
            <div class="resultado bloco_conteudo">
                <div class="listagem">
                    <table>
                        <thead>
                            <tr>
                                <th class="maior"></th>
                                <th>Minhas Ações</th>
                                <th>Subordinados Diretos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($this->view->dados) > 0) : ?>
                                <?php foreach ($this->view->dados as $nomeTotalizador => $totalizador) : ?>
                                    <?php $classeLinha = ($classeLinha == "par") ? "impar" : "par"; ?>

                                    <tr class="<?php echo $classeLinha; ?>">
                                        <td><?php echo $nomeTotalizador; ?></td>
                                        <td class="centro"><?php echo $totalizador['minhas']; ?></td>
                                        <td class="centro"><?php echo $totalizador['subordinados']; ?></td>
                                    </tr>
                                    <?php $totalMinhasAcoes += $totalizador['minhas']; ?>
                                    <?php $totalAcoesSubordinados += $totalizador['subordinados']; ?>

                                <?php endforeach; ?>
                            <?php endif; ?>

                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="esquerda"><?php echo "TOTAL"; ?></td>
                                <td class="centro"><?php echo $totalMinhasAcoes; ?></td>
                                <td class="centro"><?php echo $totalAcoesSubordinados; ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

    </div>
    

</div>
