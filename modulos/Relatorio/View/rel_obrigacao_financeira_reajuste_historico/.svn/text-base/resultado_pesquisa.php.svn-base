		

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
					<th class="menor">ofrhoid</th>
					<th class="menor">ofrhdt_inclusao</th>
					<th class="menor">ofrhdt_referencia</th>
					<th class="menor">ofrhusuoid_cadastro</th>
					<th class="menor">ofrhclioid</th>
					<th class="menor">ofrhconnumero</th>
					<th class="menor">ofrhtipo_reajuste</th>
					<th class="menor">ofrhvl_referencia</th>
					<th class="menor">ofrhobroid</th>
					<th class="menor">ofrhnfloid</th>
					<th class="menor">ofrhvalor_anterior</th>
					<th class="menor">ofrhvalor_reajustado</th>
					<th class="menor">ofrhdt_inicio_cobranca</th>
					<th class="menor">ofrhdt_fim_cobranca</th>

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
								<td class=""><?php echo $resultado->ofrhoid; ?></td>
								<td class=""><?php echo $resultado->ofrhdt_inclusao; ?></td>
								<td class=""><?php echo $resultado->ofrhdt_referencia; ?></td>
								<td class=""><?php echo $resultado->ofrhusuoid_cadastro; ?></td>
								<td class=""><?php echo $resultado->ofrhclioid; ?></td>
								<td class=""><?php echo $resultado->ofrhconnumero; ?></td>
								<td class=""><?php echo wordwrap($resultado->ofrhtipo_reajuste,30,"<br />", true); ?></td>
								<td class=""><?php echo $resultado->ofrhvl_referencia; ?></td>
								<td class=""><?php echo $resultado->ofrhobroid; ?></td>
								<td class=""><?php echo $resultado->ofrhnfloid; ?></td>
								<td class=""><?php echo $resultado->ofrhvalor_anterior; ?></td>
								<td class=""><?php echo $resultado->ofrhvalor_reajustado; ?></td>
								<td class=""><?php echo $resultado->ofrhdt_inicio_cobranca; ?></td>
								<td class=""><?php echo $resultado->ofrhdt_fim_cobranca; ?></td>
							</tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="centro">
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