		

<div class="separador"></div>
<div class="resultado bloco_titulo">Resultado da Pesquisa</div>
<div class="resultado bloco_conteudo">
    <div class="listagem">
        <table>
            <thead>
                <tr>
					<th width="5%" class="menor">Código</th>
					<th class="menor">Dt. Inclusão</th>
					<th class="menor">Conceder em</th>
					<th class="maior">Cliente</th>
					<th width="5%" class="menor">CNPJ/CPF</th>
					<th class="menor">Protocolo</th>
					<th class="menor">Motivo do Crédito</th>
					<th class="menor">Tipo de Desconto</th>
					<th class="menor">% / Valor</th>
					<th class="menor">Forma de Aplicação</th>
					<th class="menor">Inclusão</th>
					<th width="10%" class="menor">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($this->view->dados) > 0):
                    $classeLinha = "par";
	                //echo '<pre>';
	                //var_dump($this->view->dados);
	                //echo '</pre>';
                    ?>

                    <?php foreach ($this->view->dados as $resultado) : ?>
                        <?php $classeLinha = ($classeLinha == "") ? "par" : ""; ?>
							<tr class="<?php echo $classeLinha; ?>">
								<td class="direita"><?php echo $resultado->cfooid; ?></td>
								<td class=""><?php echo $resultado->cfodt_inclusao; ?></td>
								<?php 
									$data_aprovacao = date('m/Y', strtotime($resultado->conceder_em));
								?>
								<td class="centro"><?php echo $data_aprovacao; ?></td>
								<td class=""><?php echo $resultado->clinome; ?></td>
								<td class=""><?php echo $resultado->clicpfcnpj; ?></td>
								<td class=""><?php echo $resultado->cfoancoid; ?></td>
								<td class=""><?php echo $resultado->cfmcdescricao; ?></td>
								<td class=""><?php echo $resultado->cfotipo_desconto; ?></td>
								<td class=""><?php echo trim($resultado->valorDesconto); ?></td>
								<td class=""><?php echo $resultado->cfoforma_aplicacao; ?></td>
								<td class=""><?php echo $resultado->cfoforma_inclusao; ?></td>
								<td class="direita">
			                        <?php $parcelas_label = $resultado->parcelas_ativas > 1 ? ' parcelas' : ' parcela' ?>
			                        <?php echo $resultado->cfoforma_aplicacao_id == '1' ? $resultado->cfosaldo : $resultado->cfpnumero . '/' . $resultado->parcelas_ativas . $parcelas_label; ?>
			                    </td>								
							</tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="12" class="centro">
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