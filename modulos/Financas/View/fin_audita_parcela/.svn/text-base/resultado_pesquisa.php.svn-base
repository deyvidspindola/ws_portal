		

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
					<th class="menor">Número da Nota</th>
					<th class="menor">ID do Item da Nota</th>
					<th class="menor">Contrato</th>
					<th class="maior">Classe do Contrato (atual)</th>
					<th class="maior">Obrigação Financeira</th>
					<th class="medio">Data de Cancelamento da Nota</th>
					<th class="menor">Situação do Serviço (conssituacao)</th>
					<th class="medio">Data de Exclusão do Serviço</th>
					<th class="medio">Data de Instalação do Serviço</th>
					<th class="menor">Série da Nota</th>
					<th class="menor">Tipo do Item da Nota</th>					
					<th class="medio">Data de Referência da Nota</th>					
					<th class="menor">Parcela</th>
					<th class="menor">Total de Parcelas</th>
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
								<td class="esquerda"><?php echo $resultado['nfino_numero']; ?></td>
								<td class="esquerda"><?php echo $resultado['nfioid']; ?></td>
								<td class="esquerda"><?php echo $resultado['nficonoid']; ?></td>
								<td class="esquerda"><?php echo $resultado['eqcdescricao']; ?></td>
								<td class="esquerda"><?php echo $resultado['obrobrigacao']; ?></td>
								<td class="esquerda"><?php echo $resultado['nfldt_cancelamento']; ?></td>
								<td class="esquerda"><?php echo $resultado['conssituacao']; ?></td>
								<td class="esquerda"><?php echo $resultado['consiexclusao']; ?></td>
								<td class="esquerda"><?php echo $resultado['consinstalacao']; ?></td>
								<td class="esquerda"><?php echo $resultado['nflserie']; ?></td>
								<td class="esquerda"><?php echo $resultado['nfitipo']; ?></td>
								<td class="esquerda"><?php echo $resultado['nfidt_referencia']; ?></td>
								<td class="esquerda"><?php echo $resultado['nfiparcela']; ?></td>
								<td class="esquerda"><?php echo $resultado['cpvparcela']; ?></td>
							</tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="14" class="centro">
                        <?php
                        $totalRegistros = count($this->view->dados);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>