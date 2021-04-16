    <div class="resultado bloco_titulo">Histórico de Alterações</div>
<div class="resultado bloco_conteudo">
<?php if( $this->view->totalResultados > 0) :?>
    <?php echo $this->view->ordenacao; ?>
    <div class="listagem">
        <table>
            <thead>
                <tr>
					<th class="medio">Parâmetro</th>
					<th class="medio">Valor Original</th>
					<th class="medio">Valor Alterado</th>
					<th class="menor">Data</th>
					<th class="medio">Usuário</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($this->view->log) > 0):
                    $classeLinha = "par";
                    ?>

                    <?php foreach ($this->view->log as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
							<tr class="<?php echo $classeLinha; ?>">
								<td class=""><?php echo $resultado->pcslparametro; ?></td>
								<td class=""><?php echo wordwrap($resultado->pcslvalor_original, 55, "<br>", TRUE); ?></td>
								<td class=""><?php echo wordwrap($resultado->pcslvalor_alterado, 55, "<br>", TRUE); ?></td>
								<td class="centro"><?php echo $resultado->pcsldt_cadastro; ?></td>
								<td class=""><?php echo $resultado->nm_usuario; ?></td>
							</tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="centro">
                        <?php
                        $totalRegistros = count($this->view->log);
                        echo ($totalRegistros > 1) ? $totalRegistros . ' registros encontrados.' : '1 registro encontrado.';
                        ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php echo $this->view->paginacao; ?>
<?php endif; ?>
</div>