<div id="resultado_pesquisa">
    <div class="separador"></div>
    <div class="resultado bloco_titulo">Resultado da Pesquisa</div>
    <div class="resultado bloco_conteudo">
        <div class="listagem">
            <table>
                <thead>
                    <tr>
    					<th class="menor">Ordem de serviço</th>
    					<th class="menor">Contrato</th>
    					<th class="menor">Cliente</th>
    					<th class="menor">Modelo</th>
    					<th class="menor">Placa</th>
    					<th class="menor">Instalador</th>
    					<th class="menor">UF</th>
    					<th class="menor">Envio de foto</th>
    					<th class="menor">Representante</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($this->view->dados) > 0):
                        $classeLinha = "par";
                        ?>

                        <?php foreach ($this->view->dados as $resultado) : ?>
                            <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
    							<tr class="<?php echo $classeLinha; ?>">
    								<td class=""><?php echo $resultado->ordoid; ?></td>
    								<td class=""><?php echo $resultado->ordconnumero; ?></td>
    								<td class=""><?php echo $resultado->clinome; ?></td>
    								<td class=""><?php echo $resultado->mlomodelo; ?></td>
    								<td class=""><?php echo $resultado->veiplaca; ?></td>
    								<td class=""><?php echo $resultado->nome_instalador; ?></td>
    								<td class=""><?php echo $resultado->uf_representante; ?></td>
    								<td class=""><?php echo $resultado->possui_fotografia; ?></td>
    								<td class=""><?php echo $resultado->repnome; ?></td>
    							</tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="9" class="centro">
                            <?php
                            //$totalRegistros = count($this->view->dados);
                            $totalRegistros = $this->view->totalResultados;
                            echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                            ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <?php echo ($this->view->totalResultados > 10) ? $this->view->paginacao : ''; ?>
        </div>
    </div>
</div>

