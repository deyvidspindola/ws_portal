<div class="separador"></div>
<div class="bloco_titulo">Resultado </div>
<div class="bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
                    <th class="menor">Dt. Inclusão</th>
                    <th class="maior">Cliente Indicador</th>
                    <th class="maior">CNPJ/CPF</th>
                    <th class="menor">Contrato</th>
                    <th class="maior">Cliente Indicado</th>
                    <th class="maior">Campanha Promocional</th>
                    <th class="maior">Vigência Camp. Prom.</th>
                    <th class="menor">Equipamento Instalado?</th>
                    <th class="menor">Inclusão</th>
                    <th class="maior">Incluso por</th>
                </tr>
            </thead>
            <tbody>	
                <?php
foreach ($this->view->dados as $item) { 
    $class = $class == 'impar' ? 'par' : 'impar'; 
    if ((!empty ($item->dt_inicio_vigencia)) || (!empty ($item->dt_fim_vigencia))) {
        $dtVigencia = $item->dt_inicio_vigencia . " a " . $item->dt_fim_vigencia;
    } else {
        $dtVigencia = "";
    }
                ?>
                    <tr class="<?php echo $class ?>">
                        <td align="center"><?php echo $item->dt_inclusao; ?></td>
                        <td align="left"><?php echo $item->cliente_nome; ?></td>
                        <td align="center"><?php echo $item->doc; ?></td>
                        <td align="center"><?php echo $item->contrato; ?></td>
                        <td align="center"><?php echo $item->cliente_nome_indicado; ?></td>
                        <td align="left"><?php echo $item->nome_campanha; ?></td>
                        <td align="left"><?php echo $dtVigencia; ?></td>
                        <td align="center"><?php echo $item->cfcieqpto_instalado_descricao; ?></td>
                        <td align="center"><?php echo $item->cfciforma_inclusao_descricao; ?></td>
                        <td align="center"><?php echo $item->usuario_inclusao_nome; ?></td>
                    </tr>
                <?php 
}
                ?>
            </tbody>
        </table>
    </div>
</div> 
<div class="bloco_acoes">
    <p>
    <?php
        $totalRegistros = count($this->view->dados);
        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
    ?>
    </p>
</div> 

<div class="separador"></div>

<?php if ($this->view->xls !== false) { ?>
    <div class="bloco_titulo">Download</div>
    <div class="bloco_conteudo">
        <div class="conteudo centro">
            <a target="_blank" href="download.php?arquivo=<?php echo $this->view->xls ?>">
                <img src="images/icones/t3/caixa2.jpg">
                <br>
                <?php echo "Relatório Cliente Indicador" ?>
            </a>
        </div>
    </div>

    <?php 
}
?>   