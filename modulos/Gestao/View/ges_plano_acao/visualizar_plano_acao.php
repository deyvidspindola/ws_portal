
<style type="text/css">
    .bloco_opcoes li {
        display: block;
    }

    .campo_menor_fix{
        width: 126px;
    }

    .textare-group{                 
         width: 356px !important;
    }

    .titulo-plano-acao{
            font-weight: bold;
            margin: 0 20px 20px;
    }

    .no-border{
        border: 0px !important;
    }

    .em-execucao{
        background: url('images/execucao-icon.png') no-repeat 6px 5px #E6EAEE !important;
    }

    .a-iniciar{
        background: url('images/iniciar-icon.png') no-repeat 6px 5px #E6EAEE !important;
    }

    .em-atraso{
        background: url('images/atraso-icon.png') no-repeat 6px 5px #E6EAEE !important;
    }

    .concluido{
        background: url('images/concluido-icon.png') no-repeat 6px 5px #E6EAEE !important;
    }

    .cancelado{
        background: url('images/cancelado.png') no-repeat 6px 5px #E6EAEE !important;
    }
</style>        
    <div class="titulo-plano-acao"><?php echo $this->view->plano_acao ?></div>

    <?php foreach ($this->view->acoes AS $key => $status): ?>
    <div class="<?php echo $key ?> titulo-bloco-acoes no-border bloco_titulo">&nbsp&nbsp&nbsp&nbsp<?php echo $status['acao'] ?></div>
    <div class=" no-border bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
                        <th class="menor centro">Data</th>
                        <th class="maior centro">Descrição</th>
                        <th class="maior centro">Responsável</th>
                        <th class="menor centro">%</th>
                    </tr>
                </thead>
                <tbody>
                    <?php unset($status['acao']); ?>
                    <?php foreach ($status AS $item) : ?>                                
                        <tr class="impar">
                            <td class="centro"><?php echo $item['data'] ?></td>
                            <td class="esquerda"><a href="ges_acoes.php?acao=editar&plano=<?php echo $item['id_plano_acao']?>&ano=<?php echo $_GET['ano']?>&id_acao=<?php echo $item['id_acao']?>"><?php echo $item['descricao'] ?></a></td>
                            <td class="esquerda"><?php echo $item['responsavel'] ?></td>
                            <td class="direita"><?php echo $item['porcentagem'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                
            </table>
        </div>
    </div>

    <div class="separador"></div>
<?php endforeach; ?>

<!-- </div> -->
